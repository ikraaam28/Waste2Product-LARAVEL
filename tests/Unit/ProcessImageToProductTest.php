<?php

namespace Tests\Unit;

use App\Jobs\ProcessImageToProduct;
use App\Models\Category;
use App\Models\Product;
use App\Services\ImageClassifier;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Mockery;

class ProcessImageToProductTest extends TestCase
{
    use DatabaseTransactions;

    private ImageClassifier $mockClassifier;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test categories
        Category::create([
            'name' => 'Plastique',
            'slug' => 'plastique',
            'description' => 'Matériaux plastiques',
            'color' => '#FF6B6B'
        ]);
        
        Category::create([
            'name' => 'Verre',
            'slug' => 'verre',
            'description' => 'Matériaux en verre',
            'color' => '#4ECDC4'
        ]);

        $this->mockClassifier = Mockery::mock(ImageClassifier::class);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function it_processes_image_successfully_and_creates_product()
    {
        // Arrange
        $imagePath = storage_path('app/test-image.jpg');
        $this->createTestImage($imagePath);
        
        $labels = [
            ['label' => 'plastic bottle', 'score' => 0.95],
            ['label' => 'water bottle', 'score' => 0.87]
        ];
        $caption = 'A plastic water bottle';
        
        $this->mockClassifier
            ->shouldReceive('classify')
            ->once()
            ->with($imagePath)
            ->andReturn($labels);
            
        $this->mockClassifier
            ->shouldReceive('caption')
            ->once()
            ->with($imagePath)
            ->andReturn($caption);
            
        $this->mockClassifier
            ->shouldReceive('mapLabelsToCategoryWithCaption')
            ->once()
            ->with($labels, $caption)
            ->andReturn('plastique');

        $job = new ProcessImageToProduct($imagePath, 1);

        // Act
        $job->handle($this->mockClassifier);

        // Assert
        $this->assertDatabaseHas('products', [
            'name' => 'Produit généré automatiquement',
            'category_id' => 1, // plastique category
            'created_by' => 1
        ]);

        $product = Product::where('name', 'Produit généré automatiquement')->first();
        $this->assertNotNull($product);
        $this->assertEquals('plastique', $product->category->slug);
        
        // Cleanup
        unlink($imagePath);
    }

    /** @test */
    public function it_handles_empty_labels_and_uses_filename_fallback()
    {
        // Arrange
        $imagePath = storage_path('app/plastic-bottle.jpg');
        $this->createTestImage($imagePath);
        
        $this->mockClassifier
            ->shouldReceive('classify')
            ->once()
            ->with($imagePath)
            ->andReturn([]);
            
        $this->mockClassifier
            ->shouldReceive('caption')
            ->once()
            ->with($imagePath)
            ->andReturn(null);
            
        $this->mockClassifier
            ->shouldReceive('mapLabelsToCategoryWithCaption')
            ->once()
            ->with([], null)
            ->andReturn(null);

        $job = new ProcessImageToProduct($imagePath, 1);

        // Act
        $job->handle($this->mockClassifier);

        // Assert
        $this->assertDatabaseHas('products', [
            'name' => 'Produit généré automatiquement',
            'category_id' => 1, // plastique category from filename
            'created_by' => 1
        ]);
        
        // Cleanup
        unlink($imagePath);
    }

    /** @test */
    public function it_handles_glass_filename_pattern()
    {
        // Arrange
        $imagePath = storage_path('app/wine-glass.jpg');
        $this->createTestImage($imagePath);
        
        $this->mockClassifier
            ->shouldReceive('classify')
            ->once()
            ->andReturn([]);
            
        $this->mockClassifier
            ->shouldReceive('caption')
            ->once()
            ->andReturn(null);
            
        $this->mockClassifier
            ->shouldReceive('mapLabelsToCategoryWithCaption')
            ->once()
            ->andReturn(null);

        $job = new ProcessImageToProduct($imagePath, 1);

        // Act
        $job->handle($this->mockClassifier);

        // Assert
        $this->assertDatabaseHas('products', [
            'name' => 'Produit généré automatiquement',
            'category_id' => 2, // verre category
            'created_by' => 1
        ]);
        
        // Cleanup
        unlink($imagePath);
    }

    /** @test */
    public function it_handles_wood_filename_pattern()
    {
        // Arrange
        $imagePath = storage_path('app/wooden-pallet.jpg');
        $this->createTestImage($imagePath);
        
        $this->mockClassifier
            ->shouldReceive('classify')
            ->once()
            ->andReturn([]);
            
        $this->mockClassifier
            ->shouldReceive('caption')
            ->once()
            ->andReturn(null);
            
        $this->mockClassifier
            ->shouldReceive('mapLabelsToCategoryWithCaption')
            ->once()
            ->andReturn(null);

        // Create wood category
        Category::create([
            'name' => 'Bois',
            'slug' => 'bois',
            'description' => 'Matériaux en bois',
            'color' => '#8B4513'
        ]);

        $job = new ProcessImageToProduct($imagePath, 1);

        // Act
        $job->handle($this->mockClassifier);

        // Assert
        $this->assertDatabaseHas('products', [
            'name' => 'Produit généré automatiquement',
            'category_id' => 3, // bois category
            'created_by' => 1
        ]);
        
        // Cleanup
        unlink($imagePath);
    }

    /** @test */
    public function it_handles_metal_filename_pattern()
    {
        // Arrange
        $imagePath = storage_path('app/aluminum-can.jpg');
        $this->createTestImage($imagePath);
        
        $this->mockClassifier
            ->shouldReceive('classify')
            ->once()
            ->andReturn([]);
            
        $this->mockClassifier
            ->shouldReceive('caption')
            ->once()
            ->andReturn(null);
            
        $this->mockClassifier
            ->shouldReceive('mapLabelsToCategoryWithCaption')
            ->once()
            ->andReturn(null);

        // Create metal category
        Category::create([
            'name' => 'Métal',
            'slug' => 'metal',
            'description' => 'Matériaux métalliques',
            'color' => '#C0C0C0'
        ]);

        $job = new ProcessImageToProduct($imagePath, 1);

        // Act
        $job->handle($this->mockClassifier);

        // Assert
        $this->assertDatabaseHas('products', [
            'name' => 'Produit généré automatiquement',
            'category_id' => 3, // metal category
            'created_by' => 1
        ]);
        
        // Cleanup
        unlink($imagePath);
    }

    /** @test */
    public function it_handles_paper_filename_pattern()
    {
        // Arrange
        $imagePath = storage_path('app/cardboard-box.jpg');
        $this->createTestImage($imagePath);
        
        $this->mockClassifier
            ->shouldReceive('classify')
            ->once()
            ->andReturn([]);
            
        $this->mockClassifier
            ->shouldReceive('caption')
            ->once()
            ->andReturn(null);
            
        $this->mockClassifier
            ->shouldReceive('mapLabelsToCategoryWithCaption')
            ->once()
            ->andReturn(null);

        // Create paper category
        Category::create([
            'name' => 'Papier',
            'slug' => 'papier',
            'description' => 'Matériaux en papier',
            'color' => '#F4E4BC'
        ]);

        $job = new ProcessImageToProduct($imagePath, 1);

        // Act
        $job->handle($this->mockClassifier);

        // Assert
        $this->assertDatabaseHas('products', [
            'name' => 'Produit généré automatiquement',
            'category_id' => 3, // papier category
            'created_by' => 1
        ]);
        
        // Cleanup
        unlink($imagePath);
    }

    /** @test */
    public function it_handles_textile_filename_pattern()
    {
        // Arrange
        $imagePath = storage_path('app/cotton-shirt.jpg');
        $this->createTestImage($imagePath);
        
        $this->mockClassifier
            ->shouldReceive('classify')
            ->once()
            ->andReturn([]);
            
        $this->mockClassifier
            ->shouldReceive('caption')
            ->once()
            ->andReturn(null);
            
        $this->mockClassifier
            ->shouldReceive('mapLabelsToCategoryWithCaption')
            ->once()
            ->andReturn(null);

        // Create textile category
        Category::create([
            'name' => 'Textile',
            'slug' => 'textile',
            'description' => 'Matériaux textiles',
            'color' => '#FF69B4'
        ]);

        $job = new ProcessImageToProduct($imagePath, 1);

        // Act
        $job->handle($this->mockClassifier);

        // Assert
        $this->assertDatabaseHas('products', [
            'name' => 'Produit généré automatiquement',
            'category_id' => 3, // textile category
            'created_by' => 1
        ]);
        
        // Cleanup
        unlink($imagePath);
    }

    /** @test */
    public function it_handles_electronics_filename_pattern()
    {
        // Arrange
        $imagePath = storage_path('app/old-phone.jpg');
        $this->createTestImage($imagePath);
        
        $this->mockClassifier
            ->shouldReceive('classify')
            ->once()
            ->andReturn([]);
            
        $this->mockClassifier
            ->shouldReceive('caption')
            ->once()
            ->andReturn(null);
            
        $this->mockClassifier
            ->shouldReceive('mapLabelsToCategoryWithCaption')
            ->once()
            ->andReturn(null);

        // Create electronics category
        Category::create([
            'name' => 'Électronique',
            'slug' => 'electronique',
            'description' => 'Appareils électroniques',
            'color' => '#4169E1'
        ]);

        $job = new ProcessImageToProduct($imagePath, 1);

        // Act
        $job->handle($this->mockClassifier);

        // Assert
        $this->assertDatabaseHas('products', [
            'name' => 'Produit généré automatiquement',
            'category_id' => 3, // electronique category
            'created_by' => 1
        ]);
        
        // Cleanup
        unlink($imagePath);
    }

    /** @test */
    public function it_handles_organic_filename_pattern()
    {
        // Arrange
        $imagePath = storage_path('app/food-waste.jpg');
        $this->createTestImage($imagePath);
        
        $this->mockClassifier
            ->shouldReceive('classify')
            ->once()
            ->andReturn([]);
            
        $this->mockClassifier
            ->shouldReceive('caption')
            ->once()
            ->andReturn(null);
            
        $this->mockClassifier
            ->shouldReceive('mapLabelsToCategoryWithCaption')
            ->once()
            ->andReturn(null);

        // Create organic category
        Category::create([
            'name' => 'Organique',
            'slug' => 'organique',
            'description' => 'Déchets organiques',
            'color' => '#228B22'
        ]);

        $job = new ProcessImageToProduct($imagePath, 1);

        // Act
        $job->handle($this->mockClassifier);

        // Assert
        $this->assertDatabaseHas('products', [
            'name' => 'Produit généré automatiquement',
            'category_id' => 3, // organique category
            'created_by' => 1
        ]);
        
        // Cleanup
        unlink($imagePath);
    }

    /** @test */
    public function it_defaults_to_plastique_when_no_pattern_matches()
    {
        // Arrange
        $imagePath = storage_path('app/unknown-material.jpg');
        $this->createTestImage($imagePath);
        
        $this->mockClassifier
            ->shouldReceive('classify')
            ->once()
            ->andReturn([]);
            
        $this->mockClassifier
            ->shouldReceive('caption')
            ->once()
            ->andReturn(null);
            
        $this->mockClassifier
            ->shouldReceive('mapLabelsToCategoryWithCaption')
            ->once()
            ->andReturn(null);

        $job = new ProcessImageToProduct($imagePath, 1);

        // Act
        $job->handle($this->mockClassifier);

        // Assert
        $this->assertDatabaseHas('products', [
            'name' => 'Produit généré automatiquement',
            'category_id' => 1, // plastique category (default)
            'created_by' => 1
        ]);
        
        // Cleanup
        unlink($imagePath);
    }

    /** @test */
    public function it_works_without_user_id()
    {
        // Arrange
        $imagePath = storage_path('app/test-image.jpg');
        $this->createTestImage($imagePath);
        
        $this->mockClassifier
            ->shouldReceive('classify')
            ->once()
            ->andReturn([['label' => 'plastic bottle', 'score' => 0.95]]);
            
        $this->mockClassifier
            ->shouldReceive('caption')
            ->once()
            ->andReturn('A plastic bottle');
            
        $this->mockClassifier
            ->shouldReceive('mapLabelsToCategoryWithCaption')
            ->once()
            ->andReturn('plastique');

        $job = new ProcessImageToProduct($imagePath);

        // Act
        $job->handle($this->mockClassifier);

        // Assert
        $this->assertDatabaseHas('products', [
            'name' => 'Produit généré automatiquement',
            'category_id' => 1,
            'created_by' => null
        ]);
        
        // Cleanup
        unlink($imagePath);
    }

    /** @test */
    public function it_logs_processing_start()
    {
        // Arrange
        $imagePath = storage_path('app/test-image.jpg');
        $this->createTestImage($imagePath);
        
        Log::shouldReceive('info')
            ->once()
            ->with('ProcessImageToProduct: start', ['path' => $imagePath, 'user' => 1]);
            
        $this->mockClassifier
            ->shouldReceive('classify')
            ->once()
            ->andReturn([]);
            
        $this->mockClassifier
            ->shouldReceive('caption')
            ->once()
            ->andReturn(null);
            
        $this->mockClassifier
            ->shouldReceive('mapLabelsToCategoryWithCaption')
            ->once()
            ->andReturn(null);

        $job = new ProcessImageToProduct($imagePath, 1);

        // Act
        $job->handle($this->mockClassifier);
        
        // Cleanup
        unlink($imagePath);
    }

    /** @test */
    public function it_logs_warning_when_no_labels_returned()
    {
        // Arrange
        $imagePath = storage_path('app/test-image.jpg');
        $this->createTestImage($imagePath);
        
        Log::shouldReceive('info')
            ->once()
            ->with('ProcessImageToProduct: start', ['path' => $imagePath, 'user' => 1]);
            
        Log::shouldReceive('warning')
            ->once()
            ->with('ProcessImageToProduct: no labels returned, attempting caption fallback');
            
        $this->mockClassifier
            ->shouldReceive('classify')
            ->once()
            ->andReturn([]);
            
        $this->mockClassifier
            ->shouldReceive('caption')
            ->once()
            ->andReturn(null);
            
        $this->mockClassifier
            ->shouldReceive('mapLabelsToCategoryWithCaption')
            ->once()
            ->andReturn(null);

        $job = new ProcessImageToProduct($imagePath, 1);

        // Act
        $job->handle($this->mockClassifier);
        
        // Cleanup
        unlink($imagePath);
    }

    private function createTestImage(string $path): void
    {
        // Create a simple test image
        $image = imagecreate(100, 100);
        $bg = imagecolorallocate($image, 255, 255, 255);
        $textColor = imagecolorallocate($image, 0, 0, 0);
        imagestring($image, 5, 20, 40, 'TEST', $textColor);
        imagejpeg($image, $path);
        imagedestroy($image);
    }
}
