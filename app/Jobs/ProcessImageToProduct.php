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
        // If name is too generic or labels empty, try to synthesize from caption
        if ((!$labels || empty($labels)) && $caption) {
            $meta['name'] = ($categorySlug ? ucfirst($categorySlug) . ' - ' : '') . ucfirst(str_replace('.', '', strtok($caption, '.')));
            $meta['description'] = 'Generated from caption: ' . $caption;
        }
        // Try to synthesize a concise upcycling description like: "plastic bottle transformed into a flowerpot"
        $transformation = $this->synthesizeTransformation($labels, $caption, $categorySlug);
        if (!$transformation && $caption) {
            // attempt to infer transformation specifically for bird-feeder patterns
            $lower = strtolower($caption);
            if (str_contains($lower, 'bird') && (str_contains($lower, 'bottle') || str_contains($lower, 'plastic'))) {
                $transformation = 'plastic bottle transformed into a bird feeder';
            }
        }

        $categoryId = null;
        if ($categorySlug) {
            $category = Category::firstOrCreate(['slug' => $categorySlug], [
                'name' => ucfirst($categorySlug),
                'description' => 'Auto-created category for AI-detected items',
            ]);
            $categoryId = $category->id;
        } else {
            // Fallback to a default category to satisfy NOT NULL constraint
            $fallbackSlug = 'recyclable';
            $fallbackCategory = Category::firstOrCreate(['slug' => $fallbackSlug], [
                'name' => 'Recyclable',
                'description' => 'Default category for AI imports when material is unknown',
            ]);
            $categoryId = $fallbackCategory->id;
            Log::info('ProcessImageToProduct: using fallback category', [
                'slug' => $fallbackSlug,
                'id' => $categoryId,
            ]);
        }

        // Create product using existing schema (images array, stock fields)
        $product = new Product();
        $product->name = $meta['name'];
        // slug and sku auto-generated in Product::boot if empty
        $description = $meta['description'];
        if ($transformation) {
            $description = "Upcycled: " . $transformation . "\n\n" . $description;
        }
        if ($caption) {
            $description .= "\n\nImage description: " . $caption;
        }
        $product->description = $description;
        $product->price = 0.00;
        $product->stock_quantity = 1;
        $product->manage_stock = false;
        $product->stock_status = 'in_stock';
        $product->is_featured = false;
        $product->is_active = true;
        $product->category_id = $categoryId;
        $product->created_by = $this->userId ?: 1;

        // Store image path under images array
        try {
            $storagePath = storage_path('app/public');
            if (str_starts_with($this->absoluteImagePath, $storagePath)) {
                $relative = ltrim(str_replace($storagePath, '', $this->absoluteImagePath), DIRECTORY_SEPARATOR);
                $product->images = [$relative];
            }
        } catch (\Throwable $e) {
            // ignore
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
            throw $e;
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
}


