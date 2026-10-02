<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate(['email' => ['required', 'email']]);

        // Ne pas révéler si une adresse existe dans la base : la réponse visible
        // reste identique, tandis que Laravel envoie le lien uniquement si le
        // compte correspondant existe et respecte les règles du broker.
        Password::sendResetLink($request->only('email'));

        return back()->with(
            'status',
            'Si un compte correspond à cette adresse, un lien de réinitialisation a été envoyé.'
        );
    }
}
