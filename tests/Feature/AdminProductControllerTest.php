<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminProductControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;
    private User $regularUser;
    private Category $category;
    private Warehouse $warehouse;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create admin user
        $this->adminUser = User::create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'is_active' => true,
            'email_verified_at' => now(),
            'terms_accepted' => true
        ]);

        // Create regular user
        $this->regularUser = User::create([
            'first_name' => 'Regular',
            'last_name' => 'User',
            'email' => 'user@example.com',
            'password' => bcrypt('password'),
            'role' => 'user',
            'is_active' => true,
            'email_verified_at' => now(),
            'terms_accepted' => true
        ]);

        // Create category
        $this->category = Category::create([
            'name' => 'Plastique',
            'slug' => 'plastique',
            'description' => 'Matériaux plastiques',
            'color' => '#FF6B6B'
        ]);

        // Create warehouse
        $this->warehouse = Warehouse::create([
            'name' => 'Entrepôt Principal',
            'location' => 'Tunis',
            'capacity' => 1000,
            'is_active' => true
        ]);
    }

    /** @test */
    public function admin_can_view_products_index()
    {
        // Arrange
        Product::create([
            'name' => 'Product 1',
            'slug' => 'product-1',
            'description' => 'Description 1',
            'short_description' => 'Short 1',
            'price' => 10.50,
            'sku' => 'SKU001',
            'stock_quantity' => 100,
            'category_id' => $this->category->id,
            'created_by' => $this->adminUser->id,
            'materials' => ['plastique'],
            'recycling_process' => 'Tri et recyclage',
            'environmental_impact_score' => 75,
            'is_active' => true
        ]);

        Product::create([
            'name' => 'Product 2',
            'slug' => 'product-2',
            'description' => 'Description 2',
            'short_description' => 'Short 2',
            'price' => 15.75,
            'sku' => 'SKU002',
            'stock_quantity' => 50,
            'category_id' => $this->category->id,
            'created_by' => $this->adminUser->id,
            'materials' => ['verre'],
            'recycling_process' => 'Tri et recyclage',
            'environmental_impact_score' => 80,
            'is_active' => true
        ]);

        // Act
        $response = $this->actingAs($this->adminUser)
            ->get('/admin/products');

        // Assert
        $response->assertStatus(200);
        $response->assertViewIs('admin.products.index');
        $response->assertViewHas('products');
    }

    /** @test */
    public function admin_can_view_product_create_form()
    {
        // Act
        $response = $this->actingAs($this->adminUser)
            ->get('/admin/products/create');

        // Assert
        $response->assertStatus(200);
        $response->assertViewIs('admin.products.create');
        $response->assertViewHas(['categories', 'warehouses']);
    }

    /** @test */
    public function admin_can_create_product()
    {
        // Arrange
        Storage::fake('public');
        $image = UploadedFile::fake()->image('product.jpg');

        $productData = [
            'name' => 'New Product',
            'description' => 'New Product Description',
            'short_description' => 'Short Description',
            'price' => 25.99,
            'sku' => 'SKU003',
            'stock_quantity' => 75,
            'category_id' => $this->category->id,
            'materials' => ['plastique', 'verre'],
            'recycling_process' => 'Tri, nettoyage et recyclage',
            'environmental_impact_score' => 85,
            'is_active' => true,
            'image' => $image
        ];

        // Act
        $response = $this->actingAs($this->adminUser)
            ->post('/admin/products', $productData);

        // Assert
        $response->assertRedirect('/admin/products');
        $this->assertDatabaseHas('products', [
            'name' => 'New Product',
            'description' => 'New Product Description',
            'price' => 25.99,
            'sku' => 'SKU003',
            'stock_quantity' => 75,
            'category_id' => $this->category->id,
            'created_by' => $this->adminUser->id,
            'environmental_impact_score' => 85,
            'is_active' => true
        ]);

        Storage::disk('public')->assertExists('products/' . $image->hashName());
    }

    /** @test */
    public function admin_can_view_product_details()
    {
        // Arrange
        $product = Product::create([
            'name' => 'Product Details',
            'slug' => 'product-details',
            'description' => 'Product Description',
            'short_description' => 'Short Description',
            'price' => 20.00,
            'sku' => 'SKU004',
            'stock_quantity' => 60,
            'category_id' => $this->category->id,
            'created_by' => $this->adminUser->id,
            'materials' => ['plastique'],
            'recycling_process' => 'Tri et recyclage',
            'environmental_impact_score' => 70,
            'is_active' => true
        ]);

        // Act
        $response = $this->actingAs($this->adminUser)
            ->get("/admin/products/{$product->id}");

        // Assert
        $response->assertStatus(200);
        $response->assertViewIs('admin.products.show');
        $response->assertViewHas('product', $product);
    }

    /** @test */
    public function admin_can_view_product_edit_form()
    {
        // Arrange
        $product = Product::create([
            'name' => 'Product Edit',
            'slug' => 'product-edit',
            'description' => 'Product Description',
            'short_description' => 'Short Description',
            'price' => 30.00,
            'sku' => 'SKU005',
            'stock_quantity' => 40,
            'category_id' => $this->category->id,
            'created_by' => $this->adminUser->id,
            'materials' => ['verre'],
            'recycling_process' => 'Tri et recyclage',
            'environmental_impact_score' => 75,
            'is_active' => true
        ]);

        // Act
        $response = $this->actingAs($this->adminUser)
            ->get("/admin/products/{$product->id}/edit");

        // Assert
        $response->assertStatus(200);
        $response->assertViewIs('admin.products.edit');
        $response->assertViewHas(['product', 'categories', 'warehouses']);
    }

    /** @test */
    public function admin_can_update_product()
    {
        // Arrange
        $product = Product::create([
            'name' => 'Original Product',
            'slug' => 'original-product',
            'description' => 'Original Description',
            'short_description' => 'Original Short',
            'price' => 15.00,
            'sku' => 'SKU006',
            'stock_quantity' => 30,
            'category_id' => $this->category->id,
            'created_by' => $this->adminUser->id,
            'materials' => ['plastique'],
            'recycling_process' => 'Tri et recyclage',
            'environmental_impact_score' => 65,
            'is_active' => true
        ]);

        $updateData = [
            'name' => 'Updated Product',
            'description' => 'Updated Description',
            'short_description' => 'Updated Short',
            'price' => 35.50,
            'sku' => 'SKU006-UPDATED',
            'stock_quantity' => 80,
            'category_id' => $this->category->id,
            'materials' => ['plastique', 'verre'],
            'recycling_process' => 'Tri, nettoyage et recyclage amélioré',
            'environmental_impact_score' => 90,
            'is_active' => true
        ];

        // Act
        $response = $this->actingAs($this->adminUser)
            ->put("/admin/products/{$product->id}", $updateData);

        // Assert
        $response->assertRedirect('/admin/products');
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Updated Product',
            'description' => 'Updated Description',
            'price' => 35.50,
            'sku' => 'SKU006-UPDATED',
            'stock_quantity' => 80,
            'environmental_impact_score' => 90
        ]);
    }

    /** @test */
    public function admin_can_delete_product()
    {
        // Arrange
        $product = Product::create([
            'name' => 'Product to Delete',
            'slug' => 'product-to-delete',
            'description' => 'Product Description',
            'short_description' => 'Short Description',
            'price' => 12.00,
            'sku' => 'SKU007',
            'stock_quantity' => 25,
            'category_id' => $this->category->id,
            'created_by' => $this->adminUser->id,
            'materials' => ['papier'],
            'recycling_process' => 'Tri et recyclage',
            'environmental_impact_score' => 60,
            'is_active' => true
        ]);

        // Act
        $response = $this->actingAs($this->adminUser)
            ->delete("/admin/products/{$product->id}");

        // Assert
        $response->assertRedirect('/admin/products');
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    /** @test */
    public function admin_can_toggle_product_status()
    {
        // Arrange
        $product = Product::create([
            'name' => 'Toggle Product',
            'slug' => 'toggle-product',
            'description' => 'Product Description',
            'short_description' => 'Short Description',
            'price' => 18.00,
            'sku' => 'SKU008',
            'stock_quantity' => 35,
            'category_id' => $this->category->id,
            'created_by' => $this->adminUser->id,
            'materials' => ['metal'],
            'recycling_process' => 'Tri et recyclage',
            'environmental_impact_score' => 80,
            'is_active' => true
        ]);

        // Act - Deactivate
        $response = $this->actingAs($this->adminUser)
            ->patch("/admin/products/{$product->id}/toggle-status");

        // Assert
        $response->assertStatus(200);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'is_active' => false
        ]);

        // Act - Reactivate
        $response = $this->actingAs($this->adminUser)
            ->patch("/admin/products/{$product->id}/toggle-status");

        // Assert
        $response->assertStatus(200);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'is_active' => true
        ]);
    }

    /** @test */
    public function admin_can_update_product_stock()
    {
        // Arrange
        $product = Product::create([
            'name' => 'Stock Product',
            'slug' => 'stock-product',
            'description' => 'Product Description',
            'short_description' => 'Short Description',
            'price' => 22.00,
            'sku' => 'SKU009',
            'stock_quantity' => 50,
            'category_id' => $this->category->id,
            'created_by' => $this->adminUser->id,
            'materials' => ['bois'],
            'recycling_process' => 'Tri et recyclage',
            'environmental_impact_score' => 70,
            'is_active' => true
        ]);

        // Act
        $response = $this->actingAs($this->adminUser)
            ->patch("/admin/products/{$product->id}/stock", [
                'stock_quantity' => 100
            ]);

        // Assert
        $response->assertStatus(200);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock_quantity' => 100
        ]);
    }

    /** @test */
    public function admin_can_export_products()
    {
        // Arrange
        Product::create([
            'name' => 'Export Product 1',
            'slug' => 'export-product-1',
            'description' => 'Export Description 1',
            'short_description' => 'Export Short 1',
            'price' => 25.00,
            'sku' => 'SKU010',
            'stock_quantity' => 60,
            'category_id' => $this->category->id,
            'created_by' => $this->adminUser->id,
            'materials' => ['textile'],
            'recycling_process' => 'Tri et recyclage',
            'environmental_impact_score' => 75,
            'is_active' => true
        ]);

        Product::create([
            'name' => 'Export Product 2',
            'slug' => 'export-product-2',
            'description' => 'Export Description 2',
            'short_description' => 'Export Short 2',
            'price' => 30.00,
            'sku' => 'SKU011',
            'stock_quantity' => 40,
            'category_id' => $this->category->id,
            'created_by' => $this->adminUser->id,
            'materials' => ['electronique'],
            'recycling_process' => 'Tri et recyclage',
            'environmental_impact_score' => 85,
            'is_active' => true
        ]);

        // Act
        $response = $this->actingAs($this->adminUser)
            ->get('/admin/products/export');

        // Assert
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv');
    }

    /** @test */
    public function admin_can_bulk_update_products()
    {
        // Arrange
        $product1 = Product::create([
            'name' => 'Bulk Product 1',
            'slug' => 'bulk-product-1',
            'description' => 'Bulk Description 1',
            'short_description' => 'Bulk Short 1',
            'price' => 20.00,
            'sku' => 'SKU012',
            'stock_quantity' => 30,
            'category_id' => $this->category->id,
            'created_by' => $this->adminUser->id,
            'materials' => ['organique'],
            'recycling_process' => 'Tri et recyclage',
            'environmental_impact_score' => 65,
            'is_active' => true
        ]);

        $product2 = Product::create([
            'name' => 'Bulk Product 2',
            'slug' => 'bulk-product-2',
            'description' => 'Bulk Description 2',
            'short_description' => 'Bulk Short 2',
            'price' => 25.00,
            'sku' => 'SKU013',
            'stock_quantity' => 45,
            'category_id' => $this->category->id,
            'created_by' => $this->adminUser->id,
            'materials' => ['plastique'],
            'recycling_process' => 'Tri et recyclage',
            'environmental_impact_score' => 70,
            'is_active' => true
        ]);

        // Act
        $response = $this->actingAs($this->adminUser)
            ->patch('/admin/products/bulk-update', [
                'product_ids' => [$product1->id, $product2->id],
                'action' => 'activate'
            ]);

        // Assert
        $response->assertStatus(200);
        $this->assertDatabaseHas('products', [
            'id' => $product1->id,
            'is_active' => true
        ]);
        $this->assertDatabaseHas('products', [
            'id' => $product2->id,
            'is_active' => true
        ]);
    }

    /** @test */
    public function admin_can_search_products()
    {
        // Arrange
        Product::create([
            'name' => 'Searchable Product',
            'slug' => 'searchable-product',
            'description' => 'This is a searchable product',
            'short_description' => 'Searchable',
            'price' => 15.00,
            'sku' => 'SKU014',
            'stock_quantity' => 20,
            'category_id' => $this->category->id,
            'created_by' => $this->adminUser->id,
            'materials' => ['plastique'],
            'recycling_process' => 'Tri et recyclage',
            'environmental_impact_score' => 60,
            'is_active' => true
        ]);

        // Act
        $response = $this->actingAs($this->adminUser)
            ->get('/admin/products?search=searchable');

        // Assert
        $response->assertStatus(200);
        $response->assertViewIs('admin.products.index');
        $response->assertViewHas('products');
    }

    /** @test */
    public function admin_can_filter_products_by_category()
    {
        // Arrange
        $category2 = Category::create([
            'name' => 'Verre',
            'slug' => 'verre',
            'description' => 'Matériaux en verre',
            'color' => '#4ECDC4'
        ]);

        Product::create([
            'name' => 'Glass Product',
            'slug' => 'glass-product',
            'description' => 'Glass product description',
            'short_description' => 'Glass',
            'price' => 18.00,
            'sku' => 'SKU015',
            'stock_quantity' => 25,
            'category_id' => $category2->id,
            'created_by' => $this->adminUser->id,
            'materials' => ['verre'],
            'recycling_process' => 'Tri et recyclage',
            'environmental_impact_score' => 80,
            'is_active' => true
        ]);

        // Act
        $response = $this->actingAs($this->adminUser)
            ->get("/admin/products?category={$category2->id}");

        // Assert
        $response->assertStatus(200);
        $response->assertViewIs('admin.products.index');
        $response->assertViewHas('products');
    }

    /** @test */
    public function regular_user_cannot_access_admin_product_routes()
    {
        // Arrange
        $product = Product::create([
            'name' => 'Test Product',
            'slug' => 'test-product',
            'description' => 'Test Description',
            'short_description' => 'Test Short',
            'price' => 10.00,
            'sku' => 'SKU016',
            'stock_quantity' => 15,
            'category_id' => $this->category->id,
            'created_by' => $this->adminUser->id,
            'materials' => ['plastique'],
            'recycling_process' => 'Tri et recyclage',
            'environmental_impact_score' => 50,
            'is_active' => true
        ]);

        // Act & Assert
        $this->actingAs($this->regularUser)
            ->get('/admin/products')
            ->assertStatus(403);

        $this->actingAs($this->regularUser)
            ->get('/admin/products/create')
            ->assertStatus(403);

        $this->actingAs($this->regularUser)
            ->get("/admin/products/{$product->id}")
            ->assertStatus(403);
    }

    /** @test */
    public function guest_cannot_access_admin_product_routes()
    {
        // Arrange
        $product = Product::create([
            'name' => 'Test Product',
            'slug' => 'test-product',
            'description' => 'Test Description',
            'short_description' => 'Test Short',
            'price' => 10.00,
            'sku' => 'SKU017',
            'stock_quantity' => 15,
            'category_id' => $this->category->id,
            'created_by' => $this->adminUser->id,
            'materials' => ['plastique'],
            'recycling_process' => 'Tri et recyclage',
            'environmental_impact_score' => 50,
            'is_active' => true
        ]);

        // Act & Assert
        $this->get('/admin/products')
            ->assertRedirect('/login');

        $this->get('/admin/products/create')
            ->assertRedirect('/login');

        $this->get("/admin/products/{$product->id}")
            ->assertRedirect('/login');
    }

    /** @test */
    public function product_creation_requires_valid_data()
    {
        // Arrange
        $invalidData = [
            'name' => '', // Empty name
            'description' => 'Valid Description',
            'price' => -10, // Invalid price
            'sku' => '', // Empty SKU
            'stock_quantity' => -5, // Invalid stock
            'category_id' => 999 // Non-existent category
        ];

        // Act
        $response = $this->actingAs($this->adminUser)
            ->post('/admin/products', $invalidData);

        // Assert
        $response->assertSessionHasErrors(['name', 'price', 'sku', 'stock_quantity', 'category_id']);
        $this->assertDatabaseMissing('products', ['description' => 'Valid Description']);
    }

    /** @test */
    public function product_update_requires_valid_data()
    {
        // Arrange
        $product = Product::create([
            'name' => 'Original Product',
            'slug' => 'original-product',
            'description' => 'Original Description',
            'short_description' => 'Original Short',
            'price' => 15.00,
            'sku' => 'SKU018',
            'stock_quantity' => 30,
            'category_id' => $this->category->id,
            'created_by' => $this->adminUser->id,
            'materials' => ['plastique'],
            'recycling_process' => 'Tri et recyclage',
            'environmental_impact_score' => 65,
            'is_active' => true
        ]);

        $invalidData = [
            'name' => '', // Empty name
            'description' => 'Updated Description',
            'price' => -20, // Invalid price
            'sku' => '', // Empty SKU
            'stock_quantity' => -10, // Invalid stock
            'category_id' => 999 // Non-existent category
        ];

        // Act
        $response = $this->actingAs($this->adminUser)
            ->put("/admin/products/{$product->id}", $invalidData);

        // Assert
        $response->assertSessionHasErrors(['name', 'price', 'sku', 'stock_quantity', 'category_id']);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Original Product' // Should remain unchanged
        ]);
    }
}
