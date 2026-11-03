<?php

namespace App\Models;

use App\Models\Concerns\BelongsToExploitation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vente extends Model
{
    use BelongsToExploitation;

    protected $fillable = ['exploitation_id', 'lot_id', 'acheteur', 'quantite_kg', 'montant', 'date_vente'];

    protected $casts = ['quantite_kg' => 'decimal:2', 'montant' => 'decimal:2', 'date_vente' => 'date'];

    public function lot(): BelongsTo
    {
        return $this->belongsTo(Lot::class);
    }
}
