<?php

namespace App\Http\Controllers;

use App\Enums\AlertStatus;
use App\Http\Requests\Alert\StoreAlertRequest;
use App\Http\Requests\Alert\UpdateAlertRequest;
use App\Models\Alert;
use App\Models\AlertPhoto;
use App\Models\Disaster;
use App\Services\AlertPhotoService;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AlertController extends Controller
{
    public function index(Request $request): View
    {
        $query = Alert::query()->with(['disaster.category', 'user']);

        if (! $request->user()->canModerate()) {
            $query->where('user_id', $request->user()->id);
        }

        if ($request->filled('status') && AlertStatus::tryFrom((string) $request->input('status'))) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->where(function ($q) use ($search): void {
                $q->where('address', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('disaster', fn ($disaster) => $disaster->where('name', 'like', "%{$search}%"));
            });
        }

        return view('alerts.index', [
            'alerts' => $query->latest()->paginate(15)->withQueryString(),
        ]);
    }

    public function create(): View
    {
        return view('alerts.create', [
            'disasters' => Disaster::query()
                ->availableForReporting()
                ->with('category')
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function store(StoreAlertRequest $request, AlertPhotoService $photos, AuditLogger $audit): RedirectResponse
    {
        $alert = DB::transaction(function () use ($request, $photos): Alert {
            $alert = Alert::create([
                ...$request->safe()->except(['photos', 'camera_photo']),
                'user_id' => $request->user()->id,
                'status' => AlertStatus::PENDING,
            ]);

            $photos->store($alert, $this->photoUploads($request));

            return $alert;
        });

        $audit->log('alert.created', $alert);

        return redirect()->route('alerts.show', $alert)->with('status', 'Votre alerte a été enregistrée et attend une validation.');
    }

    public function show(Alert $alert): View
    {
        $this->authorize('view', $alert);
        $alert->loadMissing(['disaster.category', 'user', 'photos', 'moderator']);

        return view('alerts.show', compact('alert'));
    }

    public function edit(Alert $alert): View
    {
        $this->authorize('update', $alert);

        return view('alerts.edit', [
            'alert' => $alert->loadMissing('photos'),
            'disasters' => Disaster::query()
                ->availableForReporting()
                ->with('category')
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function update(UpdateAlertRequest $request, Alert $alert, AlertPhotoService $photos, AuditLogger $audit): RedirectResponse
    {
        DB::transaction(function () use ($request, $alert, $photos): void {
            $alert->update([
                ...$request->safe()->except(['photos', 'camera_photo']),
                'status' => AlertStatus::PENDING,
                'rejection_reason' => null,
                'validated_by' => null,
                'validated_at' => null,
                'resolved_at' => null,
            ]);

            $photos->store($alert, $this->photoUploads($request));
        });

        $audit->log('alert.updated', $alert);

        return redirect()->route('alerts.show', $alert)->with('status', 'Alerte mise à jour et renvoyée en validation.');
    }

    public function destroy(Alert $alert, AlertPhotoService $photos, AuditLogger $audit): RedirectResponse
    {
        $this->authorize('delete', $alert);
        $storedPhotos = $alert->photos()->get();

        DB::transaction(function () use ($alert, $audit): void {
            $audit->log('alert.deleted', $alert, [
                'status' => $alert->status->value,
            ]);
            $alert->delete();
        });

        $photos->deleteFiles($storedPhotos);

        return redirect()->route('alerts.index')->with('status', 'Alerte supprimée.');
    }

    public function destroyPhoto(Alert $alert, AlertPhoto $photo, AlertPhotoService $photos, AuditLogger $audit): RedirectResponse
    {
        $this->authorize('update', $alert);
        abort_unless($photo->alert_id === $alert->id, 404);

        $path = $photo->path;
        DB::transaction(function () use ($alert, $photo, $audit): void {
            $audit->log('alert.photo_deleted', $alert, [
                'photo_id' => $photo->id,
                'original_name' => $photo->original_name,
            ]);
            $photo->delete();
        });

        $photos->deletePath($path);

        return back()->with('status', 'Photo supprimée.');
    }

    /**
     * Fusionne la photo prise avec la caméra et les photos choisies dans la galerie.
     *
     * @return array<int, UploadedFile>
     */
    private function photoUploads(Request $request): array
    {
        $uploads = [];
        $cameraPhoto = $request->file('camera_photo');

        if ($cameraPhoto instanceof UploadedFile) {
            $uploads[] = $cameraPhoto;
        }

        foreach ((array) $request->file('photos', []) as $photo) {
            if ($photo instanceof UploadedFile) {
                $uploads[] = $photo;
            }
        }

        return $uploads;
    }
}
