<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVehiculeRequest;
use App\Http\Requests\UpdateVehiculeRequest;
use App\Models\Vehicule;
use Illuminate\Http\Request;

class VehiculeController extends Controller
{
    public function index(Request $request)
    {
        $query = Vehicule::with('client');

        if ($request->user()->role === 'client') {
            $query->where('client_id', $request->user()->client_id);
        }

        if ($request->filled('recherche')) {
            $recherche = $request->input('recherche');
            $query->where(function ($q) use ($recherche) {
                $q->where('marque', 'like', "%{$recherche}%")
                  ->orWhere('immatriculation', 'like', "%{$recherche}%");
            });
        }

        if ($request->boolean('all')) {
            return $query->get();
        }

        return $query->paginate(10);
    }

    public function store(StoreVehiculeRequest $request)
    {
        $data = $request->validated();

        if ($request->user()->role === 'client') {
            $data['client_id'] = $request->user()->client_id;
        }

        $vehicule = Vehicule::create($data);
        return response()->json($vehicule, 201);
    }

    public function show(Request $request, $id)
    {
        $vehicule = Vehicule::findOrFail($id);

        if (! $request->user()->can('view', $vehicule)) {
            return response()->json(['message' => 'Accès refusé.'], 403);
        }

        return response()->json($vehicule);
    }

    public function update(UpdateVehiculeRequest $request, $id)
    {
        // L'autorisation (propriété du véhicule) est déjà vérifiée dans
        // UpdateVehiculeRequest::authorize(), avant même la validation.
        $vehicule = Vehicule::findOrFail($id);

        $data = $request->validated();

        if ($request->user()->role === 'client') {
            $data['client_id'] = $request->user()->client_id;
        }

        $vehicule->update($data);
        return response()->json($vehicule);
    }

    public function destroy(Request $request, $id)
    {
        $vehicule = Vehicule::findOrFail($id);

        if (! $request->user()->can('delete', $vehicule)) {
            return response()->json(['message' => 'Accès refusé.'], 403);
        }

        $vehicule->delete();
        return response()->json(null, 204);
    }
}
