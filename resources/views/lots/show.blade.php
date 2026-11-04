@extends('layouts.app')
@section('titre', 'Lot '.$lot->code)
@section('contenu')
<div class="page-head">
    <div>
        <h1>{{ $lot->code }}</h1>
        <p class="lead">
            <span class="badge {{ $lot->statut === 'ouvert' ? 'success' : ($lot->statut === 'cloture' ? 'warning' : 'info') }}">{{ ['ouvert' => 'Ouvert', 'cloture' => 'Clôturé', 'vendu' => 'Vendu'][$lot->statut] ?? $lot->statut }}</span>
            &nbsp;{{ $lot->poids_total_kg }} kg @if ($lot->qualite) — qualité {{ $lot->qualite }} @endif
        </p>
    </div>
    @if ($lot->statut !== 'ouvert' && class_exists('SimpleSoftwareIO\QrCode\Facades\QrCode'))
        <div class="panel flush qr-card" title="QR code de traçabilité">{!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(110)->generate(route('tracabilite.public', $lot->code)) !!}</div>
    @endif
</div>

<section class="panel">
    <div class="panel-head"><h3>Origine du lot <span class="muted">(traçabilité remontante)</span></h3></div>
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Parcelle</th>
                    <th>Variété</th>
                    <th>Campagne</th>
                    <th>Date de récolte</th>
                    <th class="text-end">Quantité (kg)</th>
                </tr>
            </thead>
            <tbody>
            @foreach ($lot->recoltes as $r)
                <tr>
                    <td>{{ $r->campagne?->parcelle?->code }} — {{ $r->campagne?->parcelle?->nom }}</td>
                    <td>{{ $r->campagne?->parcelle?->variete }}</td>
                    <td>{{ $r->campagne?->libelle }}</td>
                    <td class="nowrap">{{ $r->date_recolte->format('d/m/Y') }}</td>
                    <td class="text-end">{{ $r->pivot->quantite_kg }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</section>

@canany(['cloturer', 'genererFiche'], $lot)
<section class="panel">
    <div class="panel-head"><h3>Actions</h3></div>
    <div class="panel-body">
        <div class="flex gap-2 wrap">
            @can('cloturer', $lot)
                <form method="POST" action="{{ route('lots.cloturer', $lot) }}" data-confirm="Clôturer ce lot ? Il ne sera plus modifiable." data-confirm-label="Clôturer">
                    @csrf
                    <button type="submit" class="btn btn-warning">Clôturer le lot</button>
                </form>
            @endcan
            @can('genererFiche', $lot)
                <form method="POST" action="{{ route('lots.fiche.generer', $lot) }}">
                    @csrf
                    <button type="submit" class="btn btn-secondary">{{ $ficheDisponible ? 'Régénérer la fiche' : 'Générer la fiche de traçabilité' }}</button>
                </form>
                @if ($ficheDisponible)
                    <a class="btn btn-secondary" href="{{ route('lots.fiche.telecharger', $lot) }}">Télécharger la fiche</a>
                @endif
                <a class="btn btn-secondary" href="{{ route('tracabilite.public', $lot->code) }}" target="_blank" rel="noopener">Page publique</a>
            @endcan
        </div>
    </div>
</section>
@endcanany

@can('vendre', $lot)
<section class="panel">
    <div class="panel-head"><h3>Vendre ce lot</h3></div>
    <div class="panel-body">
        <form method="POST" action="{{ route('lots.vendre', $lot) }}">
            @csrf
            <div class="form-grid">
                <div class="field span-4">
                    <label class="label" for="acheteur">Acheteur</label>
                    <input id="acheteur" name="acheteur" class="input" value="{{ old('acheteur') }}" required>
                </div>
                <div class="field span-3">
                    <label class="label" for="montant">Montant</label>
                    <input id="montant" type="number" step="0.01" min="0.01" name="montant" class="input" value="{{ old('montant') }}" required>
                </div>
                <div class="field span-3">
                    <label class="label" for="date_vente">Date de vente</label>
                    <input id="date_vente" type="date" name="date_vente" max="{{ now()->toDateString() }}" class="input" value="{{ old('date_vente', now()->toDateString()) }}">
                </div>
                <div class="field span-2">
                    <span class="label">&nbsp;</span>
                    <button type="submit" class="btn btn-primary btn-block">Vendre</button>
                </div>
            </div>
        </form>
    </div>
</section>
@endcan

@if ($lot->vente)
    <div class="alert alert-success">
        <x-icon name="check" />
        <div class="alert-body">
            Vendu à {{ $lot->vente->acheteur }} le {{ $lot->vente->date_vente->format('d/m/Y') }}
            @can('voir-finances') — {{ number_format((float) $lot->vente->montant, 2, ',', ' ') }} @endcan
        </div>
    </div>
@endif
@endsection
