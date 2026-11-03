<?php

namespace App\Models;

use App\Models\Concerns\BelongsToExploitation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MouvementStock extends Model
{
    use BelongsToExploitation;

    public const STOCK_INITIAL = 'stock_initial';
    public const ENTREE = 'entree';
    public const SORTIE = 'sortie';
    public const CONSOMMATION = 'consommation';
    public const AJUSTEMENT = 'ajustement';
    public const TYPES = [self::STOCK_INITIAL, self::ENTREE, self::SORTIE, self::CONSOMMATION, self::AJUSTEMENT];

    protected $table = 'mouvements_stock';

    protected $fillable = ['exploitation_id', 'intrant_id', 'campagne_id', 'user_id', 'type', 'quantite', 'stock_apres', 'commentaire'];

    protected $casts = ['quantite' => 'decimal:2', 'stock_apres' => 'decimal:2'];

    public function intrant(): BelongsTo
    {
        return $this->belongsTo(Intrant::class);
    }

    public function campagne(): BelongsTo
    {
        return $this->belongsTo(Campagne::class);
    }
}
