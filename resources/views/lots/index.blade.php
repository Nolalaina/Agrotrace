@extends('layouts.app')
@section('titre', 'Lots')
@section('contenu')
<div class="page-head">
    <div>
        <span class="eyebrow">Traçabilité</span>
        <h1>Lots de production</h1>
    </div>
    <div class="actions">
        @can('create', App\Models\Lot::class)
            <a href="{{ route('lots.create') }}" class="btn btn-primary"><x-icon name="plus" /> Composer un lot</a>
        @endcan
    </div>
</div>

<div class="panel">
    <form method="GET" action="{{ route('lots.index') }}" class="filters">
        <div class="field grow">
            <label class="label" for="q">Code du lot</label>
            <input id="q" type="search" name="q" value="{{ request('q') }}" class="input" placeholder="Rechercher un code">
        </div>
        <div class="field">
            <label class="label" for="statut">Statut</label>
            <select id="statut" name="statut" class="select">
                <option value="">Tous les statuts</option>
                @foreach (['ouvert', 'cloture', 'vendu'] as $s)
                    <option value="{{ $s }}" @selected(request('statut') === $s)>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </div>
        <div class="filter-actions">
            <button type="submit" class="btn btn-secondary">Filtrer</button>
            @if (request()->hasAny(['q', 'statut']))
                <a href="{{ route('lots.index') }}" class="btn btn-link">Réinitialiser</a>
            @endif
        </div>
    </form>
</div>

<div class="panel">
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Statut</th>
                    <th>Qualité</th>
                    <th class="text-end">Poids (kg)</th>
                    <th>Créé le</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($lots as $l)
                <tr>
                    <td><a href="{{ route('lots.show', $l) }}"><strong>{{ $l->code }}</strong></a></td>
                    <td><span class="badge {{ $l->statut === 'ouvert' ? 'success' : ($l->statut === 'cloture' ? 'warning' : 'info') }}">{{ $l->statut }}</span></td>
                    <td>{{ $l->qualite }}</td>
                    <td class="text-end">{{ $l->poids_total_kg }}</td>
                    <td class="nowrap">{{ $l->created_at->format('d/m/Y') }}</td>
                </tr>
            @empty
                <tr class="empty-row"><td colspan="5">Aucun lot.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

{{ $lots->links() }}
@endsection
