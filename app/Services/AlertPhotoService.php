<?php

namespace App\Services;

use App\Models\Alert;
use App\Models\AlertPhoto;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

class AlertPhotoService
{
    /**
     * @param array<int, UploadedFile> $photos
     */
    public function store(Alert $alert, array $photos): void
    {
        if ($photos === []) {
            return;
        }

        $position = ($alert->photos()->max('position') ?? 0) + 1;
        $storedPaths = [];

        try {
            foreach ($photos as $photo) {
                if (! $photo instanceof UploadedFile || ! $photo->isValid()) {
                    continue;
                }

                $path = $photo->store('alerts/'.$alert->id, 'public');

                if (! is_string($path) || $path === '') {
                    throw new RuntimeException('Impossible d’enregistrer une photo du signalement.');
                }

                $storedPaths[] = $path;

                $alert->photos()->create([
                    'path' => $path,
                    'original_name' => $photo->getClientOriginalName(),
                    'position' => $position++,
                ]);
            }
        } catch (Throwable $exception) {
            if ($storedPaths !== []) {
                Storage::disk('public')->delete($storedPaths);
            }

            throw $exception;
        }
    }

    public function deletePath(string $path): void
    {
        Storage::disk('public')->delete($path);
    }

    /**
     * @param iterable<int, AlertPhoto> $photos
     */
    public function deleteFiles(iterable $photos): void
    {
        $paths = [];

        foreach ($photos as $photo) {
            $paths[] = $photo->path;
        }

        if ($paths !== []) {
            Storage::disk('public')->delete($paths);
        }
    }
}
