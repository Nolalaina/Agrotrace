@extends('layouts.app')
@section('titre', 'Composer un lot')
@section('contenu')
<div class="page-head">
    <div>
        <span class="eyebrow">Lots</span>
        <h1>Composer un lot</h1>
        <p class="lead">Saisissez la quantité à prélever sur chaque récolte. Laissez vide pour ne pas l'utiliser.</p>
    </div>
</div>

<form method="POST" action="{{ route('lots.store') }}">
    @csrf
    <div class="panel">
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Parcelle</th>
                        <th>Campagne</th>
                        <th>Calibre</th>
                        <th class="text-end">Disponible (kg)</th>
                        <th style="width:170px">À affecter (kg)</th>
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
                        <td>{{ $r->calibre }}</td>
                        <td class="text-end">{{ $r->quantite_disponible_kg }}</td>
                        <td>
                            <input type="number" step="0.01" min="0.01" max="{{ $r->quantite_disponible_kg }}"
                                   name="lignes[{{ $i }}][quantite_kg]" value="{{ old('lignes.'.$i.'.quantite_kg') }}"
                                   class="input input-sm" aria-label="Quantité à affecter">
                        </td>
                    </tr>
                @empty
                    <tr class="empty-row"><td colspan="6">Aucune récolte disponible.</td></tr>
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
