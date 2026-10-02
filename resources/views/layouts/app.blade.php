<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#111827">
    <title>@yield('title', 'Alerte Catastrophe') · {{ config('app.name', 'Alerte Catastrophe') }}</title>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script defer src="{{ asset('js/app.js') }}"></script>
</head>
<body class="app-body">
<a class="skip-link" href="#app-content">Aller au contenu</a>
<div class="app-shell" data-app-shell>
    <aside class="sidebar" data-sidebar>
        <a class="brand" href="{{ route('dashboard') }}">
            <span class="brand-mark" aria-hidden="true"><i class="fa-solid fa-shield-halved"></i></span>
            <span><strong>Alerte Catastrophe</strong><small>Espace de gestion</small></span>
        </a>

        <nav class="sidebar-nav" aria-label="Navigation principale">
            <p class="nav-section-title">Mon espace</p>
            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}"><i class="fa-solid fa-chart-line" aria-hidden="true"></i> Tableau de bord</a>
            <a class="nav-link {{ request()->routeIs('alerts.index','alerts.show','alerts.edit') ? 'active' : '' }}" href="{{ route('alerts.index') }}"><i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i> Gestion des alertes</a>
            <a class="nav-link nav-link-accent {{ request()->routeIs('alerts.create') ? 'active' : '' }}" href="{{ route('alerts.create') }}"><i class="fa-solid fa-plus" aria-hidden="true"></i> Nouveau signalement</a>
            <a class="nav-link" href="{{ route('public.alerts.index') }}"><i class="fa-solid fa-globe" aria-hidden="true"></i> Site public</a>

            @if(auth()->user()->isAdmin())
                <p class="nav-section-title">Administration</p>
                <a class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" href="{{ route('admin.categories.index') }}"><i class="fa-solid fa-layer-group" aria-hidden="true"></i> Catégories</a>
                <a class="nav-link {{ request()->routeIs('admin.disasters.*') ? 'active' : '' }}" href="{{ route('admin.disasters.index') }}"><i class="fa-solid fa-cloud-bolt" aria-hidden="true"></i> Types de catastrophe</a>
                <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}"><i class="fa-solid fa-users-gear" aria-hidden="true"></i> Utilisateurs & rôles</a>
                <a class="nav-link {{ request()->routeIs('admin.audit.*') ? 'active' : '' }}" href="{{ route('admin.audit.index') }}"><i class="fa-solid fa-clipboard-list" aria-hidden="true"></i> Journal d’audit</a>
            @endif
        </nav>

        <div class="sidebar-user">
            <div class="avatar" aria-hidden="true">{{ strtoupper(mb_substr(auth()->user()->name, 0, 2)) }}</div>
            <div class="sidebar-user-copy">
                <strong>{{ auth()->user()->name }}</strong>
                <span>{{ auth()->user()->role->label() }}</span>
            </div>
            <a class="sidebar-profile-link" href="{{ route('profile.edit') }}" aria-label="Ouvrir mon profil">›</a>
        </div>
    </aside>

    <div class="main-area">
        <header class="topbar">
            <div class="topbar-left">
                <button class="icon-button mobile-menu" type="button" data-sidebar-toggle aria-label="Ouvrir le menu" aria-expanded="false">☰</button>
                <div class="topbar-context">
                    <span class="topbar-context-label">Espace sécurisé</span>
                    <strong>@yield('title', 'Tableau de bord')</strong>
                </div>
            </div>
            <div class="topbar-actions">
                <span class="role-pill">{{ auth()->user()->role->label() }}</span>
                <a class="button button-ghost button-sm" href="{{ route('profile.edit') }}">Mon profil</a>
                <form method="POST" action="{{ route('logout') }}">@csrf<button class="button button-dark button-sm" type="submit">Déconnexion</button></form>
            </div>
        </header>

        <main class="content" id="app-content">
            @if(session('status'))
                <div class="flash flash-success" data-flash role="status"><span>✓</span><div>{{ session('status') }}</div><button type="button" aria-label="Fermer" data-flash-close>×</button></div>
            @endif

            @if($errors->any())
                <div class="flash flash-danger" role="alert">
                    <span>!</span>
                    <div><strong>Veuillez corriger les points suivants :</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>
<div class="sidebar-backdrop" data-sidebar-backdrop></div>
</body>
</html>
