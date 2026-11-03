@extends('layouts.app')

@section('titre', 'Inscription')

@section('contenu')
<div class="auth-layout">
    <div class="auth-visual">
        <div class="auth-panel-badge">Nouvel espace</div>
        <h1>Créez votre exploitation et lancez le suivi en quelques minutes.</h1>
        <p>Configurez votre ferme, ajoutez vos parcelles et suivez chaque étape de culture avec plus de clarté.</p>
    </div>

    <div class="auth-card">
        <div class="auth-header">
            <span class="eyebrow">Inscription</span>
            <h2>Créer mon compte</h2>
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
