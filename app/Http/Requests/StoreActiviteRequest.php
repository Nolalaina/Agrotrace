<?php

namespace App\Http\Requests;

use App\Models\Activite;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreActiviteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Activite::class);
    }

    public function rules(): array
    {
        return [
            'campagne_id' => ['required', Rule::exists('campagnes', 'id')
                ->where('exploitation_id', $this->user()->exploitation_id)],
            'type' => ['required', Rule::in(Activite::TYPES)],
            'date' => ['required', 'date', 'before_or_equal:today'],
            'description' => ['nullable', 'string', 'max:1000'],
            'cout' => ['nullable', 'numeric', 'min:0', 'max:1000000000'],
        ];
    }
}
