<?php

namespace App\Http\Requests;

use App\Models\Intrant;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreIntrantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Intrant::class);
    }

    public function rules(): array
    {
        return [
            'nom' => ['required', 'string', 'max:255',
                Rule::unique('intrants', 'nom')->where('exploitation_id', $this->user()->exploitation_id)],
            'categorie' => ['required', Rule::in(Intrant::CATEGORIES)],
            'unite' => ['required', 'string', 'max:20'],
            'seuil_minimum' => ['required', 'numeric', 'min:0'],
            'stock_initial' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
