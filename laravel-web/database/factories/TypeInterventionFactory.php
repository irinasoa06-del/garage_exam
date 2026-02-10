<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TypeInterventionFactory extends Factory
{
    public function definition()
    {
        return [
            'nom' => $this->faker->word(),
            'prix_unitaire' => $this->faker->numberBetween(10000, 100000),
            'duree_secondes' => $this->faker->numberBetween(3600, 7200),
            'description' => $this->faker->sentence(),
        ];
    }
}
