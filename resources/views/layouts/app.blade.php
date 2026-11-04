<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#123024">
    <title>@yield('titre', 'Gestion fruitière') — AgroTrace</title>
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
@auth
    @php
        $navigation = [
            'Exploitation' => [
                ['route' => 'dashboard',       'motif' => 'dashboard',   'label' => 'Tableau de bord', 'icone' => 'home'],
                ['route' => 'parcelles.index', 'motif' => 'parcelles.*', 'label' => 'Parcelles',       'icone' => 'map'],
                ['route' => 'campagnes.index', 'motif' => 'campagnes.*', 'label' => 'Campagnes',       'icone' => 'calendar'],
                ['route' => 'activites.index', 'motif' => 'activites.*', 'label' => 'Activités',       'icone' => 'activity'],
                ['route' => 'recoltes.index',  'motif' => 'recoltes.*',  'label' => 'Récoltes',        'icone' => 'basket'],
                ['route' => 'intrants.index',  'motif' => 'intrants.*',  'label' => 'Stocks',          'icone' => 'box'],
                ['route' => 'lots.index',      'motif' => 'lots.*',      'label' => 'Lots',            'icone' => 'layers'],
            ],
            'Pilotage' => [
                ['route' => 'finances.index', 'motif' => 'finances.*', 'label' => 'Finances',     'icone' => 'coins', 'can' => 'voir-finances'],
                ['route' => 'users.index',    'motif' => 'users.*',    'label' => 'Utilisateurs', 'icone' => 'users', 'can' => 'gerer-utilisateurs'],
            ],
        ];
        $utilisateur = auth()->user();
        $initiale = mb_strtoupper(mb_substr($utilisateur->name, 0, 1));
        $exploitation = $utilisateur->exploitation?->nom ?? 'Exploitation';
    @endphp

    <div class="app-shell">
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-inner">
                <a class="sidebar-brand" href="{{ route('dashboard') }}" aria-label="AgroTrace — Tableau de bord">
                    <span class="brand-mark"><x-icon name="leaf" /></span>
                    <span>
                        <strong>AgroTrace</strong>
                        <small>{{ $exploitation }}</small>
                    </span>
                </a>

                <nav class="sidebar-nav" aria-label="Navigation principale">
                    @foreach ($navigation as $section => $liens)
                        @php($visibles = array_filter($liens, fn ($lien) => ! isset($lien['can']) || $utilisateur->can($lien['can'])))
                        @if (count($visibles))
                            <p class="nav-section">{{ $section }}</p>
                            @foreach ($visibles as $lien)
                                <a class="nav-link {{ request()->routeIs($lien['motif']) ? 'active' : '' }}"
                                   href="{{ route($lien['route']) }}"
                                   @if (request()->routeIs($lien['motif'])) aria-current="page" @endif>
                                    <x-icon :name="$lien['icone']" />
                                    <span>{{ $lien['label'] }}</span>
                                </a>
                            @endforeach
                        @endif
                    @endforeach
                </nav>
            </div>

            <div class="sidebar-user">
                <div class="user-pill">
                    <span class="avatar">{{ $initiale }}</span>
                    <div>
                        <strong>{{ $utilisateur->name }}</strong>
                        <span>{{ $utilisateur->role }}</span>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-secondary btn-block">
                        <x-icon name="logout" /> Déconnexion
                    </button>
                </form>
            </div>
        </aside>
        <div class="scrim" aria-hidden="true"></div>

        <div class="main-panel">
            <header class="topbar">
                <div class="topbar-left">
                    <button type="button" class="menu-toggle" data-menu-toggle aria-controls="sidebar" aria-expanded="false" aria-label="Ouvrir le menu">
                        <x-icon name="menu" />
                    </button>
                    <div class="topbar-heading">
                        <span class="topbar-title">@yield('titre', 'Tableau de bord')</span>
                        <span class="topbar-sub">{{ now()->locale('fr_FR')->translatedFormat('l j F Y') }}</span>
                    </div>
                </div>
                <div class="topbar-actions">
                    <span class="meta-badge"><x-icon name="leaf" /> {{ $exploitation }}</span>
                    <button type="button" class="icon-btn" data-theme-toggle aria-label="Basculer le thème clair/sombre" aria-pressed="false" title="Basculer le thème">
                        <x-icon name="moon" class="icon-moon" />
                        <x-icon name="sun" class="icon-sun" />
                    </button>
                </div>
            </header>

            <main class="page-content">
                @include('layouts.messages')
                @yield('contenu')
            </main>
        </div>
    </div>
@else
    <div class="guest-shell">
        <div class="guest-topbar">
            <a class="brand" href="{{ route('login') }}">
                <span class="brand-mark"><x-icon name="leaf" /></span>
                AgroTrace
            </a>
            <button type="button" class="icon-btn" data-theme-toggle aria-label="Basculer le thème clair/sombre" aria-pressed="false" title="Basculer le thème">
                <x-icon name="moon" class="icon-moon" />
                <x-icon name="sun" class="icon-sun" />
            </button>
        </div>
        <main>
            @include('layouts.messages')
            @yield('contenu')
        </main>
    </div>
@endauth
</body>
</html>
