<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LotRecolte extends Model
{
    protected $table = 'lot_recoltes';

    protected $fillable = ['lot_id', 'recolte_id', 'quantite_kg'];

    protected $casts = ['quantite_kg' => 'decimal:2'];

    public function lot(): BelongsTo
    {
        return $this->belongsTo(Lot::class);
    }

    public function recolte(): BelongsTo
    {
        return $this->belongsTo(Recolte::class);
    }
}
