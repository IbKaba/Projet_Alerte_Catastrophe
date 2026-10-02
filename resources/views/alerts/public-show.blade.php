@extends('layouts.guest')
@section('title',$alert->disaster?->name ?? 'Alerte')
@section('content')
<section class="public-detail-page">
    <div class="public-container">
        <a class="public-back-link" href="{{ route('public.alerts.index') }}"><i class="fa-solid fa-arrow-left"></i> Retour aux alertes publiques</a>

        <article class="public-alert-detail">
            <div class="public-alert-detail-main">
                <div class="public-detail-topline"><span class="public-category">{{ $alert->disaster?->category?->name ?? 'Alerte' }}</span><span class="public-detail-id">Alerte #{{ $alert->id }}</span></div>
                <h1>{{ $alert->disaster?->name ?? 'Signalement' }}</h1>
                <div class="public-detail-badges"><x-status-badge :status="$alert->status" /></div>
                <p class="public-detail-description">{{ $alert->description }}</p>

                <dl class="public-detail-facts">
                    <div><dt>Localisation</dt><dd>{{ $alert->address ?: 'Coordonnées GPS disponibles' }}</dd></div>
                    <div><dt>Publié le</dt><dd>{{ $alert->created_at?->format('d/m/Y à H:i') ?? 'Date indisponible' }}</dd></div>
                    <div><dt>Dernière mise à jour</dt><dd>{{ $alert->updated_at?->format('d/m/Y à H:i') ?? 'Date indisponible' }}</dd></div>
                </dl>

                <div class="public-detail-actions"><a class="button button-primary" target="_blank" rel="noopener noreferrer" href="https://www.google.com/maps?q={{ $alert->latitude }},{{ $alert->longitude }}"><i class="fa-solid fa-map-location-dot"></i> Ouvrir la localisation</a></div>

                @if($alert->disaster?->instructions)
                    <div class="public-instructions"><span class="public-instructions-icon" aria-hidden="true"><i class="fa-solid fa-circle-info"></i></span><div><strong>Consignes associées</strong><p>{{ $alert->disaster->instructions }}</p></div></div>
                @endif
            </div>

            <aside class="public-alert-detail-side">
                @if($alert->photos->isNotEmpty())
                    <div class="public-evidence-main"><img src="{{ route('alert.photos.show', $alert->photos->first()) }}" alt="Photo principale du signalement"></div>
                    @if($alert->photos->count() > 1)
                        <div class="public-evidence-thumbs">@foreach($alert->photos->skip(1) as $photo)<a href="{{ route('alert.photos.show', $photo) }}" target="_blank" rel="noopener"><img src="{{ route('alert.photos.show', $photo) }}" alt="Photo du signalement" loading="lazy"></a>@endforeach</div>
                    @endif
                @else
                    <div class="public-no-photo"><i class="fa-regular fa-image"></i><strong>Aucune photo publique</strong><p>Le signalement reste consultable grâce à sa description et sa localisation.</p></div>
                @endif

                <div class="public-safety-note"><strong><i class="fa-solid fa-shield-heart"></i> Rappel de sécurité</strong><p>N’approchez pas de la zone uniquement pour vérifier cette alerte. Restez à distance d’un danger visible.</p></div>
            </aside>
        </article>
    </div>
</section>
@endsection
