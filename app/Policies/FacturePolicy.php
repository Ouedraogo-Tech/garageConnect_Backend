<?php

namespace App\Policies;

use App\Models\Facture;
use App\Models\User;

class FacturePolicy
{
    public function view(User $user, Facture $facture): bool
    {
        if ($user->role !== 'client') {
            return true;
        }

        return $facture->reparation->vehicule->client_id === $user->client_id;
    }
}
