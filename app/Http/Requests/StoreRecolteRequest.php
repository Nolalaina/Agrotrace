<?php

namespace App\Http\Requests;

use App\Models\Recolte;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/** Utilisée par le formulaire web ET par l'API REST. */
class StoreRecolteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Recolte::class);
    }

    public function rules(): array
    {
        return [
            'campagne_id' => ['required', 'integer', Rule::exists('campagnes', 'id')
                ->where('exploitation_id', $this->user()->exploitation_id)],
            'date_recolte' => ['required', 'date', 'before_or_equal:today'],
            'quantite_kg' => ['required', 'numeric', 'gt:0', 'max:1000000'],
            'calibre' => ['nullable', Rule::in(Recolte::CALIBRES)],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }
}
