<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => mb_strtolower(trim((string) $this->input('email'))),
        ]);
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
        ];
    }

    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey(), 60);

            throw ValidationException::withMessages([
                'email' => 'Les identifiants fournis sont incorrects.',
            ]);
        }

        if (! $this->user()?->is_active) {
            Auth::logout();
            RateLimiter::hit($this->throttleKey(), 60);

            throw ValidationException::withMessages([
                'email' => 'Ce compte a été désactivé. Contactez un administrateur.',
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    private function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));
        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => "Trop de tentatives. Réessayez dans {$seconds} seconde(s).",
        ]);
    }

    private function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')->toString()).'|'.$this->ip());
    }
}
