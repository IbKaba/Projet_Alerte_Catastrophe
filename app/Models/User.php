<?php

namespace App\Models;

use App\Enums\Role;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'phone', 'password', 'role', 'is_active'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => Role::class,
            'is_active' => 'boolean',
        ];
    }

    public function alerts(): HasMany
    {
        return $this->hasMany(Alert::class);
    }

    public function moderatedAlerts(): HasMany
    {
        return $this->hasMany(Alert::class, 'validated_by');
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function hasRole(Role|string ...$roles): bool
    {
        $current = $this->role instanceof Role ? $this->role->value : (string) $this->role;

        return collect($roles)->contains(function (Role|string $role) use ($current): bool {
            return ($role instanceof Role ? $role->value : $role) === $current;
        });
    }

    public function canModerate(): bool
    {
        return $this->role instanceof Role
            ? $this->role->canModerate()
            : in_array((string) $this->role, [Role::MODERATOR->value, Role::ADMIN->value], true);
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(Role::ADMIN);
    }
}
