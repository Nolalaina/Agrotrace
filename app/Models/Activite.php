<?php

namespace App\Models;

use App\Models\Concerns\BelongsToExploitation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Activite extends Model
{
    use HasFactory, BelongsToExploitation;

    public const TYPES = ['taille', 'fertilisation', 'traitement', 'irrigation', 'desherbage', 'floraison', 'recolte', 'entretien'];

    protected $fillable = ['exploitation_id', 'campagne_id', 'user_id', 'type', 'date', 'description', 'cout'];

    protected $casts = ['date' => 'date', 'cout' => 'decimal:2'];

    public function campagne(): BelongsTo
    {
        return $this->belongsTo(Campagne::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
