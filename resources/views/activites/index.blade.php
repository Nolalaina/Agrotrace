@extends('layouts.app')
@section('titre', 'Activités')
@section('contenu')
<div class="page-head">
    <div>
        <span class="eyebrow">Terrain</span>
        <h1>Activités techniques</h1>
    </div>
    <div class="actions">
        <a href="{{ route('activites.create') }}" class="btn btn-primary"><x-icon name="plus" /> Nouvelle activité</a>
    </div>
</div>

<div class="panel">
    <form method="GET" action="{{ route('activites.index') }}" class="filters">
        <div class="field">
            <label class="label" for="campagne_id">Campagne</label>
            <select id="campagne_id" name="campagne_id" class="select">
                <option value="">Toutes</option>
                @foreach ($campagnes as $c)
                    <option value="{{ $c->id }}" @selected(request('campagne_id') == $c->id)>{{ $c->libelle }}</option>
                @endforeach
            </select>
        </div>
        <div class="field">
            <label class="label" for="type">Type</label>
            <select id="type" name="type" class="select">
                <option value="">Tous</option>
                @foreach ($types as $t)
                    <option value="{{ $t }}" @selected(request('type') === $t)>{{ ucfirst($t) }}</option>
                @endforeach
            </select>
        </div>
        <div class="field">
            <label class="label" for="user_id">Utilisateur</label>
            <select id="user_id" name="user_id" class="select">
                <option value="">Tous</option>
                @foreach ($utilisateurs as $u)
                    <option value="{{ $u->id }}" @selected(request('user_id') == $u->id)>{{ $u->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="field">
            <label class="label" for="du">Du</label>
            <input id="du" type="date" name="du" value="{{ request('du') }}" class="input">
        </div>
        <div class="field">
            <label class="label" for="au">Au</label>
            <input id="au" type="date" name="au" value="{{ request('au') }}" class="input">
        </div>
        <div class="field grow">
            <label class="label" for="q">Recherche</label>
            <input id="q" type="search" name="q" value="{{ request('q') }}" class="input" placeholder="Dans la description">
        </div>
        <div class="field">
            <label class="label" for="tri">Trier par</label>
            <select id="tri" name="tri" class="select">
                @foreach (['date' => 'Date', 'cout' => 'Coût', 'type' => 'Type'] as $k => $v)
                    <option value="{{ $k }}" @selected(request('tri', 'date') === $k)>{{ $v }}</option>
                @endforeach
            </select>
        </div>
        <div class="field">
            <label class="label" for="sens">Ordre</label>
            <select id="sens" name="sens" class="select">
                <option value="desc" @selected(request('sens', 'desc') === 'desc')>Décroissant</option>
                <option value="asc" @selected(request('sens') === 'asc')>Croissant</option>
            </select>
        </div>
        <div class="filter-actions">
            <button type="submit" class="btn btn-secondary">Filtrer</button>
            <a href="{{ route('activites.index') }}" class="btn btn-link">Réinitialiser</a>
        </div>
    </form>
</div>

<div class="panel">
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Campagne</th>
                    <th>Description</th>
                    <th>Par</th>
                    @can('voir-finances')<th class="text-end">Coût</th>@endcan
                </tr>
            </thead>
            <tbody>
            @forelse ($activites as $a)
                <tr>
                    <td class="nowrap">{{ $a->date->format('d/m/Y') }}</td>
                    <td><span class="badge info">{{ $a->type }}</span></td>
                    <td>{{ $a->campagne?->libelle }}</td>
                    <td>{{ $a->description }}</td>
                    <td>{{ $a->user?->name }}</td>
                    @can('voir-finances')<td class="text-end nowrap">{{ number_format((float) $a->cout, 2, ',', ' ') }}</td>@endcan
                </tr>
            @empty
                <tr class="empty-row"><td colspan="6">Aucune activité.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

{{ $activites->links() }}
@endsection
