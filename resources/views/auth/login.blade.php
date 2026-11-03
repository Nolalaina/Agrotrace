@extends('layouts.app')

@section('titre', 'Connexion')

@section('contenu')
<div class="auth-layout">
    <div class="auth-visual">
        <div class="auth-panel-badge">AgroTrace</div>
        <h1>Suivi précis des cultures et des opérations.</h1>
        <p>Centralisez les parcelles, les récoltes, les stocks et les activités agricoles dans une interface claire et rapide.</p>

        <ul class="auth-checklist">
            <li>Suivi de terrain</li>
            <li>Traçabilité des lots</li>
            <li>Alertes de stock</li>
        </ul>
    </div>

    <div class="auth-card">
        <div class="auth-header">
            <span class="eyebrow">Connexion</span>
            <h2>Bienvenue</h2>
        </div>

        <form method="POST" action="{{ route('login') }}" class="auth-form">
            @csrf

            <div class="field">
                <label class="label" for="email">Adresse e-mail</label>
                <input class="input" id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="email" required autofocus>
            </div>

            <div class="field">
                <label class="label" for="password">Mot de passe</label>
                <input class="input" id="password" type="password" name="password" autocomplete="current-password" required>
            </div>

            <div class="form-row">
                <label class="check">
                    <input type="checkbox" name="remember" value="1">
                    <span>Se souvenir de moi</span>
                </label>
                <a href="{{ route('register') }}" class="text-link">Créer un compte</a>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Se connecter</button>
        </form>
    </div>
</div>
@endsection
