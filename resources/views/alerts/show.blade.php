@extends(auth()->user()->canModerate() ? 'layouts.app' : 'layouts.citizen')
@section('title','Alerte #'.$alert->id)
@section('content')
@php
    $disasterName = $alert->disaster?->name ?? 'Type de catastrophe indisponible';
    $categoryName = $alert->disaster?->category?->name ?? 'Catégorie indisponible';
@endphp
<div class="page-heading">
    <div>
        <span class="eyebrow">Dossier #{{ $alert->id }}</span>
        <h1>{{ $disasterName }}</h1>
        <p>{{ $categoryName }} · signalée {{ $alert->created_at?->diffForHumans() ?? 'à une date indisponible' }}</p>
    </div>
    <div class="heading-actions"><x-status-badge :status="$alert->status" /></div>
</div>

<div class="detail-grid">
    <section class="panel">
        <div class="panel-heading"><div><h2>Description du signalement</h2><p>Informations fournies par l’auteur.</p></div></div>
        <p class="long-copy">{{ $alert->description }}</p>
        <div class="detail-list">
            <div><span>Localisation</span><strong>{{ $alert->address ?: 'Non précisée' }}</strong></div>
            <div><span>Coordonnées GPS</span><strong>{{ $alert->latitude }}, {{ $alert->longitude }}</strong></div>
            <div><span>Signalé par</span><strong>{{ $alert->user?->name ?? 'Compte supprimé' }}</strong></div>
            <div><span>Dernière mise à jour</span><strong>{{ $alert->updated_at?->format('d/m/Y H:i') ?? '—' }}</strong></div>
        </div>
        <a class="button button-secondary button-sm" target="_blank" rel="noopener noreferrer" href="https://www.google.com/maps?q={{ $alert->latitude }},{{ $alert->longitude }}"><i class="fa-solid fa-map-location-dot"></i> Ouvrir la localisation sur la carte</a>

        @if($alert->rejection_reason)
            <div class="callout danger"><strong><i class="fa-solid fa-circle-exclamation"></i> Motif du rejet</strong><p>{{ $alert->rejection_reason }}</p></div>
        @endif

        @if($alert->disaster?->instructions)
            <div class="callout info"><strong><i class="fa-solid fa-circle-info"></i> Consignes utiles</strong><p>{{ $alert->disaster->instructions }}</p></div>
        @endif
    </section>

    <aside class="panel side-panel">
        <h2>Actions disponibles</h2>
        @can('update', $alert)
            <a class="button button-secondary button-full" href="{{ route('alerts.edit', $alert) }}"><i class="fa-solid fa-pen"></i> Modifier le signalement</a>
        @endcan
        @can('delete', $alert)
            <form method="POST" action="{{ route('alerts.destroy', $alert) }}" data-confirm="Supprimer définitivement cette alerte ?">
                @csrf @method('DELETE')
                <button class="button button-danger button-full" type="submit"><i class="fa-regular fa-trash-can"></i> Supprimer l’alerte</button>
            </form>
        @endcan

        @can('moderate', $alert)
            @php($nextStatuses = $alert->status->allowedTransitions())
            <hr>
            <div class="moderation-heading"><span class="eyebrow">Modération</span><h3>Décision</h3><p>Choisissez uniquement la prochaine étape du traitement.</p></div>
            <form method="POST" action="{{ route('moderation.alerts.update', $alert) }}" class="form-stack compact" data-moderation-form>
                @csrf @method('PATCH')
                <label>Nouveau statut
                    <select name="status" required data-status-select>
                        <option value="">Choisir une décision</option>
                        @foreach($nextStatuses as $status)
                            <option value="{{ $status->value }}" @selected(old('status') === $status->value)>
                                @if($status === \App\Enums\AlertStatus::VALIDATED) Valider l’alerte
                                @elseif($status === \App\Enums\AlertStatus::REJECTED) Rejeter le signalement
                                @elseif($status === \App\Enums\AlertStatus::RESOLVED) Marquer comme résolue
                                @endif
                            </option>
                        @endforeach
                    </select>
                </label>
                <label data-rejection-field hidden>Motif du rejet
                    <textarea name="rejection_reason" rows="4" maxlength="1200" placeholder="Expliquez précisément ce que l’auteur doit corriger...">{{ old('rejection_reason') }}</textarea>
                </label>
                <button class="button button-primary button-full" type="submit"><i class="fa-solid fa-check"></i> Appliquer la décision</button>
            </form>
        @elseif(auth()->user()->canModerate())
            <div class="callout"><strong>Aucune décision supplémentaire</strong><p>Cette alerte est dans un état qui ne permet plus de transition de modération.</p></div>
        @endcan
    </aside>
</div>

@if($alert->photos->isNotEmpty())
    <section class="panel">
        <div class="panel-heading"><div><h2>Photos du signalement</h2><p>{{ $alert->photos->count() }} photo(s) enregistrée(s)</p></div></div>
        <div class="evidence-grid">
            @foreach($alert->photos as $photo)
                <a href="{{ route('alert.photos.show', $photo) }}" target="_blank" rel="noopener"><img src="{{ route('alert.photos.show', $photo) }}" alt="Photo du signalement" loading="lazy"></a>
            @endforeach
        </div>
    </section>
@endif
@endsection
