<?php

namespace App\Http\Requests\Alert;

use App\Rules\ActiveDisaster;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;
use Illuminate\Validation\Validator;

class UpdateAlertRequest extends FormRequest
{
    public function authorize(): bool
    {
        $alert = $this->route('alert');

        return $alert && $this->user()?->can('update', $alert);
    }

    public function rules(): array
    {
        return [
            'disaster_id' => ['bail', 'required', 'integer', new ActiveDisaster],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'address' => ['nullable', 'string', 'max:255'],
            'google_place_id' => ['nullable', 'string', 'max:255'],
            'description' => ['required', 'string', 'min:20', 'max:3000'],
            'camera_photo' => ['nullable', File::types(['jpg', 'jpeg', 'png', 'webp'])->max('4mb')],
            'photos' => ['nullable', 'array', 'max:6'],
            'photos.*' => [File::types(['jpg', 'jpeg', 'png', 'webp'])->max('4mb')],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $existing = $this->route('alert')?->photos()->count() ?? 0;
            $galleryCount = count((array) $this->file('photos', []));
            $cameraCount = $this->file('camera_photo') ? 1 : 0;

            if (($existing + $galleryCount + $cameraCount) > 6) {
                $validator->errors()->add(
                    'photos',
                    "Vous pouvez conserver au maximum 6 photos. Vous en avez déjà {$existing}."
                );
            }
        });
    }
}
