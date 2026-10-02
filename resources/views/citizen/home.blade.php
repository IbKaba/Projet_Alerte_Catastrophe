@extends('layouts.citizen')
@section('title', 'Mon espace')
@section('content')
<section class="citizen-welcome">
    <div class="citizen-welcome-copy">
        <span class="citizen-kicker"><i class="fa-solid fa-hand-sparkles"></i> Bonjour {{ auth()->user()->name }}</span>
        <h1>Besoin de signaler une situation&nbsp;?</h1>
        <p>Quelques informations suffisent : le type d’événement, une description, votre position et, si possible, une photo. Votre signalement sera ensuite vérifié.</p>
        <div class="citizen-welcome-actions">
            <a class="button button-primary button-lg" href="{{ route('alerts.create') }}"><i class="fa-solid fa-camera"></i> Faire un signalement</a>
            <a class="button button-secondary button-lg" href="{{ route('public.alerts.index') }}"><i class="fa-solid fa-bullhorn"></i> Voir les alertes publiques</a>
        </div>
    </div>
    <div class="citizen-welcome-card" aria-hidden="true">
        <div class="citizen-illustration-icon"><i class="fa-solid fa-location-dot"></i></div>
        <div class="citizen-illustration-line"><span></span><span></span><span></span></div>
        <strong>Signaler simplement</strong>
        <small>Photo · Position · Description</small>
    </div>
</section>

<section class="citizen-guides" aria-label="Étapes du signalement">
    <article><span><i class="fa-solid fa-camera"></i></span><div><strong>1. Montrez la situation</strong><p>Prenez une photo directement ou choisissez une image dans votre galerie.</p></div></article>
    <article><span><i class="fa-solid fa-location-crosshairs"></i></span><div><strong>2. Indiquez le lieu</strong><p>Utilisez la position de votre téléphone ou renseignez un repère.</p></div></article>
    <article><span><i class="fa-solid fa-circle-check"></i></span><div><strong>3. Suivez le traitement</strong><p>Vous retrouvez ensuite le statut de votre signalement dans votre espace.</p></div></article>
</section>

<section class="citizen-recent-section">
    <div class="citizen-section-heading">
        <div><span class="citizen-kicker neutral">Votre activité</span><h2>Mes signalements récents</h2><p>{{ $alertCount === 0 ? 'Vous n’avez pas encore effectué de signalement.' : 'Retrouvez rapidement les derniers signalements que vous avez envoyés.' }}</p></div>
        @if($alertCount > 0)<a href="{{ route('alerts.index') }}">Voir tout <i class="fa-solid fa-arrow-right"></i></a>@endif
    </div>

    <div class="citizen-alert-list">
        @forelse($recentAlerts as $alert)
            <a class="citizen-alert-card" href="{{ route('alerts.show', $alert) }}">
                <div class="citizen-alert-icon"><i class="fa-solid fa-triangle-exclamation"></i></div>
                <div class="citizen-alert-copy">
                    <div class="citizen-alert-top"><strong>{{ $alert->disaster?->name ?? 'Signalement' }}</strong><x-status-badge :status="$alert->status" /></div>
                    <p>{{ \Illuminate\Support\Str::limit($alert->description, 105) }}</p>
                    <span><i class="fa-solid fa-location-dot"></i> {{ \Illuminate\Support\Str::limit($alert->address ?: 'Position GPS enregistrée', 55) }} · {{ $alert->created_at?->format('d/m/Y à H:i') ?? 'Date indisponible' }}</span>
                </div>
                <i class="fa-solid fa-chevron-right citizen-alert-chevron" aria-hidden="true"></i>
            </a>
        @empty
            <div class="citizen-empty-card">
                <span><i class="fa-regular fa-bell"></i></span>
                <div><h3>Aucun signalement pour le moment</h3><p>Si vous observez une situation à risque, vous pouvez la signaler en quelques étapes depuis votre téléphone.</p><a class="button button-primary" href="{{ route('alerts.create') }}">Créer mon premier signalement</a></div>
            </div>
        @endforelse
    </div>
</section>

<section class="citizen-safety-banner">
    <i class="fa-solid fa-shield-heart"></i>
    <div><strong>Votre sécurité passe avant la photo.</strong><p>Ne vous rapprochez jamais d’un danger uniquement pour compléter un signalement.</p></div>
</section>
@endsection
