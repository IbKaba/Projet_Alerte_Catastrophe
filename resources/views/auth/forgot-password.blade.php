@extends('layouts.guest')
@section('title', 'Mot de passe oublié')
@section('content')
<section class="auth-single-page">
    <div class="auth-card">
        <a class="public-back-link" href="{{ route('login') }}">← Retour à la connexion</a>
        <div class="auth-heading left">
            <span class="auth-step">Récupération</span>
            <h2>Mot de passe oublié</h2>
            <p>Entrez votre email. Si un compte correspondant existe, Laravel enverra un lien de réinitialisation via le service email configuré.</p>
        </div>
        @if(session('status'))<div class="flash flash-success">{{ session('status') }}</div>@endif
        @if($errors->any())<div class="form-error" role="alert">{{ $errors->first() }}</div>@endif
        <form method="POST" action="{{ route('password.email') }}" class="form-stack">
            @csrf
            <label>Email
                <input type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email">
            </label>
            <button class="button button-primary button-full" type="submit">Envoyer le lien</button>
        </form>
    </div>
</section>
@endsection
