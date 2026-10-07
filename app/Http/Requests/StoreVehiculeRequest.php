<?php

namespace App\Http\Requests;

use App\Models\Vehicule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateVehiculeRequest extends FormRequest
{
    public function authorize(): bool
    {
        $vehicule = Vehicule::find($this->route('vehicule'));

        // Si le véhicule n'existe pas, on laisse passer : le contrôleur
        // renverra un 404 propre via findOrFail plutôt qu'un 403 trompeur.
        return $vehicule ? $this->user()->can('update', $vehicule) : true;
    }

    public function rules(): array
    {
        $vehiculeId = $this->route('vehicule');

        return [
            'immatriculation' => ['required', 'string', Rule::unique('vehicules', 'immatriculation')->ignore($vehiculeId)],
            'marque' => 'required|string|max:255',
            'modele' => 'required|string|max:255',
            'couleur' => 'required|string|max:255',
            'annee' => 'required|integer|min:1950|max:' . date('Y'),
            'kilometrage' => 'required|integer|min:0',
            'carrosserie' => 'required|string|max:255',
            'energie' => 'required|string|max:255',
            'boite' => 'required|string|max:255',
            'email_proprietaire' => 'nullable|email|max:255',
            'client_id' => 'nullable|exists:clients,id',
        ];
    }
}
