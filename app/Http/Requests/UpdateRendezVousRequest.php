<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRendezVousRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Réservé à l'admin via le middleware role:admin
        return true;
    }

    public function rules(): array
    {
        return [
            'statut' => 'required|in:en_attente,confirme,refuse',
            'commentaire_admin' => 'nullable|string|max:500',
            'date_fin_prevue' => 'nullable|date',
        ];
    }
}
