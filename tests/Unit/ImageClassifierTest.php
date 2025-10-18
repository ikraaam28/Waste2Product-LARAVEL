<?php

namespace Tests\Unit;

use App\Services\ImageClassifier;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;
use Mockery;

class ImageClassifierTest extends TestCase
{
    private ImageClassifier $imageClassifier;

    protected function setUp(): void
    {
        parent::setUp();
        $this->imageClassifier = new ImageClassifier();
        
        // Mock configuration
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

    /** @test */
    public function it_can_caption_an_image_successfully()
    {
        // Arrange
        $imagePath = storage_path('app/test-image.jpg');
        $this->createTestImage($imagePath);
        
        Http::fake([
            'api-inference.huggingface.co/*' => Http::response([
                ['generated_text' => 'A bottle of water']
            ], 200)
        ]);

        // Act
        $result = $this->imageClassifier->caption($imagePath);

        // Assert
        $this->assertEquals('A bottle of water', $result);
        
        // Cleanup
        unlink($imagePath);
    }

    /** @test */
    public function it_returns_null_when_image_is_not_readable()
    {
        // Arrange
        $nonExistentPath = '/non/existent/path.jpg';

        // Act
        $result = $this->imageClassifier->caption($nonExistentPath);

        // Assert
        $this->assertNull($result);
    }

    /** @test */
    public function it_handles_api_error_gracefully()
    {
        // Arrange
        $imagePath = storage_path('app/test-image.jpg');
        $this->createTestImage($imagePath);
        
        Http::fake([
            'api-inference.huggingface.co/*' => Http::response([], 500)
        ]);

        // Act
        $result = $this->imageClassifier->caption($imagePath);

        // Assert
        $this->assertNull($result);
        
        // Cleanup
        unlink($imagePath);
    }

    /** @test */
    public function it_can_classify_an_image()
    {
        // Arrange
        $imagePath = storage_path('app/test-image.jpg');
        $this->createTestImage($imagePath);
        
        Http::fake([
            'api-inference.huggingface.co/*' => Http::response([
                [
                    'label' => 'plastic bottle',
                    'score' => 0.95
                ],
                [
                    'label' => 'water bottle',
                    'score' => 0.87
                ]
            ], 200)
        ]);

        // Act
        $result = $this->imageClassifier->classify($imagePath);

        // Assert
        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertEquals('plastic bottle', $result[0]['label']);
        $this->assertEquals(0.95, $result[0]['score']);
        
        // Cleanup
        unlink($imagePath);
    }

    /** @test */
    public function it_returns_empty_array_when_classification_fails()
    {
        // Arrange
        $imagePath = storage_path('app/test-image.jpg');
        $this->createTestImage($imagePath);
        
        Http::fake([
            'api-inference.huggingface.co/*' => Http::response([], 500)
        ]);

        // Act
        $result = $this->imageClassifier->classify($imagePath);

        // Assert
        $this->assertIsArray($result);
        $this->assertEmpty($result);
        
        // Cleanup
        unlink($imagePath);
    }

    /** @test */
    public function it_can_map_labels_to_category_with_caption()
    {
        // Arrange
        $labels = [
            ['label' => 'plastic bottle', 'score' => 0.95],
            ['label' => 'water bottle', 'score' => 0.87]
        ];
        $caption = 'A plastic water bottle';

        // Act
        $result = $this->imageClassifier->mapLabelsToCategoryWithCaption($labels, $caption);

        // Assert
        $this->assertIsString($result);
        $this->assertContains($result, ['plastique', 'verre', 'metal', 'papier', 'bois', 'textile', 'electronique', 'organique']);
    }

    /** @test */
    public function it_maps_plastic_labels_to_plastique_category()
    {
        // Arrange
        $labels = [
            ['label' => 'plastic bottle', 'score' => 0.95],
            ['label' => 'PET container', 'score' => 0.87]
        ];
        $caption = 'A plastic bottle';

        // Act
        $result = $this->imageClassifier->mapLabelsToCategoryWithCaption($labels, $caption);

        // Assert
        $this->assertEquals('plastique', $result);
    }

    /** @test */
    public function it_maps_glass_labels_to_verre_category()
    {
        // Arrange
        $labels = [
            ['label' => 'glass bottle', 'score' => 0.95],
            ['label' => 'wine bottle', 'score' => 0.87]
        ];
        $caption = 'A glass wine bottle';

        // Act
        $result = $this->imageClassifier->mapLabelsToCategoryWithCaption($labels, $caption);

        // Assert
        $this->assertEquals('verre', $result);
    }

    /** @test */
    public function it_maps_metal_labels_to_metal_category()
    {
        // Arrange
        $labels = [
            ['label' => 'aluminum can', 'score' => 0.95],
            ['label' => 'metal container', 'score' => 0.87]
        ];
        $caption = 'An aluminum can';

        // Act
        $result = $this->imageClassifier->mapLabelsToCategoryWithCaption($labels, $caption);

        // Assert
        $this->assertEquals('metal', $result);
    }

    /** @test */
    public function it_handles_empty_labels_and_caption()
    {
        // Arrange
        $labels = [];
        $caption = null;

        // Act
        $result = $this->imageClassifier->mapLabelsToCategoryWithCaption($labels, $caption);

        // Assert
        $this->assertNull($result);
    }

    /** @test */
    public function it_uses_caption_when_labels_are_ambiguous()
    {
        // Arrange
        $labels = [
            ['label' => 'container', 'score' => 0.5],
            ['label' => 'object', 'score' => 0.3]
        ];
        $caption = 'A cardboard box';

        // Act
        $result = $this->imageClassifier->mapLabelsToCategoryWithCaption($labels, $caption);

        // Assert
        $this->assertEquals('bois', $result);
    }

    /** @test */
    public function it_handles_network_timeout()
    {
        // Arrange
        $imagePath = storage_path('app/test-image.jpg');
        $this->createTestImage($imagePath);
        
        Http::fake([
            'api-inference.huggingface.co/*' => Http::response([], 408)
        ]);

        // Act
        $result = $this->imageClassifier->classify($imagePath);

        // Assert
        $this->assertIsArray($result);
        $this->assertEmpty($result);
        
        // Cleanup
        unlink($imagePath);
    }

    /** @test */
    public function it_works_without_token()
    {
        // Arrange
        config(['services.huggingface.token' => null]);
        $imagePath = storage_path('app/test-image.jpg');
        $this->createTestImage($imagePath);
        
        Http::fake([
            'api-inference.huggingface.co/*' => Http::response([
                ['generated_text' => 'A test image']
            ], 200)
        ]);

        // Act
        $result = $this->imageClassifier->caption($imagePath);

        // Assert
        $this->assertEquals('A test image', $result);
        
        // Cleanup
        unlink($imagePath);
    }

    private function createTestImage(string $path): void
    {
        // Create a simple test file instead of using GD functions
        file_put_contents($path, 'fake image content for testing');
    }
}