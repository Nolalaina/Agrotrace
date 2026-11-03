<!DOCTYPE html>
<html lang="fr">
<head><meta charset="utf-8"><title>Fiche de traçabilité {{ $t['code'] }}</title>
<style>body{font-family:Arial,sans-serif;margin:2rem}table{border-collapse:collapse;width:100%}td,th{border:1px solid #999;padding:6px;text-align:left}h1{color:#2e5e3e}</style></head>
<body>
<h1>Fiche de traçabilité — {{ $t['code'] }}</h1>
<p>Statut : {{ $t['statut'] }} · Poids total : {{ $t['poids_total_kg'] }} kg · Qualité : {{ $t['qualite'] ?? '—' }} · Clôturé le : {{ $t['cloture_le'] ?? '—' }}</p>
<table>
    <tr><th>Parcelle</th><th>Variété</th><th>Localisation</th><th>Campagne</th><th>Date de récolte</th><th>Quantité (kg)</th></tr>
    @foreach ($t['origine'] as $o)
        <tr><td>{{ $o['parcelle']['code'] ?? '' }} {{ $o['parcelle']['nom'] ?? '' }}</td><td>{{ $o['parcelle']['variete'] ?? '' }}</td>
            <td>{{ $o['parcelle']['localisation'] ?? '' }}</td><td>{{ $o['campagne'] }}</td><td>{{ $o['date_recolte'] }}</td><td>{{ $o['quantite_kg'] }}</td></tr>
    @endforeach
</table>
</body></html>
