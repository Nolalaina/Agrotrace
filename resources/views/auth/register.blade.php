@extends('layouts.app')

@section('titre', 'Inscription')

@section('contenu')
<div class="auth-layout">
    <div class="auth-visual">
        <svg class="auth-art" viewBox="0 0 220 220" fill="none" stroke="currentColor" stroke-width="1.1" aria-hidden="true">
            <path d="M112 14c46 28 69 71 62 117-46 11-90-10-108-56-17-45 6-71 46-61Z"/>
            <path d="M112 14c-7 50 5 94 62 117"/>
            <path d="M179 130c-34 7-62 29-72 67"/>
            <path d="M150 76c-19 2-36 12-46 27"/>
        </svg>
        <h1>Créez votre exploitation en quelques minutes.</h1>
        <p>Configurez votre ferme, ajoutez vos parcelles et suivez chaque étape de culture avec plus de clarté.</p>

        <ul class="auth-checklist">
            <li><x-icon name="check" /> Parcelles et campagnes</li>
            <li><x-icon name="check" /> Récoltes et lots</li>
            <li><x-icon name="check" /> Stocks et finances</li>
        </ul>
    </div>

    <div class="auth-card">
        <div class="auth-header">
            <h2>Créer mon compte</h2>
            <p class="hint">Votre exploitation sera prête immédiatement.</p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="auth-form">
            @csrf

            <div class="field">
                <label class="label" for="name">Nom complet</label>
                <input class="input" id="name" type="text" name="name" value="{{ old('name') }}" autocomplete="name" required autofocus>
            </div>

            <div class="field">
                <label class="label" for="email">Adresse e-mail</label>
                <input class="input" id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="email" required>
            </div>

            <div class="field">
                <label class="label" for="exploitation_nom">Nom de l'exploitation</label>
                <input class="input" id="exploitation_nom" type="text" name="exploitation_nom" value="{{ old('exploitation_nom') }}" required>
            </div>

            <div class="field">
                <label class="label" for="localisation">Localisation</label>
                <input class="input" id="localisation" type="text" name="localisation" value="{{ old('localisation') }}" required>
            </div>

            <div class="field">
                <label class="label" for="password">Mot de passe</label>
                <input class="input" id="password" type="password" name="password" autocomplete="new-password" minlength="8" required>
                <span class="hint">8 caractères minimum.</span>
            </div>

            <div class="field">
                <label class="label" for="password_confirmation">Confirmer le mot de passe</label>
                <input class="input" id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password" required>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Créer mon compte</button>

            <p class="auth-footer">
                Déjà inscrit ?
                <a href="{{ route('login') }}" class="text-link">Se connecter</a>
            </p>
        </form>
    </div>
</div>
@endsection
