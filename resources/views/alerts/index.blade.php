@extends(auth()->user()->canModerate() ? 'layouts.app' : 'layouts.citizen')
@section('title', auth()->user()->canModerate() ? 'Gestion des alertes' : 'Mes signalements')
@section('content')
@php($isStaff = auth()->user()->canModerate())
<div class="page-heading">
    <div>
        <span class="eyebrow">{{ $isStaff ? 'Suivi opérationnel' : 'Mon historique' }}</span>
        <h1>{{ $isStaff ? 'Gestion des alertes' : 'Mes signalements' }}</h1>
        <p>{{ $isStaff ? 'Recherchez un signalement, filtrez son statut et ouvrez son dossier.' : 'Retrouvez vos signalements et suivez simplement leur traitement.' }}</p>
    </div>
    <a class="button button-primary" href="{{ route('alerts.create') }}"><i class="fa-solid fa-plus"></i> Nouveau signalement</a>
</div>

<form class="filter-bar" method="GET" action="{{ route('alerts.index') }}">
    <div class="filter-search">
        <label class="sr-only" for="alert-search">Rechercher</label>
        <input id="alert-search" type="search" name="search" value="{{ request('search') }}" placeholder="Catastrophe, adresse, description...">
    </div>
    <select name="status" aria-label="Filtrer par statut">
        <option value="">Tous les statuts</option>
        @foreach(\App\Enums\AlertStatus::cases() as $status)
            <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
        @endforeach
    </select>
    <button class="button button-dark button-sm" type="submit"><i class="fa-solid fa-filter"></i> Filtrer</button>
    <a class="button button-ghost button-sm" href="{{ route('alerts.index') }}">Réinitialiser</a>
</form>

@if($isStaff)
    <div class="panel">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr><th>Type</th><th>Signalé par</th><th>Localisation</th><th>Statut</th><th>Date</th><th>Action</th></tr>
                </thead>
                <tbody>
                @forelse($alerts as $alert)
                    <tr>
                        <td><a class="table-title" href="{{ route('alerts.show',$alert) }}">{{ $alert->disaster?->name ?? 'Type indisponible' }}</a><small>{{ $alert->disaster?->category?->name ?? 'Catégorie indisponible' }}</small></td>
                        <td>{{ $alert->user?->name ?? 'Compte supprimé' }}</td>
                        <td>{{ \Illuminate\Support\Str::limit($alert->address ?: 'Coordonnées GPS',35) }}</td>
                        <td><x-status-badge :status="$alert->status" /></td>
                        <td>{{ $alert->created_at?->format('d/m/Y H:i') ?? '—' }}</td>
                        <td><a class="button button-ghost button-sm" href="{{ route('alerts.show',$alert) }}"><i class="fa-regular fa-eye"></i> Ouvrir</a></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="empty-cell"><strong>Aucune alerte trouvée.</strong><span>Modifiez les filtres ou créez un nouveau signalement.</span></td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="pagination-wrap">{{ $alerts->links() }}</div>
    </div>
@else
    <section class="citizen-history" aria-label="Mes signalements">
        @forelse($alerts as $alert)
            <article class="citizen-history-card">
                <a class="citizen-history-main" href="{{ route('alerts.show', $alert) }}">
                    <span class="citizen-history-icon"><i class="fa-solid fa-triangle-exclamation"></i></span>
                    <span class="citizen-history-copy">
                        <span class="citizen-history-title-row">
                            <strong>{{ $alert->disaster?->name ?? 'Signalement' }}</strong>
                            <x-status-badge :status="$alert->status" />
                        </span>
                        <span class="citizen-history-description">{{ \Illuminate\Support\Str::limit($alert->description, 130) }}</span>
                        <span class="citizen-history-meta">
                            <span><i class="fa-solid fa-location-dot"></i> {{ \Illuminate\Support\Str::limit($alert->address ?: 'Position GPS enregistrée', 55) }}</span>
                            <span><i class="fa-regular fa-clock"></i> {{ $alert->created_at?->format('d/m/Y à H:i') ?? 'Date indisponible' }}</span>
                        </span>
                    </span>
                    <span class="citizen-history-open">Ouvrir <i class="fa-solid fa-chevron-right"></i></span>
                </a>
            </article>
        @empty
            <div class="citizen-empty-card">
                <span><i class="fa-regular fa-bell"></i></span>
                <div><h3>Aucun signalement trouvé</h3><p>Créez un signalement ou modifiez vos critères de recherche.</p><a class="button button-primary" href="{{ route('alerts.create') }}">Faire un signalement</a></div>
            </div>
        @endforelse

        <div class="pagination-wrap citizen-pagination">{{ $alerts->links() }}</div>
    </section>
@endif
@endsection
