<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** Enveloppe le tableau produit par TracabiliteService::origineDuLot(). */
class TracabiliteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return $this->resource;
    }
}
