<?php

namespace App\Models;

use App\Models\Concerns\BelongsToExploitation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionFinanciere extends Model
{
    use BelongsToExploitation;

    public const DEPENSE = 'depense';
    public const RECETTE = 'recette';
    public const CATEGORIES_DEPENSE = ['intrants', 'main_oeuvre', 'traitement', 'transport', 'energie', 'activite', 'autre'];

    protected $table = 'transactions_financieres';

    protected $fillable = ['exploitation_id', 'campagne_id', 'type', 'categorie', 'libelle', 'montant', 'date_transaction'];

    protected $casts = ['montant' => 'decimal:2', 'date_transaction' => 'date'];

    public function campagne(): BelongsTo
    {
        return $this->belongsTo(Campagne::class);
    }
}
