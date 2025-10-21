<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer un utilisateur administrateur par défaut
        User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'first_name' => 'Admin',
                'last_name' => 'TeaHouse',
                'email' => 'admin@gmail.com',
                'role' => 'admin',
                'is_active' => true,
                'phone' => '+216 12345678',
                'city' => 'Tunis',
                'password' => Hash::make('admin123'),
                'newsletter_subscription' => false,
                'terms_accepted' => true,
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Utilisateur administrateur créé avec succès !');
        $this->command->info('Email: admin@gmail.com');
        $this->command->info('Mot de passe: admin123');
    }
}
