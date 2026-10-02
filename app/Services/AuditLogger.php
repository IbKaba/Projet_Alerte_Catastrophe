<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuditLogger
{
    public function log(string $action, ?Model $entity = null, array $metadata = [], ?Request $request = null): void
    {
        $request ??= request();

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'entity_type' => $entity ? $entity::class : null,
            'entity_id' => $entity?->getKey(),
            'metadata' => $metadata ?: null,
            'ip_address' => $request?->ip(),
            'user_agent' => mb_substr((string) $request?->userAgent(), 0, 500) ?: null,
        ]);
    }
}
