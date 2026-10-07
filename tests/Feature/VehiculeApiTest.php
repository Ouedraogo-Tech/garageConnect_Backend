<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Vehicule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class VehiculeApiTest extends TestCase
{
    use RefreshDatabase;

    private function connecterAdmin(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($admin, ['*']);
    }

    /**
     * Test 1 : la route GET /api/vehicules retourne un statut 200
     * et une structure JSON correcte.
     */
    public function test_liste_vehicules_retourne_200_et_json_correct(): void
    {
        $this->connecterAdmin();

        // Arrange : on crée 3 véhicules grâce à la Factory existante
        Vehicule::factory()->count(3)->create();

        // Act : on appelle l'endpoint API
        $response = $this->getJson('/api/vehicules');

        // Assert : statut 200 + exactement 3 éléments à la racine du JSON
        $response->assertStatus(200);
        $response->assertJsonCount(3, 'data');
    }

    /**
     * Test 2 : la création d'un véhicule sans immatriculation
     * est refusée par la validation (statut 422).
     */
    public function test_creation_vehicule_sans_immatriculation_est_refusee(): void
    {
        $this->connecterAdmin();

        $donneesIncompletes = [
            'marque' => 'Toyota',
            'modele' => 'Corolla',
            'couleur' => 'Gris',
            'annee' => 2020,
            'kilometrage' => 50000,
            'carrosserie' => 'Berline',
            'energie' => 'Essence',
            'boite' => 'Manuelle',
        ];

        $response = $this->postJson('/api/vehicules', $donneesIncompletes);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['immatriculation']);
    }
}
