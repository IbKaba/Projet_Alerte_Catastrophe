<?php

namespace App\Http\Controllers;

use App\Enums\AlertStatus;
use App\Models\ActivityLog;
use App\Models\Alert;
use App\Models\Disaster;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();
        abort_unless($user->canModerate(), 403);

        $base = Alert::query();

        $stats = [
            'total' => (clone $base)->count(),
            'pending' => (clone $base)->where('status', AlertStatus::PENDING->value)->count(),
            'validated' => (clone $base)->where('status', AlertStatus::VALIDATED->value)->count(),
            'resolved' => (clone $base)->where('status', AlertStatus::RESOLVED->value)->count(),
        ];

        $recentAlerts = (clone $base)->with(['disaster.category', 'user'])->latest()->limit(7)->get();

        return view('dashboard', [
            'stats' => $stats,
            'recentAlerts' => $recentAlerts,
            'disasterCount' => Disaster::query()->where('is_active', true)->count(),
            'userCount' => $user->isAdmin() ? User::query()->count() : null,
            'recentLogs' => $user->isAdmin() ? ActivityLog::with('user')->latest()->limit(5)->get() : collect(),
        ]);
    }
}
