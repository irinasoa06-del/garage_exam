<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    public function definition()
    {
        return [
            'nom' => $this->faker->lastName(),
            'prenom' => $this->faker->firstName(),
            'email' => $this->faker->unique()->safeEmail(),
            'mot_de_passe_hash' => Hash::make('password'),
            'telephone' => $this->faker->phoneNumber(),
            'adresse' => $this->faker->address(),
            'role' => 'client',
            'firebase_uid' => Str::random(20),
        ];
    }
}
