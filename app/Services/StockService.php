<?php

namespace App\Services;

use App\Events\StockCritique;
use App\Exceptions\QuantiteInsuffisanteException;
use App\Exceptions\RegleMetierException;
use App\Exceptions\StatutInvalideException;
use App\Models\Campagne;
use App\Models\Intrant;
use App\Models\MouvementStock;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Règle : le stock d'un intrant ne devient JAMAIS négatif.
 *
 * Mécanisme : transaction + verrou pessimiste (SELECT ... FOR UPDATE) sur la ligne
 * de l'intrant. Deux utilisateurs qui consomment en même temps les derniers sacs
 * sont sérialisés : le second relit le stock déjà diminué et reçoit un refus.
 * Une contrainte CHECK en base (MySQL/PostgreSQL) sert de filet de sécurité.
 */
class StockService
{
    public function __construct(private FinanceService $finance, private DashboardService $dashboard)
    {
    }

    public function enregistrer(
        User $user,
        int $intrantId,
        string $type,
        float $quantite,
        ?int $campagneId = null,
        ?float $coutTotal = null,
        ?string $commentaire = null,
    ): MouvementStock {
        if (! in_array($type, MouvementStock::TYPES, true)) {
            throw new RegleMetierException("Type de mouvement inconnu : {$type}.");
        }
        if ($quantite == 0.0 || ($type !== MouvementStock::AJUSTEMENT && $quantite < 0)) {
            throw new RegleMetierException('La quantité doit être strictement positive.');
        }
        if ($type === MouvementStock::CONSOMMATION && ! $campagneId) {
            throw new StatutInvalideException('Une consommation doit être rattachée à une campagne.');
        }

        [$mouvement, $intrant] = DB::transaction(function () use ($user, $intrantId, $type, $quantite, $campagneId, $coutTotal, $commentaire) {
            $intrant = Intrant::pourExploitation($user->exploitation_id)
                ->whereKey($intrantId)->lockForUpdate()->firstOrFail();

            if ($campagneId) {
                Campagne::pourExploitation($user->exploitation_id)->findOrFail($campagneId);
            }

            $delta = match ($type) {
                MouvementStock::STOCK_INITIAL, MouvementStock::ENTREE => $quantite,
                MouvementStock::SORTIE, MouvementStock::CONSOMMATION => -$quantite,
                default => $quantite, // ajustement : signé
            };

            $nouveau = round((float) $intrant->quantite_disponible + $delta, 2);

            if ($nouveau < 0) {
                throw new QuantiteInsuffisanteException(sprintf(
                    'Stock insuffisant pour « %s » : %s %s disponibles, %s demandés.',
                    $intrant->nom, $intrant->quantite_disponible, $intrant->unite, $quantite
                ));
            }

            $intrant->update(['quantite_disponible' => $nouveau]);

            $mouvement = MouvementStock::create([
                'exploitation_id' => $intrant->exploitation_id,
                'intrant_id' => $intrant->id,
                'campagne_id' => $campagneId,
                'user_id' => $user->id,
                'type' => $type,
                'quantite' => $quantite,
                'stock_apres' => $nouveau,
                'commentaire' => $commentaire,
            ]);

            if ($type === MouvementStock::ENTREE && $coutTotal && $coutTotal > 0) {
                $this->finance->enregistrerDepense(
                    $intrant->exploitation_id, 'intrants', $coutTotal, null, 'Achat : '.$intrant->nom
                );
            }

            return [$mouvement, $intrant];
        });

        $this->dashboard->oublier($intrant->exploitation_id);

        if ($intrant->estCritique()) {
            event(new StockCritique($intrant));
        }

        return $mouvement;
    }
}
