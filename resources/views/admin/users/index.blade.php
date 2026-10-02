@extends('layouts.app')
@section('title','Utilisateurs & rôles')
@section('content')
<div class="page-heading"><div><span class="eyebrow">Contrôle d’accès</span><h1>Utilisateurs et rôles</h1><p>Gérez les rôles et l’état des comptes. Le dernier administrateur actif reste protégé par une règle serveur.</p></div></div>

<form method="GET" class="filter-bar" action="{{ route('admin.users.index') }}">
    <div class="filter-search"><label class="sr-only" for="user-search">Rechercher</label><input id="user-search" type="search" name="search" value="{{ request('search') }}" placeholder="Nom ou email"></div>
    <select name="role" aria-label="Filtrer par rôle"><option value="">Tous les rôles</option>@foreach($roles as $role)<option value="{{ $role->value }}" @selected(request('role') === $role->value)>{{ $role->label() }}</option>@endforeach</select>
    <button class="button button-dark button-sm" type="submit">Filtrer</button>
    <a class="button button-ghost button-sm" href="{{ route('admin.users.index') }}">Réinitialiser</a>
</form>

<div class="panel">
    <div class="table-wrap"><table><thead><tr><th>Utilisateur</th><th>Rôle</th><th>Alertes</th><th>État</th><th>Mise à jour des droits</th></tr></thead><tbody>
    @forelse($users as $user)
        <tr>
            <td><strong>{{ $user->name }}</strong><small>{{ $user->email }}{{ $user->phone ? ' · '.$user->phone : '' }}</small></td>
            <td><span class="badge badge-info">{{ $user->role->label() }}</span></td>
            <td>{{ $user->alerts_count }}</td>
            <td><span class="badge {{ $user->is_active ? 'badge-success' : 'badge-danger' }}">{{ $user->is_active ? 'Actif' : 'Désactivé' }}</span></td>
            <td>
                <form method="POST" action="{{ route('admin.users.update',$user) }}" class="access-form">
                    @csrf @method('PATCH')
                    <label class="sr-only" for="role-{{ $user->id }}">Rôle de {{ $user->name }}</label>
                    <select id="role-{{ $user->id }}" name="role">@foreach($roles as $role)<option value="{{ $role->value }}" @selected($user->role === $role)>{{ $role->label() }}</option>@endforeach</select>
                    <label class="sr-only" for="state-{{ $user->id }}">État du compte</label>
                    <select id="state-{{ $user->id }}" name="is_active"><option value="1" @selected($user->is_active)>Actif</option><option value="0" @selected(!$user->is_active)>Désactivé</option></select>
                    <button class="button button-secondary button-sm" type="submit">Appliquer</button>
                </form>
            </td>
        </tr>
    @empty
        <tr><td colspan="5" class="empty-cell"><strong>Aucun utilisateur trouvé.</strong><span>Modifiez vos critères de recherche.</span></td></tr>
    @endforelse
    </tbody></table></div>
    <div class="pagination-wrap">{{ $users->links() }}</div>
</div>
@endsection
