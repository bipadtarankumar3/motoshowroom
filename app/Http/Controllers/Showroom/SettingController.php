<?php

namespace App\Http\Controllers\Showroom;

use App\Http\Controllers\Controller;
use App\Models\Showroom;
use App\Services\AuditLogger;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Show general showroom settings and staff management.
     */
    public function edit(Showroom $showroom): View
    {
        $showroom->load(['memberships.user', 'subscription.plan']);

        return view('showrooms.manage.settings', compact('showroom'));
    }

    /**
     * Update general showroom business details.
     */
    public function update(Request $request, Showroom $showroom): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'address_line1' => ['nullable', 'string', 'max:255'],
            'address_line2' => ['nullable', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
        ]);

        $oldValues = $showroom->only(array_keys($validated));
        $showroom->update($validated);

        AuditLogger::log(
            action: 'showroom.settings_updated',
            auditable: $showroom,
            oldValues: $oldValues,
            newValues: $showroom->only(array_keys($validated)),
            tenantId: $showroom->id
        );

        return redirect()->route('showrooms.manage.settings', $showroom)
            ->with('success', 'Showroom contact details updated successfully.');
    }
}
