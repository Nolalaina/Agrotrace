<?php

namespace App\Policies;

use App\Models\User;

class ActivitePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    /** Tous les profils peuvent enregistrer une activité (technicien inclus). */
    public function create(User $user): bool
    {
        return true;
    }
}
