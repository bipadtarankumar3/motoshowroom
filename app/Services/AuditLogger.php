<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Showroom;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLogger
{
    /**
     * Record an audit event into the immutable audit ledger.
     *
     * @param  array<string, mixed>|null  $oldValues
     * @param  array<string, mixed>|null  $newValues
     */
    public static function log(
        string $action,
        ?Model $auditable = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?int $tenantId = null,
        ?User $user = null
    ): AuditLog {
        $currentUser = $user ?? Auth::user();

        // Infer tenant_id from auditable model or TenantContext if available
        if ($tenantId === null) {
            if ($auditable instanceof Model && isset($auditable->tenant_id)) {
                $tenantId = (int) $auditable->tenant_id;
            } elseif ($auditable instanceof Showroom) {
                $tenantId = (int) $auditable->id;
            } elseif (app()->bound(TenantContext::class) && app(TenantContext::class)->hasTenant()) {
                $tenantId = app(TenantContext::class)->getTenantId();
            }
        }

        return AuditLog::create([
            'tenant_id' => $tenantId,
            'user_id' => $currentUser?->id,
            'action' => $action,
            'auditable_type' => $auditable ? get_class($auditable) : null,
            'auditable_id' => $auditable?->getKey(),
            'ip_address' => Request::ip() ?? '127.0.0.1',
            'user_agent' => substr(Request::userAgent() ?? 'CLI/System', 0, 500),
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'created_at' => now(),
        ]);
    }
}
