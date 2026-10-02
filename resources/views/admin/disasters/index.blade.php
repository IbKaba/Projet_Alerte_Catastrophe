@extends('layouts.app')
@section('title','Types de catastrophe')
@section('content')
<div class="page-heading"><div><span class="eyebrow">Référentiel</span><h1>Types de catastrophe</h1><p>Gérez les événements proposés aux citoyens lors d’un signalement.</p></div></div>

<div class="admin-split">
    <form method="POST" action="{{ route('admin.disasters.store') }}" class="panel form-card sticky-panel">
        @csrf
        <div class="panel-heading"><div><h2>Nouveau type</h2><p>Ajoutez un type de catastrophe et ses consignes.</p></div></div>
        <label>Catégorie<select name="category_id" required><option value="">Sélectionner</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected((string) old('category_id') === (string) $category->id)>{{ $category->name }}{{ $category->is_active ? '' : ' (inactive)' }}</option>@endforeach</select></label>
        <label>Nom<input name="name" value="{{ old('name') }}" maxlength="150" required></label>
        <label>Description <span class="field-optional">optionnel</span><textarea name="description" rows="3" maxlength="2500">{{ old('description') }}</textarea></label>
        <label>Consignes <span class="field-optional">optionnel</span><textarea name="instructions" rows="4" maxlength="5000" placeholder="Consignes simples de sécurité associées à ce type...">{{ old('instructions') }}</textarea></label>
        <label class="check"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', true))> Type actif</label>
        <button class="button button-primary" type="submit"><i class="fa-solid fa-plus"></i> Ajouter</button>
    </form>

    <section class="panel">
        <div class="table-wrap">
            <table>
                <thead><tr><th>Type</th><th>Catégorie</th><th>Alertes</th><th>État</th><th>Actions</th></tr></thead>
                <tbody>
                @forelse($disasters as $disaster)
                    <tr>
                        <td><strong>{{ $disaster->name }}</strong><small>{{ \Illuminate\Support\Str::limit($disaster->description ?: 'Aucune description', 60) }}</small></td>
                        <td>{{ $disaster->category?->name ?? 'Catégorie supprimée' }}</td>
                        <td>{{ $disaster->alerts_count }}</td>
                        <td><span class="badge {{ $disaster->is_active ? 'badge-success' : 'badge-muted' }}">{{ $disaster->is_active ? 'Actif' : 'Inactif' }}</span></td>
                        <td>
                            <details class="inline-editor"><summary>Modifier</summary>
                                <form method="POST" action="{{ route('admin.disasters.update',$disaster) }}" class="form-stack compact">
                                    @csrf @method('PUT')
                                    <label>Catégorie<select name="category_id" required>@foreach($categories as $category)<option value="{{ $category->id }}" @selected($disaster->category_id === $category->id)>{{ $category->name }}{{ $category->is_active ? '' : ' (inactive)' }}</option>@endforeach</select></label>
                                    <label>Nom<input name="name" value="{{ $disaster->name }}" maxlength="150" required></label>
                                    <label>Description<textarea name="description" rows="3" maxlength="2500">{{ $disaster->description }}</textarea></label>
                                    <label>Consignes<textarea name="instructions" rows="4" maxlength="5000">{{ $disaster->instructions }}</textarea></label>
                                    <label class="check"><input type="checkbox" name="is_active" value="1" @checked($disaster->is_active)> Actif</label>
                                    <button class="button button-dark button-sm" type="submit">Enregistrer</button>
                                </form>
                            </details>
                            <form class="inline-form" method="POST" action="{{ route('admin.disasters.destroy',$disaster) }}" data-confirm="Supprimer ce type de catastrophe ?">@csrf @method('DELETE')<button class="text-danger" type="submit">Supprimer</button></form>
                        </td>
                    </tr>
                @empty<tr><td colspan="5" class="empty-cell">Aucun type de catastrophe.</td></tr>@endforelse
                </tbody>
            </table>
        </div>
        <div class="pagination-wrap">{{ $disasters->links() }}</div>
    </section>
</div>
@endsection
