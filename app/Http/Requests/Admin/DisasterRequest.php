<?php

namespace App\Http\Requests\Admin;

use App\Models\Disaster;
use App\Models\DisasterCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class DisasterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() === true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => trim((string) $this->input('name')),
        ]);
    }

    public function rules(): array
    {
        /** @var Disaster|null $disaster */
        $disaster = $this->route('disaster');

        return [
            'category_id' => ['required', 'integer', Rule::exists('disaster_categories', 'id')],
            'name' => [
                'required', 'string', 'max:150',
                Rule::unique('disasters', 'name')
                    ->where(fn ($query) => $query->where('category_id', $this->integer('category_id')))
                    ->ignore($disaster?->id),
            ],
            'description' => ['nullable', 'string', 'max:2500'],
            'instructions' => ['nullable', 'string', 'max:5000'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($validator->errors()->has('category_id')) {
                return;
            }

            $category = DisasterCategory::query()->find($this->integer('category_id'));
            if (! $category) {
                return;
            }

            /** @var Disaster|null $disaster */
            $disaster = $this->route('disaster');
            $sameCategory = $disaster instanceof Disaster && $disaster->category_id === $category->id;

            // Une catégorie inactive peut uniquement rester associée à un type déjà
            // inactif. On ne peut ni y déplacer un autre type, ni y activer un type.
            if (! $category->is_active && (! $sameCategory || $this->boolean('is_active'))) {
                $validator->errors()->add(
                    'category_id',
                    'Cette catégorie est inactive. Activez-la d’abord avant d’y créer, déplacer ou activer un type de catastrophe.'
                );
            }
        });
    }
}
