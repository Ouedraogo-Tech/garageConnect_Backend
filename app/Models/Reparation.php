<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reparation extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicule_id',
        'date',
        'duree_main_oeuvre',
        'objet_reparation',
    ];

    // Une réparation appartient à un véhicule
    public function vehicule()
    {
        return $this->belongsTo(Vehicule::class);
    }

    // Une réparation peut être réalisée par plusieurs techniciens
    public function techniciens()
    {
        return $this->belongsToMany(Technicien::class, 'reparation_technicien');
    }
}