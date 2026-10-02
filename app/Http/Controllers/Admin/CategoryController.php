<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CategoryRequest;
use App\Models\DisasterCategory;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        return view('admin.categories.index', [
            'categories' => DisasterCategory::withCount('disasters')->orderBy('name')->paginate(15),
        ]);
    }

    public function store(CategoryRequest $request, AuditLogger $audit): RedirectResponse
    {
        $category = DisasterCategory::create([
            ...$request->safe()->except('is_active'),
            'is_active' => $request->boolean('is_active'),
        ]);
        $audit->log('category.created', $category);

        return back()->with('status', 'Catégorie créée.');
    }

    public function update(CategoryRequest $request, DisasterCategory $category, AuditLogger $audit): RedirectResponse
    {
        $newActiveState = $request->boolean('is_active');

        if (! $newActiveState && $category->disasters()->where('is_active', true)->exists()) {
            return back()->withErrors([
                'category' => 'Désactivez d’abord les types de catastrophe actifs de cette catégorie.',
            ]);
        }

        $category->update([
            ...$request->safe()->except('is_active'),
            'is_active' => $newActiveState,
        ]);
        $audit->log('category.updated', $category, ['is_active' => $newActiveState]);

        return back()->with('status', 'Catégorie mise à jour.');
    }

    public function destroy(DisasterCategory $category, AuditLogger $audit): RedirectResponse
    {
        if ($category->disasters()->exists()) {
            return back()->withErrors(['category' => 'Cette catégorie contient des types de catastrophe et ne peut pas être supprimée.']);
        }

        $audit->log('category.deleted', $category);
        $category->delete();

        return back()->with('status', 'Catégorie supprimée.');
    }
}
