<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#1b5e3f">
    <title>@yield('titre', 'Gestion fruitière') — AgroTrace</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
@auth
    @php
        $liens = [
            ['route' => 'dashboard',         'motif' => 'dashboard',    'label' => 'Tableau de bord', 'icone' => 'home'],
            ['route' => 'parcelles.index',   'motif' => 'parcelles.*',  'label' => 'Parcelles',       'icone' => 'map'],
            ['route' => 'campagnes.index',   'motif' => 'campagnes.*',  'label' => 'Campagnes',       'icone' => 'calendar'],
            ['route' => 'activites.index',   'motif' => 'activites.*',  'label' => 'Activités',       'icone' => 'activity'],
            ['route' => 'recoltes.index',    'motif' => 'recoltes.*',   'label' => 'Récoltes',        'icone' => 'basket'],
            ['route' => 'intrants.index',    'motif' => 'intrants.*',   'label' => 'Stocks',          'icone' => 'box'],
            ['route' => 'lots.index',        'motif' => 'lots.*',       'label' => 'Lots',            'icone' => 'layers'],
            ['route' => 'finances.index',    'motif' => 'finances.*',   'label' => 'Finances',        'icone' => 'coins', 'can' => 'voir-finances'],
            ['route' => 'users.index',       'motif' => 'users.*',      'label' => 'Utilisateurs',    'icone' => 'users', 'can' => 'gerer-utilisateurs'],
        ];
        $utilisateur = auth()->user();
        $initiale = mb_strtoupper(mb_substr($utilisateur->name, 0, 1));
    @endphp

    <div class="app-shell">
        <aside class="sidebar" id="sidebar">
            <div>
                <div class="sidebar-brand">
                    <span class="brand-mark">A</span>
                    <div>
                        <strong>AgroTrace</strong>
                        <small>{{ $utilisateur->exploitation?->nom ?? 'Exploitation' }}</small>
                    </div>
                </div>

                <nav class="sidebar-nav" aria-label="Navigation principale">
                    @foreach ($liens as $lien)
                        @if (! isset($lien['can']) || $utilisateur->can($lien['can']))
                            <a class="nav-link {{ request()->routeIs($lien['motif']) ? 'active' : '' }}"
                               href="{{ route($lien['route']) }}"
                               @if (request()->routeIs($lien['motif'])) aria-current="page" @endif>
                                <x-icon :name="$lien['icone']" />
                                <span>{{ $lien['label'] }}</span>
                            </a>
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
                    <span class="topbar-title">@yield('titre', 'Tableau de bord')</span>
                </div>
                <span class="meta-badge">{{ $utilisateur->exploitation?->nom }}</span>
            </header>

            <main class="page-content">
                @include('layouts.messages')
                @yield('contenu')
            </main>
        </div>
    </div>
@else
    <div class="guest-shell">
        <main>
            @include('layouts.messages')
            @yield('contenu')
        </main>
    </div>
@endauth
</body>
</html>
