<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RecolteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'campagne_id' => $this->campagne_id,
            'date_recolte' => $this->date_recolte->toDateString(),
            'quantite_kg' => (float) $this->quantite_kg,
            'quantite_disponible_kg' => (float) $this->quantite_disponible_kg,
            'calibre' => $this->calibre,
            'notes' => $this->notes,
        ];
    }
}
