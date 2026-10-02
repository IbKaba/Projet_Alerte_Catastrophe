<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::query()->withCount('alerts')->latest();

        if ($request->filled('role') && Role::tryFrom((string) $request->input('role'))) {
            $query->where('role', $request->input('role'));
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->where(fn ($q) => $q
                ->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%"));
        }

        return view('admin.users.index', [
            'users' => $query->paginate(20)->withQueryString(),
            'roles' => Role::cases(),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user, AuditLogger $audit): RedirectResponse
    {
        $actor = $request->user();
        $newRole = Role::from($request->string('role')->toString());
        $newActive = $request->boolean('is_active');
        $before = ['role' => $user->role->value, 'is_active' => $user->is_active];

        $updatedUser = DB::transaction(function () use ($actor, $user, $newRole, $newActive): User {
            $target = User::query()->lockForUpdate()->findOrFail($user->id);

            if ($actor->id === $target->id && (! $newActive || $newRole !== Role::ADMIN)) {
                throw ValidationException::withMessages(['user' => 'Vous ne pouvez pas désactiver votre propre compte administrateur ni retirer votre propre rôle administrateur.']);
            }

            if ($target->hasRole(Role::ADMIN) && (! $newActive || $newRole !== Role::ADMIN)) {
                $otherActiveAdmins = User::query()
                    ->where('id', '!=', $target->id)
                    ->where('role', Role::ADMIN->value)
                    ->where('is_active', true)
                    ->lockForUpdate()
                    ->get(['id']);

                if ($otherActiveAdmins->isEmpty()) {
                    throw ValidationException::withMessages(['user' => 'Impossible de retirer ou désactiver le dernier administrateur actif.']);
                }
            }

            $target->update([
                'role' => $newRole,
                'is_active' => $newActive,
            ]);

            return $target;
        });

        $audit->log('user.access_updated', $updatedUser, [
            'before' => $before,
            'after' => ['role' => $newRole->value, 'is_active' => $newActive],
        ]);

        return back()->with('status', 'Droits utilisateur mis à jour.');
    }
}
