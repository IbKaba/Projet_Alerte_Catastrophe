<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => mb_strtolower(trim((string) $this->input('email'))),
            'name' => trim((string) $this->input('name')),
            'phone' => $this->filled('phone') ? trim((string) $this->input('phone')) : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'email' => ['required', 'email:rfc', 'max:180', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30', 'regex:/^[0-9+() .-]{7,30}$/'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'terms' => ['accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.regex' => 'Le numéro de téléphone contient des caractères non autorisés.',
            'terms.accepted' => 'Vous devez accepter les conditions d’utilisation.',
        ];
    }
}
