@extends('layouts.app')
@section('titre', 'Parcelles')
@section('contenu')
<div class="page-head">
    <div>
        <span class="eyebrow">Exploitation</span>
        <h1>Parcelles</h1>
    </div>
    <div class="actions">
        @can('create', App\Models\Parcelle::class)
            <a href="{{ route('parcelles.create') }}" class="btn btn-primary"><x-icon name="plus" /> Nouvelle parcelle</a>
        @endcan
    </div>
</div>

<div class="panel">
    <form method="GET" action="{{ route('parcelles.index') }}" class="filters">
        <div class="field grow">
            <label class="label" for="q">Recherche</label>
            <input id="q" type="search" name="q" value="{{ request('q') }}" class="input" placeholder="Code ou nom de la parcelle">
        </div>
        <div class="filter-actions">
            <button type="submit" class="btn btn-secondary">Filtrer</button>
            @if (request()->filled('q'))
                <a href="{{ route('parcelles.index') }}" class="btn btn-link">Réinitialiser</a>
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
                    <th>Nom</th>
                    <th>Variété</th>
                    <th class="text-end">Superficie (ha)</th>
                    <th>État</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($parcelles as $p)
                <tr>
                    <td><strong>{{ $p->code }}</strong></td>
                    <td>{{ $p->nom }}</td>
                    <td>{{ $p->variete }}</td>
                    <td class="text-end">{{ $p->superficie_ha }}</td>
                    <td><span class="badge {{ $p->etat === 'productive' ? 'success' : ($p->etat === 'en_repos' ? 'info' : 'warning') }}">{{ str_replace('_', ' ', $p->etat) }}</span></td>
                    <td class="col-actions text-end">
                        <div class="row-actions">
                            @can('update', $p)
                                <a href="{{ route('parcelles.edit', $p) }}" class="btn btn-sm btn-secondary">Modifier</a>
                            @endcan
                        </div>
                    </td>
                </tr>
            @empty
                <tr class="empty-row"><td colspan="6">Aucune parcelle.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

{{ $parcelles->links() }}
@endsection
