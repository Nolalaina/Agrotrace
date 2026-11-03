@extends('layouts.app')
@section('titre', 'Récoltes')
@section('contenu')
<div class="page-head">
    <div>
        <span class="eyebrow">Production</span>
        <h1>Récoltes</h1>
    </div>
    <div class="actions">
        @can('create', App\Models\Recolte::class)
            <a href="{{ route('recoltes.create') }}" class="btn btn-primary"><x-icon name="plus" /> Enregistrer une récolte</a>
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
        <div class="field" style="flex: 0 1 auto;">
            <span class="label">&nbsp;</span>
            <label class="check" style="min-height:44px;">
                <input type="checkbox" name="non_affectees" value="1" @checked(request()->boolean('non_affectees'))>
                <span>Non affectées uniquement</span>
            </label>
        </div>
        <div class="filter-actions">
            <button type="submit" class="btn btn-secondary">Filtrer</button>
            @if (request()->hasAny(['campagne_id', 'non_affectees']))
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
                    <th class="text-end">Disponible (kg)</th>
                    <th>Calibre</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($recoltes as $r)
                <tr>
                    <td class="nowrap">{{ $r->date_recolte->format('d/m/Y') }}</td>
                    <td>{{ $r->campagne?->libelle }}</td>
                    <td>{{ $r->campagne?->parcelle?->code }}</td>
                    <td class="text-end">{{ $r->quantite_kg }}</td>
                    <td class="text-end">{{ $r->quantite_disponible_kg }}</td>
                    <td>{{ $r->calibre }}</td>
                </tr>
            @empty
                <tr class="empty-row"><td colspan="6">Aucune récolte.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

{{ $recoltes->links() }}
@endsection
