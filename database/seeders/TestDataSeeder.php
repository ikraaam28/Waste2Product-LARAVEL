<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Créer des utilisateurs de test
        $this->createUsers();
        
        // 2. Créer des catégories
        $this->createCategories();
        
        // 3. Créer des produits
        $this->createProducts();
        
        // 4. Créer des événements
        $this->createEvents();
        
        // 5. Créer des badges
        $this->createBadges();
        
        // 6. Créer des tutoriels
        $this->createTutos();
        
        // 7. Créer des publications
        $this->createPublications();
        
        // 8. Créer des commentaires
        $this->createCommentaires();
        
        // 9. Créer des réactions
        $this->createReactions();
        
        // 10. Créer des entrepôts
        $this->createWarehouses();
        
        // 11. Créer des partenaires
        $this->createPartners();
        
        echo "✅ Données de test ajoutées avec succès !\n";
    }
    
    private function createUsers()
    {
        $users = [
            [
                'first_name' => 'Admin',
                'last_name' => 'TeaHouse',
                'email' => 'admin@teahouse.com',
                'phone' => '+216 12 345 678',
                'city' => 'Tunis',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'newsletter_subscription' => true,
                'terms_accepted' => true,
                'profile_picture' => null,
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'first_name' => 'Marie',
                'last_name' => 'Dupont',
                'email' => 'marie@example.com',
                'phone' => '+216 23 456 789',
                'city' => 'Sfax',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'newsletter_subscription' => true,
                'terms_accepted' => true,
                'profile_picture' => null,
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'first_name' => 'Jean',
                'last_name' => 'Martin',
                'email' => 'jean@example.com',
                'phone' => '+216 34 567 890',
                'city' => 'Sousse',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'newsletter_subscription' => false,
                'terms_accepted' => true,
                'profile_picture' => null,
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'first_name' => 'Sophie',
                'last_name' => 'Bernard',
                'email' => 'sophie@example.com',
                'phone' => '+216 45 678 901',
                'city' => 'Tunis',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'newsletter_subscription' => true,
                'terms_accepted' => true,
                'profile_picture' => null,
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'first_name' => 'Ahmed',
                'last_name' => 'Ben Ali',
                'email' => 'ahmed@example.com',
                'phone' => '+216 56 789 012',
                'city' => 'Monastir',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'newsletter_subscription' => true,
                'terms_accepted' => true,
                'profile_picture' => null,
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];
        
        foreach ($users as $user) {
            DB::table('users')->insertOrIgnore($user);
        }
        
        echo "👥 Utilisateurs créés\n";
    }
    
    private function createCategories()
    {
        $categories = [
            [
                'name' => 'Plastique',
                'slug' => 'plastique',
                'description' => 'Articles en plastique recyclable',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Verre',
                'slug' => 'verre',
                'description' => 'Articles en verre recyclable',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Métal',
                'slug' => 'metal',
                'description' => 'Articles en métal recyclable',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Papier',
                'slug' => 'papier',
                'description' => 'Articles en papier recyclable',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Textile',
                'slug' => 'textile',
                'description' => 'Articles textiles recyclables',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];
        
        foreach ($categories as $category) {
            DB::table('categories')->insertOrIgnore($category);
        }
        
        echo "📂 Catégories créées\n";
    }
    
    private function createProducts()
    {
        $products = [
            [
                'name' => 'Bouteille d\'eau en plastique',
                'slug' => 'bouteille-deau-en-plastique',
                'description' => 'Bouteille d\'eau vide en plastique PET recyclable',
                'short_description' => 'Plastique PET recyclable',
                'category_id' => 1,
                'price' => 0.50,
                'stock_quantity' => 100,
                'stock_status' => 'in_stock',
                'images' => json_encode(['products/bouteille-plastique.jpg']),
                'materials' => 'Plastique PET',
                'recycling_process' => 'Nettoyage et transformation en granulés',
                'environmental_impact_score' => 75,
                'tags' => json_encode(['plastique', 'bouteille', 'recyclable']),
                'is_active' => true,
                'created_by' => 1,
                'published_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Bocal en verre',
                'slug' => 'bocal-en-verre',
                'description' => 'Bocal en verre vide, parfait pour le stockage',
                'short_description' => 'Verre recyclable',
                'category_id' => 2,
                'price' => 1.00,
                'stock_quantity' => 50,
                'stock_status' => 'in_stock',
                'images' => json_encode(['products/bocal-verre.jpg']),
                'materials' => 'Verre',
                'recycling_process' => 'Fusion et moulage',
                'environmental_impact_score' => 90,
                'tags' => json_encode(['verre', 'bocal', 'recyclable']),
                'is_active' => true,
                'created_by' => 1,
                'published_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Canette en aluminium',
                'slug' => 'canette-en-aluminium',
                'description' => 'Canette vide en aluminium recyclable',
                'short_description' => 'Aluminium recyclable',
                'category_id' => 3,
                'price' => 0.30,
                'stock_quantity' => 200,
                'stock_status' => 'in_stock',
                'images' => json_encode(['products/canette-aluminium.jpg']),
                'materials' => 'Aluminium',
                'recycling_process' => 'Fusion et refonte',
                'environmental_impact_score' => 85,
                'tags' => json_encode(['aluminium', 'canette', 'recyclable']),
                'is_active' => true,
                'created_by' => 1,
                'published_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Journal',
                'slug' => 'journal',
                'description' => 'Journal papier recyclable',
                'short_description' => 'Papier recyclable',
                'category_id' => 4,
                'price' => 0.20,
                'stock_quantity' => 75,
                'stock_status' => 'in_stock',
                'images' => json_encode(['products/journal.jpg']),
                'materials' => 'Papier',
                'recycling_process' => 'Pulpage et reformage',
                'environmental_impact_score' => 80,
                'tags' => json_encode(['papier', 'journal', 'recyclable']),
                'is_active' => true,
                'created_by' => 1,
                'published_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'T-shirt en coton',
                'slug' => 't-shirt-en-coton',
                'description' => 'T-shirt en coton usagé mais en bon état',
                'short_description' => 'Coton recyclable',
                'category_id' => 5,
                'price' => 2.00,
                'stock_quantity' => 30,
                'stock_status' => 'in_stock',
                'images' => json_encode(['products/t-shirt-coton.jpg']),
                'materials' => 'Coton',
                'recycling_process' => 'Nettoyage et transformation en fibres',
                'environmental_impact_score' => 70,
                'tags' => json_encode(['coton', 'textile', 'recyclable']),
                'is_active' => true,
                'created_by' => 1,
                'published_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];
        
        foreach ($products as $product) {
            DB::table('products')->insertOrIgnore($product);
        }
        
        echo "🛍️ Produits créés\n";
    }
    
    private function createEvents()
    {
        $events = [
            [
                'title' => 'Collecte de plastique à Tunis',
                'description' => 'Collecte massive de bouteilles en plastique dans le centre de Tunis',
                'date' => now()->addDays(7),
                'location' => 'Place de la République, Tunis',
                'city' => 'Tunis',
                'max_participants' => 50,
                'organizer_email' => 'sophie@example.com',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Atelier recyclage verre',
                'description' => 'Apprenez à transformer vos bouteilles en verre en objets décoratifs',
                'date' => now()->addDays(14),
                'location' => 'Centre culturel, Sfax',
                'city' => 'Sfax',
                'max_participants' => 25,
                'organizer_email' => 'admin@teahouse.com',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Collecte métaux à Sousse',
                'description' => 'Collecte de métaux recyclables dans la région de Sousse',
                'date' => now()->addDays(21),
                'location' => 'Port de Sousse',
                'city' => 'Sousse',
                'max_participants' => 40,
                'organizer_email' => 'sophie@example.com',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];
        
        foreach ($events as $event) {
            DB::table('events')->insertOrIgnore($event);
        }
        
        echo "📅 Événements créés\n";
    }
    
    private function createBadges()
    {
        $badges = [
            [
                'name' => 'Premier pas',
                'description' => 'Première participation à un événement',
                'icon' => 'badge-premier-pas.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Collectionneur',
                'description' => 'A participé à 5 événements',
                'icon' => 'badge-collectionneur.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Écologiste',
                'description' => 'A recyclé plus de 100 articles',
                'icon' => 'badge-ecologiste.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Mentor',
                'description' => 'A aidé 10 nouveaux utilisateurs',
                'icon' => 'badge-mentor.png',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];
        
        foreach ($badges as $badge) {
            DB::table('badges')->insertOrIgnore($badge);
        }
        
        echo "🏆 Badges créés\n";
    }
    
    private function createTutos()
    {
        $tutos = [
            [
                'title' => 'Comment recycler une bouteille en plastique',
                'description' => 'Guide complet pour recycler vos bouteilles en plastique et les transformer en objets utiles.',
                'category' => 'plastique',
                'steps' => json_encode([
                    'Nettoyer la bouteille',
                    'Retirer l\'étiquette',
                    'Couper selon le design souhaité',
                    'Décorer et personnaliser'
                ]),
                'media' => json_encode(['tutos/bouteille-plastique.mp4']),
                'user_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Créer un vase avec une bouteille en verre',
                'description' => 'Transformez vos bouteilles en verre en magnifiques vases décoratifs.',
                'category' => 'verre',
                'steps' => json_encode([
                    'Nettoyer le bocal',
                    'Préparer les matériaux de décoration',
                    'Appliquer la décoration',
                    'Finaliser et sécher'
                ]),
                'media' => json_encode(['tutos/vase-verre.mp4']),
                'user_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Recyclage des canettes en aluminium',
                'description' => 'Découvrez comment recycler vos canettes et créer des objets artisanaux.',
                'category' => 'metal',
                'steps' => json_encode([
                    'Nettoyer la canette',
                    'Aplatir ou façonner',
                    'Assembler les pièces',
                    'Peindre ou décorer'
                ]),
                'media' => json_encode(['tutos/canettes-aluminium.mp4']),
                'user_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];
        
        foreach ($tutos as $tuto) {
            DB::table('tutos')->insertOrIgnore($tuto);
        }
        
        echo "📚 Tutoriels créés\n";
    }
    
    private function createPublications()
    {
        $publications = [
            [
                'titre' => 'Mon expérience avec le recyclage',
                'contenu' => 'Partage de mon expérience personnelle avec le recyclage et les bénéfices que j\'en tire.',
                'categorie' => 'transformation',
                'image' => 'publications/recyclage-experience.jpg',
                'user_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'titre' => 'Conseils pour débuter le recyclage',
                'contenu' => 'Quelques conseils pratiques pour commencer votre parcours vers un mode de vie plus écologique.',
                'categorie' => 'reemployment',
                'image' => 'publications/conseils-recyclage.jpg',
                'user_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'titre' => 'Les avantages du recyclage',
                'contenu' => 'Découvrez tous les avantages du recyclage pour l\'environnement et la société.',
                'categorie' => 'repair',
                'image' => 'publications/avantages-recyclage.jpg',
                'user_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];
        
        foreach ($publications as $publication) {
            DB::table('publications')->insertOrIgnore($publication);
        }
        
        echo "📝 Publications créées\n";
    }
    
    private function createCommentaires()
    {
        $commentaires = [
            [
                'contenu' => 'Excellent article ! Merci pour ces conseils.',
                'user_id' => 3,
                'publication_id' => 1,
                'parent_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'contenu' => 'Je suis d\'accord avec vous, le recyclage est vraiment important.',
                'user_id' => 2,
                'publication_id' => 1,
                'parent_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'contenu' => 'Très inspirant ! J\'ai hâte de commencer.',
                'user_id' => 5,
                'publication_id' => 2,
                'parent_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];
        
        foreach ($commentaires as $commentaire) {
            DB::table('commentaires')->insertOrIgnore($commentaire);
        }
        
        echo "💬 Commentaires créés\n";
    }
    
    private function createReactions()
    {
        $reactions = [
            [
                'user_id' => 2,
                'tuto_id' => 1,
                'type' => 'like',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 3,
                'tuto_id' => 1,
                'type' => 'like',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 4,
                'tuto_id' => 2,
                'type' => 'like',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 5,
                'tuto_id' => 3,
                'type' => 'like',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];
        
        foreach ($reactions as $reaction) {
            DB::table('reactions')->insertOrIgnore($reaction);
        }
        
        echo "👍 Réactions créées\n";
    }
    
    private function createWarehouses()
    {
        $warehouses = [
            [
                'name' => 'Entrepôt Tunis Centre',
                'partner_id' => 1,
                'location' => 'Tunis, Tunisie',
                'address' => '123 Avenue Habib Bourguiba',
                'city' => 'Tunis',
                'postal_code' => '1000',
                'country' => 'Tunisia',
                'capacity' => 1000,
                'current_occupancy' => 250,
                'contact_person' => 'Ahmed Ben Ali',
                'contact_phone' => '+216 12 345 678',
                'contact_email' => 'tunis@ecorecycle-tn.com',
                'status' => 'active',
                'description' => 'Entrepôt principal pour le recyclage du plastique',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Entrepôt Sfax Sud',
                'partner_id' => 2,
                'location' => 'Sfax, Tunisie',
                'address' => '456 Rue de la République',
                'city' => 'Sfax',
                'postal_code' => '3000',
                'country' => 'Tunisia',
                'capacity' => 800,
                'current_occupancy' => 180,
                'contact_person' => 'Fatma Khelil',
                'contact_phone' => '+216 23 456 789',
                'contact_email' => 'sfax@verrevert.tn',
                'status' => 'active',
                'description' => 'Entrepôt spécialisé dans le recyclage du verre',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Entrepôt Sousse Est',
                'partner_id' => 3,
                'location' => 'Sousse, Tunisie',
                'address' => '789 Avenue Taher Haddad',
                'city' => 'Sousse',
                'postal_code' => '4000',
                'country' => 'Tunisia',
                'capacity' => 600,
                'current_occupancy' => 120,
                'contact_person' => 'Mohamed Trabelsi',
                'contact_phone' => '+216 34 567 890',
                'contact_email' => 'sousse@metalpro.tn',
                'status' => 'active',
                'description' => 'Entrepôt pour le recyclage des métaux',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];
        
        foreach ($warehouses as $warehouse) {
            DB::table('warehouses')->insertOrIgnore($warehouse);
        }
        
        echo "🏭 Entrepôts créés\n";
    }
    
    private function createPartners()
    {
        $partners = [
            [
                'name' => 'EcoRecycle Tunisie',
                'email' => 'contact@ecorecycle-tn.com',
                'phone' => '+216 12 345 678',
                'type' => 'entreprise',
                'address' => '123 Avenue Habib Bourguiba, Tunis',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Verre Vert',
                'email' => 'info@verrevert.tn',
                'phone' => '+216 23 456 789',
                'type' => 'entreprise',
                'address' => '456 Rue de la République, Sfax',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Métal Pro',
                'email' => 'contact@metalpro.tn',
                'phone' => '+216 34 567 890',
                'type' => 'entreprise',
                'address' => '789 Avenue Taher Haddad, Sousse',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];
        
        foreach ($partners as $partner) {
            DB::table('partners')->insertOrIgnore($partner);
        }
        
        echo "🤝 Partenaires créés\n";
    }
}