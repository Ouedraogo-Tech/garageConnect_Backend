<?php

namespace Tests\Feature;

use App\Models\Technicien;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RoleAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    private function donneesVehiculeTest(): array
    {
        return [
            'immatriculation' => '11-BF-9999',
            'marque' => 'Test',
            'modele' => 'Test',
            'couleur' => 'Noir',
            'annee' => 2024,
            'kilometrage' => 0,
            'carrosserie' => 'Berline',
            'energie' => 'Essence',
            'boite' => 'Manuelle',
        ];
    }

    /**
     * Un technicien ne doit pas pouvoir créer un véhicule.
     */
    public function test_un_technicien_ne_peut_pas_creer_un_vehicule(): void
    {
        $technicien = Technicien::factory()->create();

        $user = User::factory()->create([
            'role' => 'technicien',
            'technicien_id' => $technicien->id,
        ]);

        Sanctum::actingAs($user, ['*']);

        $response = $this->postJson('/api/vehicules', $this->donneesVehiculeTest());

        $response->assertStatus(403);
        $response->assertJson(['message' => 'Accès non autorisé pour votre rôle.']);
    }

    /**
     * Un administrateur peut créer un véhicule.
     */
    public function test_un_admin_peut_creer_un_vehicule(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        Sanctum::actingAs($admin, ['*']);

        $response = $this->postJson('/api/vehicules', $this->donneesVehiculeTest());

        $response->assertStatus(201);
    }

    /**
     * Une requête sans authentification est rejetée.
     */
    public function test_une_requete_non_authentifiee_est_rejetee(): void
    {
        $response = $this->getJson('/api/vehicules');

        $response->assertStatus(401);
    }
}
