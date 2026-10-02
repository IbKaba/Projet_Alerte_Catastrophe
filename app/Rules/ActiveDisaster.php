<?php

namespace App\Rules;

use App\Models\Disaster;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ActiveDisaster implements ValidationRule
{
    /**
     * Vérifie que le type de catastrophe est utilisable pour un nouveau signalement.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_numeric($value)) {
            return;
        }

        $isAvailable = Disaster::query()
            ->whereKey((int) $value)
            ->where('is_active', true)
            ->whereHas('category', fn ($query) => $query->where('is_active', true))
            ->exists();

        if (! $isAvailable) {
            $fail('Le type de catastrophe sélectionné est indisponible ou inactif.');
        }
    }
}
