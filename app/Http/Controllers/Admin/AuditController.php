<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditController extends Controller
{
    public function index(Request $request): View
    {
        $query = ActivityLog::query()->with('user')->latest();

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->where(function ($q) use ($search): void {
                $q->where('action', 'like', "%{$search}%")
                    ->orWhere('entity_type', 'like', "%{$search}%")
                    ->orWhere('ip_address', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"));
            });
        }

        return view('admin.audit.index', ['logs' => $query->paginate(30)->withQueryString()]);
    }
}
