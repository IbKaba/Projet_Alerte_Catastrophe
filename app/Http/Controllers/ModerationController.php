<?php

namespace App\Http\Controllers;

use App\Enums\AlertStatus;
use App\Http\Requests\Alert\ModerateAlertRequest;
use App\Models\Alert;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class ModerationController extends Controller
{
    public function update(ModerateAlertRequest $request, Alert $alert, AuditLogger $audit): RedirectResponse
    {
        $previousStatus = $alert->status;
        $nextStatus = AlertStatus::from($request->string('status')->toString());

        DB::transaction(function () use ($request, $alert, $nextStatus): void {
            $changes = [
                'status' => $nextStatus,
            ];

            if ($nextStatus === AlertStatus::VALIDATED) {
                $changes += [
                    'rejection_reason' => null,
                    'validated_by' => $request->user()->id,
                    'validated_at' => now(),
                    'resolved_at' => null,
                ];
            } elseif ($nextStatus === AlertStatus::REJECTED) {
                $changes += [
                    'rejection_reason' => $request->string('rejection_reason')->trim()->toString(),
                    'validated_by' => $request->user()->id,
                    'validated_at' => null,
                    'resolved_at' => null,
                ];
            } elseif ($nextStatus === AlertStatus::RESOLVED) {
                $changes += [
                    'rejection_reason' => null,
                    'resolved_at' => now(),
                ];
            }

            $alert->update($changes);
        });

        $audit->log('alert.moderated', $alert, [
            'from_status' => $previousStatus->value,
            'to_status' => $nextStatus->value,
        ]);

        return back()->with('status', 'Le statut de l’alerte a été mis à jour.');
    }
}
