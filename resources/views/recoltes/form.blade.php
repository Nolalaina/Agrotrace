@extends('layouts.app')
@section('titre', 'Nouvelle récolte')
@section('contenu')
<div class="page-head">
    <div>
        <h1>Enregistrer une récolte</h1>
    </div>
</div>

<form method="POST" action="{{ route('recoltes.store') }}" class="panel">
    @csrf
    <div class="panel-body">
        @if ($campagnes->isEmpty())
            <div class="alert alert-warning">
                <x-icon name="info" />
                <div class="alert-body">
                    Aucune campagne en cours : une récolte doit être rattachée à une campagne active.
                    @can('create', App\Models\Campagne::class)
                        <a href="{{ route('campagnes.create') }}" class="btn btn-sm btn-secondary ml-2">Créer une campagne</a>
                    @endcan
                </div>
            </div>
        @endif

        <div class="form-grid">
            <div class="field span-6">
                <label class="label" for="campagne_id">Campagne (en cours)</label>
                <select id="campagne_id" name="campagne_id" class="select" required>
                    @foreach ($campagnes as $c)
                        <option value="{{ $c->id }}" @selected(old('campagne_id') == $c->id)>{{ $c->libelle }} — {{ $c->parcelle?->code }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field span-3">
                <label class="label" for="date_recolte">Date</label>
                <input id="date_recolte" type="date" name="date_recolte" class="input" value="{{ old('date_recolte', now()->toDateString()) }}" required>
            </div>
            <div class="field span-3">
                <label class="label" for="quantite_kg">Quantité (kg)</label>
                <input id="quantite_kg" type="number" step="0.01" min="0.01" name="quantite_kg" class="input" value="{{ old('quantite_kg') }}" required>
            </div>
            <div class="field span-3">
                <label class="label" for="calibre">Calibre</label>
                <select id="calibre" name="calibre" class="select">
                    <option value="">—</option>
                    @foreach ($calibres as $c)
                        <option value="{{ $c }}" @selected(old('calibre') === $c)>{{ $c }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field span-9">
                <label class="label" for="notes">Notes</label>
                <input id="notes" name="notes" class="input" maxlength="500" value="{{ old('notes') }}">
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary" @disabled($campagnes->isEmpty())>Enregistrer</button>
            <a href="{{ route('recoltes.index') }}" class="btn btn-link">Annuler</a>
        </div>
    </div>
</form>
@endsection
