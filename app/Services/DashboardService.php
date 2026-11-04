<?php

namespace App\Services;

use App\Models\Activite;
use App\Models\Campagne;
use App\Models\Intrant;
use App\Models\Lot;
use App\Models\Parcelle;
use App\Models\Recolte;
use Illuminate\Support\Facades\Cache;

/**
 * Statistiques du tableau de bord, mises en cache par exploitation.
 * Stratégie d'invalidation : les événements métier (RecolteEnregistree, LotCree,
 * LotVendu, StockCritique) déclenchent le listener InvalidateDashboardCache ;
 * les activités et les mouvements de stock appellent oublier() directement.
 * TTL de sécurité : 10 minutes.
 */
class DashboardService
{
    public const TTL_MINUTES = 10;

    public function __construct(private FinanceService $finance)
    {
    }

    public static function cle(int $exploitationId): string
    {
        return "dashboard:exploitation:{$exploitationId}";
    }

    public function oublier(int $exploitationId): void
    {
        Cache::forget(self::cle($exploitationId));
    }

    public function stats(int $exploitationId): array
    {
        return Cache::remember(
            self::cle($exploitationId),
            now()->addMinutes(self::TTL_MINUTES),
            fn () => $this->calculer($exploitationId)
        );
    }

    private function calculer(int $id): array
    {
        $superficie = (float) Parcelle::pourExploitation($id)->sum('superficie_ha');
        $recolteTotale = (float) Recolte::pourExploitation($id)->sum('quantite_kg');
        $nonAffectee = (float) Recolte::pourExploitation($id)->sum('quantite_disponible_kg');
        $poidsLots = (float) Lot::pourExploitation($id)->sum('poids_total_kg');
        $finance = $this->finance->resume($id);

        $anciennes = Recolte::pourExploitation($id)
            ->where('quantite_disponible_kg', '>', 0)
            ->whereDate('date_recolte', '<=', now()->subDays(3))
            ->count();

        return [
            'campagnes_actives' => Campagne::pourExploitation($id)->where('statut', Campagne::EN_COURS)->count(),
            'campagne_principale' => Campagne::pourExploitation($id)->where('statut', Campagne::EN_COURS)->orderBy('id')->value('libelle'),
            'nb_parcelles' => Parcelle::pourExploitation($id)->count(),
            'superficie_ha' => $superficie,
            'recolte_totale_kg' => $recolteTotale,
            'recolte_non_affectee_kg' => $nonAffectee,
            'poids_lots_kg' => $poidsLots,
            'stocks' => Intrant::pourExploitation($id)->orderBy('nom')
                ->get(['id', 'nom', 'unite', 'quantite_disponible', 'seuil_minimum'])->toArray(),
            'stocks_critiques' => Intrant::pourExploitation($id)
                ->whereColumn('quantite_disponible', '<=', 'seuil_minimum')
                ->get(['id', 'nom', 'unite', 'quantite_disponible', 'seuil_minimum'])->toArray(),
            'recoltes_non_affectees_anciennes' => $anciennes,
            'finance' => $finance,

            // Indicateurs calculés propres au projet
            'rendement_kg_ha' => $superficie > 0 ? round($recolteTotale / $superficie, 1) : 0,
            'taux_perte_potentielle' => $recolteTotale > 0 ? round($nonAffectee / $recolteTotale * 100, 1) : 0,
            'cout_production_kg' => $poidsLots > 0 ? round($finance['depenses'] / $poidsLots, 2) : null,

            'activites_recentes' => Activite::pourExploitation($id)
                ->with(['campagne:id,libelle', 'user:id,name'])
                ->orderByDesc('date')->orderByDesc('id')->limit(5)->get()->toArray(),
        ];
    }
}
