<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Voiture;
use App\Models\Intervention;
use App\Models\TypeIntervention;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InterventionControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_interventions()
    {
        $user = User::factory()->create();
        $voiture = Voiture::factory()->create(['user_id' => $user->user_id]);
        $type = TypeIntervention::create(['nom' => 'Vidange', 'prix_unitaire' => 50000, 'duree_secondes' => 3600]);
        $intervention = Intervention::create([
             'voiture_id' => $voiture->voiture_id,
             'type_id' => $type->type_id,
             'description_panne' => 'Test Panne',
             'priorite' => 'haute',
             'date_signalement' => now()
        ]);

        $response = $this->actingAs($user)->getJson('/api/interventions');

        $response->assertStatus(200)
            ->assertJsonStructure(['interventions'])
            ->assertJsonFragment(['description_panne' => 'Test Panne']);
    }

    public function test_store_creates_intervention()
    {
        $user = User::factory()->create();
        $voiture = Voiture::factory()->create(['user_id' => $user->user_id]);
        $type = TypeIntervention::create(['nom' => 'Vidange', 'prix_unitaire' => 50000, 'duree_secondes' => 3600]);

        $response = $this->actingAs($user)->postJson('/api/interventions', [
            'voiture_id' => $voiture->voiture_id,
            'type_id' => $type->type_id,
            'description_panne' => 'Freins cassés',
            'priorite' => 'haute',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('interventions', ['description_panne' => 'Freins cassés']);
    }

    public function test_store_for_voiture_creates_intervention()
    {
        $user = User::factory()->create();
        $voiture = Voiture::factory()->create(['user_id' => $user->user_id]);
        $type = TypeIntervention::create(['nom' => 'Vidange', 'prix_unitaire' => 50000, 'duree_secondes' => 3600]);

        $response = $this->actingAs($user)->postJson("/api/voitures/{$voiture->voiture_id}/interventions", [
            'type_id' => $type->type_id,
            'description_panne' => 'Pneu crevé',
            'priorite' => 'moyenne',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('interventions', ['description_panne' => 'Pneu crevé']);
    }
}
