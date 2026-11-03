@extends('layouts.app')
@section('titre', 'Campagne')
@section('contenu')
<div class="page-head">
    <div>
        <span class="eyebrow">Campagnes</span>
        <h1>{{ $campagne->exists ? 'Modifier la campagne' : 'Nouvelle campagne' }}</h1>
    </div>
</div>

<form method="POST" action="{{ $campagne->exists ? route('campagnes.update', $campagne) : route('campagnes.store') }}" class="panel">
    @csrf
    @if ($campagne->exists) @method('PUT') @endif
    <div class="panel-body">
        <div class="form-grid">
            <div class="field span-6">
                <label class="label" for="libelle">Libellé</label>
                <input id="libelle" name="libelle" class="input" value="{{ old('libelle', $campagne->libelle) }}" required>
            </div>
            <div class="field span-6">
                <label class="label" for="parcelle_id">Parcelle</label>
                <select id="parcelle_id" name="parcelle_id" class="select" required>
                    @foreach ($parcelles as $p)
                        <option value="{{ $p->id }}" @selected(old('parcelle_id', $campagne->parcelle_id) == $p->id)>{{ $p->code }} — {{ $p->nom }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field span-4">
                <label class="label" for="date_debut">Début</label>
                <input id="date_debut" type="date" name="date_debut" class="input" value="{{ old('date_debut', optional($campagne->date_debut)->toDateString()) }}" required>
            </div>
            <div class="field span-4">
                <label class="label" for="date_fin_prevue">Fin prévue</label>
                <input id="date_fin_prevue" type="date" name="date_fin_prevue" class="input" value="{{ old('date_fin_prevue', optional($campagne->date_fin_prevue)->toDateString()) }}" required>
            </div>
            <div class="field span-4">
                <label class="label" for="statut">Statut</label>
                <select id="statut" name="statut" class="select">
                    @foreach (App\Models\Campagne::STATUTS as $s)
                        <option value="{{ $s }}" @selected(old('statut', $campagne->statut) === $s)>{{ ucfirst(str_replace('_', ' ', $s)) }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Enregistrer</button>
            <a href="{{ route('campagnes.index') }}" class="btn btn-link">Annuler</a>
        </div>
    </div>
</form>

@if ($campagne->exists)
    @can('delete', $campagne)
        <section class="panel">
            <div class="panel-head">
                <div>
                    <h3>Zone sensible</h3>
                    <p class="hint">Une campagne qui contient des activités ou des récoltes ne peut pas être supprimée.</p>
                </div>
                <form method="POST" action="{{ route('campagnes.destroy', $campagne) }}" data-confirm="Supprimer définitivement cette campagne ?" data-confirm-label="Supprimer">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger">Supprimer la campagne</button>
                </form>
            </div>
        </section>
    @endcan
@endif
@endsection
