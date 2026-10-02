<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CitizenHomeController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        abort_if($user->canModerate(), 404);

        $recentAlerts = Alert::query()
            ->where('user_id', $user->id)
            ->with(['disaster.category', 'photos'])
            ->latest()
            ->limit(5)
            ->get();

        return view('citizen.home', [
            'recentAlerts' => $recentAlerts,
            'alertCount' => Alert::query()->where('user_id', $user->id)->count(),
        ]);
    }
}
