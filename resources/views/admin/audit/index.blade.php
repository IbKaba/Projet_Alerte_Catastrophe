@extends('layouts.app')
@section('title','Journal d’audit')
@section('content')
<div class="page-heading"><div><span class="eyebrow">Traçabilité</span><h1>Journal d’audit</h1><p>Historique des connexions et des opérations sensibles enregistrées par l’application.</p></div></div>

<form method="GET" class="filter-bar" action="{{ route('admin.audit.index') }}">
    <div class="filter-search"><label class="sr-only" for="audit-search">Rechercher</label><input id="audit-search" type="search" name="search" value="{{ request('search') }}" placeholder="Action, utilisateur, adresse IP..."></div>
    <button class="button button-dark button-sm" type="submit">Rechercher</button>
    <a class="button button-ghost button-sm" href="{{ route('admin.audit.index') }}">Réinitialiser</a>
</form>

<div class="panel">
    <div class="table-wrap"><table><thead><tr><th>Date</th><th>Utilisateur</th><th>Action</th><th>Cible</th><th>IP</th></tr></thead><tbody>
    @forelse($logs as $log)
        <tr>
            <td>{{ $log->created_at?->format('d/m/Y H:i:s') ?? '—' }}</td>
            <td><strong>{{ $log->user?->name ?? 'Système' }}</strong><small>{{ $log->user?->email }}</small></td>
            <td><code>{{ $log->action }}</code></td>
            <td>{{ $log->entity_type ? class_basename($log->entity_type).' #'.$log->entity_id : '—' }}</td>
            <td><code>{{ $log->ip_address ?: '—' }}</code></td>
        </tr>
    @empty
        <tr><td colspan="5" class="empty-cell"><strong>Aucune activité enregistrée.</strong><span>Les actions sensibles apparaîtront ici.</span></td></tr>
    @endforelse
    </tbody></table></div>
    <div class="pagination-wrap">{{ $logs->links() }}</div>
</div>
@endsection
