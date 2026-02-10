<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Créer un admin
        User::create([
            'nom' => 'Admin',
            'prenom' => 'Garage',
            'email' => 'admin@garage.com',
            'mot_de_passe_hash' => Hash::make('admin123'),
            'telephone' => '0123456789',
            'adresse' => '123 Rue du Garage, 75000 Paris',
            'role' => 'admin',
        ]);

        // Créer quelques clients
        for ($i = 1; $i <= 5; $i++) {
            User::create([
                'nom' => 'Client' . $i,
                'prenom' => 'Test' . $i,
                'email' => 'client' . $i . '@example.com',
                'mot_de_passe_hash' => Hash::make('password123'),
                'telephone' => '06' . str_pad($i, 8, '0', STR_PAD_LEFT),
                'adresse' => $i . ' Rue de Test, 7500' . $i . ' Paris',
                'role' => 'client',
            ]);
        }
    }
}