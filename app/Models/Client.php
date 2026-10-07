<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = ['nom', 'prenom', 'telephone', 'adresse'];

    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function vehicules()
    {
        return $this->hasMany(Vehicule::class);
    }

    public function rendezVous()
    {
        return $this->hasMany(RendezVous::class);
    }
}
