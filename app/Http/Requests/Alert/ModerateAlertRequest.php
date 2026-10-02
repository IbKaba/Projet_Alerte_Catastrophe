<?php

namespace App\Http\Requests\Alert;

use App\Models\Alert;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ModerateAlertRequest extends FormRequest
{
    public function authorize(): bool
    {
        $alert = $this->route('alert');

        return $alert instanceof Alert && $this->user()?->can('moderate', $alert);
    }

    public function rules(): array
    {
        /** @var Alert|null $alert */
        $alert = $this->route('alert');
        $allowedStatuses = $alert instanceof Alert
            ? array_map(fn ($status) => $status->value, $alert->status->allowedTransitions())
            : [];

        return [
            'status' => ['bail', 'required', Rule::in($allowedStatuses)],
            'rejection_reason' => ['nullable', 'string', 'max:1200', 'required_if:status,rejected'],
        ];
    }

    public function messages(): array
    {
        return [
            'status.in' => 'Cette transition de statut n’est pas autorisée pour cette alerte.',
            'rejection_reason.required_if' => 'Indiquez le motif du rejet afin que le citoyen puisse corriger son signalement.',
        ];
    }
}
