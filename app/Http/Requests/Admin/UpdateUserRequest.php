<?php

namespace App\Http\Requests\Admin;

use App\Enums\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() === true;
    }

    public function rules(): array
    {
        return [
            'role' => ['required', Rule::enum(Role::class)],
            'is_active' => ['required', 'boolean'],
        ];
    }
}
