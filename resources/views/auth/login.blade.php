@extends('layouts.guest')
@section('title', 'Connexion')
@section('content')
<section class="auth-page compact-auth">
    <div class="auth-side-copy">
        <span class="public-kicker">Espace personnel</span>
        <h1>Retrouvez vos signalements et leur état de traitement.</h1>
        <p>La connexion donne accès à votre espace citoyen, modérateur ou administrateur selon les droits associés à votre compte.</p>
        <div class="auth-security-note"><span aria-hidden="true"><i class="fa-solid fa-shield-halved"></i></span><p>Les tentatives de connexion répétées sont limitées afin de protéger les comptes.</p></div>
    </div>

    <div class="auth-card">
        <div class="auth-heading left">
            <span class="auth-step">Accès sécurisé</span>
            <h2>Connexion</h2>
            <p>Entrez l’adresse email et le mot de passe associés à votre compte.</p>
        </div>
        @if($errors->any())<div class="form-error" role="alert">{{ $errors->first() }}</div>@endif
        <form method="POST" action="{{ route('login') }}" class="form-stack">
            @csrf
            <label>Email
                <input type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email" placeholder="nom@exemple.com">
            </label>
            <label>Mot de passe
                <input type="password" name="password" required autocomplete="current-password" placeholder="Votre mot de passe">
            </label>
            <div class="form-row between auth-options">
                <label class="check"><input type="checkbox" name="remember" value="1"> Se souvenir de moi</label>
                <a href="{{ route('password.request') }}">Mot de passe oublié ?</a>
            </div>
            <button class="button button-primary button-full button-lg" type="submit">Se connecter</button>
        </form>
        <p class="auth-foot">Pas encore de compte ? <a href="{{ route('register') }}">Créer un compte citoyen</a></p>
    </div>
</section>
@endsection
