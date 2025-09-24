<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\ProductCategory;
use App\Models\Product;
use App\Models\Event;
use App\Models\EventFeedback;
use App\Models\Badge;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class EventSeeder extends Seeder
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
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );
            $createdUsers[] = $user;
        }

        // Créer 3 catégories de produits
        $categories = [
            [
                'name' => 'Plastique',
                'description' => 'Produits en plastique recyclables',
                'color' => '#007bff',
                'icon' => 'fa fa-recycle'
            ],
            [
                'name' => 'Verre',
                'description' => 'Produits en verre recyclables',
                'color' => '#28a745',
                'icon' => 'fa fa-wine-glass'
            ],
            [
                'name' => 'Métal',
                'description' => 'Produits métalliques recyclables',
                'color' => '#ffc107',
                'icon' => 'fa fa-cog'
            ]
        ];

        $createdCategories = [];
        foreach ($categories as $categoryData) {
            $createdCategories[] = ProductCategory::create($categoryData);
        }

        // Créer 10 produits
        $products = [
            // Plastique
            [
                'name' => 'Bouteille d\'eau en plastique',
                'description' => 'Bouteille d\'eau 1.5L en PET recyclable',
                'category_id' => $createdCategories[0]->id,
                'recyclable' => true,
                'co2_saved_per_unit' => 0.15,
                'points_value' => 5
            ],
            [
                'name' => 'Emballage alimentaire',
                'description' => 'Emballage plastique pour produits alimentaires',
                'category_id' => $createdCategories[0]->id,
                'recyclable' => true,
                'co2_saved_per_unit' => 0.08,
                'points_value' => 3
            ],
            [
                'name' => 'Sachet plastique',
                'description' => 'Sachet en plastique souple',
                'category_id' => $createdCategories[0]->id,
                'recyclable' => true,
                'co2_saved_per_unit' => 0.02,
                'points_value' => 1
            ],
            [
                'name' => 'Bouchon de bouteille',
                'description' => 'Bouchon en plastique dur',
                'category_id' => $createdCategories[0]->id,
                'recyclable' => true,
                'co2_saved_per_unit' => 0.01,
                'points_value' => 1
            ],
            // Verre
            [
                'name' => 'Bouteille de vin',
                'description' => 'Bouteille de vin en verre',
                'category_id' => $createdCategories[1]->id,
                'recyclable' => true,
                'co2_saved_per_unit' => 0.25,
                'points_value' => 8
            ],
            [
                'name' => 'Pot de confiture',
                'description' => 'Pot en verre pour confiture',
                'category_id' => $createdCategories[1]->id,
                'recyclable' => true,
                'co2_saved_per_unit' => 0.12,
                'points_value' => 4
            ],
            [
                'name' => 'Bocal de conserve',
                'description' => 'Bocal en verre pour conserves',
                'category_id' => $createdCategories[1]->id,
                'recyclable' => true,
                'co2_saved_per_unit' => 0.18,
                'points_value' => 6
            ],
            // Métal
            [
                'name' => 'Canette de soda',
                'description' => 'Canette en aluminium',
                'category_id' => $createdCategories[2]->id,
                'recyclable' => true,
                'co2_saved_per_unit' => 0.20,
                'points_value' => 7
            ],
            [
                'name' => 'Boîte de conserve',
                'description' => 'Boîte de conserve en fer blanc',
                'category_id' => $createdCategories[2]->id,
                'recyclable' => true,
                'co2_saved_per_unit' => 0.30,
                'points_value' => 10
            ],
            [
                'name' => 'Capsule de bouteille',
                'description' => 'Capsule métallique de bouteille',
                'category_id' => $createdCategories[2]->id,
                'recyclable' => true,
                'co2_saved_per_unit' => 0.05,
                'points_value' => 2
            ]
        ];

        $createdProducts = [];
        foreach ($products as $productData) {
            $createdProducts[] = Product::create($productData);
        }

        // Créer 6 événements de test avec villes tunisiennes
        $events = [
            [
                'title' => 'Plastic Collection - City Center',
                'description' => 'Join us for a major plastic waste collection in the city center. Bring your bottles, packaging, and other plastic waste to help clean up our environment.',
                'category' => 'Collection',
                'date' => now()->addDays(3),
                'time' => now()->setTime(14, 0),
                'location' => 'Place de la République',
                'city' => 'Tunis',
                'status' => true,
                'max_participants' => 50,
                'qr_code' => Str::random(32),
                'created_by' => $createdUsers[0]->id,
                'organizer_email' => 'events@teahouse.com'
            ],
            [
                'title' => 'Glass Recycling Workshop',
                'description' => 'Learn how to recycle glass and discover reuse techniques. Hands-on workshop with demonstrations and practical exercises.',
                'category' => 'Workshop',
                'date' => now()->addDays(7),
                'time' => now()->setTime(10, 0),
                'location' => 'Centre Culturel',
                'city' => 'Sousse',
                'status' => true,
                'max_participants' => 25,
                'qr_code' => Str::random(32),
                'created_by' => $createdUsers[0]->id,
                'organizer_email' => 'workshops@teahouse.com'
            ],
            [
                'title' => 'Metal Recycling Awareness Session',
                'description' => 'Information session about metal recycling. Discover the environmental impact and best practices for sustainable living.',
                'category' => 'Awareness',
                'date' => now()->addDays(10),
                'time' => now()->setTime(16, 30),
                'location' => 'Bibliothèque Municipale',
                'city' => 'Sfax',
                'status' => true,
                'max_participants' => 30,
                'qr_code' => Str::random(32),
                'created_by' => $createdUsers[1]->id,
                'organizer_email' => 'awareness@teahouse.com'
            ],
            [
                'title' => 'Eco-Friendly Living Education',
                'description' => 'Comprehensive education session on eco-friendly living practices. Learn about sustainable choices and their impact on the environment.',
                'category' => 'Education',
                'date' => now()->addDays(14),
                'time' => now()->setTime(18, 0),
                'location' => 'Green Academy',
                'city' => 'Monastir',
                'status' => true,
                'max_participants' => 40,
                'qr_code' => Str::random(32),
                'created_by' => $createdUsers[0]->id,
                'organizer_email' => 'education@teahouse.com'
            ],
            [
                'title' => 'Community Cleanup Drive',
                'description' => 'Join our community cleanup drive to make our neighborhood cleaner and greener. All materials will be properly sorted and recycled.',
                'category' => 'Collection',
                'date' => now()->addDays(21),
                'time' => now()->setTime(9, 0),
                'location' => 'Parc du Lac',
                'city' => 'Tunis',
                'status' => true,
                'max_participants' => 60,
                'qr_code' => Str::random(32),
                'created_by' => $createdUsers[1]->id,
                'organizer_email' => 'cleanup@teahouse.com'
            ],
            [
                'title' => 'Upcycling Craft Workshop',
                'description' => 'Transform waste materials into beautiful and useful items. Learn creative upcycling techniques in this hands-on workshop.',
                'category' => 'Workshop',
                'date' => now()->addDays(28),
                'time' => now()->setTime(15, 0),
                'location' => 'Atelier d\'Art',
                'city' => 'Nabeul',
                'status' => true,
                'max_participants' => 20,
                'qr_code' => Str::random(32),
                'created_by' => $createdUsers[0]->id,
                'organizer_email' => 'crafts@teahouse.com'
            ]
        ];

        $createdEvents = [];
        foreach ($events as $eventData) {
            $createdEvents[] = Event::create($eventData);
        }

        // Associer des produits aux événements
        $createdEvents[0]->products()->attach([$createdProducts[0]->id, $createdProducts[1]->id, $createdProducts[2]->id, $createdProducts[3]->id]);
        $createdEvents[1]->products()->attach([$createdProducts[4]->id, $createdProducts[5]->id, $createdProducts[6]->id]);
        $createdEvents[2]->products()->attach([$createdProducts[7]->id, $createdProducts[8]->id, $createdProducts[9]->id]);

        // Inscrire des participants aux événements
        foreach ($createdEvents as $event) {
            foreach ($createdUsers as $user) {
                $event->participants()->attach($user->id, [
                    'participant_id' => Str::random(32),
                    'scanned_at' => rand(0, 1) ? now()->subDays(rand(1, 10)) : null,
                    'badge_earned' => rand(0, 1)
                ]);
            }
        }

        // Créer des feedbacks pour les événements passés
        $pastEvent = $createdEvents[2]; // L'événement passé
        foreach ($createdUsers as $user) {
            EventFeedback::create([
                'event_id' => $pastEvent->id,
                'user_id' => $user->id,
                'rating' => rand(3, 5),
                'comment' => 'Très bon événement, très informatif !',
                'recycled_quantity' => rand(5, 25),
                'co2_saved' => rand(2, 10),
                'satisfaction_level' => rand(7, 10)
            ]);
        }

        // Créer des badges
        $badges = [
            [
                'name' => 'Premier Pas',
                'description' => 'Première participation à un événement',
                'icon' => 'fa fa-baby',
                'color' => '#28a745',
                'criteria_type' => 'events_participated',
                'criteria_value' => 1,
                'points_required' => 10,
                'is_active' => true
            ],
            [
                'name' => 'Écologiste Confirmé',
                'description' => 'Participation à 5 événements',
                'icon' => 'fa fa-leaf',
                'color' => '#20c997',
                'criteria_type' => 'events_participated',
                'criteria_value' => 5,
                'points_required' => 50,
                'is_active' => true
            ],
            [
                'name' => 'Recycleur Expert',
                'description' => 'A recyclé plus de 50kg de matière',
                'icon' => 'fa fa-recycle',
                'color' => '#007bff',
                'criteria_type' => 'recycled_quantity',
                'criteria_value' => 50,
                'points_required' => 100,
                'is_active' => true
            ],
            [
                'name' => 'Champion CO₂',
                'description' => 'A économisé plus de 20kg de CO₂',
                'icon' => 'fa fa-globe',
                'color' => '#ffc107',
                'criteria_type' => 'co2_saved',
                'criteria_value' => 20,
                'points_required' => 80,
                'is_active' => true
            ],
            [
                'name' => 'Organisateur',
                'description' => 'A créé son premier événement',
                'icon' => 'fa fa-calendar-plus',
                'color' => '#6f42c1',
                'criteria_type' => 'events_created',
                'criteria_value' => 1,
                'points_required' => 200,
                'is_active' => true
            ]
        ];

        foreach ($badges as $badgeData) {
            Badge::create($badgeData);
        }

        // Attribuer quelques badges aux utilisateurs
        $badges = Badge::all();
        
        // Premier badge pour tous les utilisateurs
        foreach ($createdUsers as $user) {
            $user->badges()->attach($badges[0]->id, [
                'earned_at' => now()->subDays(rand(1, 30)),
                'event_id' => $createdEvents[0]->id
            ]);
        }

        // Badge organisateur pour le deuxième utilisateur
        $createdUsers[1]->badges()->attach($badges[4]->id, [
            'earned_at' => now()->subDays(5),
            'event_id' => $createdEvents[2]->id
        ]);
    }
}