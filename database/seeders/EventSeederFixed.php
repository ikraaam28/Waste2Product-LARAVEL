<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\Event;
use App\Models\EventFeedback;
use App\Models\Badge;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class EventSeederFixed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer 2 utilisateurs de test
        $users = [
            [
                'first_name' => 'Jean',
                'last_name' => 'Dupont',
                'email' => 'jean.dupont@teahouse.com',
                'phone' => '0123456789',
                'city' => 'Paris',
                'password' => Hash::make('password'),
                'newsletter_subscription' => true,
                'terms_accepted' => true,
                'email_verified_at' => now(),
            ],
            [
                'first_name' => 'Marie',
                'last_name' => 'Martin',
                'email' => 'marie.martin@teahouse.com',
                'phone' => '0987654321',
                'city' => 'Lyon',
                'password' => Hash::make('password'),
                'newsletter_subscription' => false,
                'terms_accepted' => true,
                'email_verified_at' => now(),
            ]
        ];

        $createdUsers = [];
        foreach ($users as $userData) {
            $createdUsers[] = User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }

        // Créer des catégories si elles n'existent pas
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
        ];

        $createdCategories = [];
        foreach ($categories as $categoryData) {
            $createdCategories[] = Category::firstOrCreate(
                ['slug' => $categoryData['slug']],
                $categoryData
            );
        }

        // Créer des produits avec les bons attributs
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
                'created_by' => $createdUsers[0]->id,
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
                'created_by' => $createdUsers[0]->id,
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
                'created_by' => $createdUsers[0]->id,
                'materials' => 'Aluminium',
                'recycling_process' => 'Tri, broyage, fusion',
                'environmental_impact_score' => 90,
                'is_active' => true,
                'published_at' => now()
            ],
        ];

        foreach ($products as $productData) {
            Product::firstOrCreate(
                ['sku' => $productData['sku']],
                $productData
            );
        }

        // Créer des événements
        $events = [
            [
                'title' => 'Journée de recyclage plastique',
                'description' => 'Événement de collecte et sensibilisation au recyclage du plastique',
                'category' => 'recyclage',
                'date' => now()->addDays(7)->format('Y-m-d'),
                'time' => '09:00:00',
                'location' => 'Parc de la Villette, Paris',
                'max_participants' => 100,
                'qr_code' => 'QR-' . Str::random(10),
                'created_by' => $createdUsers[0]->id,
                'status' => true,
            ],
            [
                'title' => 'Atelier recyclage verre',
                'description' => 'Apprenez à recycler le verre et créer des objets décoratifs',
                'category' => 'atelier',
                'date' => now()->addDays(14)->format('Y-m-d'),
                'time' => '14:00:00',
                'location' => 'Centre culturel, Lyon',
                'max_participants' => 50,
                'qr_code' => 'QR-' . Str::random(10),
                'created_by' => $createdUsers[0]->id,
                'status' => true,
            ],
        ];

        foreach ($events as $eventData) {
            Event::create($eventData);
        }

        // Créer des badges
        $badges = [
            [
                'name' => 'Recycleur débutant',
                'description' => 'Premier pas dans le recyclage',
                'icon' => 'fa-seedling',
                'color' => '#28a745',
                'criteria_type' => 'events_participated',
                'criteria_value' => 1,
                'points_required' => 10,
                'is_active' => true,
            ],
            [
                'name' => 'Expert recyclage',
                'description' => 'Maître du recyclage',
                'icon' => 'fa-trophy',
                'color' => '#ffc107',
                'criteria_type' => 'events_participated',
                'criteria_value' => 10,
                'points_required' => 100,
                'is_active' => true,
            ],
        ];

        foreach ($badges as $badgeData) {
            Badge::create($badgeData);
        }
    }
}
