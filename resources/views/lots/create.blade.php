@extends('layouts.app')
@section('titre', 'Composer un lot')
@section('contenu')
<div class="page-head">
    <div>
        <h1>Composer un lot</h1>
        <p class="lead">Saisissez la quantité à prélever sur chaque récolte. Laissez vide pour ne pas l'utiliser.</p>
    </div>
</div>

<form method="POST" action="{{ route('lots.store') }}" data-lot-form>
    @csrf
    <div class="panel">
        <div class="panel-head">
            <div>
                <h3>Sélection des récoltes</h3>
                <p class="hint">Poids total sélectionné : <strong id="lot-total-poids">0,00</strong> kg (<span id="lot-count">0</span> récolte(s))</p>
            </div>
            @if ($recoltes->isNotEmpty())
                <div class="flex gap-2">
                    <button type="button" class="btn btn-sm btn-secondary" data-lot-fill-all>Tout remplir au max</button>
                    <button type="button" class="btn btn-sm btn-link" data-lot-clear-all>Tout vider</button>
                </div>
            @endif
        </div>
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Parcelle</th>
                        <th>Campagne</th>
                        <th>Calibre</th>
                        <th class="text-end">Disponible (kg)</th>
                        <th style="width:200px">À affecter (kg)</th>
                    </tr>
                </thead>
                <tbody>
                @forelse ($recoltes as $i => $r)
                    <tr>
                        <td class="nowrap">
                            {{ $r->date_recolte->format('d/m/Y') }}
                            <input type="hidden" name="lignes[{{ $i }}][recolte_id]" value="{{ $r->id }}">
                        </td>
                        <td>{{ $r->campagne?->parcelle?->code }}</td>
                        <td>{{ $r->campagne?->libelle }}</td>
                        <td>{{ ucfirst($r->calibre ?? '—') }}</td>
                        <td class="text-end">{{ $r->quantite_disponible_kg }}</td>
                        <td>
                            <div style="display:flex;gap:6px;align-items:center;">
                                <input type="number" step="0.01" min="0.01" max="{{ $r->quantite_disponible_kg }}"
                                       name="lignes[{{ $i }}][quantite_kg]" value="{{ old('lignes.'.$i.'.quantite_kg') }}"
                                       class="input input-sm lot-qty-input" aria-label="Quantité à affecter" data-dispo="{{ $r->quantite_disponible_kg }}">
                                <button type="button" class="btn btn-sm btn-secondary" data-lot-max title="Affecter le maximum disponible">Max</button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr class="empty-row">
                        <td colspan="6">
                            Aucune récolte disponible.
                            @can('create', App\Models\Recolte::class)
                                <a href="{{ route('recoltes.create') }}" class="btn btn-sm btn-primary ml-2">Enregistrer une récolte</a>
                            @endcan
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="panel">
        <div class="panel-body">
            <div class="form-grid">
                <div class="field span-3">
                    <label class="label" for="qualite">Qualité</label>
                    <select id="qualite" name="qualite" class="select">
                        <option value="">—</option>
                        @foreach (['extra', 'premiere', 'standard'] as $q)
                            <option value="{{ $q }}" @selected(old('qualite') === $q)>{{ ucfirst($q) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary" @disabled($recoltes->isEmpty())>Créer le lot</button>
                <a href="{{ route('lots.index') }}" class="btn btn-link">Annuler</a>
            </div>
        </div>
    </div>
</form>
@endsection
