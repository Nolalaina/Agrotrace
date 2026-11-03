<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LotResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'statut' => $this->statut,
            'qualite' => $this->qualite,
            'poids_total_kg' => (float) $this->poids_total_kg,
            'cloture_le' => $this->cloture_le?->toIso8601String(),
            'recoltes' => $this->whenLoaded('recoltes', fn () => $this->recoltes->map(fn ($r) => [
                'recolte_id' => $r->id,
                'date_recolte' => $r->date_recolte->toDateString(),
                'quantite_kg' => (float) $r->pivot->quantite_kg,
            ])),
        ];
    }
}
