@extends('layouts.app')
@section('titre', 'Campagnes')
@section('contenu')
<div class="page-head">
    <div>
        <h1>Campagnes</h1>
        <p class="lead">Planifiez les cycles de production et gardez une campagne active par parcelle.</p>
    </div>
    <div class="actions">
        @can('create', App\Models\Campagne::class)
            <a href="{{ route('campagnes.create') }}" class="btn btn-primary"><x-icon name="plus" /> Nouvelle campagne</a>
        @endcan
    </div>
</div>

<div class="panel">
    <form method="GET" action="{{ route('campagnes.index') }}" class="filters">
        <div class="field grow">
            <label class="label" for="q">Recherche</label>
            <input id="q" type="search" name="q" value="{{ request('q') }}" class="input" placeholder="Libellé de la campagne">
        </div>
        <div class="field">
            <label class="label" for="statut">Statut</label>
            <select id="statut" name="statut" class="select">
                <option value="">Tous les statuts</option>
                @foreach (App\Models\Campagne::STATUTS as $s)
                    <option value="{{ $s }}" @selected(request('statut') === $s)>{{ ucfirst(str_replace('_', ' ', $s)) }}</option>
                @endforeach
            </select>
        </div>
        <div class="field">
            <label class="label" for="parcelle_id">Parcelle</label>
            <select id="parcelle_id" name="parcelle_id" class="select">
                <option value="">Toutes les parcelles</option>
                @foreach ($parcelles as $p)
                    <option value="{{ $p->id }}" @selected(request('parcelle_id') == $p->id)>{{ $p->code }} — {{ $p->nom }}</option>
                @endforeach
            </select>
        </div>
        <div class="filter-actions">
            <button type="submit" class="btn btn-secondary">Filtrer</button>
            @if (request()->hasAny(['q', 'statut', 'parcelle_id']))
                <a href="{{ route('campagnes.index') }}" class="btn btn-link">Réinitialiser</a>
            @endif
        </div>
    </form>
</div>

<div class="panel">
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Libellé</th>
                    <th>Parcelle</th>
                    <th>Début</th>
                    <th>Fin prévue</th>
                    <th>Statut</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($campagnes as $c)
                <tr>
                    <td><a href="{{ route('campagnes.edit', $c) }}"><strong>{{ $c->libelle }}</strong></a></td>
                    <td>{{ $c->parcelle?->code }}</td>
                    <td class="nowrap">{{ $c->date_debut->format('d/m/Y') }}</td>
                    <td class="nowrap">{{ $c->date_fin_prevue->format('d/m/Y') }}</td>
                    <td><span class="badge {{ $c->statut === 'en_cours' ? 'success' : '' }}">{{ str_replace('_', ' ', $c->statut) }}</span></td>
                    <td class="col-actions text-end">
                        <div class="row-actions">
                            @can('update', $c)
                                <a href="{{ route('campagnes.edit', $c) }}" class="btn btn-sm btn-secondary">Modifier</a>
                            @endcan
                            @can('delete', $c)
                                <form method="POST" action="{{ route('campagnes.destroy', $c) }}" data-confirm="Supprimer définitivement la campagne {{ $c->libelle }} ?" data-confirm-label="Supprimer">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Supprimer</button>
                                </form>
                            @endcan
                        </div>
                    </td>
                </tr>
            @empty
                <tr class="empty-row"><td colspan="6">Aucune campagne.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

{{ $campagnes->links() }}
@endsection
