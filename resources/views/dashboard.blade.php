@extends('layouts.app')
@section('title', 'Tableau de bord')
@section('content')
<div class="page-heading">
    <div><span class="eyebrow">Vue opérationnelle</span><h1>Bonjour, {{ auth()->user()->name }}</h1><p>Supervisez les signalements, examinez les nouvelles alertes et suivez leur résolution.</p></div>
    <a class="button button-primary" href="{{ route('alerts.create') }}"><i class="fa-solid fa-plus"></i> Nouvelle alerte</a>
</div>

<div class="stats-grid">
    <div class="stat-card"><span><i class="fa-solid fa-bell"></i> Total alertes</span><strong>{{ $stats['total'] }}</strong><small>signalements enregistrés</small></div>
    <div class="stat-card warning"><span><i class="fa-regular fa-clock"></i> En attente</span><strong>{{ $stats['pending'] }}</strong><small>à examiner</small></div>
    <div class="stat-card success"><span><i class="fa-solid fa-circle-check"></i> Validées</span><strong>{{ $stats['validated'] }}</strong><small>publiées</small></div>
    <div class="stat-card info"><span><i class="fa-solid fa-flag-checkered"></i> Résolues</span><strong>{{ $stats['resolved'] }}</strong><small>clôturées</small></div>
</div>

<div class="dashboard-grid">
    <section class="panel">
        <div class="panel-heading"><div><h2>Alertes récentes</h2><p>Dernières activités enregistrées.</p></div><a href="{{ route('alerts.index') }}">Tout voir <i class="fa-solid fa-arrow-right"></i></a></div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Catastrophe</th><th>Localisation</th><th>Statut</th><th>Date</th><th></th></tr></thead>
                <tbody>
                @forelse($recentAlerts as $alert)
                    <tr>
                        <td><a class="table-title" href="{{ route('alerts.show', $alert) }}">{{ $alert->disaster?->name ?? 'Type indisponible' }}</a><small>{{ $alert->disaster?->category?->name ?? 'Catégorie indisponible' }}</small></td>
                        <td>{{ \Illuminate\Support\Str::limit($alert->address ?: 'Coordonnées GPS', 34) }}</td>
                        <td><x-status-badge :status="$alert->status" /></td>
                        <td>{{ $alert->created_at?->format('d/m/Y H:i') ?? '—' }}</td>
                        <td><a class="button button-ghost button-sm" href="{{ route('alerts.show', $alert) }}"><i class="fa-regular fa-eye"></i></a></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="empty-cell"><strong>Aucune alerte enregistrée.</strong><span>Les nouveaux signalements apparaîtront ici.</span></td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <aside class="panel side-panel">
        <div class="panel-heading"><div><h2>Repères</h2><p>Informations utiles</p></div></div>
        <div class="metric-line"><span>Types de catastrophe actifs</span><strong>{{ $disasterCount }}</strong></div>
        @if($userCount !== null)<div class="metric-line"><span>Utilisateurs enregistrés</span><strong>{{ $userCount }}</strong></div>@endif
        <a class="button button-secondary button-full" href="{{ route('alerts.index', ['status' => 'pending']) }}"><i class="fa-solid fa-inbox"></i> Voir les alertes en attente</a>
    </aside>
</div>

@if(auth()->user()->isAdmin() && $recentLogs->isNotEmpty())
<section class="panel">
    <div class="panel-heading"><div><h2>Activité administrative récente</h2><p>Derniers événements du journal d’audit.</p></div><a href="{{ route('admin.audit.index') }}">Journal complet <i class="fa-solid fa-arrow-right"></i></a></div>
    <div class="activity-list">@foreach($recentLogs as $log)<div class="activity-item"><span class="activity-dot"></span><div><strong>{{ $log->action }}</strong><p>{{ $log->user?->name ?? 'Système' }} · {{ $log->created_at?->diffForHumans() ?? 'Date indisponible' }}</p></div></div>@endforeach</div>
</section>
@endif
@endsection
