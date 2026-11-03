<?php

namespace App\Models;

use App\Models\Concerns\BelongsToExploitation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Campagne extends Model
{
    use HasFactory, BelongsToExploitation;

    public const PLANIFIEE = 'planifiee';
    public const EN_COURS = 'en_cours';
    public const TERMINEE = 'terminee';
    public const STATUTS = [self::PLANIFIEE, self::EN_COURS, self::TERMINEE];

    protected $fillable = ['exploitation_id', 'parcelle_id', 'libelle', 'date_debut', 'date_fin_prevue', 'statut'];

    protected $casts = ['date_debut' => 'date', 'date_fin_prevue' => 'date'];

    public function parcelle(): BelongsTo
    {
        return $this->belongsTo(Parcelle::class);
    }

    public function activites(): HasMany
    {
        return $this->hasMany(Activite::class);
    }

    public function recoltes(): HasMany
    {
        return $this->hasMany(Recolte::class);
    }
}
