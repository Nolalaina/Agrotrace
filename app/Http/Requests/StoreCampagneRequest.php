<?php

namespace App\Http\Requests;

use App\Models\Campagne;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreCampagneRequest extends FormRequest
{
    public function authorize(): bool
    {
        $campagne = $this->route('campagne');

        return $campagne
            ? $this->user()->can('update', $campagne)
            : $this->user()->can('create', Campagne::class);
    }

    public function rules(): array
    {
        return [
            'parcelle_id' => ['required', Rule::exists('parcelles', 'id')
                ->where('exploitation_id', $this->user()->exploitation_id)],
            'libelle' => ['required', 'string', 'max:255'],
            'date_debut' => ['required', 'date'],
            'date_fin_prevue' => ['required', 'date', 'after:date_debut'],
            'statut' => ['required', Rule::in(Campagne::STATUTS)],
        ];
    }

    /** Règle métier : une seule campagne « en cours » par parcelle. */
    public function after(): array
    {
        return [function (Validator $validator) {
            if ($this->input('statut') !== Campagne::EN_COURS || $validator->errors()->isNotEmpty()) {
                return;
            }

            $existe = Campagne::pourExploitation($this->user()->exploitation_id)
                ->where('parcelle_id', $this->input('parcelle_id'))
                ->where('statut', Campagne::EN_COURS)
                ->when($this->route('campagne'), fn ($q, $c) => $q->where('id', '!=', $c->id))
                ->exists();

            if ($existe) {
                $validator->errors()->add('parcelle_id', 'Cette parcelle a déjà une campagne en cours.');
            }
        }];
    }
}
