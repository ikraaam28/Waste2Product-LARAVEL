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
            $client = Http::timeout(60);
            if (!empty($token)) {
                $client = $client->withToken($token);
            } else {
                Log::warning('ImageClassifier: no HF token configured, calling public endpoint (may be rate limited)');
            }
            $response = $client
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
            $client = Http::timeout(30);
            if (!empty($token)) {
                $client = $client->withToken($token);
            } else {
                Log::warning('ImageClassifier: no HF token configured, calling public endpoint (may be rate limited)');
            }
            $response = $client
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
                'keywords' => ['paper', 'newspaper', 'cardboard', 'box', 'magazine', 'carton', 'envelope'],
                'weight' => 1,
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

        // Heuristic fallback: infer material from generic object types
        // Furniture -> bois if wood cues or typical wooden objects appear
        $woodHints = ['wood', 'wooden', 'timber', 'table', 'desk', 'chair', 'cabinet', 'drawer', 'shelf', 'wardrobe', 'pallet'];
        foreach ($woodHints as $w) {
            if (str_contains($text, $w)) {
                Log::info('ImageClassifier: heuristic material -> bois', ['hint' => $w]);
                return 'bois';
            }
        }

        // Metal cues
        $metalHints = ['metal', 'steel', 'iron', 'aluminum', 'aluminium', 'tin', 'can', 'bolt', 'screw', 'wrench'];
        foreach ($metalHints as $m) {
            if (str_contains($text, $m)) {
                Log::info('ImageClassifier: heuristic material -> metal', ['hint' => $m]);
                return 'metal';
            }
        }

        // Glass cues
        $glassHints = ['glass', 'bottle', 'jar', 'vase', 'goblet'];
        foreach ($glassHints as $g) {
            if (str_contains($text, $g)) {
                Log::info('ImageClassifier: heuristic material -> verre', ['hint' => $g]);
                return 'verre';
            }
        }

        // Paper/Cardboard cues
        $paperHints = ['paper', 'cardboard', 'box', 'carton', 'newspaper', 'magazine'];
        foreach ($paperHints as $p) {
            if (str_contains($text, $p)) {
                Log::info('ImageClassifier: heuristic material -> papier', ['hint' => $p]);
                return 'papier';
            }
        }

        // Fallback to labels-only mapping
        return $this->mapLabelsToCategory($labels);
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

    /**
     * Zero-shot material detection using a VLM classifier (e.g., CLIP zero-shot image classification)
     * Tries to classify among a fixed set of materials including wood.
     * Returns slug among: plastique, verre, papier, metal, textile, bois, organique, electronique
     */
    public function zeroShotMaterial(string $absoluteImagePath): ?string
    {
        $token = config('services.huggingface.token');
        $model = config('services.huggingface.zeroshot_model', 'openai/clip-vit-base-patch32');

        if (!is_readable($absoluteImagePath)) {
            Log::warning('ImageClassifier: zeroShotMaterial image not readable', ['path' => $absoluteImagePath]);
            return null;
        }

        $candidates = [
            'plastique', 'verre', 'papier', 'metal', 'textile', 'bois', 'organique', 'electronique'
        ];
        $prompts = [
            'plastique' => 'a product made of plastic',
            'verre' => 'a product made of glass',
            'papier' => 'a product made of paper or cardboard',
            'metal' => 'a product made of metal',
            'textile' => 'a product made of fabric or textile',
            'bois' => 'a product made of wood',
            'organique' => 'organic material like food or plants',
            'electronique' => 'an electronic device or component',
        ];

        try {
            $bytes = @file_get_contents($absoluteImagePath);
            $mime = function_exists('mime_content_type') ? mime_content_type($absoluteImagePath) : 'image/jpeg';
            if ($bytes === false) {
                Log::warning('ImageClassifier: zeroShotMaterial failed reading file', ['path' => $absoluteImagePath]);
                return null;
            }

            // Some HF zero-shot pipelines accept inputs as multipart or JSON with text candidates.
            // We'll send as a JSON payload with base64 image and candidate labels if supported by a hosted space.
            // Since the public model endpoints for CLIP may not support this directly, we fallback to caption+keywords if call fails.
            $client = Http::timeout(30);
            if (!empty($token)) {
                $client = $client->withToken($token);
            } else {
                Log::warning('ImageClassifier: zeroShotMaterial no HF token configured');
            }

            $response = $client->post('https://api-inference.huggingface.co/models/' . $model, [
                'parameters' => [
                    'candidate_labels' => array_values($prompts),
                    'multi_label' => false,
                ],
                'inputs' => base64_encode($bytes),
            ]);

            if ($response->ok()) {
                $data = $response->json();
                // Expected like: { labels: [..], scores: [..] }
                $labels = $data['labels'] ?? [];
                $scores = $data['scores'] ?? [];
                if ($labels && $scores) {
                    $topIndex = 0;
                    $topScore = -1;
                    foreach ($scores as $i => $s) {
                        if ($s > $topScore) { $topScore = $s; $topIndex = $i; }
                    }
                    $topPrompt = $labels[$topIndex] ?? null;
                    if ($topPrompt) {
                        // Map back from prompt to slug
                        foreach ($prompts as $slug => $promptText) {
                            if ($promptText === $topPrompt) {
                                Log::info('ImageClassifier: zero-shot material detected', ['slug' => $slug, 'score' => $topScore]);
                                return $slug;
                            }
                        }
                    }
                }
            } else {
                Log::warning('ImageClassifier: zeroShotMaterial API error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('ImageClassifier: zeroShotMaterial exception', ['message' => $e->getMessage()]);
        }

        // Fallback heuristic: try caption + keywords emphasizing wood
        $caption = $this->caption($absoluteImagePath);
        $text = strtolower((string) $caption);
        if (str_contains($text, 'wood') || str_contains($text, 'wooden') || str_contains($text, 'table')) {
            Log::info('ImageClassifier: zeroShotMaterial fallback -> bois', ['caption' => $caption]);
            return 'bois';
        }
        return null;
    }
}


