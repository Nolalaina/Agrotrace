<?php

namespace App\Policies;

use App\Models\Recolte;
use App\Models\User;

class RecoltePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Recolte $recolte): bool
    {
        return (int) $user->exploitation_id === (int) $recolte->exploitation_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Recolte $recolte): bool
    {
        return $user->peutGerer() && (int) $user->exploitation_id === (int) $recolte->exploitation_id;
    }
}
