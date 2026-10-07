<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Piece extends Model
{
    protected $fillable = ['nom', 'reference', 'quantite_stock', 'seuil_alerte', 'prix_unitaire'];

    protected $appends = ['en_alerte'];

    public function reparations()
    {
        return $this->belongsToMany(Reparation::class, 'piece_reparation')
            ->withPivot('quantite_utilisee')
            ->withTimestamps();
    }

    public function getEnAlerteAttribute(): bool
    {
        return $this->quantite_stock <= $this->seuil_alerte;
    }
}
