<?php

namespace App\Http\Requests;

use App\Models\Lot;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ComposerLotRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Lot::class);
    }

    public function rules(): array
    {
        return [
            'qualite' => ['nullable', Rule::in(['extra', 'premiere', 'standard'])],
            'lignes' => ['required', 'array', 'min:1'],
            'lignes.*.recolte_id' => ['required', 'integer', 'distinct', Rule::exists('recoltes', 'id')
                ->where('exploitation_id', $this->user()->exploitation_id)],
            'lignes.*.quantite_kg' => ['required', 'numeric', 'gt:0', 'max:1000000'],
        ];
    }

    /** Ne conserve que les lignes dont une quantité a été saisie. */
    protected function prepareForValidation(): void
    {
        $lignes = collect($this->input('lignes', []))
            ->filter(fn ($l) => isset($l['quantite_kg']) && $l['quantite_kg'] !== '' && $l['quantite_kg'] !== null)
            ->values()->all();

        $this->merge(['lignes' => $lignes]);
    }
}
