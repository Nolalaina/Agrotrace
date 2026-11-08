@extends('layouts.app')
@section('titre', 'Récoltes')
@section('contenu')
<div class="page-head">
    <div>
        <h1>Récoltes</h1>
        <p class="lead">Quantités récoltées et volumes encore disponibles pour composer des lots.</p>
    </div>
    <div class="actions">
        @can('create', App\Models\Recolte::class)
            <a href="{{ route('recoltes.create') }}" class="btn btn-primary"><x-icon name="plus" /> Enregistrer une récolte</a>
        @endcan
        @can('create', App\Models\Lot::class)
            <a href="{{ route('lots.create') }}" class="btn btn-secondary"><x-icon name="layers" /> Composer un lot</a>
        @endcan
    </div>
</div>

<div class="panel">
    <form method="GET" action="{{ route('recoltes.index') }}" class="filters">
        <div class="field">
            <label class="label" for="campagne_id">Campagne</label>
            <select id="campagne_id" name="campagne_id" class="select">
                <option value="">Toutes les campagnes</option>
                @foreach ($campagnes as $c)
                    <option value="{{ $c->id }}" @selected(request('campagne_id') == $c->id)>{{ $c->libelle }}</option>
                @endforeach
            </select>
        </div>
        <div class="field">
            <label class="label" for="calibre">Calibre</label>
            <select id="calibre" name="calibre" class="select">
                <option value="">Tous les calibres</option>
                @foreach ($calibres as $cal)
                    <option value="{{ $cal }}" @selected(request('calibre') === $cal)>{{ ucfirst($cal) }}</option>
                @endforeach
            </select>
        </div>
        <div class="field" style="flex: 0 1 auto;">
            <span class="label">&nbsp;</span>
            <label class="check" style="min-height:44px;">
                <input type="checkbox" name="non_affectees" value="1" @checked(request()->boolean('non_affectees'))>
                <span>Non affectées uniquement</span>
            </label>
        </div>
        <div class="filter-actions">
            <button type="submit" class="btn btn-secondary">Filtrer</button>
            @if (request()->hasAny(['campagne_id', 'non_affectees', 'calibre']))
                <a href="{{ route('recoltes.index') }}" class="btn btn-link">Réinitialiser</a>
            @endif
        </div>
    </form>
</div>

<div class="panel">
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Campagne</th>
                    <th>Parcelle</th>
                    <th class="text-end">Quantité (kg)</th>
                    <th class="text-end">Disponible</th>
                    <th>Calibre</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($recoltes as $r)
                <tr>
                    <td class="nowrap">{{ $r->date_recolte->format('d/m/Y') }}</td>
                    <td>{{ $r->campagne?->libelle }}</td>
                    <td>{{ $r->campagne?->parcelle?->code }}</td>
                    <td class="text-end">{{ $r->quantite_kg }}</td>
                    <td class="text-end">
                        @if ($r->quantite_disponible_kg > 0)
                            <span class="badge success">{{ $r->quantite_disponible_kg }} kg</span>
                        @else
                            <span class="badge">Affecté</span>
                        @endif
                    </td>
                    <td>{{ ucfirst($r->calibre ?? '—') }}</td>
                    <td class="col-actions text-end">
                        <div class="row-actions">
                            @if ($r->quantite_disponible_kg > 0)
                                @can('create', App\Models\Lot::class)
                                    <a href="{{ route('lots.create') }}" class="btn btn-sm btn-secondary">Affecter</a>
                                @endcan
                            @endif
                            @can('delete', $r)
                                @if ((float) $r->quantite_disponible_kg === (float) $r->quantite_kg)
                                    <form method="POST" action="{{ route('recoltes.destroy', $r) }}" data-confirm="Supprimer cette récolte de {{ $r->quantite_kg }} kg ?" data-confirm-label="Supprimer">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Supprimer</button>
                                    </form>
                                @endif
                            @endcan
                        </div>
                    </td>
                </tr>
            @empty
                <tr class="empty-row"><td colspan="7">Aucune récolte.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

{{ $recoltes->links() }}
@endsection
