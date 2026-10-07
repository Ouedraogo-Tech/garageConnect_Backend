<?php

namespace App\Http\Controllers;

use App\Models\Piece;
use Illuminate\Http\Request;

class PieceController extends Controller
{
    public function index(Request $request)
    {
        $query = Piece::query();

        if ($request->filled('recherche')) {
            $recherche = $request->input('recherche');
            $query->where(function ($q) use ($recherche) {
                $q->where('nom', 'like', "%{$recherche}%")
                  ->orWhere('reference', 'like', "%{$recherche}%");
            });
        }
        if ($request->boolean('all')) {
    return $query->get();
}

        return $query->paginate(10);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'reference' => 'required|string|max:255|unique:pieces,reference',
            'quantite_stock' => 'required|integer|min:0',
            'seuil_alerte' => 'required|integer|min:0',
            'prix_unitaire' => 'required|numeric|min:0',
        ]);

        $piece = Piece::create($request->all());
        return response()->json($piece, 201);
    }

    public function show($id)
    {
        $piece = Piece::findOrFail($id);
        return response()->json($piece);
    }

    public function update(Request $request, $id)
    {
        $piece = Piece::findOrFail($id);

        $request->validate([
            'nom' => 'required|string|max:255',
            'reference' => 'required|string|max:255|unique:pieces,reference,' . $piece->id,
            'quantite_stock' => 'required|integer|min:0',
            'seuil_alerte' => 'required|integer|min:0',
            'prix_unitaire' => 'required|numeric|min:0',
        ]);

        $piece->update($request->all());
        return response()->json($piece);
    }

    public function destroy($id)
    {
        Piece::destroy($id);
        return response()->json(null, 204);
    }
}
