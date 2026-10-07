<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Facture extends Model
{
    protected $fillable = [
        'reparation_id', 'numero', 'montant_main_oeuvre',
        'montant_pieces', 'montant_total', 'statut', 'date_emission',
    ];

    public function reparation()
    {
        return $this->belongsTo(Reparation::class);
    }
}
