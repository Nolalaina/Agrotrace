<?php

namespace App\Http\Requests;

use App\Models\Parcelle;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreParcelleRequest extends FormRequest
{
    public function authorize(): bool
    {
        $parcelle = $this->route('parcelle');

        return $parcelle
            ? $this->user()->can('update', $parcelle)
            : $this->user()->can('create', Parcelle::class);
    }

    public function rules(): array
    {
        $id = $this->route('parcelle')?->id;

        return [
            'code' => ['required', 'string', 'max:30',
                Rule::unique('parcelles', 'code')
                    ->where('exploitation_id', $this->user()->exploitation_id)->ignore($id)],
            'nom' => ['required', 'string', 'max:255'],
            'superficie_ha' => ['required', 'numeric', 'gt:0', 'max:100000'],
            'localisation' => ['nullable', 'string', 'max:255'],
            'variete' => ['required', 'string', 'max:255'],
            'etat' => ['required', Rule::in(['productive', 'en_repos', 'en_replantation'])],
            'date_creation' => ['nullable', 'date', 'before_or_equal:today'],
        ];
    }
}
