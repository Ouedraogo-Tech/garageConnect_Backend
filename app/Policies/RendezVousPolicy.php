<?php

namespace App\Policies;

use App\Models\RendezVous;
use App\Models\User;

class RendezVousPolicy
{
    public function view(User $user, RendezVous $rendezVous): bool
    {
        return $user->role !== 'client' || $rendezVous->client_id === $user->client_id;
    }

    public function cancel(User $user, RendezVous $rendezVous): bool
    {
        if ($user->role !== 'client') {
            return true; // l'admin peut toujours supprimer
        }

        return $rendezVous->client_id === $user->client_id && $rendezVous->statut === 'en_attente';
    }
}
