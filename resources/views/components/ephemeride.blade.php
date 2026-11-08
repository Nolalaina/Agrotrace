@props(['stats' => null, 'compact' => false])

@php
    $now = now('UTC');
    $days = ($now->timestamp - 1791658211) / 86400;
    $age = fmod($days, 29.53058867);
    if ($age < 0) {
        $age += 29.53058867;
    }
    $frac = $age / 29.53058867;
    $illumination = (int) round((1 - cos(2 * M_PI * $frac)) / 2 * 100);
    $phase = match (true) {
        $age < 1 => 'Nouvelle lune',
        $age < 6.4 => 'Premier croissant',
        $age < 8.4 => 'Premier quartier',
        $age < 13.8 => 'Gibbeuse croissante',
        $age < 15.8 => 'Pleine lune',
        $age < 21.2 => 'Gibbeuse décroissante',
        $age < 23.2 => 'Dernier quartier',
        $age < 28.6 => 'Dernier croissant',
        default => 'Nouvelle lune',
    };
    $fin = !empty($stats['campagne_fin']) ? \Illuminate\Support\Carbon::parse($stats['campagne_fin'])->locale('fr_FR')->translatedFormat('j M Y') : null;
    $finTxt = $fin ? ' · jusqu\'au '.$fin : '';
@endphp

@if ($compact)
    <div class="ephemeride ephemeride-compact" role="note" aria-label="Éphéméride">
        <span class="moon"><x-icon name="moon" /></span>
        <span class="txt"><b>Jour {{ $now->dayOfYear }}</b><span class="stop">·</span>{{ $phase }} · {{ $illumination }} % éclairée</span>
    </div>
@else
    <div class="ephemeride" role="note" aria-label="Éphéméride">
        <span class="moon"><x-icon name="moon" /></span>
        <span class="txt"><b>{{ $now->locale('fr_FR')->translatedFormat('l j F Y') }}</b><span class="stop">·</span>jour {{ $now->dayOfYear }}<span class="stop">·</span>{{ $phase }} · {{ $illumination }} % éclairée<span class="stop">·</span>{{ $stats['campagnes_actives'] ?? 0 }} campagne{{ ($stats['campagnes_actives'] ?? 0) > 1 ? 's' : '' }}<span class="stop">·</span>{{ number_format((float) ($stats['superficie_ha'] ?? 0), 1, ',', ' ') }} ha{{ $finTxt }}</span>
        <span class="cap">Éphéméride</span>
    </div>
@endif
