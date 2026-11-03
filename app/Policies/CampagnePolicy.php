<?php

namespace App\Policies;

use App\Models\Campagne;
use App\Models\User;

class CampagnePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->peutGerer();
    }

    public function update(User $user, Campagne $campagne): bool
    {
        return $user->peutGerer() && (int) $user->exploitation_id === (int) $campagne->exploitation_id;
    }

    public function delete(User $user, Campagne $campagne): bool
    {
        return $this->update($user, $campagne);
    }
}
