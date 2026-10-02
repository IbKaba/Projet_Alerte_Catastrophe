@extends('layouts.guest')
@section('title', 'Créer un compte')
@section('content')
<section class="auth-page">
    <div class="auth-side-copy">
        <span class="public-kicker">Compte citoyen</span>
        <h1>Signalez et suivez vos alertes depuis un espace personnel.</h1>
        <p>Votre compte permet de retrouver l’état de vos signalements, de corriger une alerte rejetée et de conserver un historique fiable.</p>
        <ul class="auth-benefits">
            <li><span>✓</span> Suivi du statut de chaque signalement</li>
            <li><span>✓</span> Ajout de localisation et de preuves visuelles</li>
            <li><span>✓</span> Correction d’un signalement si nécessaire</li>
        </ul>
    </div>

    <div class="auth-card auth-card-wide">
        <div class="auth-heading left">
            <span class="auth-step">Inscription</span>
            <h2>Créer votre compte</h2>
            <p>Remplissez les informations ci-dessous. Les champs marqués sont nécessaires au suivi de vos alertes.</p>
        </div>

        @if($errors->any())
            <div class="form-error" role="alert"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
        @endif

        <form method="POST" action="{{ route('register') }}" class="form-stack">
            @csrf
            <div class="form-grid two">
                <label>Nom complet
                    <input type="text" name="name" value="{{ old('name') }}" required maxlength="120" autocomplete="name">
                </label>
                <label>Téléphone <span class="field-optional">optionnel</span>
                    <input type="tel" name="phone" value="{{ old('phone') }}" placeholder="+224 ..." autocomplete="tel">
                </label>
            </div>
            <label>Email
                <input type="email" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="nom@exemple.com">
            </label>
            <div class="form-grid two">
                <label>Mot de passe
                    <input type="password" name="password" required minlength="6" maxlength="128" autocomplete="new-password">
                    <small>6 caractères minimum. Un mot de passe plus long reste recommandé.</small>
                </label>
                <label>Confirmation
                    <input type="password" name="password_confirmation" required minlength="6" maxlength="128" autocomplete="new-password">
                </label>
            </div>
            <label class="check consent-check">
                <input type="checkbox" name="terms" value="1" required>
                <span>J’accepte l’utilisation de mes données pour le traitement et le suivi de mes alertes.</span>
            </label>
            <button class="button button-primary button-full button-lg" type="submit">Créer mon compte</button>
        </form>
        <p class="auth-foot">Déjà inscrit ? <a href="{{ route('login') }}">Se connecter</a></p>
    </div>
</section>
@endsection
