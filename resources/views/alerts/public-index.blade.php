@extends('layouts.guest')
@section('title','Alertes publiques')
@section('content')
<section class="public-page-hero">
    <div class="public-container public-page-hero-inner">
        <div>
            <span class="public-kicker"><i class="fa-solid fa-bullhorn"></i> Signalements citoyens</span>
            <h1>Alertes publiques</h1>
            <p>Consultez les signalements récents. Les alertes en attente sont affichées avec leur statut afin de distinguer clairement celles qui ne sont pas encore validées. Les alertes rejetées restent privées.</p>
        </div>
        @auth
            <a class="button button-primary" href="{{ route('alerts.create') }}"><i class="fa-solid fa-plus"></i> Signaler une catastrophe</a>
        @else
            <a class="button button-primary" href="{{ route('register') }}"><i class="fa-solid fa-plus"></i> Faire un signalement</a>
        @endauth
    </div>
</section>

<section class="public-list-section">
    <div class="public-container">
        <form class="public-filter-panel simple" method="GET" action="{{ route('public.alerts.index') }}">
            <div class="public-filter-search">
                <label for="search">Rechercher</label>
                <input id="search" type="search" name="search" value="{{ request('search') }}" placeholder="Lieu, catastrophe, description...">
            </div>
            <div>
                <label for="category">Catégorie</label>
                <select id="category" name="category">
                    <option value="">Toutes</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" @selected((string) request('category') === (string) $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="status">Statut</label>
                <select id="status" name="status">
                    <option value="">Tous</option>
                    <option value="pending" @selected(request('status') === 'pending')>En attente</option>
                    <option value="validated" @selected(request('status') === 'validated')>Validée</option>
                    <option value="resolved" @selected(request('status') === 'resolved')>Résolue</option>
                </select>
            </div>
            <div class="public-filter-actions">
                <button class="button button-dark button-sm" type="submit"><i class="fa-solid fa-filter"></i> Filtrer</button>
                <a class="button button-ghost button-sm" href="{{ route('public.alerts.index') }}">Effacer</a>
            </div>
        </form>

        <div class="public-results-head">
            <div><strong>{{ $alerts->total() }}</strong><span>{{ $alerts->total() > 1 ? 'alertes trouvées' : 'alerte trouvée' }}</span></div>
            <span>Les plus récentes en premier</span>
        </div>

        <div class="public-alert-grid">
            @forelse($alerts as $alert)
                <a class="public-alert-card" href="{{ route('public.alerts.show', $alert) }}">
                    @if($alert->photos->isNotEmpty())
                        <img class="public-alert-card-image" src="{{ route('alert.photos.show', $alert->photos->first()) }}" alt="Photo du signalement" loading="lazy">
                    @endif
                    <div class="public-alert-card-body">
                        <div class="public-alert-card-head">
                            <span class="public-category">{{ $alert->disaster?->category?->name ?? 'Alerte' }}</span>
                            <x-status-badge :status="$alert->status" />
                        </div>
                        <h2>{{ $alert->disaster?->name ?? 'Signalement' }}</h2>
                        <p>{{ \Illuminate\Support\Str::limit($alert->description, 145) }}</p>
                        <div class="public-alert-card-foot"><span class="public-location"><i class="fa-solid fa-location-dot"></i> {{ \Illuminate\Support\Str::limit($alert->address ?: 'Localisation GPS disponible', 48) }}</span></div>
                        <span class="public-card-date">{{ $alert->created_at?->format('d/m/Y · H:i') ?? 'Date indisponible' }}</span>
                    </div>
                </a>
            @empty
                <div class="public-empty-state wide"><i class="fa-regular fa-face-smile"></i><div><strong>Aucun résultat</strong><p>Modifiez vos critères ou réinitialisez les filtres.</p></div></div>
            @endforelse
        </div>

        <div class="pagination-wrap public-pagination">{{ $alerts->links() }}</div>
    </div>
</section>
@endsection
