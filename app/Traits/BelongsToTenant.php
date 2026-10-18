<?php

namespace App\Traits;

use App\Models\Showroom;
use App\Scopes\TenantScope;
use App\Services\TenantContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Trait providing tenant scoping, automatic tenant_id injection, and showroom relationship.
 */
trait BelongsToTenant
{
    /**
     * Boot the trait to attach global tenant scope and auto-populate tenant_id on create.
     */
    public static function bootBelongsToTenant(): void
    {
        static::addGlobalScope(new TenantScope);

        static::creating(function ($model): void {
            if (empty($model->tenant_id)) {
                $tenantContext = app(TenantContext::class);
                if ($tenantContext->hasTenant()) {
                    $model->tenant_id = $tenantContext->getTenantId();
                }
            }
        });
    }

    /**
     * Relationship pointing to the parent showroom tenant.
     */
    public function showroom(): BelongsTo
    {
        return $this->belongsTo(Showroom::class, 'tenant_id');
    }

    /**
     * Scope to bypass tenant filtering explicitly for administrative queries.
     */
    public function scopeWithoutTenantScope(Builder $query): Builder
    {
        return $query->withoutGlobalScope(TenantScope::class);
    }
}
