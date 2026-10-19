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
            'razorpay_enabled' => ['nullable', 'boolean'],
            'razorpay_key_id' => ['nullable', 'string', 'max:150'],
            'razorpay_key_secret' => ['nullable', 'string', 'max:150'],
            'razorpay_webhook_secret' => ['nullable', 'string', 'max:150'],
            'booking_token_amount' => ['nullable', 'numeric', 'min:100'],
        ]);

        $settings = $showroom->settings ?? [];
        $settings['razorpay_enabled'] = $request->boolean('razorpay_enabled');

        if ($request->filled('razorpay_key_id')) {
            $settings['razorpay_key_id'] = trim($request->input('razorpay_key_id'));
        } elseif ($request->has('razorpay_key_id') && empty($request->input('razorpay_key_id'))) {
            unset($settings['razorpay_key_id']);
        }

        // Only update secret if provided and not masked dummy
        if ($request->filled('razorpay_key_secret') && ! str_starts_with($request->input('razorpay_key_secret'), '••••')) {
            $settings['razorpay_key_secret'] = trim($request->input('razorpay_key_secret'));
        }

        if ($request->filled('razorpay_webhook_secret') && ! str_starts_with($request->input('razorpay_webhook_secret'), '••••')) {
            $settings['razorpay_webhook_secret'] = trim($request->input('razorpay_webhook_secret'));
        }

        if ($request->filled('booking_token_amount')) {
            $settings['booking_token_amount'] = (float) $request->input('booking_token_amount');
        }

        $coreFields = [
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'address_line1' => $validated['address_line1'] ?? null,
            'address_line2' => $validated['address_line2'] ?? null,
            'city' => $validated['city'],
            'state' => $validated['state'] ?? null,
            'postal_code' => $validated['postal_code'] ?? null,
            'settings' => $settings,
        ];

        $oldValues = $showroom->only(['email', 'phone', 'address_line1', 'address_line2', 'city', 'state', 'postal_code', 'settings']);
        $showroom->update($coreFields);

        AuditLogger::log(
            action: 'showroom.settings_updated',
            auditable: $showroom,
            oldValues: $oldValues,
            newValues: $showroom->only(['email', 'phone', 'address_line1', 'address_line2', 'city', 'state', 'postal_code']),
            tenantId: $showroom->id
        );

        return redirect()->route('showrooms.manage.settings', $showroom)
            ->with('success', 'Showroom settings & Razorpay gateway credentials updated successfully.');
    }
}
