<?php

namespace App\Policies;

use App\Models\Intrant;
use App\Models\User;

class IntrantPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    /** Créer un intrant : responsable / admin. */
    public function create(User $user): bool
    {
        return $user->peutGerer();
    }

    /** Saisir un mouvement : tout le monde, mais uniquement dans son exploitation. */
    public function mouvement(User $user, Intrant $intrant): bool
    {
        return (int) $user->exploitation_id === (int) $intrant->exploitation_id;
    }
}
