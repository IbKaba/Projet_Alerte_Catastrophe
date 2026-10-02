@extends('layouts.guest')
@section('title', 'Accueil')
@section('content')
<section class="public-hero modern-hero">
    <div class="public-container public-hero-grid">
        <div class="public-hero-copy">
            <span class="public-kicker"><i class="fa-solid fa-shield-heart"></i> Plateforme de vigilance citoyenne</span>
            <h1>Signalez un danger. <span>Aidez à mieux informer.</span></h1>
            <p class="public-hero-lead">Une plateforme simple pour signaler une catastrophe avec une description, une localisation et des photos. Les signalements apparaissent immédiatement avec leur statut, puis sont vérifiés par l’équipe de modération.</p>
            <div class="public-hero-actions">
                @auth
                    <a class="button button-primary button-lg" href="{{ route('alerts.create') }}"><i class="fa-solid fa-camera"></i> Faire un signalement</a>
                    <a class="button button-public-outline button-lg" href="{{ auth()->user()->canModerate() ? route('dashboard') : route('citizen.home') }}"><i class="fa-regular fa-user"></i> Mon espace</a>
                @else
                    <a class="button button-primary button-lg" href="{{ route('register') }}"><i class="fa-solid fa-triangle-exclamation"></i> Faire un signalement</a>
                    <a class="button button-public-outline button-lg" href="{{ route('public.alerts.index') }}"><i class="fa-solid fa-bullhorn"></i> Voir les alertes</a>
                @endauth
            </div>
            <div class="public-trust-row" aria-label="Informations de la plateforme">
                <div><strong>{{ $activeAlertCount }}</strong><span>alertes actives</span></div>
                <div><strong>{{ $resolvedAlertCount }}</strong><span>situations résolues</span></div>
                <div><strong>{{ $categoryCount }}</strong><span>catégories suivies</span></div>
            </div>
        </div>

        <div class="hero-demo-card" aria-label="Aperçu d'un signalement">
            <div class="hero-demo-top">
                <span class="hero-demo-live"><i class="fa-solid fa-circle"></i> Signalement citoyen</span>
                <span class="hero-demo-status">En attente</span>
            </div>
            <div class="hero-demo-map">
                <span class="demo-street street-one"></span><span class="demo-street street-two"></span><span class="demo-street street-three"></span>
                <div class="hero-demo-pin"><i class="fa-solid fa-location-dot"></i></div>
            </div>
            <div class="hero-demo-info">
                <div class="hero-demo-icon"><i class="fa-solid fa-water"></i></div>
                <div><strong>Inondation signalée</strong><span><i class="fa-solid fa-location-dot"></i> Localisation enregistrée</span></div>
            </div>
            <div class="hero-demo-proof"><i class="fa-solid fa-camera"></i><span>Photo ajoutée</span><i class="fa-solid fa-circle-check"></i></div>
        </div>
    </div>
</section>

<section class="public-process" id="comment-signaler">
    <div class="public-container">
        <div class="public-section-heading centered">
            <span class="public-kicker neutral">Simple sur téléphone</span>
            <h2>Un signalement en trois étapes</h2>
            <p>Pas de formulaire compliqué : décrivez, localisez et ajoutez une photo si vous pouvez le faire sans danger.</p>
        </div>
        <div class="process-grid">
            <article class="process-card"><span class="process-number">01</span><div class="process-icon"><i class="fa-solid fa-pen-to-square"></i></div><h3>Décrivez</h3><p>Choisissez le type de catastrophe et expliquez simplement ce que vous observez.</p></article>
            <article class="process-card"><span class="process-number">02</span><div class="process-icon"><i class="fa-solid fa-location-crosshairs"></i></div><h3>Localisez</h3><p>Utilisez la position de votre téléphone ou indiquez un repère facile à reconnaître.</p></article>
            <article class="process-card"><span class="process-number">03</span><div class="process-icon"><i class="fa-solid fa-camera"></i></div><h3>Ajoutez des photos</h3><p>Prenez une photo directement avec le téléphone ou choisissez une image dans la galerie.</p></article>
        </div>
    </div>
</section>

<section class="public-latest">
    <div class="public-container">
        <div class="public-section-heading split">
            <div><span class="public-kicker neutral">Signalements récents</span><h2>Alertes publiques récentes</h2><p>Les nouvelles alertes citoyennes apparaissent immédiatement avec leur statut. Une alerte « En attente » n’est pas encore validée par la modération.</p></div>
            <a class="public-text-link" href="{{ route('public.alerts.index') }}">Voir toutes les alertes <i class="fa-solid fa-arrow-right"></i></a>
        </div>

        <div class="public-alert-grid">
            @forelse($latestAlerts as $alert)
                <a class="public-alert-card" href="{{ route('public.alerts.show', $alert) }}">
                    @if($alert->photos->isNotEmpty())
                        <img class="public-alert-card-image" src="{{ route('alert.photos.show', $alert->photos->first()) }}" alt="Photo du signalement" loading="lazy">
                    @endif
                    <div class="public-alert-card-body">
                        <div class="public-alert-card-head"><span class="public-category">{{ $alert->disaster?->category?->name ?? 'Alerte' }}</span><x-status-badge :status="$alert->status" /></div>
                        <h3>{{ $alert->disaster?->name ?? 'Signalement' }}</h3>
                        <p>{{ \Illuminate\Support\Str::limit($alert->description, 125) }}</p>
                        <div class="public-alert-card-foot"><span class="public-location"><i class="fa-solid fa-location-dot"></i> {{ \Illuminate\Support\Str::limit($alert->address ?: 'Localisation GPS disponible', 45) }}</span></div>
                        <span class="public-card-date">{{ $alert->created_at?->format('d/m/Y · H:i') ?? 'Date indisponible' }}</span>
                    </div>
                </a>
            @empty
                <div class="public-empty-state"><i class="fa-regular fa-bell"></i><div><strong>Aucune alerte publique récente</strong><p>Aucun signalement public n’est actuellement affiché.</p></div></div>
            @endforelse
        </div>
    </div>
</section>

<section class="public-safety-section">
    <div class="public-container public-safety-card">
        <div class="safety-symbol" aria-hidden="true"><i class="fa-solid fa-shield-heart"></i></div>
        <div><span class="public-kicker light">Priorité à votre sécurité</span><h2>Ne vous exposez jamais au danger pour prendre une photo.</h2><p>Mettez-vous à l’abri en priorité. Une photo ou une position précise ne justifie jamais de rester dans une zone dangereuse.</p></div>
        @auth<a class="button button-light button-lg" href="{{ route('alerts.create') }}">Créer une alerte</a>@else<a class="button button-light button-lg" href="{{ route('register') }}">Créer un compte</a>@endauth
    </div>
</section>
@endsection
