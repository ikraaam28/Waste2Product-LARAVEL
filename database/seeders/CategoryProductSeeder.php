<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Str;

class CategoryProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer des catégories
        $categories = [
            [
                'name' => 'Plastique',
                'slug' => 'plastique',
                'description' => 'Produits en plastique recyclable',
                'color' => '#007bff',
                'is_active' => true,
            ],
            [
                'name' => 'Verre',
                'slug' => 'verre',
                'description' => 'Produits en verre recyclable',
                'color' => '#28a745',
                'is_active' => true,
            ],
            [
                'name' => 'Métal',
                'slug' => 'metal',
                'description' => 'Produits en métal recyclable',
                'color' => '#ffc107',
                'is_active' => true,
            ],
            [
                'name' => 'Papier',
                'slug' => 'papier',
                'description' => 'Produits en papier recyclable',
                'color' => '#dc3545',
                'is_active' => true,
            ],
        ];

        $createdCategories = [];
        foreach ($categories as $categoryData) {
            $createdCategories[] = Category::firstOrCreate(
                ['slug' => $categoryData['slug']],
                $categoryData
            );
        }

        // Obtenir un utilisateur pour created_by
        $user = User::first();
        if (!$user) {
            $user = User::create([
                'first_name' => 'Admin',
                'last_name' => 'System',
                'email' => 'admin@teahouse.com',
                'password' => bcrypt('password'),
                'terms_accepted' => true,
                'email_verified_at' => now(),
            ]);
        }

        // Créer des produits
        $products = [
            [
                'name' => 'Bouteille d\'eau en plastique',
                'slug' => 'bouteille-deau-en-plastique',
                'description' => 'Bouteille d\'eau 1.5L en PET recyclable',
                'short_description' => 'Bouteille PET recyclable',
                'price' => 0.50,
                'sku' => 'PRD-PET001',
                'stock_quantity' => 100,
                'category_id' => $createdCategories[0]->id,
                'created_by' => $user->id,
                'materials' => 'PET (Polyéthylène téréphtalate)',
                'recycling_process' => 'Tri, broyage, lavage, transformation en granulés',
                'environmental_impact_score' => 75,
                'is_active' => true,
                'published_at' => now()
            ],
            [
                'name' => 'Bouteille de vin',
                'slug' => 'bouteille-vin',
                'description' => 'Bouteille de vin en verre',
                'short_description' => 'Bouteille verre recyclable',
                'price' => 1.20,
                'sku' => 'PRD-GLS001',
                'stock_quantity' => 50,
                'category_id' => $createdCategories[1]->id,
                'created_by' => $user->id,
                'materials' => 'Verre',
                'recycling_process' => 'Tri par couleur, broyage, fusion',
                'environmental_impact_score' => 85,
                'is_active' => true,
                'published_at' => now()
            ],
            [
                'name' => 'Canette de soda',
                'slug' => 'canette-soda',
                'description' => 'Canette en aluminium',
                'short_description' => 'Canette aluminium recyclable',
                'price' => 0.30,
                'sku' => 'PRD-MET001',
                'stock_quantity' => 200,
                'category_id' => $createdCategories[2]->id,
                'created_by' => $user->id,
                'materials' => 'Aluminium',
                'recycling_process' => 'Tri, broyage, fusion',
                'environmental_impact_score' => 90,
                'is_active' => true,
                'published_at' => now()
            ],
            [
                'name' => 'Journal',
                'slug' => 'journal',
                'description' => 'Journal en papier recyclable',
                'short_description' => 'Journal papier recyclable',
                'price' => 0.10,
                'sku' => 'PRD-PAP001',
                'stock_quantity' => 400,
                'category_id' => $createdCategories[3]->id,
                'created_by' => $user->id,
                'materials' => 'Papier journal',
                'recycling_process' => 'Tri, désencrage, repulpage',
                'environmental_impact_score' => 80,
                'is_active' => true,
                'published_at' => now()
            ],
        ];

        foreach ($products as $productData) {
            Product::create($productData);
        }
    }
}