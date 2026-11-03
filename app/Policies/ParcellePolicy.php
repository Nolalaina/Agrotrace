<?php

namespace App\Policies;

use App\Models\Parcelle;
use App\Models\User;

class ParcellePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Parcelle $parcelle): bool
    {
        return (int) $user->exploitation_id === (int) $parcelle->exploitation_id;
    }

    public function create(User $user): bool
    {
        return $user->peutGerer();
    }

    public function update(User $user, Parcelle $parcelle): bool
    {
        return $user->peutGerer() && (int) $user->exploitation_id === (int) $parcelle->exploitation_id;
    }

    public function delete(User $user, Parcelle $parcelle): bool
    {
        return $this->update($user, $parcelle);
    }
}
