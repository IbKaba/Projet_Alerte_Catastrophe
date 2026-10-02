<?php

namespace App\Http\Controllers;

use App\Enums\AlertStatus;
use App\Models\AlertPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AlertPhotoController extends Controller
{
    public function show(Request $request, AlertPhoto $photo): StreamedResponse
    {
        $photo->loadMissing('alert');
        $alert = $photo->alert;

        abort_unless($alert, 404);

        $isPublic = in_array($alert->status, [
            AlertStatus::PENDING,
            AlertStatus::VALIDATED,
            AlertStatus::RESOLVED,
        ], true);

        $user = $request->user();
        $canViewPrivate = $user && (
            $user->canModerate() || (int) $alert->user_id === (int) $user->id
        );

        abort_unless($isPublic || $canViewPrivate, 404);

        $disk = Storage::disk('public');
        abort_unless($disk->exists($photo->path), 404);

        return $disk->response($photo->path, null, [
            'Cache-Control' => $isPublic ? 'public, max-age=3600' : 'private, no-store',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
