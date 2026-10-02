<?php

namespace App\Http\Controllers;

use App\Enums\AlertStatus;
use App\Models\Alert;
use App\Models\DisasterCategory;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $latestAlerts = Alert::query()
            ->publiclyVisible()
            ->with(['disaster.category', 'photos'])
            ->latest()
            ->limit(6)
            ->get();

        return view('welcome', [
            'latestAlerts' => $latestAlerts,
            'categoryCount' => DisasterCategory::query()->where('is_active', true)->count(),
            'activeAlertCount' => Alert::query()->whereIn('status', [AlertStatus::PENDING->value, AlertStatus::VALIDATED->value])->count(),
            'resolvedAlertCount' => Alert::query()->where('status', AlertStatus::RESOLVED->value)->count(),
        ]);
    }
}
