<?php

namespace App\Policies;

use App\Enums\AlertStatus;
use App\Models\Alert;
use App\Models\User;

class AlertPolicy
{
    public function view(User $user, Alert $alert): bool
    {
        return $user->canModerate() || $alert->user_id === $user->id;
    }

    public function update(User $user, Alert $alert): bool
    {
        return $alert->user_id === $user->id
            && in_array($alert->status, [AlertStatus::PENDING, AlertStatus::REJECTED], true);
    }

    public function delete(User $user, Alert $alert): bool
    {
        return $user->isAdmin()
            || ($alert->user_id === $user->id
                && in_array($alert->status, [AlertStatus::PENDING, AlertStatus::REJECTED], true));
    }

    public function moderate(User $user, Alert $alert): bool
    {
        return $user->canModerate() && $alert->status->allowedTransitions() !== [];
    }
}
