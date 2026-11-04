@extends('layouts.app')

@section('titre', 'Connexion')

@section('contenu')
<div class="auth-layout">
    <div class="auth-visual">
        <svg class="auth-art" viewBox="0 0 220 220" fill="none" stroke="currentColor" stroke-width="1.1" aria-hidden="true">
            <path d="M108 18c44 26 66 68 60 112-44 11-86-9-103-53-17-43 5-68 43-59Z"/>
            <path d="M108 18c-6 48 5 90 60 112"/>
            <path d="M172 130c-32 6-59 27-69 64"/>
            <path d="M145 78c-18 2-34 11-44 25"/>
        </svg>
        <h1>La traçabilité, du verger au lot.</h1>
        <p>Centralisez les parcelles, les récoltes, les stocks et les activités agricoles dans une interface claire et rapide.</p>

        <ul class="auth-checklist">
            <li><x-icon name="check" /> Suivi de terrain</li>
            <li><x-icon name="check" /> Traçabilité des lots</li>
            <li><x-icon name="check" /> Alertes de stock</li>
        </ul>
    </div>

    <div class="auth-card">
        <div class="auth-header">
            <h2>Connexion</h2>
            <p class="hint">Accédez à l'espace de votre exploitation.</p>
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
