<?php

namespace App\Http\Controllers;

use App\Models\Technicien;
use Illuminate\Http\Request;

class TechnicienController extends Controller
{
    // Liste de tous les techniciens
    public function index(Request $request)
    {
        $query = Technicien::query();

        if ($request->filled('recherche')) {
            $recherche = $request->input('recherche');
            $query->where(function ($q) use ($recherche) {
                $q->where('nom', 'like', "%{$recherche}%")
                  ->orWhere('prenom', 'like', "%{$recherche}%")
                  ->orWhere('specialite', 'like', "%{$recherche}%");
            });
        }

        return $query->get();
    }

    // Crée un nouveau technicien
    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'specialite' => 'required|string|max:255',
        ]);

        $technicien = Technicien::create($request->all());
        return response()->json($technicien, 201);
    }

    // Affiche un technicien précis
    public function show($id)
    {
        $technicien = Technicien::findOrFail($id);
        return response()->json($technicien);
    }

    // Met à jour un technicien
    public function update(Request $request, $id)
    {
        $technicien = Technicien::findOrFail($id);

        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'specialite' => 'required|string|max:255',
        ]);

        $technicien->update($request->all());
        return response()->json($technicien);
    }

    // Supprime un technicien
    public function destroy($id)
    {
        $technicien = Technicien::findOrFail($id);
        $technicien->reparations()->detach();
        $technicien->delete();

        return response()->json(null, 204);
    }
}
