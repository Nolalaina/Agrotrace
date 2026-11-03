<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#1b5e3f">
    <title>Traçabilité du lot {{ $t['code'] }} — AgroTrace</title>
    @vite(['resources/css/app.css'])
    <style>
        .public-wrap { width: min(720px, 100%); margin: 0 auto; padding: 28px 16px 48px; }
        .public-brand { display: flex; align-items: center; gap: 10px; margin-bottom: 22px; font-weight: 700; color: var(--primary-strong); }
        .origin { padding: 16px 20px; border-bottom: 1px solid var(--line); }
        .origin:last-child { border-bottom: 0; }
        .origin dl { display: grid; grid-template-columns: auto 1fr; gap: 4px 14px; margin: 8px 0 0; }
        .origin dt { color: var(--muted); }
        .origin dd { margin: 0; }
    </style>
</head>
<body>
<div class="public-wrap">
    <div class="public-brand"><span class="brand-mark">A</span> AgroTrace</div>

    <span class="eyebrow">Traçabilité</span>
    <h1>Lot {{ $t['code'] }}</h1>
    <p class="lead">
        {{ number_format($t['poids_total_kg'], 2, ',', ' ') }} kg
        @if ($t['qualite']) · qualité {{ $t['qualite'] }} @endif
        · lot {{ $t['statut'] === 'vendu' ? 'vendu' : 'clôturé' }}
        @if ($t['cloture_le']) le {{ \Illuminate\Support\Carbon::parse($t['cloture_le'])->format('d/m/Y') }} @endif
    </p>

    <section class="panel mt-3">
        <div class="panel-head"><h3>Origine des fruits</h3></div>
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
</div>
</body>
</html>
