<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Voiture;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VoitureControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_user_voitures()
    {
        $user = User::factory()->create();
        $voiture = Voiture::factory()->create(['user_id' => $user->user_id]);
        $otherVoiture = Voiture::factory()->create();

        $response = $this->actingAs($user)->getJson('/api/voitures');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'voitures')
            ->assertJsonFragment(['immatriculation' => $voiture->immatriculation]);
    }

    public function test_store_creates_new_voiture()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/voitures', [
            'immatriculation' => '1234TBE',
            'marque' => 'Toyota',
            'modele' => 'Corolla',
            'annee' => 2020,
            'couleur' => 'Rouge',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['message', 'voiture']);

        $this->assertDatabaseHas('voitures', [
            'immatriculation' => '1234TBE',
            'user_id' => $user->user_id
        ]);
    }

    public function test_update_modifies_voiture()
    {
        $user = User::factory()->create();
        $voiture = Voiture::factory()->create(['user_id' => $user->user_id]);

        $response = $this->actingAs($user)->putJson("/api/voitures/{$voiture->voiture_id}", [
            'couleur' => 'Bleu',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('voitures', [
            'voiture_id' => $voiture->voiture_id,
            'couleur' => 'Bleu'
        ]);
    }

    public function test_destroy_deletes_voiture()
    {
        $user = User::factory()->create();
        $voiture = Voiture::factory()->create(['user_id' => $user->user_id]);

        $response = $this->actingAs($user)->deleteJson("/api/voitures/{$voiture->voiture_id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('voitures', ['voiture_id' => $voiture->voiture_id]);
    }
}
