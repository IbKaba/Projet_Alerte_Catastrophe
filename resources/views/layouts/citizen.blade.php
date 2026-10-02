<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0f3d5e">
    <title>@yield('title', 'Mon espace') · {{ config('app.name', 'Alerte Catastrophe') }}</title>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script defer src="{{ asset('js/app.js') }}"></script>
</head>
<body class="citizen-body">
<a class="skip-link" href="#citizen-content">Aller au contenu</a>

<header class="citizen-navbar">
    <div class="citizen-nav-inner">
        <a class="citizen-brand" href="{{ route('citizen.home') }}">
            <span class="citizen-brand-icon"><i class="fa-solid fa-shield-halved"></i></span>
            <span><strong>Alerte Catastrophe</strong><small>Espace citoyen</small></span>
        </a>

        <nav class="citizen-desktop-nav" aria-label="Navigation citoyenne">
            <a class="{{ request()->routeIs('citizen.home') ? 'active' : '' }}" href="{{ route('citizen.home') }}"><i class="fa-solid fa-house"></i> Accueil</a>
            <a class="{{ request()->routeIs('alerts.index','alerts.show','alerts.edit') ? 'active' : '' }}" href="{{ route('alerts.index') }}"><i class="fa-regular fa-rectangle-list"></i> Mes signalements</a>
            <a href="{{ route('public.alerts.index') }}"><i class="fa-solid fa-bullhorn"></i> Alertes publiques</a>
        </nav>

        <div class="citizen-nav-actions">
            <a class="button button-primary button-sm" href="{{ route('alerts.create') }}"><i class="fa-solid fa-plus"></i> Signaler</a>
            <a class="citizen-profile-link" href="{{ route('profile.edit') }}" aria-label="Mon profil"><i class="fa-regular fa-user"></i></a>
            <form method="POST" action="{{ route('logout') }}">@csrf<button class="citizen-logout" type="submit" aria-label="Déconnexion"><i class="fa-solid fa-arrow-right-from-bracket"></i></button></form>
        </div>

        <details class="citizen-mobile-menu">
            <summary aria-label="Ouvrir le menu"><i class="fa-solid fa-bars"></i></summary>
            <div class="citizen-mobile-panel">
                <a href="{{ route('citizen.home') }}"><i class="fa-solid fa-house"></i> Accueil</a>
                <a href="{{ route('alerts.index') }}"><i class="fa-regular fa-rectangle-list"></i> Mes signalements</a>
                <a href="{{ route('alerts.create') }}"><i class="fa-solid fa-triangle-exclamation"></i> Nouveau signalement</a>
                <a href="{{ route('public.alerts.index') }}"><i class="fa-solid fa-bullhorn"></i> Alertes publiques</a>
                <a href="{{ route('profile.edit') }}"><i class="fa-regular fa-user"></i> Mon profil</a>
                <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit"><i class="fa-solid fa-arrow-right-from-bracket"></i> Déconnexion</button></form>
            </div>
        </details>
    </div>
</header>

<main class="citizen-main" id="citizen-content">
    @if(session('status'))
        <div class="flash flash-success citizen-flash" data-flash role="status"><i class="fa-solid fa-circle-check"></i><div>{{ session('status') }}</div><button type="button" aria-label="Fermer" data-flash-close>×</button></div>
    @endif

    @if($errors->any())
        <div class="flash flash-danger citizen-flash" role="alert">
            <i class="fa-solid fa-circle-exclamation"></i>
            <div><strong>Veuillez corriger les points suivants :</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
        </div>
    @endif

    @yield('content')
</main>

<nav class="citizen-mobile-bottom" aria-label="Navigation rapide mobile">
    <a class="{{ request()->routeIs('citizen.home') ? 'active' : '' }}" href="{{ route('citizen.home') }}"><i class="fa-solid fa-house"></i><span>Accueil</span></a>
    <a class="{{ request()->routeIs('alerts.index','alerts.show','alerts.edit') ? 'active' : '' }}" href="{{ route('alerts.index') }}"><i class="fa-regular fa-rectangle-list"></i><span>Mes alertes</span></a>
    <a class="citizen-bottom-primary {{ request()->routeIs('alerts.create') ? 'active' : '' }}" href="{{ route('alerts.create') }}"><i class="fa-solid fa-plus"></i><span>Signaler</span></a>
    <a href="{{ route('public.alerts.index') }}"><i class="fa-solid fa-bullhorn"></i><span>Public</span></a>
    <a class="{{ request()->routeIs('profile.*') ? 'active' : '' }}" href="{{ route('profile.edit') }}"><i class="fa-regular fa-user"></i><span>Profil</span></a>
</nav>
</body>
</html>
