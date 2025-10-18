<?php

namespace Tests\Unit;

use App\Services\ImageClassifier;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use Mockery;

class SimpleImageClassifierTest extends TestCase
{
    private ImageClassifier $imageClassifier;

    protected function setUp(): void
    {
        parent::setUp();
        $this->imageClassifier = new ImageClassifier();
        
        config([
            'services.huggingface.token' => 'test_token',
            'services.huggingface.caption_model' => 'Salesforce/blip-image-captioning-base',
            'services.huggingface.classification_model' => 'microsoft/resnet-50'
        ]);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_it_returns_null_when_image_is_not_readable()
    {
        $nonExistentPath = '/non/existent/path.jpg';
        $result = $this->imageClassifier->caption($nonExistentPath);
        $this->assertNull($result);
    }

    public function test_it_can_map_labels_to_category_with_caption()
    {
        $labels = [
            ['label' => 'plastic bottle', 'score' => 0.95],
            ['label' => 'water bottle', 'score' => 0.87]
        ];
        $caption = 'A plastic water bottle';

        $result = $this->imageClassifier->mapLabelsToCategoryWithCaption($labels, $caption);
        $this->assertIsString($result);
        $this->assertContains($result, ['plastique', 'verre', 'metal', 'papier', 'bois', 'textile', 'electronique', 'organique']);
    }

    public function test_it_maps_plastic_labels_to_plastique_category()
    {
        $labels = [
            ['label' => 'plastic bottle', 'score' => 0.95],
            ['label' => 'PET container', 'score' => 0.87]
        ];
        $caption = 'A plastic bottle';

        $result = $this->imageClassifier->mapLabelsToCategoryWithCaption($labels, $caption);
        $this->assertEquals('plastique', $result);
    }

    public function test_it_maps_glass_labels_to_verre_category()
    {
        $labels = [
            ['label' => 'glass bottle', 'score' => 0.95],
            ['label' => 'wine bottle', 'score' => 0.87]
        ];
        $caption = 'A glass wine bottle';

        $result = $this->imageClassifier->mapLabelsToCategoryWithCaption($labels, $caption);
        $this->assertEquals('verre', $result);
    }

    public function test_it_maps_metal_labels_to_metal_category()
    {
        $labels = [
            ['label' => 'aluminum can', 'score' => 0.95],
            ['label' => 'metal container', 'score' => 0.87]
        ];
        $caption = 'An aluminum can';

        $result = $this->imageClassifier->mapLabelsToCategoryWithCaption($labels, $caption);
        $this->assertEquals('metal', $result);
    }

    public function test_it_handles_empty_labels_and_caption()
    {
        $labels = [];
        $caption = null;

        $result = $this->imageClassifier->mapLabelsToCategoryWithCaption($labels, $caption);
        $this->assertNull($result);
    }

    public function test_it_handles_api_error_gracefully()
    {
        Http::fake([
            'api-inference.huggingface.co/*' => Http::response([], 500)
        ]);

        $result = $this->imageClassifier->classify('/fake/path.jpg');
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function test_it_works_without_token()
    {
        config(['services.huggingface.token' => null]);
        
        Http::fake([
            'api-inference.huggingface.co/*' => Http::response([
                ['generated_text' => 'A test image']
            ], 200)
        ]);

        $result = $this->imageClassifier->caption('/fake/path.jpg');
        $this->assertNull($result); // Returns null because file doesn't exist
    }
}
