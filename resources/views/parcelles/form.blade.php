@extends('layouts.app')
@section('titre', $parcelle->exists ? 'Modifier la parcelle' : 'Nouvelle parcelle')
@section('contenu')
<div class="page-head">
    <div>
        <h1>{{ $parcelle->exists ? 'Parcelle '.$parcelle->code : 'Nouvelle parcelle' }}</h1>
    </div>
</div>

<form method="POST" action="{{ $parcelle->exists ? route('parcelles.update', $parcelle) : route('parcelles.store') }}" class="panel">
    @csrf
    @if ($parcelle->exists) @method('PUT') @endif
    <div class="panel-body">
        <div class="form-grid">
            <div class="field span-3">
                <label class="label" for="code">Code</label>
                <input id="code" name="code" class="input" value="{{ old('code', $parcelle->code) }}" maxlength="30" required>
            </div>
            <div class="field span-5">
                <label class="label" for="nom">Nom</label>
                <input id="nom" name="nom" class="input" value="{{ old('nom', $parcelle->nom) }}" required>
            </div>
            <div class="field span-4">
                <label class="label" for="variete">Variété</label>
                <input id="variete" name="variete" class="input" value="{{ old('variete', $parcelle->variete) }}" required>
            </div>
            <div class="field span-3">
                <label class="label" for="superficie_ha">Superficie (ha)</label>
                <input id="superficie_ha" type="number" step="0.01" min="0.01" name="superficie_ha" class="input" value="{{ old('superficie_ha', $parcelle->superficie_ha) }}" required>
            </div>
            <div class="field span-5">
                <label class="label" for="localisation">Localisation</label>
                <input id="localisation" name="localisation" class="input" value="{{ old('localisation', $parcelle->localisation) }}">
            </div>
            <div class="field span-2">
                <label class="label" for="etat">État</label>
                <select id="etat" name="etat" class="select">
                    @foreach (['productive' => 'Productive', 'en_repos' => 'En repos', 'en_replantation' => 'En replantation'] as $valeur => $libelle)
                        <option value="{{ $valeur }}" @selected(old('etat', $parcelle->etat) === $valeur)>{{ $libelle }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field span-2">
                <label class="label" for="date_creation">Création</label>
                <input id="date_creation" type="date" name="date_creation" max="{{ now()->toDateString() }}" class="input" value="{{ old('date_creation', optional($parcelle->date_creation)->toDateString()) }}">
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Enregistrer</button>
            <a href="{{ route('parcelles.index') }}" class="btn btn-link">Annuler</a>
        </div>
    </div>
</form>

@if ($parcelle->exists)
    <section class="panel">
        <div class="panel-head"><h3>Lots produits par cette parcelle <span class="muted">(traçabilité descendante)</span></h3></div>
        <div class="table-wrap">
            <table class="table">
                <tbody>
                @forelse ($lots as $l)
                    <tr>
                        <td><a href="{{ route('lots.show', $l) }}"><strong>{{ $l->code }}</strong></a></td>
                        <td><span class="badge {{ $l->statut === 'ouvert' ? 'success' : ($l->statut === 'cloture' ? 'warning' : 'info') }}">{{ ['ouvert' => 'Ouvert', 'cloture' => 'Clôturé', 'vendu' => 'Vendu'][$l->statut] ?? $l->statut }}</span></td>
                        <td class="text-end">{{ $l->poids_total_kg }} kg</td>
                    </tr>
                @empty
                    <tr class="empty-row"><td colspan="3">Aucun lot pour l'instant.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>

    @can('delete', $parcelle)
        <section class="panel">
            <div class="panel-head">
                <div>
                    <h3>Zone sensible</h3>
                    <p class="hint">La suppression est impossible tant que la parcelle possède des campagnes.</p>
                </div>
                <form method="POST" action="{{ route('parcelles.destroy', $parcelle) }}" data-confirm="Supprimer définitivement cette parcelle ?" data-confirm-label="Supprimer">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger">Supprimer la parcelle</button>
                </form>
            </div>
        </section>
    @endcan
@endif
@endsection
