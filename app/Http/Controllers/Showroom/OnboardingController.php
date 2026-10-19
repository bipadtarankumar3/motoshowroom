<?php

namespace App\Http\Controllers\Showroom;

use App\Http\Controllers\Controller;
use App\Models\Showroom;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OnboardingController extends Controller
{
    /**
     * Save user profile and showroom details from the modern onboarding modal.
     */
    public function save(Request $request, Showroom $showroom): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            // User profile fields (mandatory)
            'user_name' => ['required', 'string', 'max:255'],
            'user_phone' => ['required', 'string', 'max:30'],
            'user_city' => ['required', 'string', 'max:100'],
            'user_address' => ['nullable', 'string', 'max:255'],
            'user_bio' => ['nullable', 'string', 'max:1000'],

            // Showroom details (mandatory)
            'showroom_name' => ['required', 'string', 'max:255'],
            'showroom_email' => ['required', 'email', 'max:255'],
            'showroom_phone' => ['required', 'string', 'max:50'],
            'showroom_city' => ['required', 'string', 'max:100'],
            'showroom_state' => ['required', 'string', 'max:100'],
            'showroom_address_line1' => ['required', 'string', 'max:255'],
            'showroom_address_line2' => ['nullable', 'string', 'max:255'],
            'showroom_postal_code' => ['required', 'string', 'max:20'],
            'brand_color_primary' => ['nullable', 'string', 'regex:/^#[a-fA-F0-9]{6}$/'],
            'brand_color_accent' => ['nullable', 'string', 'regex:/^#[a-fA-F0-9]{6}$/'],
            'tagline' => ['nullable', 'string', 'max:255'],
            'about_text' => ['nullable', 'string', 'max:2000'],
        ]);

        // 1. Update user profile details & mark onboarding as completed
        $user->update([
            'name' => $validated['user_name'],
            'phone' => $validated['user_phone'],
            'city' => $validated['user_city'],
            'address' => $validated['user_address'] ?? $user->address,
            'bio' => $validated['user_bio'] ?? $user->bio,
            'onboarding_completed_at' => now(),
        ]);

        // 2. Update showroom details
        $settings = $showroom->settings ?? [];
        if (! empty($validated['tagline'])) {
            $settings['tagline'] = $validated['tagline'];
        }
        if (! empty($validated['about_text'])) {
            $settings['about_text'] = $validated['about_text'];
        }

        $showroomOld = $showroom->only(['name', 'email', 'phone', 'city', 'state', 'address_line1']);

        $showroom->update([
            'name' => $validated['showroom_name'],
            'email' => $validated['showroom_email'],
            'phone' => $validated['showroom_phone'],
            'city' => $validated['showroom_city'],
            'state' => $validated['showroom_state'],
            'address_line1' => $validated['showroom_address_line1'],
            'address_line2' => $validated['showroom_address_line2'] ?? $showroom->address_line2,
            'postal_code' => $validated['showroom_postal_code'],
            'brand_color_primary' => $validated['brand_color_primary'] ?? $showroom->brand_color_primary,
            'brand_color_accent' => $validated['brand_color_accent'] ?? $showroom->brand_color_accent,
            'settings' => $settings,
        ]);

        AuditLogger::log(
            action: 'showroom.onboarding_completed',
            auditable: $showroom,
            oldValues: $showroomOld,
            newValues: $showroom->only(['name', 'email', 'phone', 'city', 'state', 'address_line1']),
            tenantId: $showroom->id
        );

        return redirect()->route('showrooms.manage', $showroom)
            ->with('success', 'Profile and showroom details set up successfully! Welcome to your back-office.');
    }
}
