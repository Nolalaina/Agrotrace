<?php

namespace App\Services;

use App\Events\RecolteEnregistree;
use App\Exceptions\StatutInvalideException;
use App\Models\Campagne;
use App\Models\Recolte;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class HarvestService
{
    public function enregistrer(User $user, array $data): Recolte
    {
        $campagne = Campagne::pourExploitation($user->exploitation_id)->findOrFail($data['campagne_id']);

        if ($campagne->statut !== Campagne::EN_COURS) {
            throw new StatutInvalideException('La campagne « '.$campagne->libelle.' » n\'est pas en cours : récolte refusée.');
        }
        if ($campagne->date_debut->gt($data['date_recolte'])) {
            throw new StatutInvalideException('La date de récolte précède le début de la campagne.');
        }

        $recolte = DB::transaction(fn () => Recolte::create([
            'exploitation_id' => $user->exploitation_id,
            'campagne_id' => $campagne->id,
            'user_id' => $user->id,
            'date_recolte' => $data['date_recolte'],
            'quantite_kg' => $data['quantite_kg'],
            'quantite_disponible_kg' => $data['quantite_kg'], // tout est disponible pour un lot
            'calibre' => $data['calibre'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]));

        event(new RecolteEnregistree($recolte));

        return $recolte;
    }
}
