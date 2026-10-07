<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reparation extends Model
{
    use HasFactory;

   protected $fillable = [
    'vehicule_id',
    'rendez_vous_id',
    'date',
    'date_fin_prevue',
    'date_fin_reelle',
    'signalee_terminee_le',
    'duree_main_oeuvre',
    'objet_reparation',
];
    // Une réparation appartient à un véhicule
    public function vehicule()
    {
        return $this->belongsTo(Vehicule::class);
    }

    // Une réparation peut provenir d'un rendez-vous
    public function rendezVous()
    {
        return $this->belongsTo(RendezVous::class);
    }

    public function pieces()
    {
        return $this->belongsToMany(Piece::class, 'piece_reparation')
            ->withPivot('quantite_utilisee')
            ->withTimestamps();
    }

    // Une réparation peut être réalisée par plusieurs techniciens
    public function techniciens()
    {
        return $this->belongsToMany(Technicien::class, 'reparation_technicien');
    }
}
