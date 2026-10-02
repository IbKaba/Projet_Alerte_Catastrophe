<?php

namespace App\Http\Controllers;

use App\Http\Requests\Profile\UpdatePasswordRequest;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('profile.edit', ['user' => $request->user()]);
    }

    public function update(UpdateProfileRequest $request, AuditLogger $audit): RedirectResponse
    {
        $user = $request->user();
        $data = $request->validated();

        if ($data['email'] !== $user->email) {
            $data['email_verified_at'] = null;
        }

        $user->update($data);
        $audit->log('profile.updated', $user);

        return back()->with('status', 'Profil mis à jour.');
    }

    public function updatePassword(UpdatePasswordRequest $request, AuditLogger $audit): RedirectResponse
    {
        $user = $request->user();
        $user->forceFill([
            'password' => Hash::make($request->string('password')->toString()),
            'remember_token' => Str::random(60),
        ])->save();

        $request->session()->regenerate();
        $audit->log('profile.password_changed', $user);

        return back()->with('status', 'Mot de passe modifié.');
    }
}
