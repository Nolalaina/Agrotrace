<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Fiche de traçabilité {{ $t['code'] }}</title>
    <style>
        :root { color-scheme: light; }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            padding: 40px;
            font-family: "Instrument Sans", "Segoe UI", Arial, sans-serif;
            color: #1c2a21;
            background: #fff;
            font-size: 14px;
            line-height: 1.5;
        }
        .brand { display: flex; align-items: center; gap: 10px; font-weight: 700; color: #173a27; margin-bottom: 28px; }
        .brand-mark {
            width: 34px; height: 34px; border-radius: 10px;
            display: grid; place-items: center;
            background: #2f6b46; color: #fff; font-size: 16px; font-weight: 800;
        }
        h1 { margin: 0 0 6px; font-size: 24px; letter-spacing: -0.02em; color: #173a27; }
        .lead { margin: 0 0 24px; color: #68736a; }
        .meta {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 12px;
            margin-bottom: 26px;
            padding: 18px 20px;
            border: 1px solid #e5e1d5;
            border-radius: 14px;
            background: #f7f5ed;
        }
        .meta dt { margin: 0; font-size: 10px; letter-spacing: .08em; text-transform: uppercase; color: #68736a; font-weight: 700; }
        .meta dd { margin: 4px 0 0; font-weight: 650; color: #1c2a21; }
        table { width: 100%; border-collapse: collapse; font-size: 13px; }
        th, td { padding: 10px 12px; text-align: left; border-bottom: 1px solid #e5e1d5; }
        th { font-size: 10px; letter-spacing: .07em; text-transform: uppercase; color: #68736a; background: #f7f5ed; }
        tbody tr:last-child td { border-bottom: 0; }
        .foot { margin-top: 26px; color: #68736a; font-size: 12px; }
        @media print { body { padding: 0; } }
    </style>
</head>
<body>
    <div class="brand"><span class="brand-mark">A</span> AgroTrace</div>

    <h1>Fiche de traçabilité — {{ $t['code'] }}</h1>
    <p class="lead">Origine des fruits et données de récolte rattachées au lot.</p>

    <dl class="meta">
        <div><dt>Statut</dt><dd>{{ $t['statut'] }}</dd></div>
        <div><dt>Poids total</dt><dd>{{ $t['poids_total_kg'] }} kg</dd></div>
        <div><dt>Qualité</dt><dd>{{ $t['qualite'] ?? '—' }}</dd></div>
        <div><dt>Clôturé le</dt><dd>{{ $t['cloture_le'] ?? '—' }}</dd></div>
    </dl>

    <table>
        <thead>
            <tr>
                <th>Parcelle</th>
                <th>Variété</th>
                <th>Localisation</th>
                <th>Campagne</th>
                <th>Date de récolte</th>
                <th>Quantité (kg)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($t['origine'] as $o)
                <tr>
                    <td>{{ $o['parcelle']['code'] ?? '' }} {{ $o['parcelle']['nom'] ?? '' }}</td>
                    <td>{{ $o['parcelle']['variete'] ?? '' }}</td>
                    <td>{{ $o['parcelle']['localisation'] ?? '' }}</td>
                    <td>{{ $o['campagne'] }}</td>
                    <td>{{ $o['date_recolte'] }}</td>
                    <td>{{ $o['quantite_kg'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p class="foot">Document généré par AgroTrace le {{ now()->format('d/m/Y à H:i') }}.</p>
</body>
</html>
