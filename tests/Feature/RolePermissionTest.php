<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Technicien;
use App\Models\User;
use App\Models\Vehicule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RolePermissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_client_peut_creer_son_propre_vehicule(): void
    {
        $client = Client::factory()->create();
        $user = User::factory()->client($client->id)->create();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/vehicules', [
            'immatriculation' => '11-AA-1234',
            'marque' => 'Toyota',
            'modele' => 'Yaris',
            'couleur' => 'Rouge',
            'annee' => 2020,
            'kilometrage' => 50000,
            'carrosserie' => 'Berline',
            'energie' => 'Essence',
            'boite' => 'Manuelle',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('vehicules', [
            'immatriculation' => '11-AA-1234',
            'client_id' => $client->id,
        ]);
    }

    public function test_un_client_ne_peut_pas_sapproprier_le_vehicule_dun_autre_client(): void
    {
        $clientA = Client::factory()->create();
        $clientB = Client::factory()->create();
        $vehiculeDeB = Vehicule::factory()->create(['client_id' => $clientB->id]);

        $userA = User::factory()->client($clientA->id)->create();
        Sanctum::actingAs($userA);

        // userA tente de voir le véhicule de B
        $response = $this->getJson("/api/vehicules/{$vehiculeDeB->id}");
        $response->assertStatus(403);
    }

    public function test_un_client_ne_peut_pas_modifier_le_vehicule_dun_autre_client(): void
    {
        $clientA = Client::factory()->create();
        $clientB = Client::factory()->create();
        $vehiculeDeB = Vehicule::factory()->create(['client_id' => $clientB->id]);

        $userA = User::factory()->client($clientA->id)->create();
        Sanctum::actingAs($userA);

        $response = $this->putJson("/api/vehicules/{$vehiculeDeB->id}", [
            'immatriculation' => $vehiculeDeB->immatriculation,
            'marque' => 'Modifié',
            'modele' => $vehiculeDeB->modele,
            'couleur' => $vehiculeDeB->couleur,
            'annee' => $vehiculeDeB->annee,
            'kilometrage' => $vehiculeDeB->kilometrage,
            'carrosserie' => $vehiculeDeB->carrosserie,
            'energie' => $vehiculeDeB->energie,
            'boite' => $vehiculeDeB->boite,
        ]);

        $response->assertStatus(403);
    }

    public function test_un_client_ne_peut_pas_creer_un_technicien(): void
    {
        $user = User::factory()->client()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/techniciens', [
            'nom' => 'Test',
            'prenom' => 'Test',
            'specialite' => 'Moteur',
        ]);

        $response->assertStatus(403);
    }

    public function test_un_technicien_ne_peut_pas_renseigner_la_date_de_fin_reelle_dune_reparation(): void
    {
        $technicien = Technicien::factory()->create();
        $userTechnicien = User::factory()->technicien($technicien->id)->create();
        $vehicule = Vehicule::factory()->create();

        $reparation = \App\Models\Reparation::create([
            'vehicule_id' => $vehicule->id,
            'date' => now()->toDateString(),
            'duree_main_oeuvre' => 1,
            'objet_reparation' => 'Test',
        ]);
        $reparation->techniciens()->attach($technicien->id);

        Sanctum::actingAs($userTechnicien);

        $response = $this->putJson("/api/reparations/{$reparation->id}", [
            'vehicule_id' => $vehicule->id,
            'date' => $reparation->date,
            'date_fin_reelle' => now()->toDateString(), // tentative d'auto-clôture
            'duree_main_oeuvre' => 2,
            'objet_reparation' => 'Test modifié',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('reparations', [
            'id' => $reparation->id,
            'date_fin_reelle' => null, // doit rester null malgré la tentative
        ]);
    }
}
