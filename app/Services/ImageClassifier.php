<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ImageClassifier
{
    /**
     * Generate an image caption using a captioning model.
     * Returns a short natural language description of the image.
     */
    public function caption(string $absoluteImagePath): ?string
    {
        $token = config('services.huggingface.token');
        $model = config('services.huggingface.caption_model', 'nlpconnect/vit-gpt2-image-captioning');

        if (!is_readable($absoluteImagePath)) {
            Log::warning('ImageClassifier: caption image not readable', ['path' => $absoluteImagePath]);
            return null;
        }

        try {
            $bytes = @file_get_contents($absoluteImagePath);
            $mime = function_exists('mime_content_type') ? mime_content_type($absoluteImagePath) : 'image/jpeg';
            if ($bytes === false) {
                Log::warning('ImageClassifier: failed reading file for caption', ['path' => $absoluteImagePath]);
                return null;
            }

            Log::info('ImageClassifier: sending image to HF caption model', [
                'model' => $model,
                'path' => $absoluteImagePath,
                'mime' => $mime,
                'size' => strlen($bytes),
            ]);
            $response = Http::withToken($token)
                ->timeout(60)
                ->withHeaders(['Content-Type' => $mime])
                ->send('POST', "https://api-inference.huggingface.co/models/{$model}", [
                    'body' => $bytes,
                ]);

            if (!$response->ok()) {
                Log::warning('ImageClassifier: caption API error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return null;
            }

            $data = $response->json();
            // Expected like: [{"generated_text":"a man riding a bike ..."}]
            $text = $data[0]['generated_text'] ?? null;
            if ($text) {
                Log::info('ImageClassifier: caption generated', ['caption' => $text]);
            } else {
                Log::warning('ImageClassifier: caption missing in response', ['data' => $data]);
            }
            return $text ? trim($text) : null;
        } catch (\Throwable $e) {
            Log::error('ImageClassifier: caption exception', ['message' => $e->getMessage()]);
            return null;
        }
    }
    /**
     * Classify an image using Hugging Face Inference API.
     * Returns an array of labels with scores.
     *
     * @param string $absoluteImagePath Local absolute path to image
     * @return array<int, array{label:string, score:float}>
     */
    public function classify(string $absoluteImagePath): array
    {
        $token = config('services.huggingface.token');
        $model = config('services.huggingface.model', 'google/vit-base-patch16-224');

        if (!is_readable($absoluteImagePath)) {
            Log::warning('ImageClassifier: image not readable', ['path' => $absoluteImagePath]);
            return [];
        }

        try {
            $bytes = @file_get_contents($absoluteImagePath);
            $mime = function_exists('mime_content_type') ? mime_content_type($absoluteImagePath) : 'image/jpeg';
            if ($bytes === false) {
                Log::warning('ImageClassifier: failed reading file', ['path' => $absoluteImagePath]);
                return [];
            }

            Log::info('ImageClassifier: sending image to HF', [
                'model' => $model,
                'path' => $absoluteImagePath,
                'mime' => $mime,
                'size' => strlen($bytes),
            ]);
            $response = Http::withToken($token)
                ->timeout(30)
                ->withHeaders(['Content-Type' => $mime])
                ->send('POST', "https://api-inference.huggingface.co/models/{$model}", [
                    'body' => $bytes,
                ]);

            if (!$response->ok()) {
                Log::warning('ImageClassifier: API error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return [];
            }

            $data = $response->json();

            // Some HF models return nested array; normalize to flat label/score list
            if (isset($data[0]) && is_array($data[0]) && isset($data[0]['label'])) {
                Log::info('ImageClassifier: received labels', [
                    'count' => count($data),
                    'top' => $data[0] ?? null,
                ]);
                return array_map(function ($item) {
                    return [
                        'label' => (string) ($item['label'] ?? ''),
                        'score' => (float) ($item['score'] ?? 0),
                    ];
                }, $data);
            }

            // Fallback empty
            Log::warning('ImageClassifier: unexpected response format', [ 'data' => $data ]);
            return [];
        } catch (\Throwable $e) {
            Log::error('ImageClassifier: exception', ['message' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Map classifier labels to your internal category slug.
     * Supports: plastique, verre, papier, metal, textile, bois, organique, electronique.
     *
     * @param array<int, array{label:string, score:float}> $labels
     * @return string|null Category slug or null if not found
     */
    public function mapLabelsToCategory(array $labels): ?string
    {
        $labelString = strtolower(join(' ', array_map(fn($l) => $l['label'], $labels)));

        $mapping = [
            'plastique' => ['plastic', 'bottle', 'container', 'polypropylene', 'polyethylene', 'cup', 'package', 'bag'],
            'verre' => ['glass', 'wine', 'bottle', 'jar', 'cup'],
            'papier' => ['paper', 'newspaper', 'book', 'cardboard', 'magazine', 'envelope', 'box'],
            'metal' => ['can', 'aluminum', 'steel', 'tin', 'metal'],
            'textile' => ['cloth', 'tshirt', 'shirt', 'jeans', 'fabric', 'towel'],
            'bois' => ['wood', 'wooden', 'pallet'],
            'organique' => ['banana', 'apple', 'food', 'organic', 'vegetable', 'fruit'],
            'electronique' => ['phone', 'laptop', 'keyboard', 'mouse', 'remote'],
        ];

        foreach ($mapping as $category => $keywords) {
            foreach ($keywords as $kw) {
                if (str_contains($labelString, $kw)) {
                    Log::info('ImageClassifier: category mapped', [
                        'category' => $category,
                        'keyword' => $kw,
                    ]);
                    return $category;
                }
            }
        }

        Log::info('ImageClassifier: no category match', ['labels' => $labelString]);
        return null;
    }

    /**
     * Disambiguated mapping using both classifier labels and an optional caption.
     * Prioritizes material cues like glass vs plastic.
     */
    public function mapLabelsToCategoryWithCaption(array $labels, ?string $caption): ?string
    {
        $labelText = strtolower(join(' ', array_map(fn($l) => (string)($l['label'] ?? ''), $labels)));
        $captionText = strtolower((string) $caption);
        $text = trim($labelText . ' ' . $captionText);

        // Weighted keywords: higher weight resolves conflicts
        $weights = [
            'verre' => [
                'keywords' => ['glass', 'jar', 'goblet', 'vase', 'bocal', 'verre', 'transparent'],
                'weight' => 3,
            ],
            'plastique' => [
                'keywords' => ['plastic', 'bottle', 'container', 'polypropylene', 'polyethylene', 'pet', 'synthetic'],
                'weight' => 2,
            ],
            'papier' => [
                'keywords' => ['paper', 'newspaper', 'sheet', 'magazine', 'envelope', 'imprimé', 'feuille'],
                'weight' => 1,
            ],
            'carton' => [
                'keywords' => ['cardboard', 'carton', 'box', 'package', 'packaging', 'boite'],
                'weight' => 2,
            ],
            'metal' => [
                'keywords' => ['aluminum', 'steel', 'tin', 'can', 'metallic'],
                'weight' => 2,
            ],
            'textile' => [
                'keywords' => ['cloth', 'fabric', 'tshirt', 'shirt', 'jeans', 'textile', 'yarn'],
                'weight' => 1,
            ],
            'bois' => [
                'keywords' => ['wood', 'wooden', 'pallet', 'timber', 'bamboo'],
                'weight' => 1,
            ],
            'organique' => [
                'keywords' => ['banana', 'apple', 'food', 'organic', 'vegetable', 'fruit'],
                'weight' => 1,
            ],
            'electronique' => [
                'keywords' => ['phone', 'laptop', 'keyboard', 'mouse', 'remote', 'circuit'],
                'weight' => 1,
            ],
        ];

        $scores = [];
        foreach ($weights as $category => $cfg) {
            $score = 0;
            foreach ($cfg['keywords'] as $kw) {
                if (str_contains($text, $kw)) {
                    $score += $cfg['weight'];
                }
            }
            $scores[$category] = $score;
        }

        arsort($scores);
        $best = array_key_first($scores);
        $runnerUp = array_keys($scores)[1] ?? null;
        if ($best && $scores[$best] > 0) {
            // Resolve close ties favoring glass over plastic when both present
            if ($runnerUp && $scores[$best] === $scores[$runnerUp]) {
                if (in_array('verre', [$best, $runnerUp]) && in_array('plastique', [$best, $runnerUp])) {
                    $best = 'verre';
                }
            }
            Log::info('ImageClassifier: caption-aware category mapped', [
                'category' => $best,
                'scores' => $scores,
            ]);
            return $best;
        }

        // fallback to labels-only mapping
        return $this->mapLabelsToCategory($labels);
    }

    /**
     * Format strict feedback with category and description.
     * Category must be one of: Plastique, Métal, Verre, Bois, Papier, Carton, Textile, Autre
     * Description highlights transformation if provided; otherwise brief item description.
     *
     * @param string|null $categorySlug One of internal slugs: plastique, verre, papier, carton, metal, textile, bois, organique, electronique
     * @param string|null $transformation Concise upcycling sentence (e.g., "plastic bottle transformed into a flowerpot")
     * @param string|null $caption Optional caption describing the image
     * @param array<int, array{label:string, score:float}> $labels
     * @return array{category:string, description:string}
     */
    public function formatStrictFeedback(?string $categorySlug, ?string $transformation, ?string $caption, array $labels): array
    {
        $mapping = [
            'plastique' => 'Plastique',
            'verre' => 'Verre',
            'papier' => 'Papier',
            'carton' => 'Carton',
            'metal' => 'Métal',
            'textile' => 'Textile',
            'bois' => 'Bois',
        ];

        $category = $mapping[$categorySlug ?? ''] ?? 'Autre';

        // Build description
        $description = '';
        if ($transformation) {
            // Make it elegant and positive
            $nice = ucfirst(trim($transformation, '.')) . '.';
            // Add a touch if caption is available
            if ($caption) {
                $description = $nice . ' ' . ucfirst(rtrim($caption, '.')) . '.';
            } else {
                $description = $nice;
            }
        } else {
            // Brief clear description from top label or caption
            $top = $labels[0]['label'] ?? null;
            if ($top) {
                $description = ucfirst(trim((string)$top)) . '.';
            } elseif ($caption) {
                $description = ucfirst(rtrim($caption, '.')) . '.';
            } else {
                $description = 'Objet recyclé.';
            }
        }

        return [
            'category' => $category,
            'description' => $description,
        ];
    }

    /**
     * Generate a product name and description from labels
     *
     * @param array<int, array{label:string, score:float}> $labels
     * @param string|null $categorySlug
     * @return array{name:string, description:string}
     */
    public function generateMetadata(array $labels, ?string $categorySlug): array
    {
        $top = $labels[0]['label'] ?? 'Recycled Item';
        $score = isset($labels[0]['score']) ? number_format($labels[0]['score'] * 100, 1) : null;

        $categoryText = $categorySlug ? ucfirst($categorySlug) : 'Recyclable';
        $name = $categoryText . ' - ' . ucwords(str_replace(['_', '-'], ' ', (string) $top));

        $desc = "Automatically generated via AI image analysis. Top prediction: {$top}";
        if ($score) {
            $desc .= " ({$score}% confidence).";
        } else {
            $desc .= '.';
        }
        $desc .= " Category: {$categoryText}.";

        Log::info('ImageClassifier: generated metadata', [
            'name' => $name,
            'category' => $categorySlug,
            'top_label' => $top,
            'score_pct' => $score,
        ]);
        return [
            'name' => $name,
            'description' => $desc,
        ];
    }
}


