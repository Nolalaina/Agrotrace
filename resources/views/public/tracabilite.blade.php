<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#123024">
    <title>Traçabilité du lot {{ $t['code'] }} — AgroTrace</title>
    <script>
        (function () {
            try {
                var stored = localStorage.getItem('agrotrace-theme');
                var dark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                document.documentElement.dataset.theme = stored === 'dark' || stored === 'light'
                    ? stored
                    : (dark ? 'dark' : 'light');
            } catch (e) {
                document.documentElement.dataset.theme = 'light';
            }
        })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<div class="public-wrap">
    <div class="guest-topbar">
        <a class="brand" href="{{ url('/') }}">
            <span class="brand-mark"><x-icon name="leaf" /></span>
            AgroTrace
        </a>
        <div class="flex gap-2 items-center">
            <button type="button" class="btn btn-sm btn-secondary" onclick="window.print()"><x-icon name="printer" /> Imprimer</button>
            <button type="button" class="icon-btn" data-theme-toggle aria-label="Basculer le thème clair/sombre" aria-pressed="false" title="Basculer le thème">
                <x-icon name="moon" class="icon-moon" />
                <x-icon name="sun" class="icon-sun" />
            </button>
        </div>
    </div>

    <header class="panel public-head">
        <span class="badge {{ $t['statut'] === 'vendu' ? 'success' : 'warning' }}">Lot {{ $t['statut'] === 'vendu' ? 'vendu' : 'clôturé' }}</span>
        <h1>Lot {{ $t['code'] }}</h1>
        <p class="lead">
            {{ number_format($t['poids_total_kg'], 2, ',', ' ') }} kg
            @if ($t['qualite']) · qualité {{ $t['qualite'] }} @endif
            @if ($t['cloture_le']) · clôturé le {{ \Illuminate\Support\Carbon::parse($t['cloture_le'])->format('d/m/Y') }} @endif
        </p>
    </header>

    <section class="panel">
        <div class="panel-head">
            <div>
                <h3>Origine des fruits</h3>
                <p class="hint">Parcelles et dates de récolte rattachées à ce lot.</p>
            </div>
        </div>
        @foreach ($t['origine'] as $o)
            <div class="origin">
                <strong>Parcelle {{ $o['parcelle']['code'] ?? '—' }}</strong> — {{ $o['parcelle']['nom'] ?? '' }}
                <dl>
                    <dt>Variété</dt><dd>{{ $o['parcelle']['variete'] ?? '—' }}</dd>
                    <dt>Lieu</dt><dd>{{ $o['parcelle']['localisation'] ?? '—' }}</dd>
                    <dt>Récolté le</dt><dd>{{ \Illuminate\Support\Carbon::parse($o['date_recolte'])->format('d/m/Y') }}</dd>
                    <dt>Quantité</dt><dd>{{ number_format($o['quantite_kg'], 2, ',', ' ') }} kg</dd>
                    @if ($o['calibre'])<dt>Calibre</dt><dd>{{ $o['calibre'] }}</dd>@endif
                </dl>
            </div>
        @endforeach
    </section>

    <p class="hint">Cette page ne contient aucune donnée financière.</p>
    <p class="colophon">AgroTrace · Registre public des lots — feuille du {{ now()->locale('fr_FR')->translatedFormat('j F Y') }} · traçabilité vérifiable par QR code.</p>
</div>
</body>
</html>
