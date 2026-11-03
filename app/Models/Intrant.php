<?php

namespace App\Models;

use App\Models\Concerns\BelongsToExploitation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Intrant extends Model
{
    use HasFactory, BelongsToExploitation;

    public const CATEGORIES = ['engrais', 'phytosanitaire', 'emballage', 'carburant', 'consommable'];

    protected $fillable = ['exploitation_id', 'nom', 'categorie', 'unite', 'quantite_disponible', 'seuil_minimum'];

    protected $casts = ['quantite_disponible' => 'decimal:2', 'seuil_minimum' => 'decimal:2'];

    public function mouvements(): HasMany
    {
        return $this->hasMany(MouvementStock::class);
    }

    public function estCritique(): bool
    {
        return (float) $this->quantite_disponible <= (float) $this->seuil_minimum;
    }
}
