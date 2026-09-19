<?php

namespace App\Http\Controllers;

use App\Models\Vehicule;
use Illuminate\Http\Request;

class VehiculeController extends Controller
{
    // Liste de tous les véhicules
    public function index(Request $request)
    {
        $query = Vehicule::query();

        if ($request->filled('recherche')) {
            $recherche = $request->input('recherche');
            $query->where(function ($q) use ($recherche) {
                $q->where('marque', 'like', "%{$recherche}%")
                  ->orWhere('immatriculation', 'like', "%{$recherche}%");
            });
        }

        return $query->get();
    }

    // Crée un nouveau véhicule
    public function store(Request $request)
    {
        $request->validate([
            'immatriculation' => 'required|string|unique:vehicules,immatriculation',
            'marque' => 'required|string|max:255',
            'modele' => 'required|string|max:255',
            'couleur' => 'required|string|max:255',
            'annee' => 'required|integer|min:1950|max:' . date('Y'),
            'kilometrage' => 'required|integer|min:0',
            'carrosserie' => 'required|string|max:255',
            'energie' => 'required|string|max:255',
            'boite' => 'required|string|max:255',
        ]);

        $vehicule = Vehicule::create($request->all());
        return response()->json($vehicule, 201);
    }

    // Affiche un véhicule précis
    public function show($id)
    {
        $vehicule = Vehicule::findOrFail($id);
        return response()->json($vehicule);
    }

    // Met à jour un véhicule
    public function update(Request $request, $id)
    {
        $vehicule = Vehicule::findOrFail($id);

        $request->validate([
            'immatriculation' => 'required|string|unique:vehicules,immatriculation,' . $vehicule->id,
            'marque' => 'required|string|max:255',
            'modele' => 'required|string|max:255',
            'couleur' => 'required|string|max:255',
            'annee' => 'required|integer|min:1950|max:' . date('Y'),
            'kilometrage' => 'required|integer|min:0',
            'carrosserie' => 'required|string|max:255',
            'energie' => 'required|string|max:255',
            'boite' => 'required|string|max:255',
        ]);

        $vehicule->update($request->all());
        return response()->json($vehicule);
    }

    // Supprime un véhicule
    public function destroy($id)
    {
        Vehicule::destroy($id);
        return response()->json(null, 204);
    }
}
