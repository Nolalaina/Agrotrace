<?php

namespace App\Policies;

use App\Models\Lot;
use App\Models\User;

class LotPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Lot $lot): bool
    {
        return (int) $user->exploitation_id === (int) $lot->exploitation_id;
    }

    /** Composer un lot : réservé au responsable / admin. */
    public function create(User $user): bool
    {
        return $user->peutGerer();
    }

    public function cloturer(User $user, Lot $lot): bool
    {
        return $user->peutGerer() && (int) $user->exploitation_id === (int) $lot->exploitation_id && $lot->estOuvert();
    }

    public function vendre(User $user, Lot $lot): bool
    {
        return $user->peutGerer() && (int) $user->exploitation_id === (int) $lot->exploitation_id && $lot->statut === Lot::CLOTURE;
    }

    public function genererFiche(User $user, Lot $lot): bool
    {
        return (int) $user->exploitation_id === (int) $lot->exploitation_id && $lot->statut !== Lot::OUVERT;
    }
}
