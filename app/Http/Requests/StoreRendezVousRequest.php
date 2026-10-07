<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRendezVousRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Réservé aux clients via le middleware role:client
        return true;
    }

    public function rules(): array
    {
        return [
            'vehicule_id' => 'required|exists:vehicules,id',
            'date_rdv' => 'required|date|after_or_equal:today',
            'heure_rdv' => 'required',
            'motif' => 'required|string|max:255',
        ];
    }
}
