<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VendreLotRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('vendre', $this->route('lot'));
    }

    public function rules(): array
    {
        return [
            'acheteur' => ['required', 'string', 'max:255'],
            'montant' => ['required', 'numeric', 'gt:0', 'max:1000000000'],
            'date_vente' => ['nullable', 'date', 'before_or_equal:today'],
        ];
    }
}
