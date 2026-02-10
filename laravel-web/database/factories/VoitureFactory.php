<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class VoitureFactory extends Factory
{
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'immatriculation' => $this->faker->unique()->bothify('####???'),
            'marque' => $this->faker->word(),
            'modele' => $this->faker->word(),
            'annee' => $this->faker->year(),
            'couleur' => $this->faker->colorName(),
            'statut' => 'disponible',
        ];
    }
}
