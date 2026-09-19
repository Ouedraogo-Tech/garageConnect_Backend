<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Technicien extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'prenom',
        'specialite',
    ];

    // Un technicien participe à plusieurs réparations
    public function reparations()
    {
        return $this->belongsToMany(Reparation::class, 'reparation_technicien');
    }
}