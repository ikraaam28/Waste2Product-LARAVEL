<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Créer un utilisateur admin par défaut
        User::firstOrCreate(
            ['email' => 'admin@waste2product.com'],
            [
                'first_name' => 'Admin',
                'last_name' => 'System',
                'password' => Hash::make('admin123'),
                'terms_accepted' => true,
                'email_verified_at' => now(),
            ]
        );

        $this->call([
            CategoryProductSeeder::class,
            EventSeederFixed::class,
        ]);
    }
}
