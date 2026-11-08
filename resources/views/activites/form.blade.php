@extends('layouts.app')
@section('titre', 'Nouvelle activité')
@section('contenu')
<div class="page-head">
    <div>
        <h1>Nouvelle activité</h1>
    </div>
</div>

<form method="POST" action="{{ route('activites.store') }}" class="panel">
    @csrf
    <div class="panel-body">
        @if ($campagnes->isEmpty())
            <div class="alert alert-warning">
                <x-icon name="info" />
                <div class="alert-body">
                    Aucune campagne en cours : une activité doit être rattachée à une campagne active.
                    @can('create', App\Models\Campagne::class)
                        <a href="{{ route('campagnes.create') }}" class="btn btn-sm btn-secondary ml-2">Créer une campagne</a>
                    @endcan
                </div>
            </div>
        @endif

        <div class="form-grid">
            <div class="field span-6">
                <label class="label" for="campagne_id">Campagne</label>
                <select id="campagne_id" name="campagne_id" class="select" required>
                    @foreach ($campagnes as $c)
                        <option value="{{ $c->id }}" @selected(old('campagne_id') == $c->id)>{{ $c->libelle }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field span-3">
                <label class="label" for="type">Type</label>
                <select id="type" name="type" class="select">
                    @foreach ($types as $t)
                        <option value="{{ $t }}" @selected(old('type') === $t)>{{ ucfirst($t) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field span-3">
                <label class="label" for="date">Date</label>
                <input id="date" type="date" name="date" class="input" value="{{ old('date', now()->toDateString()) }}" required>
            </div>
            <div class="field span-9">
                <label class="label" for="description">Description</label>
                <textarea id="description" name="description" class="textarea" rows="2">{{ old('description') }}</textarea>
            </div>
            <div class="field span-3">
                <label class="label" for="cout">Coût (optionnel)</label>
                <input id="cout" type="number" step="0.01" min="0" name="cout" class="input" value="{{ old('cout') }}">
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary" @disabled($campagnes->isEmpty())>Enregistrer</button>
            <a href="{{ route('activites.index') }}" class="btn btn-link">Annuler</a>
        </div>
    </div>
</form>
@endsection
