<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFactureRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Réservé à l'admin via le middleware role:admin
        return true;
    }

    public function rules(): array
    {
        return [
            'reparation_id' => 'required|exists:reparations,id|unique:factures,reparation_id',
        ];
    }
}
