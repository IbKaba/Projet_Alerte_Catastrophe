<?php

namespace App\Http\Controllers\Auth;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(RegisterRequest $request, AuditLogger $audit): RedirectResponse
    {
        $user = User::create([
            'name' => $request->string('name')->trim()->toString(),
            'email' => $request->string('email')->lower()->toString(),
            'phone' => $request->filled('phone') ? $request->string('phone')->trim()->toString() : null,
            'password' => $request->string('password')->toString(),
            'role' => Role::CITIZEN,
            'is_active' => true,
        ]);

        event(new Registered($user));
        Auth::login($user);
        request()->session()->regenerate();
        $audit->log('auth.register', $user);

        return redirect()->route('citizen.home')->with('status', 'Votre compte a été créé avec succès.');
    }
}
