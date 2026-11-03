<?php

namespace App\Models;

use App\Models\Concerns\BelongsToExploitation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Parcelle extends Model
{
    use HasFactory, BelongsToExploitation;

    protected $fillable = ['exploitation_id', 'code', 'nom', 'superficie_ha', 'localisation', 'variete', 'etat', 'date_creation'];

    protected $casts = ['superficie_ha' => 'decimal:2', 'date_creation' => 'date'];

    public function campagnes(): HasMany
    {
        return $this->hasMany(Campagne::class);
    }
}
