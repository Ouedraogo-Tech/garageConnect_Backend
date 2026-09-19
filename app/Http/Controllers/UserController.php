<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

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
            'password' => bcrypt($request->password),
            'role' => $request->role,
            'technicien_id' => $request->role === 'technicien' ? $request->technicien_id : null,
        ]);

        return response()->json($user, 201);
    }

    /**
     * Supprime un compte (admin uniquement).
     */
    public function destroy($id)
    {
        User::destroy($id);
        return response()->json(null, 204);
    }
}
