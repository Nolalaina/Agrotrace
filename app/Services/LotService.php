<?php

namespace App\Services;

use App\Events\LotCree;
use App\Exceptions\QuantiteInsuffisanteException;
use App\Exceptions\RegleMetierException;
use App\Exceptions\StatutInvalideException;
use App\Models\Lot;
use App\Models\LotRecolte;
use App\Models\Recolte;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Cœur métier : composition d'un lot à partir de récoltes (traçabilité).
 *
 * Règle d'intégrité : le poids affecté à des lots ne dépasse jamais la quantité
 * récoltée (pas de « poids fantôme »).
 *
 * Transaction : création du lot + lignes du pivot + décrément des récoltes sont
 * atomiques ; la moindre erreur annule tout (rollback).
 *
 * Concurrence : les récoltes concernées sont verrouillées (FOR UPDATE) et le
 * décrément est conditionnel (WHERE quantite_disponible_kg >= :q). Deux agents
 * qui puisent dans la même récolte ne peuvent pas la dépasser.
 */
class LotService
{
    /**
     * @param  array<int, array{recolte_id:int, quantite_kg:float|string}>  $lignes
     */
    public function composer(User $user, array $lignes, ?string $qualite = null): Lot
    {
        if (count($lignes) === 0) {
            throw new RegleMetierException('Un lot doit contenir au moins une récolte.');
        }

        $lot = DB::transaction(function () use ($user, $lignes, $qualite) {
            $ids = collect($lignes)->pluck('recolte_id')->map(fn ($v) => (int) $v);

            if ($ids->count() !== $ids->unique()->count()) {
                throw new RegleMetierException('Une récolte ne peut apparaître qu\'une seule fois dans un lot.');
            }

            // Verrou pessimiste + filtre explicite sur l'exploitation de l'utilisateur.
            $recoltes = Recolte::pourExploitation($user->exploitation_id)
                ->whereIn('id', $ids)->lockForUpdate()->get()->keyBy('id');

            if ($recoltes->count() !== $ids->count()) {
                throw new RegleMetierException('Une des récoltes sélectionnées est introuvable pour cette exploitation.');
            }

            $lot = Lot::create([
                'exploitation_id' => $user->exploitation_id,
                'user_id' => $user->id,
                'code' => $this->genererCode($user->exploitation_id),
                'statut' => Lot::OUVERT,
                'qualite' => $qualite,
                'poids_total_kg' => 0,
            ]);

            $total = 0.0;

            foreach ($lignes as $ligne) {
                $quantite = round((float) $ligne['quantite_kg'], 2);

                if ($quantite <= 0) {
                    throw new RegleMetierException('Chaque quantité affectée doit être strictement positive.');
                }

                $affectees = Recolte::pourExploitation($user->exploitation_id)
                    ->whereKey($ligne['recolte_id'])
                    ->where('quantite_disponible_kg', '>=', $quantite)
                    ->decrement('quantite_disponible_kg', $quantite);

                if ($affectees === 0) {
                    $r = $recoltes[(int) $ligne['recolte_id']];
                    throw new QuantiteInsuffisanteException(sprintf(
                        'Récolte du %s : %s kg disponibles, %s kg demandés.',
                        $r->date_recolte->format('d/m/Y'), $r->quantite_disponible_kg, $quantite
                    ));
                }

                LotRecolte::create([
                    'lot_id' => $lot->id,
                    'recolte_id' => (int) $ligne['recolte_id'],
                    'quantite_kg' => $quantite,
                ]);

                $total += $quantite;
            }

            $lot->update(['poids_total_kg' => round($total, 2)]);

            return $lot;
        });

        event(new LotCree($lot));

        return $lot;
    }

    /** Clôture : le lot n'est plus modifiable et devient vendable. */
    public function cloturer(User $user, int $lotId): Lot
    {
        return DB::transaction(function () use ($user, $lotId) {
            $lot = Lot::pourExploitation($user->exploitation_id)->whereKey($lotId)->lockForUpdate()->firstOrFail();

            if ($lot->statut !== Lot::OUVERT) {
                throw new StatutInvalideException('Seul un lot ouvert peut être clôturé (statut actuel : '.$lot->statut.').');
            }
            if ((float) $lot->poids_total_kg <= 0) {
                throw new StatutInvalideException('Impossible de clôturer un lot vide.');
            }

            $lot->update(['statut' => Lot::CLOTURE, 'cloture_le' => now()]);

            return $lot;
        });
    }

    private function genererCode(int $exploitationId): string
    {
        return sprintf('LOT-%d-%s-%s', $exploitationId, now()->format('ymd'), Str::upper(Str::random(4)));
    }
}
