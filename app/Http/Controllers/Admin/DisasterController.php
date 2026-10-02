<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DisasterRequest;
use App\Models\Disaster;
use App\Models\DisasterCategory;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DisasterController extends Controller
{
    public function index(): View
    {
        return view('admin.disasters.index', [
            'disasters' => Disaster::with('category')->withCount('alerts')->orderBy('name')->paginate(15),
            'categories' => DisasterCategory::query()->orderByDesc('is_active')->orderBy('name')->get(),
        ]);
    }

    public function store(DisasterRequest $request, AuditLogger $audit): RedirectResponse
    {
        $disaster = Disaster::create([
            ...$request->safe()->except('is_active'),
            'is_active' => $request->boolean('is_active'),
        ]);
        $audit->log('disaster.created', $disaster);

        return back()->with('status', 'Type de catastrophe créé.');
    }

    public function update(DisasterRequest $request, Disaster $disaster, AuditLogger $audit): RedirectResponse
    {
        $before = [
            'category_id' => $disaster->category_id,
            'name' => $disaster->name,
            'is_active' => $disaster->is_active,
        ];

        $disaster->update([
            ...$request->safe()->except('is_active'),
            'is_active' => $request->boolean('is_active'),
        ]);

        $audit->log('disaster.updated', $disaster, [
            'before' => $before,
            'after' => [
                'category_id' => $disaster->category_id,
                'name' => $disaster->name,
                'is_active' => $disaster->is_active,
            ],
        ]);

        return back()->with('status', 'Type de catastrophe mis à jour.');
    }

    public function destroy(Disaster $disaster, AuditLogger $audit): RedirectResponse
    {
        if ($disaster->alerts()->exists()) {
            return back()->withErrors(['disaster' => 'Ce type de catastrophe possède déjà des alertes et ne peut pas être supprimé. Désactivez-le plutôt.']);
        }

        $audit->log('disaster.deleted', $disaster);
        $disaster->delete();

        return back()->with('status', 'Type de catastrophe supprimé.');
    }
}
