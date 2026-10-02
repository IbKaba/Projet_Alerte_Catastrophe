<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request, AuditLogger $audit): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();
        $audit->log('auth.login', $request->user());

        if (! $request->user()->canModerate()) {
            return redirect()->route('citizen.home');
        }

        return redirect()->intended(route('dashboard'));
    }

    public function destroy(Request $request, AuditLogger $audit): RedirectResponse
    {
        if ($request->user()) {
            $audit->log('auth.logout', $request->user());
        }

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('status', 'Vous êtes maintenant déconnecté.');
    }
}
