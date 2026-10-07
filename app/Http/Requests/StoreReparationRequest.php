<?php

namespace App\Http\Requests;

use App\Models\Reparation;
use Illuminate\Foundation\Http\FormRequest;

class UpdateReparationRequest extends FormRequest
{
    public function authorize(): bool
    {
        $reparation = Reparation::find($this->route('reparation'));

        return $reparation ? $this->user()->can('update', $reparation) : true;
    }

    public function rules(): array
    {
        return [
            'vehicule_id' => 'required|exists:vehicules,id',
            'date' => 'required|date',
            'date_fin_prevue' => 'nullable|date',
            'date_fin_reelle' => 'nullable|date',
            'duree_main_oeuvre' => 'required|numeric|min:0|max:24',
            'objet_reparation' => 'required|string|max:255',
            'techniciens' => 'nullable|array',
            'techniciens.*' => 'exists:techniciens,id',
            'pieces' => 'nullable|array',
            'pieces.*.piece_id' => 'exists:pieces,id',
            'pieces.*.quantite' => 'integer|min:1',
        ];
    }
}
