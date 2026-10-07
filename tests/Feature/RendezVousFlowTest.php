<?php

namespace Tests\Feature;

use App\Mail\RdvConfirmeMail;
use App\Mail\RdvRefuseMail;
use App\Mail\VehiculePretMail;
use App\Models\Client;
use App\Models\Reparation;
use App\Models\Technicien;
use App\Models\User;
use App\Models\Vehicule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RendezVousFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_client_peut_creer_un_rendez_vous_avec_son_vehicule(): void
    {
        $client = Client::factory()->create();
        $vehicule = Vehicule::factory()->create(['client_id' => $client->id]);
        $user = User::factory()->client($client->id)->create();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/rendez-vous', [
            'vehicule_id' => $vehicule->id,
            'date_rdv' => now()->addDays(3)->toDateString(),
            'heure_rdv' => '09:00',
            'motif' => 'Vidange',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('rendez_vous', [
            'client_id' => $client->id,
            'vehicule_id' => $vehicule->id,
            'statut' => 'en_attente',
        ]);
    }

    public function test_confirmer_un_rdv_cree_automatiquement_une_reparation_et_envoie_un_email(): void
    {
        Mail::fake();

        $client = Client::factory()->create();
        $clientUser = User::factory()->client($client->id)->create(['email' => 'sara@test.com']);
        $vehicule = Vehicule::factory()->create(['client_id' => $client->id]);

        $rdv = \App\Models\RendezVous::create([
            'client_id' => $client->id,
            'vehicule_id' => $vehicule->id,
            'date_rdv' => now()->addDays(2)->toDateString(),
            'heure_rdv' => '10:00',
            'motif' => 'Cylindre',
            'statut' => 'en_attente',
        ]);

        $admin = User::factory()->admin()->create();
        Sanctum::actingAs($admin);

        $response = $this->putJson("/api/rendez-vous/{$rdv->id}", [
            'statut' => 'confirme',
            'date_fin_prevue' => now()->addDays(7)->toDateString(),
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('rendez_vous', ['id' => $rdv->id, 'statut' => 'confirme']);
        $this->assertDatabaseHas('reparations', [
            'rendez_vous_id' => $rdv->id,
            'vehicule_id' => $vehicule->id,
        ]);

        Mail::assertSent(RdvConfirmeMail::class, function ($mail) use ($clientUser) {
            return $mail->hasTo($clientUser->email);
        });
    }

    public function test_confirmer_deux_fois_ne_cree_pas_deux_reparations(): void
    {
        Mail::fake();

        $client = Client::factory()->create();
        User::factory()->client($client->id)->create();
        $vehicule = Vehicule::factory()->create(['client_id' => $client->id]);

        $rdv = \App\Models\RendezVous::create([
            'client_id' => $client->id,
            'vehicule_id' => $vehicule->id,
            'date_rdv' => now()->addDays(2)->toDateString(),
            'heure_rdv' => '10:00',
            'motif' => 'Cylindre',
            'statut' => 'confirme', // déjà confirmé
        ]);

        Reparation::create([
            'vehicule_id' => $vehicule->id,
            'rendez_vous_id' => $rdv->id,
            'date' => $rdv->date_rdv,
            'duree_main_oeuvre' => 0,
            'objet_reparation' => $rdv->motif,
        ]);

        $admin = User::factory()->admin()->create();
        Sanctum::actingAs($admin);

        $this->putJson("/api/rendez-vous/{$rdv->id}", ['statut' => 'confirme']);

        $this->assertDatabaseCount('reparations', 1);
    }

    public function test_refuser_un_rdv_envoie_un_email_avec_le_motif(): void
    {
        Mail::fake();

        $client = Client::factory()->create();
        $clientUser = User::factory()->client($client->id)->create();
        $vehicule = Vehicule::factory()->create(['client_id' => $client->id]);

        $rdv = \App\Models\RendezVous::create([
            'client_id' => $client->id,
            'vehicule_id' => $vehicule->id,
            'date_rdv' => now()->addDays(2)->toDateString(),
            'heure_rdv' => '10:00',
            'motif' => 'Cylindre',
            'statut' => 'en_attente',
        ]);

        $admin = User::factory()->admin()->create();
        Sanctum::actingAs($admin);

        $this->putJson("/api/rendez-vous/{$rdv->id}", [
            'statut' => 'refuse',
            'commentaire_admin' => 'Pièces indisponibles',
        ])->assertStatus(200);

        $this->assertDatabaseHas('rendez_vous', ['id' => $rdv->id, 'statut' => 'refuse']);
        Mail::assertSent(RdvRefuseMail::class, function ($mail) use ($clientUser) {
            return $mail->hasTo($clientUser->email);
        });
    }

    public function test_un_technicien_peut_signaler_une_reparation_terminee_sans_notifier_le_client(): void
    {
        Mail::fake();

        $technicien = Technicien::factory()->create();
        $userTechnicien = User::factory()->technicien($technicien->id)->create();
        $vehicule = Vehicule::factory()->create();

        $reparation = Reparation::create([
            'vehicule_id' => $vehicule->id,
            'date' => now()->toDateString(),
            'duree_main_oeuvre' => 1,
            'objet_reparation' => 'Test',
        ]);
        $reparation->techniciens()->attach($technicien->id);

        Sanctum::actingAs($userTechnicien);

        $this->putJson("/api/reparations/{$reparation->id}/signaler-terminee")
            ->assertStatus(200);

        $reparation->refresh();
        $this->assertNotNull($reparation->signalee_terminee_le);
        $this->assertNull($reparation->date_fin_reelle);

        Mail::assertNothingSent();
    }

    public function test_marquer_une_reparation_terminee_envoie_lemail_vehicule_pret(): void
    {
        Mail::fake();

        $client = Client::factory()->create();
        $clientUser = User::factory()->client($client->id)->create();
        $vehicule = Vehicule::factory()->create(['client_id' => $client->id]);

        $reparation = Reparation::create([
            'vehicule_id' => $vehicule->id,
            'date' => now()->toDateString(),
            'duree_main_oeuvre' => 1,
            'objet_reparation' => 'Test',
        ]);

        $admin = User::factory()->admin()->create();
        Sanctum::actingAs($admin);

        $this->putJson("/api/reparations/{$reparation->id}", [
            'vehicule_id' => $vehicule->id,
            'date' => $reparation->date,
            'date_fin_reelle' => now()->toDateString(),
            'duree_main_oeuvre' => 1,
            'objet_reparation' => 'Test',
        ])->assertStatus(200);

        Mail::assertSent(VehiculePretMail::class, function ($mail) use ($clientUser) {
            return $mail->hasTo($clientUser->email);
        });
    }
}
