<?php

namespace App\Http\Controllers;

use App\Enums\AlertStatus;
use App\Models\Alert;
use App\Models\DisasterCategory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicAlertController extends Controller
{
    public function index(Request $request): View
    {
        $query = Alert::query()
            ->publiclyVisible()
            ->with(['disaster.category', 'photos'])
            ->latest();

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->where(function ($q) use ($search): void {
                $q->where('address', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('disaster', fn ($d) => $d->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('category') && ctype_digit((string) $request->input('category'))) {
            $categoryId = (int) $request->input('category');
            $query->whereHas('disaster', fn ($disaster) => $disaster->where('category_id', $categoryId));
        }

        if ($request->filled('status') && in_array((string) $request->input('status'), [
            AlertStatus::PENDING->value,
            AlertStatus::VALIDATED->value,
            AlertStatus::RESOLVED->value,
        ], true)) {
            $query->where('status', $request->input('status'));
        }

        return view('alerts.public-index', [
            'alerts' => $query->paginate(12)->withQueryString(),
            'categories' => DisasterCategory::query()->where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function show(Alert $alert): View
    {
        abort_unless(in_array($alert->status, [AlertStatus::PENDING, AlertStatus::VALIDATED, AlertStatus::RESOLVED], true), 404);
        $alert->loadMissing(['disaster.category', 'photos']);

        return view('alerts.public-show', compact('alert'));
    }
}
