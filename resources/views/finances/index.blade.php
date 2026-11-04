@extends('layouts.app')
@section('titre', 'Finances')
@section('contenu')
<div class="page-head">
    <div>
        <h1>Finances <span class="muted" style="font-weight:500;font-size:1rem">(simplifiées)</span></h1>
        <p class="lead">Dépenses et recettes de l'exploitation, avec une marge calculée automatiquement.</p>
    </div>
</div>

<div class="panel">
    <form method="GET" action="{{ route('finances.index') }}" class="filters">
        <div class="field">
            <label class="label" for="campagne_id">Campagne</label>
            <select id="campagne_id" name="campagne_id" class="select">
                <option value="">Toute l'exploitation</option>
                @foreach ($campagnes as $c)
                    <option value="{{ $c->id }}" @selected(request('campagne_id') == $c->id)>{{ $c->libelle }}</option>
                @endforeach
            </select>
        </div>
        <div class="field">
            <label class="label" for="type">Type</label>
            <select id="type" name="type" class="select">
                <option value="">Dépenses et recettes</option>
                <option value="depense" @selected(request('type') === 'depense')>Dépenses</option>
                <option value="recette" @selected(request('type') === 'recette')>Recettes</option>
            </select>
        </div>
        <div class="filter-actions">
            <button type="submit" class="btn btn-secondary">Filtrer</button>
            @if (request()->hasAny(['campagne_id', 'type']))
                <a href="{{ route('finances.index') }}" class="btn btn-link">Réinitialiser</a>
            @endif
        </div>
    </form>
</div>

<div class="grid grid-3">
    <div class="stat-card has-icon">
        <span class="stat-icon accent"><x-icon name="coins" /></span>
        <div class="stat-body">
            <span class="stat-label">Dépenses</span>
            <span class="stat-value">{{ number_format($resume['depenses'], 2, ',', ' ') }}</span>
            <span class="stat-sub">charges enregistrées</span>
        </div>
    </div>
    <div class="stat-card has-icon">
        <span class="stat-icon success"><x-icon name="coins" /></span>
        <div class="stat-body">
            <span class="stat-label">Recettes</span>
            <span class="stat-value">{{ number_format($resume['recettes'], 2, ',', ' ') }}</span>
            <span class="stat-sub">ventes encaissées</span>
        </div>
    </div>
    <div class="stat-card has-icon">
        <span class="stat-icon"><x-icon name="activity" /></span>
        <div class="stat-body">
            <span class="stat-label">Marge</span>
            <span class="stat-value {{ $resume['marge'] >= 0 ? 'text-success' : 'text-danger' }}">{{ number_format($resume['marge'], 2, ',', ' ') }}</span>
            <span class="stat-sub">recettes − dépenses</span>
        </div>
    </div>
</div>

<div class="panel">
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Catégorie</th>
                    <th>Libellé</th>
                    <th class="text-end">Montant</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($transactions as $t)
                <tr>
                    <td class="nowrap">{{ $t->date_transaction->format('d/m/Y') }}</td>
                    <td><span class="badge {{ $t->type === 'recette' ? 'success' : 'warning' }}">{{ ucfirst($t->type) }}</span></td>
                    <td>{{ $t->categorie }}</td>
                    <td>{{ $t->libelle }}</td>
                    <td class="text-end nowrap">{{ number_format((float) $t->montant, 2, ',', ' ') }}</td>
                </tr>
            @empty
                <tr class="empty-row"><td colspan="5">Aucune transaction.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

{{ $transactions->links() }}
@endsection
