<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRendezVousRequest;
use App\Http\Requests\UpdateRendezVousRequest;
use App\Mail\RdvConfirmeMail;
use App\Mail\RdvRefuseMail;
use App\Models\Reparation;
use App\Models\RendezVous;
use App\Models\Vehicule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class RendezVousController extends Controller
{
    /**
     * Liste des RDV : l'admin voit tout, le client ne voit que les siens.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $query = RendezVous::with('client.user', 'vehicule', 'reparation')
            ->orderBy('date_rdv')
            ->orderBy('heure_rdv');

        if ($user->role === 'client') {
            $query->where('client_id', $user->client_id);
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->input('statut'));
        }

        if ($request->filled('date_debut')) {
            $query->whereDate('date_rdv', '>=', $request->input('date_debut'));
        }

        if ($request->filled('date_fin')) {
            $query->whereDate('date_rdv', '<=', $request->input('date_fin'));
        }

        if ($request->boolean('all')) {
            return $query->get();
        }

        return $query->paginate(10);
    }

    /**
     * Le client crée une demande de RDV.
     * Le véhicule est désormais obligatoire : un client qui réserve doit
     * préciser le véhicule qu'il vient faire réparer.
     */
    public function store(StoreRendezVousRequest $request)
    {
        $user = $request->user();

        $appartient = Vehicule::where('id', $request->vehicule_id)
            ->where('client_id', $user->client_id)
            ->exists();

        if (! $appartient) {
            return response()->json(['message' => 'Ce véhicule ne vous appartient pas.'], 403);
        }

        $rendezVous = RendezVous::create([
            'client_id' => $user->client_id,
            'vehicule_id' => $request->vehicule_id,
            'date_rdv' => $request->date_rdv,
            'heure_rdv' => $request->heure_rdv,
            'motif' => $request->motif,
            'statut' => 'en_attente',
        ]);

        return response()->json($rendezVous->load('client', 'vehicule'), 201);
    }

    public function show(Request $request, $id)
{
    $rendezVous = RendezVous::with('client', 'vehicule')->findOrFail($id);

    if (! $request->user()->can('view', $rendezVous)) {
        return response()->json(['message' => 'Accès refusé.'], 403);
    }

    return response()->json($rendezVous);
}
    /**
     * L'admin confirme/refuse un RDV.
     * - Confirmer : crée automatiquement la Réparation liée (avec date de fin
     *   prévue optionnelle), puis envoie l'email de confirmation au client.
     * - Refuser : envoie l'email de refus avec le motif (commentaire_admin).
     */
    public function update(UpdateRendezVousRequest $request, $id)
    {
        $rendezVous = RendezVous::with('vehicule', 'client.user')->findOrFail($id);

        $statutAvant = $rendezVous->statut;

        $rendezVous->update($request->only(['statut', 'commentaire_admin']));

        // Confirmation : on crée la réparation liée (une seule fois)
        if ($statutAvant !== 'confirme' && $rendezVous->statut === 'confirme') {
            if (! $rendezVous->vehicule_id) {
                return response()->json([
                    'message' => "Impossible de confirmer : aucun véhicule n'est associé à ce rendez-vous.",
                ], 422);
            }

            $reparationExistante = Reparation::where('rendez_vous_id', $rendezVous->id)->first();

            if (! $reparationExistante) {
                Reparation::create([
                    'vehicule_id' => $rendezVous->vehicule_id,
                    'rendez_vous_id' => $rendezVous->id,
                    'date' => $rendezVous->date_rdv,
                    'date_fin_prevue' => $request->input('date_fin_prevue'),
                    'duree_main_oeuvre' => 0,
                    'objet_reparation' => $rendezVous->motif,
                ]);
            }

            $emailClient = $rendezVous->client->user->email ?? null;
            if ($emailClient) {
                Mail::to($emailClient)->send(new RdvConfirmeMail($rendezVous, $request->input('date_fin_prevue')));
            }
        }

        // Refus : on notifie le client
        if ($statutAvant !== 'refuse' && $rendezVous->statut === 'refuse') {
            $emailClient = $rendezVous->client->user->email ?? null;
            if ($emailClient) {
                Mail::to($emailClient)->send(new RdvRefuseMail($rendezVous));
            }
        }

        return response()->json($rendezVous->load('client', 'vehicule'));
    }

    /**
     * Le client peut annuler sa propre demande si elle est encore en attente.
     * L'admin peut supprimer n'importe quel RDV.
     */
    public function destroy(Request $request, $id)
{
    $rendezVous = RendezVous::findOrFail($id);

    if (! $request->user()->can('cancel', $rendezVous)) {
        return response()->json(['message' => "Impossible d'annuler ce rendez-vous."], 403);
    }

    $rendezVous->delete();

    return response()->json(null, 204);
}
}
