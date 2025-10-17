<?php

namespace Tests\Unit;

use App\Services\ImageClassifier;
use Tests\TestCase;

class ImageClassifierTest extends TestCase
{
    public function test_map_labels_to_category_matches_keywords(): void
    {
        $svc = new ImageClassifier();
        $labels = [
            ['label' => 'plastic bottle', 'score' => 0.9],
            ['label' => 'container', 'score' => 0.5],
        ];
        $this->assertSame('plastique', $svc->mapLabelsToCategory($labels));

        $labels = [ ['label' => 'glass jar', 'score' => 0.8] ];
        $this->assertSame('verre', $svc->mapLabelsToCategory($labels));

        $labels = [ ['label' => 'cardboard box', 'score' => 0.7] ];
        $this->assertSame('papier', $svc->mapLabelsToCategory($labels));

        $labels = [ ['label' => 'steel can', 'score' => 0.6] ];
        $this->assertSame('metal', $svc->mapLabelsToCategory($labels));

        $labels = [ ['label' => 'wooden pallet', 'score' => 0.6] ];
        $this->assertSame('bois', $svc->mapLabelsToCategory($labels));
    }

    public function test_map_labels_to_category_with_caption_disambiguates(): void
    {
        $svc = new ImageClassifier();
        $labels = [
            ['label' => 'bottle', 'score' => 0.8],
            ['label' => 'jar', 'score' => 0.79],
        ];
        // With glass cues, should prefer 'verre'
        $this->assertSame('verre', $svc->mapLabelsToCategoryWithCaption($labels, 'transparent glass bottle'));

        // With wood cues, should favor 'bois' when close to paper
        $labels = [ ['label' => 'box', 'score' => 0.8] ];
        $this->assertSame('bois', $svc->mapLabelsToCategoryWithCaption($labels, 'wooden table'));
    }

    public function test_generate_metadata_includes_top_label_and_category(): void
    {
        $svc = new ImageClassifier();
        $labels = [ ['label' => 'plastic', 'score' => 0.42] ];
        $meta = $svc->generateMetadata($labels, 'plastique');

        $this->assertIsArray($meta);
        $this->assertArrayHasKey('name', $meta);
        $this->assertArrayHasKey('description', $meta);
        $this->assertStringContainsString('Plastique', $meta['name']);
        $this->assertStringContainsString('plastic', $meta['description']);
    }
}
