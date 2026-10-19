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
        $heroCard = $settings['hero_card'] ?? [];
        $heroBanners = $settings['hero_banners'] ?? null;
        $categoriesShowcase = $settings['categories_showcase'] ?? null;
        $homepageSections = $settings['homepage_sections'] ?? null;
        $operatingHours = $showroom->getOperatingHours();
        $socialLinks = $showroom->getSocialLinks();

        $entitlements = [
            'has_360_viewer' => $showroom->canAccessFeature('has_360_viewer'),
            'has_ai_assistant' => $showroom->canAccessFeature('has_ai_assistant'),
            'has_custom_domain' => $showroom->canAccessFeature('has_custom_domain'),
            'has_careers_module' => $showroom->canAccessFeature('has_careers_module'),
            'has_scratch_cards' => $showroom->canAccessFeature('has_scratch_cards'),
        ];

        return view('showrooms.manage.website', compact(
            'showroom',
            'settings',
            'heroCard',
            'heroBanners',
            'categoriesShowcase',
            'homepageSections',
            'operatingHours',
            'socialLinks',
            'entitlements'
        ));
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
            'hero_badge' => ['nullable', 'string', 'max:100'],
            'hero_title_prefix' => ['nullable', 'string', 'max:100'],
            'hero_title_highlight' => ['nullable', 'string', 'max:100'],
            'hero_title_suffix' => ['nullable', 'string', 'max:100'],
            'hero_subtitle' => ['nullable', 'string', 'max:500'],
            'hero_btn_primary_text' => ['nullable', 'string', 'max:100'],
            'hero_btn_primary_url' => ['nullable', 'string', 'max:1024'],
            'hero_btn_secondary_text' => ['nullable', 'string', 'max:100'],
            'hero_btn_secondary_url' => ['nullable', 'string', 'max:1024'],
            'hero_feature_1_title' => ['nullable', 'string', 'max:100'],
            'hero_feature_1_sub' => ['nullable', 'string', 'max:100'],
            'hero_feature_2_title' => ['nullable', 'string', 'max:100'],
            'hero_feature_2_sub' => ['nullable', 'string', 'max:100'],
            'hero_feature_3_title' => ['nullable', 'string', 'max:100'],
            'hero_feature_3_sub' => ['nullable', 'string', 'max:100'],
            'hero_image' => ['nullable', 'string', 'max:1024'],
            'hero_banners' => ['nullable'],
            'categories_showcase' => ['nullable'],
            'homepage_sections' => ['nullable'],
        ]);

        $currentSettings = $showroom->settings ?? [];

        // Parse hero_banners if submitted as JSON or array
        $heroBanners = null;
        if ($request->has('hero_banners')) {
            $rawBanners = $request->input('hero_banners');
            if (is_string($rawBanners)) {
                $decoded = json_decode($rawBanners, true);
                if (is_array($decoded)) {
                    $heroBanners = $decoded;
                }
            } elseif (is_array($rawBanners)) {
                $heroBanners = $rawBanners;
            }
        }

        // Backward-compatible single hero card
        $heroCard = [
            'badge' => $validated['hero_badge'] ?? ($currentSettings['hero_card']['badge'] ?? 'NEW COLLECTION 2026'),
            'title_prefix' => $validated['hero_title_prefix'] ?? ($currentSettings['hero_card']['title_prefix'] ?? 'Fuel Your'),
            'title_highlight' => $validated['hero_title_highlight'] ?? ($currentSettings['hero_card']['title_highlight'] ?? 'Next'),
            'title_suffix' => $validated['hero_title_suffix'] ?? ($currentSettings['hero_card']['title_suffix'] ?? 'Adventure'),
            'subtitle' => $validated['hero_subtitle'] ?? ($currentSettings['hero_card']['subtitle'] ?? 'Explore the latest bikes with unmatched performance, style and technology.'),
            'btn_primary_text' => $validated['hero_btn_primary_text'] ?? ($currentSettings['hero_card']['btn_primary_text'] ?? 'Explore Bikes'),
            'btn_primary_url' => $validated['hero_btn_primary_url'] ?? ($currentSettings['hero_card']['btn_primary_url'] ?? null),
            'btn_secondary_text' => $validated['hero_btn_secondary_text'] ?? ($currentSettings['hero_card']['btn_secondary_text'] ?? 'Book a Test Ride'),
            'btn_secondary_url' => $validated['hero_btn_secondary_url'] ?? ($currentSettings['hero_card']['btn_secondary_url'] ?? null),
            'feature_1_title' => $validated['hero_feature_1_title'] ?? ($currentSettings['hero_card']['feature_1_title'] ?? 'Trusted Showroom'),
            'feature_1_sub' => $validated['hero_feature_1_sub'] ?? ($currentSettings['hero_card']['feature_1_sub'] ?? '100% Genuine Bikes'),
            'feature_2_title' => $validated['hero_feature_2_title'] ?? ($currentSettings['hero_card']['feature_2_title'] ?? 'After Sales Support'),
            'feature_2_sub' => $validated['hero_feature_2_sub'] ?? ($currentSettings['hero_card']['feature_2_sub'] ?? 'Always Here for You'),
            'feature_3_title' => $validated['hero_feature_3_title'] ?? ($currentSettings['hero_card']['feature_3_title'] ?? 'Best Price Guarantee'),
            'feature_3_sub' => $validated['hero_feature_3_sub'] ?? ($currentSettings['hero_card']['feature_3_sub'] ?? 'Unbeatable Offers'),
            'hero_image' => $validated['hero_image'] ?? ($currentSettings['hero_card']['hero_image'] ?? null),
        ];

        // If hero_banners are updated, ensure the first banner also mirrors into heroCard
        if (! empty($heroBanners) && isset($heroBanners[0])) {
            $first = $heroBanners[0];
            $heroCard['badge'] = $first['badge'] ?? $heroCard['badge'];
            $heroCard['title_prefix'] = $first['title_prefix'] ?? $heroCard['title_prefix'];
            $heroCard['title_highlight'] = $first['title_highlight'] ?? $heroCard['title_highlight'];
            $heroCard['title_suffix'] = $first['title_suffix'] ?? $heroCard['title_suffix'];
            $heroCard['subtitle'] = $first['subtitle'] ?? $heroCard['subtitle'];
            $heroCard['hero_image'] = $first['hero_image'] ?? $heroCard['hero_image'];
        }

        // Parse categories_showcase
        $categoriesShowcase = null;
        if ($request->has('categories_showcase')) {
            $rawCats = $request->input('categories_showcase');
            if (is_string($rawCats)) {
                $decoded = json_decode($rawCats, true);
                if (is_array($decoded)) {
                    $categoriesShowcase = $decoded;
                }
            } elseif (is_array($rawCats)) {
                $categoriesShowcase = $rawCats;
            }
        }

        // Parse homepage_sections
        $homepageSections = null;
        if ($request->has('homepage_sections')) {
            $rawSecs = $request->input('homepage_sections');
            if (is_string($rawSecs)) {
                $decoded = json_decode($rawSecs, true);
                if (is_array($decoded)) {
                    $homepageSections = $decoded;
                }
            } elseif (is_array($rawSecs)) {
                $homepageSections = $rawSecs;
            }
        }

        $newSettings = array_merge($currentSettings, [
            'headline' => $validated['headline'],
            'tagline' => $validated['tagline'],
            'about_text' => $validated['about_text'] ?? null,
            'whatsapp_number' => $validated['whatsapp_number'] ?? null,
            'google_maps_url' => $validated['google_maps_url'] ?? null,
            'operating_hours' => $request->input('operating_hours', $showroom->getOperatingHours()),
            'social_links' => $request->input('social_links', $showroom->getSocialLinks()),
            'hero_card' => $heroCard,
        ]);

        if ($heroBanners !== null) {
            $newSettings['hero_banners'] = $heroBanners;
        }

        if ($categoriesShowcase !== null) {
            $newSettings['categories_showcase'] = $categoriesShowcase;
        }

        if ($homepageSections !== null) {
            $newSettings['homepage_sections'] = $homepageSections;
        }

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
