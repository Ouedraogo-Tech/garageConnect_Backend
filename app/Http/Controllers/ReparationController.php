<?php

namespace App\Http\Controllers;

use App\Models\Reparation;
use Illuminate\Http\Request;

class ReparationController extends Controller
{
    // Liste de toutes les réparations
    public function index(Request $request)
    {
        $query = Reparation::with('vehicule', 'techniciens');

        if ($request->filled('recherche')) {
            $recherche = $request->input('recherche');
            $query->where('objet_reparation', 'like', "%{$recherche}%");
        }

        return $query->get();
    }

    // Pour crée une nouvelle réparation
    public function store(Request $request)
    {
        $request->validate([
            'vehicule_id' => 'required|exists:vehicules,id',
            'date' => 'required|date',
            'duree_main_oeuvre' => 'required|numeric|min:0|max:24',
            'objet_reparation' => 'required|string|max:255',
            'techniciens' => 'nullable|array',
            'techniciens.*' => 'exists:techniciens,id',
        ]);

        $reparation = Reparation::create($request->only([
            'vehicule_id', 'date', 'duree_main_oeuvre', 'objet_reparation',
        ]));

        if ($request->has('techniciens')) {
            $reparation->techniciens()->attach($request->techniciens);
        }

        return response()->json($reparation->load('vehicule', 'techniciens'), 201);
    }

    // Affiche une réparation précise
    public function show($id)
    {
        $reparation = Reparation::with('vehicule', 'techniciens')->findOrFail($id);
        return response()->json($reparation);
    }

    // Met à jour une réparation
    public function update(Request $request, $id)
    {
        $reparation = Reparation::findOrFail($id);
        $user = $request->user();

        if ($user->role === 'technicien') {
            $estAssigne = $reparation->techniciens()
                ->where('techniciens.id', $user->technicien_id)
                ->exists();

            if (! $estAssigne) {
                return response()->json([
                    'message' => 'Vous n\'êtes pas assigné à cette réparation.',
                ], 403);
            }
        }

        $request->validate([
            'vehicule_id' => 'required|exists:vehicules,id',
            'date' => 'required|date',
            'duree_main_oeuvre' => 'required|numeric|min:0|max:24',
            'objet_reparation' => 'required|string|max:255',
            'techniciens' => 'nullable|array',
            'techniciens.*' => 'exists:techniciens,id',
        ]);

        $reparation->update($request->only([
            'vehicule_id', 'date', 'duree_main_oeuvre', 'objet_reparation',
        ]));

        if ($request->has('techniciens')) {
            $reparation->techniciens()->sync($request->techniciens);
        }

        return response()->json($reparation->load('vehicule', 'techniciens'));
    }

    // Supprime une réparation
    public function destroy($id)
    {
        $reparation = Reparation::findOrFail($id);
        $reparation->techniciens()->detach();
        $reparation->delete();

        return response()->json(null, 204);
    }
}
