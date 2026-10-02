<?php

namespace App\Http\Middleware;

use App\Enums\Role;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user || ! $user->is_active) {
            abort(403, 'Votre compte est inactif ou non autorisé.');
        }

        $allowed = collect($roles)->map(fn (string $role) => Role::tryFrom($role)?->value ?? $role)->all();

        if (! $user->hasRole(...$allowed)) {
            abort(403, 'Vous ne disposez pas des droits nécessaires pour cette action.');
        }

        return $next($request);
    }
}
