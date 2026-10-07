<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Liste tous les comptes utilisateurs visible uniquement par l'admin.
     */
    public function index()
    {
        return User::with('technicien')->get(['id', 'name', 'email', 'role', 'technicien_id']);
    }

    /**
     * Crée un nouveau compte (admin uniquement).
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,technicien',
            'technicien_id' => 'required_if:role,technicien|nullable|exists:techniciens,id',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'technicien_id' => $request->role === 'technicien' ? $request->technicien_id : null,
        ]);

        return response()->json($user, 201);
    }

    /**
     * Supprime un compte (admin uniquement).
     * Un admin ne peut pas supprimer son propre compte, pour éviter
     * de se retrouver bloqué hors de l'application par erreur.
     */
    public function destroy(Request $request, $id)
    {
        if ((int) $id === $request->user()->id) {
            return response()->json(['message' => 'Vous ne pouvez pas supprimer votre propre compte.'], 403);
        }

        User::destroy($id);
        return response()->json(null, 204);
    }
}
