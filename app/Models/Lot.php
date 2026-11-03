<?php

namespace App\Models;

use App\Models\Concerns\BelongsToExploitation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Lot extends Model
{
    use HasFactory, BelongsToExploitation;

    public const OUVERT = 'ouvert';
    public const CLOTURE = 'cloture';
    public const VENDU = 'vendu';

    protected $fillable = ['exploitation_id', 'user_id', 'code', 'poids_total_kg', 'statut', 'qualite', 'cloture_le'];

    protected $casts = ['poids_total_kg' => 'decimal:2', 'cloture_le' => 'datetime'];

    public function lotRecoltes(): HasMany
    {
        return $this->hasMany(LotRecolte::class);
    }

    public function recoltes(): BelongsToMany
    {
        return $this->belongsToMany(Recolte::class, 'lot_recoltes')->withPivot('quantite_kg')->withTimestamps();
    }

    public function vente(): HasOne
    {
        return $this->hasOne(Vente::class);
    }

    public function estOuvert(): bool
    {
        return $this->statut === self::OUVERT;
    }

    /** Un lot clôturé ou vendu n'est plus modifiable. */
    public function estModifiable(): bool
    {
        return $this->estOuvert();
    }
}
