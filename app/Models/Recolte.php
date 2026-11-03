<?php

namespace App\Models;

use App\Models\Concerns\BelongsToExploitation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Recolte extends Model
{
    use HasFactory, BelongsToExploitation;

    public const CALIBRES = ['petit', 'moyen', 'gros'];

    protected $fillable = ['exploitation_id', 'campagne_id', 'user_id', 'date_recolte', 'quantite_kg', 'quantite_disponible_kg', 'calibre', 'notes'];

    protected $casts = [
        'date_recolte' => 'date',
        'quantite_kg' => 'decimal:2',
        'quantite_disponible_kg' => 'decimal:2',
    ];

    public function campagne(): BelongsTo
    {
        return $this->belongsTo(Campagne::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lots(): BelongsToMany
    {
        return $this->belongsToMany(Lot::class, 'lot_recoltes')->withPivot('quantite_kg')->withTimestamps();
    }
}
