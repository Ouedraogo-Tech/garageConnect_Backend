<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vehicule;

class VehiculePolicy
{
    public function view(User $user, Vehicule $vehicule): bool
    {
        return $user->role !== 'client' || $vehicule->client_id === $user->client_id;
    }

    public function update(User $user, Vehicule $vehicule): bool
    {
        return $this->view($user, $vehicule);
    }

    public function delete(User $user, Vehicule $vehicule): bool
    {
        return $this->view($user, $vehicule);
    }
}
