<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RendezVous extends Model
{
    use HasFactory;

    protected $table = 'rendez_vous';

    protected $fillable = [
        'client_id', 'vehicule_id', 'date_rdv', 'heure_rdv',
        'motif', 'statut', 'commentaire_admin',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function vehicule()
    {
        return $this->belongsTo(Vehicule::class);
    }

    public function reparation()
    {
        return $this->hasOne(Reparation::class);
    }
}
