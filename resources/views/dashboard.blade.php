@extends('layouts.app')

@section('titre', 'Tableau de bord')

@section('contenu')
@php($n = fn ($v, $d = 0) => number_format((float) $v, $d, ',', ' '))

<div class="page-head">
    <div>
        <h1>Tableau de bord</h1>
        <p class="lead">Suivi de l'exploitation : parcelles, récoltes, lots et santé des stocks.</p>
    </div>
    <div class="actions">
        <a href="{{ route('recoltes.create') }}" class="btn btn-primary"><x-icon name="plus" /> Enregistrer une récolte</a>
        <a href="{{ route('activites.create') }}" class="btn btn-secondary"><x-icon name="activity" /> Nouvelle activité</a>
    </div>
</div>

@if (!empty($notifications) && count($notifications))
    <div class="stack">
        @foreach ($notifications as $notif)
            <div class="alert alert-warning">
                <x-icon name="alert" />
                <div class="alert-body">{{ $notif->data['message'] ?? 'Notification' }}</div>
            </div>
        @endforeach
    </div>
@endif

<div class="grid grid-4">
    <div class="stat-card has-icon">
        <span class="stat-icon"><x-icon name="calendar" /></span>
        <div class="stat-body">
            <span class="stat-label">Campagnes actives</span>
            <span class="stat-value">{{ $stats['campagnes_actives'] }}</span>
            <span class="stat-sub">en cours</span>
        </div>
    </div>
    <div class="stat-card has-icon">
        <span class="stat-icon info"><x-icon name="map" /></span>
        <div class="stat-body">
            <span class="stat-label">Parcelles</span>
            <span class="stat-value">{{ $stats['nb_parcelles'] }}</span>
            <span class="stat-sub">{{ $n($stats['superficie_ha'], 1) }} ha au total</span>
        </div>
    </div>
    <div class="stat-card has-icon">
        <span class="stat-icon success"><x-icon name="basket" /></span>
        <div class="stat-body">
            <span class="stat-label">Récolte totale</span>
            <span class="stat-value">{{ $n($stats['recolte_totale_kg']) }}</span>
            <span class="stat-sub">kg cumulés</span>
        </div>
    </div>
    <div class="stat-card has-icon">
        <span class="stat-icon accent"><x-icon name="layers" /></span>
        <div class="stat-body">
            <span class="stat-label">Poids des lots</span>
            <span class="stat-value">{{ $n($stats['poids_lots_kg']) }}</span>
            <span class="stat-sub">kg tracés</span>
        </div>
    </div>
</div>

<div class="grid grid-3">
    <section class="panel">
        <div class="panel-head">
            <div>
                <h3>Indicateurs calculés</h3>
                <p class="hint">Performance de la campagne en cours.</p>
            </div>
        </div>
        <ul class="data-list">
            <li><span>Rendement</span><strong>{{ $n($stats['rendement_kg_ha'], 1) }} kg/ha</strong></li>
            <li><span>Récolte non affectée</span><strong>{{ $n($stats['taux_perte_potentielle'], 1) }} %</strong></li>
            @isset($stats['cout_production_kg'])
                <li>
                    <span>Coût de production</span>
                    <strong>{{ $stats['cout_production_kg'] !== null ? $n($stats['cout_production_kg'], 2).' /kg' : '—' }}</strong>
                </li>
            @endisset
        </ul>
    </section>

    @isset($stats['finance'])
        <section class="panel">
            <div class="panel-head">
                <div>
                    <h3>Finances</h3>
                    <p class="hint">Marge simplifiée sur l'exploitation.</p>
                </div>
                <a href="{{ route('finances.index') }}" class="btn btn-link btn-sm">Détail</a>
            </div>
            <ul class="data-list">
                <li><span>Dépenses</span><strong>{{ $n($stats['finance']['depenses'], 2) }}</strong></li>
                <li><span>Recettes</span><strong>{{ $n($stats['finance']['recettes'], 2) }}</strong></li>
                <li><span>Marge</span><strong class="{{ $stats['finance']['marge'] >= 0 ? 'text-success' : 'text-danger' }}">{{ $n($stats['finance']['marge'], 2) }}</strong></li>
            </ul>
        </section>
    @endisset

    <section class="panel">
        <div class="panel-head">
            <div>
                <h3>Alertes</h3>
                <p class="hint">Points à traiter en priorité.</p>
            </div>
        </div>
        <ul class="data-list">
            @forelse ($stats['stocks_critiques'] as $s)
                <li><span>Stock critique</span><strong class="text-danger">{{ $s['nom'] }} ({{ $s['quantite_disponible'] }} {{ $s['unite'] }})</strong></li>
            @empty
                <li><span>Stocks</span><strong class="text-success">Aucune alerte</strong></li>
            @endforelse
            @if ($stats['recoltes_non_affectees_anciennes'] > 0)
                <li><span>Récoltes non affectées</span><strong>{{ $stats['recoltes_non_affectees_anciennes'] }} depuis plus de 3 jours</strong></li>
            @endif
        </ul>
    </section>
</div>

<div class="grid grid-2">
    <section class="panel">
        <div class="panel-head">
            <h3>État des stocks</h3>
            <a href="{{ route('intrants.index') }}" class="btn btn-link btn-sm">Voir tout</a>
        </div>
        <div class="table-wrap">
            <table class="table">
                <tbody>
                    @forelse ($stats['stocks'] as $s)
                        <tr>
                            <td>{{ $s['nom'] }}</td>
                            <td class="text-end">{{ $s['quantite_disponible'] }} {{ $s['unite'] }}</td>
                            <td class="muted">seuil {{ $s['seuil_minimum'] }}</td>
                        </tr>
                    @empty
                        <tr class="empty-row"><td colspan="3">Aucun intrant enregistré.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="panel">
        <div class="panel-head">
            <h3>Activités récentes</h3>
            <a href="{{ route('activites.index') }}" class="btn btn-link btn-sm">Voir tout</a>
        </div>
        <div class="table-wrap">
            <table class="table">
                <tbody>
                    @forelse ($stats['activites_recentes'] as $a)
                        <tr>
                            <td class="nowrap">{{ \Illuminate\Support\Carbon::parse($a['date'])->format('d/m/Y') }}</td>
                            <td>{{ ucfirst($a['type']) }}</td>
                            <td class="muted">{{ $a['campagne']['libelle'] ?? '' }}</td>
                        </tr>
                    @empty
                        <tr class="empty-row"><td colspan="3">Aucune activité enregistrée.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
