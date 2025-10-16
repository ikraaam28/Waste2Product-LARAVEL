<?php

namespace App\Jobs;

use App\Models\Category;
use App\Models\Product;
use App\Services\ImageClassifier;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProcessImageToProduct implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $absoluteImagePath;
    public ?int $userId;

    public function __construct(string $absoluteImagePath, ?int $userId = null)
    {
        $this->absoluteImagePath = $absoluteImagePath;
        $this->userId = $userId;
    }

    public function handle(ImageClassifier $classifier): void
    {
        Log::info('ProcessImageToProduct: start', ['path' => $this->absoluteImagePath, 'user' => $this->userId]);
        $labels = $classifier->classify($this->absoluteImagePath);
        if (empty($labels)) {
            Log::warning('ProcessImageToProduct: no labels returned, attempting caption fallback');
        }

        // Optional caption first (helps disambiguate material like glass vs plastic)
        $caption = $classifier->caption($this->absoluteImagePath);
        $categorySlug = $classifier->mapLabelsToCategoryWithCaption($labels, $caption);
        // Normalize to app category slugs (fr): plastique, verre, metal, papier, bois, textile, electronique, organique
        $categorySlug = $this->normalizeCategorySlug($categorySlug, $labels, $caption);
        // Extra fallback: infer from filename
        if (!$categorySlug) {
            $base = strtolower(basename($this->absoluteImagePath));
            if (preg_match('/(plastic|plastique|pet|bouteille|flacon|sachet|barquette|tray)/', $base)) {
                $categorySlug = 'plastique';
            } elseif (preg_match('/(glass|verre|bocal|vase|jar)/', $base)) {
                $categorySlug = 'verre';
            } elseif (preg_match('/(wood|bois|pallet|palette|planche|board)/', $base)) {
                $categorySlug = 'bois';
            } elseif (preg_match('/(metal|métal|steel|iron|aluminium|aluminum|can|canette)/', $base)) {
                $categorySlug = 'metal';
            } elseif (preg_match('/(paper|papier|cardboard|carton|box|boîte)/', $base)) {
                $categorySlug = 'papier';
            } elseif (preg_match('/(textile|fabric|cloth|tshirt|jeans|denim|vêtement)/', $base)) {
                $categorySlug = 'textile';
            }
        }
        if (!$categorySlug) {
            // Try zero-shot material detector for better precision (e.g., bois for wooden table)
            try {
                $zeroShot = $classifier->zeroShotMaterial($this->absoluteImagePath);
                if ($zeroShot) {
                    $categorySlug = $zeroShot;
                    Log::info('ProcessImageToProduct: zero-shot override category', ['category_slug' => $categorySlug]);
                }
            } catch (\Throwable $e) {
                Log::warning('ProcessImageToProduct: zero-shot detection failed', ['error' => $e->getMessage()]);
            }
        }
        Log::info('ProcessImageToProduct: mapping result', [
            'caption' => $caption,
            'category_slug' => $categorySlug,
            'labels' => $labels,
        ]);
        $meta = $classifier->generateMetadata($labels, $categorySlug);
        Log::info('ProcessImageToProduct: meta generated', $meta);
        // Build French transformation sentence (e.g., "Bouteille en plastique transformée en porte-stylos")
        $transformation = $this->synthesizeTransformationFr($labels, $caption, $categorySlug);
        
        // Always derive natural French name/description per business rules
        $nameFr = $this->buildFrenchName($categorySlug, $transformation, $caption);
        $descFr = $this->buildFrenchDescription($categorySlug, $transformation, $caption, $labels);
        if (!$transformation && $caption) {
            // attempt to infer transformation specifically for bird-feeder patterns
            $lower = strtolower($caption);
            if (str_contains($lower, 'bird') && (str_contains($lower, 'bottle') || str_contains($lower, 'plastic'))) {
                $transformation = 'plastic bottle transformed into a bird feeder';
            }
        }

        $categoryId = null;
        if ($categorySlug) {
            Log::info('ProcessImageToProduct: creating/fetching category', ['slug' => $categorySlug]);
            $category = Category::firstOrCreate(['slug' => $categorySlug], [
                'name' => ucfirst($categorySlug),
                'description' => 'Auto-created category for AI-detected items',
            ]);
            $categoryId = $category->id;
        } else {
            // Fallback to a default category to satisfy NOT NULL constraint
            $fallbackSlug = 'inconnu';
            $fallbackCategory = Category::firstOrCreate(['slug' => $fallbackSlug], [
                'name' => 'Inconnu',
                'description' => 'Catégorie inconnue détectée par IA',
            ]);
            $categoryId = $fallbackCategory->id;
            Log::info('ProcessImageToProduct: using fallback category', [
                'slug' => $fallbackSlug,
                'id' => $categoryId,
            ]);
        }

        // Create product using existing schema (images array, stock fields)
        $product = new Product();
        $product->name = $nameFr;
        // slug and sku auto-generated in Product::boot if empty
        $description = $descFr;
        $product->description = $description;
        $product->price = 0.00;
        $product->stock_quantity = 1;
        $product->manage_stock = false;
        $product->stock_status = 'in_stock';
        $product->is_featured = false;
        $product->is_active = true;
        $product->category_id = $categoryId;
        $product->created_by = $this->userId ?: 1;

        // Ensure unique slug (avoid SQL duplicate constraint)
        try {
            $baseSlug = Str::slug($product->name);
            $product->slug = $this->generateUniqueSlug($baseSlug);
        } catch (\Throwable $e) {
            // fallback: random slug if something goes wrong
            $product->slug = Str::slug($product->name) . '-' . strtolower(Str::random(6));
        }

        // Store image path under images array
        try {
            $storagePath = storage_path('app/public');
            if (str_starts_with($this->absoluteImagePath, $storagePath)) {
                $relative = ltrim(str_replace($storagePath, '', $this->absoluteImagePath), DIRECTORY_SEPARATOR);
                $product->images = [$relative];
                Log::info('ProcessImageToProduct: computed relative image path', ['relative' => $relative]);
            }
        } catch (\Throwable $e) {
            Log::warning('ProcessImageToProduct: image path processing failed', ['error' => $e->getMessage()]);
        }

        try {
            $product->save();
        } catch (\Throwable $e) {
            Log::error('ProcessImageToProduct: failed saving product', [
                'error' => $e->getMessage(),
                'payload' => [
                    'name' => $product->name,
                    'category_id' => $product->category_id,
                    'images' => $product->images,
                ]
            ]);
            // Retry once with a new unique slug if duplicate key on slug
            if (str_contains(strtolower($e->getMessage()), 'duplicate') && str_contains(strtolower($e->getMessage()), 'slug')) {
                try {
                    $product->slug = Str::slug($product->name) . '-' . strtolower(Str::random(6));
                    $product->save();
                } catch (\Throwable $e2) {
                    Log::error('ProcessImageToProduct: retry save failed', ['error' => $e2->getMessage()]);
                    throw $e2;
                }
            } else {
                throw $e;
            }
        }
        Log::info('ProcessImageToProduct: product created', [
            'product_id' => $product->id,
            'name' => $product->name,
            'category_id' => $product->category_id,
        ]);
    }

    /**
     * Build a short transformation sentence from labels/caption/category.
     * Example: "A plastic bottle transformed into a flowerpot".
     */
    private function synthesizeTransformation(array $labels, ?string $caption, ?string $categorySlug): ?string
    {
        $labelText = strtolower(implode(' ', array_map(fn($l) => (string)($l['label'] ?? ''), $labels)));
        $captionText = strtolower(trim((string)$caption));

        $sourceKeywords = [
            'bottle' => ['bottle', 'bouteille'],
            'jar' => ['jar', 'bocal'],
            'can' => ['can', 'canette', 'tin'],
            'box' => ['box', 'carton', 'cardboard'],
            'bag' => ['bag', 'sac'],
            'fabric' => ['cloth', 'fabric', 'tshirt', 'shirt', 'jeans', 'textile'],
            'wood' => ['wood', 'pallet'],
            'electronic' => ['phone', 'laptop', 'keyboard', 'mouse', 'remote'],
        ];

        $targetKeywords = [
            'flowerpot' => ['flowerpot', 'planter', 'vase', 'pot'],
            'storage' => ['storage', 'organizer', 'box', 'holder', 'container'],
            'lamp' => ['lamp', 'lantern', 'light'],
            'decor' => ['decor', 'decoration', 'ornament'],
        ];

        $materialByCategory = [
            'plastique' => 'plastic',
            'verre' => 'glass',
            'papier' => 'paper',
            'metal' => 'metal',
            'textile' => 'textile',
            'bois' => 'wood',
            'organique' => 'organic material',
            'electronique' => 'electronic component',
        ];

        $text = $labelText . ' ' . $captionText;

        $source = null;
        foreach ($sourceKeywords as $name => $words) {
            foreach ($words as $w) {
                if (str_contains($text, $w)) { $source = $name; break 2; }
            }
        }

        $target = null;
        foreach ($targetKeywords as $name => $words) {
            foreach ($words as $w) {
                if (str_contains($text, $w)) { $target = $name; break 2; }
            }
        }

        $material = $materialByCategory[$categorySlug ?? ''] ?? null;

        if (!$source && !$target && !$material) {
            return null;
        }

        // Build sentence
        $parts = [];
        if ($material) {
            $parts[] = $material;
        }
        $sourceNoun = $source ? $source : 'item';
        $start = trim(implode(' ', [$parts ? implode(' ', $parts) : null, $sourceNoun]));
        $end = $target ? ($target === 'flowerpot' ? 'flowerpot' : $target) : 'new product';
        $sentence = ucfirst(trim($start)) . ' transformed into a ' . $end;

        return $sentence;
    }

    private function generateUniqueSlug(string $baseSlug): string
    {
        $slug = $baseSlug ?: strtolower(Str::random(8));
        $original = $slug;
        $i = 1;
        while (Product::where('slug', $slug)->exists()) {
            $slug = $original . '-' . $i;
            $i++;
            if ($i > 50) {
                $slug = $original . '-' . strtolower(Str::random(6));
                break;
            }
        }
        return $slug;
    }

    /**
     * Map model category or materials to app slugs (plastique, verre, metal, papier, bois, textile, electronique, organique)
     */
    private function normalizeCategorySlug(?string $categorySlug, array $labels, ?string $caption): ?string
    {
        $text = strtolower(trim(($caption ?? '')) . ' ' . implode(' ', array_map(fn($l) => strtolower((string)($l['label'] ?? '')), $labels)));

        $maps = [
            'plastique' => ['plastic', 'plastique', 'pet', 'polyethylene'],
            'verre' => ['glass', 'verre'],
            'metal' => ['metal', 'métal', 'aluminum', 'steel', 'fer', 'alu'],
            'papier' => ['paper', 'papier', 'cardboard', 'carton'],
            'bois' => ['wood', 'bois', 'pallet'],
            'textile' => ['textile', 'cloth', 'fabric', 'tshirt', 'jeans', 'shirt'],
            'electronique' => ['electronic', 'electronics', 'phone', 'laptop', 'keyboard'],
            'organique' => ['organic', 'food', 'vegetable', 'fruit', 'compost']
        ];

        foreach ($maps as $slug => $keywords) {
            foreach ($keywords as $kw) {
                if (str_contains($text, $kw)) {
                    return $slug;
                }
            }
        }
        return $categorySlug ? strtolower($categorySlug) : null;
    }

    /**
     * Build French transformation text from labels/caption/category
     */
    private function synthesizeTransformationFr(array $labels, ?string $caption, ?string $categorySlug): ?string
    {
        $en = $this->synthesizeTransformation($labels, $caption, $categorySlug);
        if (!$en) return null;
        // Rough translation rules for common phrases
        $map = [
            'plastic' => 'plastique',
            'glass' => 'verre',
            'paper' => 'papier',
            'metal' => 'métal',
            'wood' => 'bois',
            'textile' => 'textile',
            'electronic' => 'électronique',
            'organic material' => 'matière organique',
            'bottle' => 'bouteille',
            'jar' => 'bocal',
            'can' => 'canette',
            'box' => 'boîte',
            'item' => 'objet',
            'flowerpot' => 'pot de fleur',
            'storage' => 'rangement',
            'lamp' => 'lampe',
            'decor' => 'décoration',
            'transformed into a' => 'transformée en',
        ];
        $fr = $en;
        foreach ($map as $from => $to) {
            $fr = str_ireplace($from, $to, $fr);
        }
        // Improve phrasing: "Bouteille en plastique transformée en porte-stylos"
        $fr = preg_replace('/^([A-Za-zéèêàùç\s]+)\s(bois|plastique|verre|papier|métal|textile|électronique|matière organique)/ui', '$1 en $2', $fr);
        return ucfirst($fr);
    }

    private function buildFrenchName(?string $categorySlug, ?string $transformation, ?string $caption): string
    {
        if ($transformation) {
            return $transformation;
        }
        if ($caption) {
            $base = ucfirst(trim(str_replace(['.', '#'], '', strtok($caption, '.'))));
            return $base;
        }
        return ucfirst(($categorySlug ?: 'Objet recyclé')) . ' - Création recyclée';
    }

    private function buildFrenchDescription(?string $categorySlug, ?string $transformation, ?string $caption, array $labels): string
    {
        $parts = [];
        if ($transformation) $parts[] = $transformation;
        if ($caption) $parts[] = 'Analyse IA: ' . $caption;
        if (!empty($labels)) {
            $top = array_slice(array_map(fn($l) => $l['label'] ?? null, $labels), 0, 5);
            $top = array_filter($top);
            if ($top) $parts[] = 'Mots-clés: ' . implode(', ', $top);
        }
        if ($categorySlug) $parts[] = 'Catégorie: ' . ucfirst($categorySlug);
        return implode("\n\n", $parts) ?: 'Produit recyclé généré automatiquement.';
    }
}


