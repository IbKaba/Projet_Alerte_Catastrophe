@extends('layouts.app')
@section('title','Catégories')
@section('content')
<div class="page-heading"><div><span class="eyebrow">Administration</span><h1>Catégories de catastrophe</h1><p>Organisez les types de catastrophe utilisés lors de la création des signalements.</p></div></div>

<div class="admin-split">
    <section class="panel form-card">
        <div class="panel-heading"><div><h2>Nouvelle catégorie</h2><p>Une catégorie regroupe plusieurs types de catastrophe.</p></div></div>
        <form method="POST" action="{{ route('admin.categories.store') }}" class="form-stack">
            @csrf
            <label>Nom<input type="text" name="name" required maxlength="100" value="{{ old('name') }}"></label>
            <label>Description <span class="field-optional">optionnel</span><textarea name="description" rows="4" maxlength="1500">{{ old('description') }}</textarea></label>
            <input type="hidden" name="is_active" value="0">
            <label class="check"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', '1') === '1')> Catégorie active</label>
            <button class="button button-primary" type="submit">Créer la catégorie</button>
        </form>
    </section>

    <section class="panel">
        <div class="panel-heading"><div><h2>Catégories existantes</h2><p>Désactivez d’abord les types actifs avant de désactiver leur catégorie.</p></div></div>
        <div class="table-wrap"><table><thead><tr><th>Catégorie</th><th>Types</th><th>État</th><th>Actions</th></tr></thead><tbody>
        @forelse($categories as $category)
            <tr>
                <td><strong>{{ $category->name }}</strong><small>{{ \Illuminate\Support\Str::limit($category->description,70) ?: 'Aucune description' }}</small></td>
                <td>{{ $category->disasters_count }}</td>
                <td><span class="badge {{ $category->is_active ? 'badge-success' : 'badge-muted' }}">{{ $category->is_active ? 'Active' : 'Inactive' }}</span></td>
                <td class="table-actions">
                    <details class="inline-editor"><summary>Modifier</summary>
                        <form method="POST" action="{{ route('admin.categories.update',$category) }}" class="form-stack compact">
                            @csrf @method('PUT')
                            <input type="text" name="name" value="{{ $category->name }}" required maxlength="100">
                            <textarea name="description" rows="3" maxlength="1500">{{ $category->description }}</textarea>
                            <input type="hidden" name="is_active" value="0">
                            <label class="check"><input type="checkbox" name="is_active" value="1" @checked($category->is_active)> Active</label>
                            <button class="button button-secondary button-sm" type="submit">Enregistrer</button>
                        </form>
                    </details>
                    <form method="POST" action="{{ route('admin.categories.destroy',$category) }}" class="inline-form" data-confirm="Supprimer cette catégorie ?">
                        @csrf @method('DELETE')<button class="text-danger" type="submit">Supprimer</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="4" class="empty-cell"><strong>Aucune catégorie.</strong><span>Créez la première catégorie depuis le formulaire.</span></td></tr>
        @endforelse
        </tbody></table></div>
        <div class="pagination-wrap">{{ $categories->links() }}</div>
    </section>
</div>
@endsection
