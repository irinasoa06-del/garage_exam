<?php

namespace Database\Seeders;

use App\Models\TypeIntervention;
use Illuminate\Database\Seeder;

class TypeInterventionSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            [
                'nom' => 'Frein',
                'duree_secondes' => 3600,
                'prix_unitaire' => 150.00,
                'description' => 'Réparation du système de freinage'
            ],
            [
                'nom' => 'Vidange',
                'duree_secondes' => 1800,
                'prix_unitaire' => 80.00,
                'description' => 'Vidange d\'huile et remplacement du filtre'
            ],
            [
                'nom' => 'Filtre',
                'duree_secondes' => 1200,
                'prix_unitaire' => 30.00,
                'description' => 'Remplacement des filtres'
            ],
            [
                'nom' => 'Batterie',
                'duree_secondes' => 2400,
                'prix_unitaire' => 120.00,
                'description' => 'Remplacement de la batterie'
            ],
            [
                'nom' => 'Amortisseurs',
                'duree_secondes' => 4800,
                'prix_unitaire' => 300.00,
                'description' => 'Remplacement des amortisseurs'
            ],
            [
                'nom' => 'Embrayage',
                'duree_secondes' => 7200,
                'prix_unitaire' => 450.00,
                'description' => 'Réparation du système d\'embrayage'
            ],
            [
                'nom' => 'Pneus',
                'duree_secondes' => 3600,
                'prix_unitaire' => 200.00,
                'description' => 'Remplacement des pneus'
            ],
            [
                'nom' => 'Système de refroidissement',
                'duree_secondes' => 4200,
                'prix_unitaire' => 250.00,
                'description' => 'Réparation du système de refroidissement'
            ],
        ];

        foreach ($types as $type) {
            TypeIntervention::create($type);
        }
    }
}