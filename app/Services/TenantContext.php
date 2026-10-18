<?php

namespace App\Services;

use App\Models\Showroom;

/**
 * Service managing the active showroom tenant context during request lifecycle.
 */
class TenantContext
{
    protected ?Showroom $activeTenant = null;

    protected bool $bypassScope = false;

    /**
     * Set the currently active showroom tenant.
     */
    public function setTenant(?Showroom $showroom): void
    {
        $this->activeTenant = $showroom;
    }

    /**
     * Get the currently active showroom tenant model.
     */
    public function getTenant(): ?Showroom
    {
        return $this->activeTenant;
    }

    /**
     * Get the ID of the currently active showroom tenant.
     */
    public function getTenantId(): ?int
    {
        return $this->activeTenant?->id;
    }

    /**
     * Check if a showroom tenant context is currently resolved and active.
     */
    public function hasTenant(): bool
    {
        return $this->activeTenant !== null;
    }

    /**
     * Temporarily bypass tenant scoping (e.g. for super admin platform metrics).
     */
    public function setBypassScope(bool $bypass): void
    {
        $this->bypassScope = $bypass;
    }

    /**
     * Determine if tenant scoping is currently bypassed.
     */
    public function isBypassingScope(): bool
    {
        return $this->bypassScope;
    }
}
