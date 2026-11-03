<?php

namespace App\Services;

use App\Events\LotVendu;
use App\Exceptions\StatutInvalideException;
use App\Models\Lot;
use App\Models\TransactionFinanciere;
use App\Models\User;
use App\Models\Vente;
use Illuminate\Support\Facades\DB;

class FinanceService
{
    /** Marge simplifiée = Recettes − Dépenses. */
    public function resume(int $exploitationId, ?int $campagneId = null): array
    {
        $base = TransactionFinanciere::pourExploitation($exploitationId)
            ->when($campagneId, fn ($q) => $q->where('campagne_id', $campagneId));

        $depenses = (float) (clone $base)->where('type', TransactionFinanciere::DEPENSE)->sum('montant');
        $recettes = (float) (clone $base)->where('type', TransactionFinanciere::RECETTE)->sum('montant');

        return [
            'depenses' => $depenses,
            'recettes' => $recettes,
            'marge' => round($recettes - $depenses, 2),
        ];
    }

    public function enregistrerDepense(int $exploitationId, string $categorie, float $montant, ?int $campagneId = null, ?string $libelle = null): TransactionFinanciere
    {
        return TransactionFinanciere::create([
            'exploitation_id' => $exploitationId,
            'campagne_id' => $campagneId,
            'type' => TransactionFinanciere::DEPENSE,
            'categorie' => $categorie,
            'libelle' => $libelle,
            'montant' => $montant,
            'date_transaction' => now()->toDateString(),
        ]);
    }

    /**
     * Vente d'un lot clôturé : vente + recette + changement de statut, en une seule transaction.
     */
    public function vendreLot(User $user, int $lotId, string $acheteur, float $montant, ?string $dateVente = null): Vente
    {
        $vente = DB::transaction(function () use ($user, $lotId, $acheteur, $montant, $dateVente) {
            $lot = Lot::pourExploitation($user->exploitation_id)->whereKey($lotId)->lockForUpdate()->firstOrFail();

            if ($lot->statut !== Lot::CLOTURE) {
                throw new StatutInvalideException('Seul un lot clôturé peut être vendu (statut actuel : '.$lot->statut.').');
            }

            $date = $dateVente ?: now()->toDateString();

            $vente = Vente::create([
                'exploitation_id' => $lot->exploitation_id,
                'lot_id' => $lot->id,
                'acheteur' => $acheteur,
                'quantite_kg' => $lot->poids_total_kg,
                'montant' => $montant,
                'date_vente' => $date,
            ]);

            TransactionFinanciere::create([
                'exploitation_id' => $lot->exploitation_id,
                'type' => TransactionFinanciere::RECETTE,
                'categorie' => 'vente_lot',
                'libelle' => 'Vente du lot '.$lot->code,
                'montant' => $montant,
                'date_transaction' => $date,
            ]);

            $lot->update(['statut' => Lot::VENDU]);

            return $vente;
        });

        event(new LotVendu($vente->lot()->withoutGlobalScopes()->first()));

        return $vente;
    }
}
