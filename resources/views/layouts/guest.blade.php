<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0f3d5e">
    <title>@yield('title', 'Alerte Catastrophe') · {{ config('app.name', 'Alerte Catastrophe') }}</title>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script defer src="{{ asset('js/app.js') }}"></script>
</head>
<body class="guest-body">
<a class="skip-link" href="#contenu">Aller au contenu</a>

<header class="public-site-header">
    <div class="public-header-inner">
        <a class="public-brand" href="{{ route('home') }}" aria-label="Alerte Catastrophe - Accueil">
            <span class="public-brand-mark" aria-hidden="true"><i class="fa-solid fa-shield-halved"></i></span>
            <span class="public-brand-copy">
                <strong>Alerte Catastrophe</strong>
                <small>Informer · Signaler · Suivre</small>
            </span>
        </a>

        <nav class="public-desktop-nav" aria-label="Navigation publique">
            <a class="{{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Accueil</a>
            <a class="{{ request()->routeIs('public.alerts.*') ? 'active' : '' }}" href="{{ route('public.alerts.index') }}">Alertes publiques</a>
            <a href="{{ route('home') }}#comment-signaler">Comment signaler ?</a>
        </nav>

        <div class="public-header-actions">
            @auth
                <a class="public-account-link" href="{{ auth()->user()->canModerate() ? route('dashboard') : route('citizen.home') }}"><i class="fa-regular fa-user"></i> Mon espace</a>
                <a class="button button-primary button-sm" href="{{ route('alerts.create') }}"><i class="fa-solid fa-plus"></i> Signaler</a>
            @else
                <a class="public-account-link" href="{{ route('login') }}">Connexion</a>
                <a class="button button-primary button-sm" href="{{ route('register') }}">Créer un compte</a>
            @endauth
        </div>

        <details class="public-mobile-menu">
            <summary aria-label="Ouvrir le menu"><i class="fa-solid fa-bars"></i><span>Menu</span></summary>
            <div class="public-mobile-menu-panel">
                <a href="{{ route('home') }}"><i class="fa-solid fa-house"></i> Accueil</a>
                <a href="{{ route('public.alerts.index') }}"><i class="fa-solid fa-bell"></i> Alertes publiques</a>
                <a href="{{ route('home') }}#comment-signaler"><i class="fa-regular fa-circle-question"></i> Comment signaler ?</a>
                @auth
                    <a href="{{ auth()->user()->canModerate() ? route('dashboard') : route('citizen.home') }}"><i class="fa-regular fa-user"></i> Mon espace</a>
                    <a href="{{ route('alerts.create') }}"><i class="fa-solid fa-triangle-exclamation"></i> Signaler une catastrophe</a>
                @else
                    <a href="{{ route('login') }}"><i class="fa-solid fa-right-to-bracket"></i> Connexion</a>
                    <a href="{{ route('register') }}"><i class="fa-solid fa-user-plus"></i> Créer un compte</a>
                @endauth
            </div>
        </details>
    </div>
</header>

@if(session('status'))
    <div class="public-flash" role="status" data-flash>
        <i class="fa-solid fa-circle-check" aria-hidden="true"></i>
        <span>{{ session('status') }}</span>
        <button type="button" aria-label="Fermer" data-flash-close>×</button>
    </div>
@endif

<main id="contenu">
    @yield('content')
</main>

<footer class="public-site-footer">
    <div class="public-footer-inner">
        <div class="public-footer-brand">
            <span class="public-brand-mark" aria-hidden="true"><i class="fa-solid fa-shield-halved"></i></span>
            <div>
                <strong>Alerte Catastrophe</strong>
                <p>Une plateforme simple pour signaler et suivre les situations à risque.</p>
            </div>
        </div>
        <div class="public-footer-links">
            <strong>Navigation</strong>
            <a href="{{ route('public.alerts.index') }}">Alertes publiques</a>
            <a href="{{ route('home') }}#comment-signaler">Comment signaler</a>
            @guest<a href="{{ route('login') }}">Connexion</a>@endguest
        </div>
        <div class="public-footer-emergency">
            <strong>Danger immédiat</strong>
            <p>Éloignez-vous d’abord de la zone dangereuse et contactez les secours disponibles dans votre localité.</p>
        </div>
    </div>
    <div class="public-footer-bottom">© {{ now()->year }} {{ config('app.name', 'Alerte Catastrophe') }} · Les alertes en attente sont identifiées clairement jusqu’à leur validation.</div>
</footer>
</body>
</html>
