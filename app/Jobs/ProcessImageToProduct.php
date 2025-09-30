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
            Log::warning('ProcessImageToProduct: no labels returned, using fallback');
            $labels = [['label' => 'Recycled item', 'score' => 0.0]];
        }

        // Optional caption first (helps disambiguate material like glass vs plastic)
        $caption = $classifier->caption($this->absoluteImagePath);
        $categorySlug = $classifier->mapLabelsToCategoryWithCaption($labels, $caption);
        $meta = $classifier->generateMetadata($labels, $categorySlug);
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
        }

		// Strict feedback format (Category + Description)
		$strict = $classifier->formatStrictFeedback($categorySlug, $transformation, $caption, $labels);
		Log::info('ProcessImageToProduct: strict feedback', [
			'category_slug' => $categorySlug,
			'category' => $strict['category'] ?? null,
			'has_transformation' => (bool) $transformation,
			'has_caption' => (bool) $caption,
		]);

        // Create product using existing schema (images array, stock fields)
        $product = new Product();
        $product->name = $meta['name'];
		// slug and sku auto-generated in Product::boot if empty
		$descriptionHeader = 'Catégorie : ' . ($strict['category'] ?? 'Autre') . "\n" . 'Description : ' . ($strict['description'] ?? '') . "\n\n";
		Log::info('ProcessImageToProduct: building description', [
			'description_header_preview' => substr($descriptionHeader, 0, 120),
			'meta_name' => $meta['name'] ?? null,
		]);
        $description = $descriptionHeader . $meta['description'];
        if ($transformation) {
            $description = "Upcycled: " . $transformation . "\n\n" . $description;
        }
        if ($caption) {
            $description .= "\n\nImage description: " . $caption;
        }
		Log::info('ProcessImageToProduct: final description preview', [
			'preview' => substr($description, 0, 200)
		]);
        $product->description = $description;
        // Save structured meta
        $product->meta_data = [
            'feedback' => $strict,
            'ai' => [
                'labels' => $labels,
                'caption' => $caption,
                'category_slug' => $categorySlug,
                'transformation' => $transformation,
            ],
        ];
		Log::info('ProcessImageToProduct: meta_data set', [
			'has_feedback' => isset($product->meta_data['feedback']),
			'has_ai' => isset($product->meta_data['ai']),
			'labels_count' => is_array($labels) ? count($labels) : 0,
		]);
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

		Log::info('ProcessImageToProduct: saving product');
		$product->save();
		Log::info('ProcessImageToProduct: product created', [
            'product_id' => $product->id,
            'name' => $product->name,
            'category_id' => $product->category_id,
			'has_images' => is_array($product->images) && count($product->images) > 0,
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


