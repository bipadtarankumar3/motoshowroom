<?php

namespace App\Http\Controllers\Showroom;

use App\Http\Controllers\Controller;
use App\Models\Showroom;
use App\Services\AuditLogger;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class WebsiteBuilderController extends Controller
{
    /**
     * Show the website customization builder panel.
     */
    public function edit(Showroom $showroom): View
    {
        $settings = $showroom->settings ?? [];
        $operatingHours = $showroom->getOperatingHours();
        $socialLinks = $showroom->getSocialLinks();

        $entitlements = [
            'has_360_viewer' => $showroom->canAccessFeature('has_360_viewer'),
            'has_ai_assistant' => $showroom->canAccessFeature('has_ai_assistant'),
            'has_custom_domain' => $showroom->canAccessFeature('has_custom_domain'),
            'has_careers_module' => $showroom->canAccessFeature('has_careers_module'),
            'has_scratch_cards' => $showroom->canAccessFeature('has_scratch_cards'),
        ];

        return view('showrooms.manage.website', compact('showroom', 'settings', 'operatingHours', 'socialLinks', 'entitlements'));
    }

    /**
     * Update the showroom website branding, hours, and content.
     */
    public function update(Request $request, Showroom $showroom): RedirectResponse
    {
        $validated = $request->validate([
            'headline' => ['required', 'string', 'max:255'],
            'tagline' => ['required', 'string', 'max:255'],
            'about_text' => ['nullable', 'string'],
            'banner_url' => ['nullable', 'string', 'max:1024'],
            'logo_url' => ['nullable', 'string', 'max:1024'],
            'brand_color_primary' => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'brand_color_accent' => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'whatsapp_number' => ['nullable', 'string', 'max:30'],
            'google_maps_url' => ['nullable', 'string', 'max:1024'],
            'operating_hours' => ['nullable', 'array'],
            'social_links' => ['nullable', 'array'],
            'public_website_enabled' => ['nullable'],
        ]);

        $currentSettings = $showroom->settings ?? [];

        $newSettings = array_merge($currentSettings, [
            'headline' => $validated['headline'],
            'tagline' => $validated['tagline'],
            'about_text' => $validated['about_text'] ?? null,
            'whatsapp_number' => $validated['whatsapp_number'] ?? null,
            'google_maps_url' => $validated['google_maps_url'] ?? null,
            'operating_hours' => $request->input('operating_hours', $showroom->getOperatingHours()),
            'social_links' => $request->input('social_links', $showroom->getSocialLinks()),
        ]);

        $oldAttributes = [
            'brand_color_primary' => $showroom->brand_color_primary,
            'brand_color_accent' => $showroom->brand_color_accent,
            'public_website_enabled' => $showroom->public_website_enabled,
            'settings' => $showroom->settings,
        ];

        $showroom->update([
            'brand_color_primary' => $validated['brand_color_primary'] ?? $showroom->brand_color_primary,
            'brand_color_accent' => $validated['brand_color_accent'] ?? $showroom->brand_color_accent,
            'banner_url' => $validated['banner_url'] ?? $showroom->banner_url,
            'logo_url' => $validated['logo_url'] ?? $showroom->logo_url,
            'public_website_enabled' => $request->has('public_website_enabled'),
            'settings' => $newSettings,
        ]);

        AuditLogger::log(
            action: 'showroom.website_updated',
            auditable: $showroom,
            oldValues: $oldAttributes,
            newValues: [
                'headline' => $validated['headline'],
                'tagline' => $validated['tagline'],
                'public_website_enabled' => $showroom->public_website_enabled,
                'brand_color_primary' => $showroom->brand_color_primary,
            ],
            tenantId: $showroom->id
        );

        return redirect()->route('showrooms.manage.website', $showroom)
            ->with('success', 'Showroom storefront content & branding saved successfully!');
    }
}
