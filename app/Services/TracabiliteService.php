<?php

namespace App\Services;

use App\Models\Campagne;
use App\Models\Lot;
use App\Models\Parcelle;
use App\Models\Recolte;

/**
 * Traçabilité dans les deux sens :
 *  - remontante  : lot → récoltes → campagnes → parcelles
 *  - descendante : parcelle → récoltes → lots → vente
 * Les requêtes ignorent le scope utilisateur : la visibilité est décidée par l'appelant
 * (page publique = lots clôturés/vendus uniquement, sans données financières).
 */
class TracabiliteService
{
    public function origineDuLot(string $code): ?array
    {
        $lot = Lot::withoutGlobalScopes()
            ->where('code', $code)
            ->whereIn('statut', [Lot::CLOTURE, Lot::VENDU])
            ->with(['recoltes' => fn ($q) => $q->withoutGlobalScopes()->with([
                'campagne' => fn ($c) => $c->withoutGlobalScopes()->with([
                    'parcelle' => fn ($p) => $p->withoutGlobalScopes(),
                ]),
            ])])
            ->first();

        if (! $lot) {
            return null;
        }

        return [
            'code' => $lot->code,
            'statut' => $lot->statut,
            'poids_total_kg' => (float) $lot->poids_total_kg,
            'qualite' => $lot->qualite,
            'cloture_le' => $lot->cloture_le?->toDateString(),
            'origine' => $lot->recoltes->map(fn ($r) => [
                'date_recolte' => $r->date_recolte->toDateString(),
                'quantite_kg' => (float) $r->pivot->quantite_kg,
                'calibre' => $r->calibre,
                'campagne' => $r->campagne?->libelle,
                'parcelle' => $r->campagne?->parcelle ? [
                    'code' => $r->campagne->parcelle->code,
                    'nom' => $r->campagne->parcelle->nom,
                    'variete' => $r->campagne->parcelle->variete,
                    'localisation' => $r->campagne->parcelle->localisation,
                ] : null,
            ])->values()->all(),
        ];
    }

    /** Parcelle → lots produits (usage interne, scope utilisateur actif). */
    public function lotsDeLaParcelle(Parcelle $parcelle)
    {
        $campagneIds = Campagne::where('parcelle_id', $parcelle->id)->pluck('id');
        $recolteIds = Recolte::whereIn('campagne_id', $campagneIds)->pluck('id');

        return Lot::whereHas('recoltes', fn ($q) => $q->whereIn('recoltes.id', $recolteIds))
            ->with('vente')->orderByDesc('created_at')->get();
    }
}
