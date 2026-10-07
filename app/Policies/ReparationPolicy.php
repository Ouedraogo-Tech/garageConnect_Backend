<?php

namespace App\Policies;

use App\Models\Reparation;
use App\Models\User;

class ReparationPolicy
{
    public function update(User $user, Reparation $reparation): bool
    {
        if ($user->role === 'technicien') {
            return $reparation->techniciens()->where('techniciens.id', $user->technicien_id)->exists();
        }

        return $user->role === 'admin';
    }

    public function signalerTerminee(User $user, Reparation $reparation): bool
    {
        return $this->update($user, $reparation);
    }
}
