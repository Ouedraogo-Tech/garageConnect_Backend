<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReparationRequest;
use App\Http\Requests\UpdateReparationRequest;
use App\Mail\VehiculePretMail;
use App\Models\Reparation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ReparationController extends Controller
{
    public function index(Request $request)
    {
        $query = Reparation::with('vehicule', 'techniciens', 'pieces');

        if ($request->filled('recherche')) {
            $recherche = $request->input('recherche');
            $query->where('objet_reparation', 'like', "%{$recherche}%");
        }

        if ($request->filled('statut')) {
            match ($request->input('statut')) {
                'en_cours' => $query->whereNull('signalee_terminee_le')->whereNull('date_fin_reelle'),
                'signalee' => $query->whereNotNull('signalee_terminee_le')->whereNull('date_fin_reelle'),
                'terminee' => $query->whereNotNull('date_fin_reelle'),
                default => null,
            };
        }

        if ($request->filled('technicien_id')) {
            $query->whereHas('techniciens', function ($q) use ($request) {
                $q->where('techniciens.id', $request->input('technicien_id'));
            });
        }

        if ($request->filled('date_debut')) {
            $query->whereDate('date', '>=', $request->input('date_debut'));
        }

        if ($request->filled('date_fin')) {
            $query->whereDate('date', '<=', $request->input('date_fin'));
        }

        if ($request->boolean('all')) {
            return $query->get();
        }

        return $query->paginate(10);
    }

    public function store(StoreReparationRequest $request)
    {
        $reparation = Reparation::create($request->only([
            'vehicule_id', 'date', 'date_fin_prevue', 'date_fin_reelle', 'duree_main_oeuvre', 'objet_reparation',
        ]));

        if ($request->has('techniciens')) {
            $reparation->techniciens()->attach($request->techniciens);
        }

        if ($request->has('pieces')) {
            foreach ($request->pieces as $p) {
                $reparation->pieces()->attach($p['piece_id'], ['quantite_utilisee' => $p['quantite']]);
            }
        }

        return response()->json($reparation->load('vehicule', 'techniciens', 'pieces'), 201);
    }

    public function show($id)
    {
        $reparation = Reparation::with('vehicule', 'techniciens', 'pieces')->findOrFail($id);
        return response()->json($reparation);
    }

    public function update(UpdateReparationRequest $request, $id)
    {
        // L'autorisation (admin, ou technicien assigné) est déjà vérifiée
        // dans UpdateReparationRequest::authorize(), avant la validation.
        $reparation = Reparation::findOrFail($id);
        $user = $request->user();

        $finReellesAvant = $reparation->date_fin_reelle;

        $donnees = $request->only([
            'vehicule_id', 'date', 'date_fin_prevue', 'date_fin_reelle', 'duree_main_oeuvre', 'objet_reparation',
        ]);

        // Sécurité : seul l'admin peut renseigner/modifier la date de fin réelle
        // (qui déclenche la notification client).
        if ($user->role === 'technicien') {
            $donnees['date_fin_reelle'] = $reparation->date_fin_reelle;
        }

        $reparation->update($donnees);

        if ($request->has('techniciens')) {
            $reparation->techniciens()->sync($request->techniciens);
        }

        if ($request->has('pieces')) {
            $sync = [];
            foreach ($request->pieces as $p) {
                $sync[$p['piece_id']] = ['quantite_utilisee' => $p['quantite']];
            }
            $reparation->pieces()->sync($sync);
        }

        if (! $finReellesAvant && $reparation->date_fin_reelle) {
            $reparation->load('vehicule.client.user');
            $emailClient = $reparation->vehicule->client->user->email
                ?? $reparation->vehicule->email_proprietaire
                ?? null;

            if ($emailClient) {
                Mail::to($emailClient)->send(new VehiculePretMail($reparation));
            }
        }

        return response()->json($reparation->load('vehicule', 'techniciens', 'pieces'));
    }

    public function signalerTerminee(Request $request, $id)
    {
        $reparation = Reparation::findOrFail($id);

        if (! $request->user()->can('signalerTerminee', $reparation)) {
            return response()->json(['message' => 'Vous n\'êtes pas assigné à cette réparation.'], 403);
        }

        $reparation->update(['signalee_terminee_le' => now()]);

        return response()->json($reparation->load('vehicule', 'techniciens', 'pieces'));
    }

    public function destroy($id)
    {
        $reparation = Reparation::findOrFail($id);
        $reparation->techniciens()->detach();
        $reparation->delete();

        return response()->json(null, 204);
    }
}
