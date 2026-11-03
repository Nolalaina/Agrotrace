<?php

namespace App\Http\Requests;

use App\Models\MouvementStock;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMouvementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('mouvement', $this->route('intrant'));
    }

    public function rules(): array
    {
        return [
            'type' => ['required', Rule::in(MouvementStock::TYPES)],
            'quantite' => ['required', 'numeric', 'max:1000000'],
            'campagne_id' => ['nullable', Rule::exists('campagnes', 'id')
                ->where('exploitation_id', $this->user()->exploitation_id)],
            'cout_total' => ['nullable', 'numeric', 'min:0'],
            'commentaire' => ['nullable', 'string', 'max:255'],
        ];
    }
}
