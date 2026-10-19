<?php

namespace App\Http\Controllers\Showroom;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Showroom;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class BannerManagementController extends Controller
{
    /**
     * Display a listing of hero and promotional banners.
     */
    public function index(Request $request, Showroom $showroom): View
    {
        $banners = $showroom->banners()->get();

        return view('showrooms.manage.banners.index', compact('showroom', 'banners'));
    }

    /**
     * Store a newly created banner with optional file upload.
     */
    public function store(Request $request, Showroom $showroom): RedirectResponse
    {
        $validated = $request->validate([
            'badge' => ['nullable', 'string', 'max:100'],
            'title_prefix' => ['nullable', 'string', 'max:100'],
            'title_highlight' => ['nullable', 'string', 'max:100'],
            'title_suffix' => ['nullable', 'string', 'max:100'],
            'subtitle' => ['nullable', 'string', 'max:500'],
            'btn_primary_text' => ['nullable', 'string', 'max:100'],
            'btn_primary_url' => ['nullable', 'string', 'max:1024'],
            'btn_secondary_text' => ['nullable', 'string', 'max:100'],
            'btn_secondary_url' => ['nullable', 'string', 'max:1024'],
            'feature_1_title' => ['nullable', 'string', 'max:100'],
            'feature_1_sub' => ['nullable', 'string', 'max:100'],
            'feature_2_title' => ['nullable', 'string', 'max:100'],
            'feature_2_sub' => ['nullable', 'string', 'max:100'],
            'feature_3_title' => ['nullable', 'string', 'max:100'],
            'feature_3_sub' => ['nullable', 'string', 'max:100'],
            'image_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,svg', 'max:10240'],
            'image_url' => ['nullable', 'string', 'max:1024'],
            'is_active' => ['nullable'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $imagePath = null;
        if ($request->hasFile('image_file')) {
            $imagePath = $request->file('image_file')->store("showrooms/{$showroom->id}/banners", 'public');
        }

        $showroom->banners()->create([
            'badge' => $validated['badge'] ?? 'NEW COLLECTION 2026',
            'title_prefix' => $validated['title_prefix'] ?? 'Fuel Your',
            'title_highlight' => $validated['title_highlight'] ?? 'Next',
            'title_suffix' => $validated['title_suffix'] ?? 'Adventure',
            'subtitle' => $validated['subtitle'] ?? 'Explore the latest bikes with unmatched performance, style and technology.',
            'btn_primary_text' => $validated['btn_primary_text'] ?? 'Explore Bikes',
            'btn_primary_url' => $validated['btn_primary_url'] ?? '',
            'btn_secondary_text' => $validated['btn_secondary_text'] ?? 'Book a Test Ride',
            'btn_secondary_url' => $validated['btn_secondary_url'] ?? '',
            'feature_1_title' => $validated['feature_1_title'] ?? 'Trusted Showroom',
            'feature_1_sub' => $validated['feature_1_sub'] ?? '100% Genuine Bikes',
            'feature_2_title' => $validated['feature_2_title'] ?? 'After Sales Support',
            'feature_2_sub' => $validated['feature_2_sub'] ?? 'Always Here for You',
            'feature_3_title' => $validated['feature_3_title'] ?? 'Best Price Guarantee',
            'feature_3_sub' => $validated['feature_3_sub'] ?? 'Unbeatable Offers',
            'image_path' => $imagePath,
            'image_url' => $validated['image_url'] ?? null,
            'is_active' => $request->boolean('is_active', true),
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
        ]);

        return redirect()->route('showrooms.manage.banners.index', $showroom)
            ->with('success', 'Hero banner added successfully.');
    }

    /**
     * Update the specified banner.
     */
    public function update(Request $request, Showroom $showroom, Banner $banner): RedirectResponse
    {
        abort_unless($banner->tenant_id === $showroom->id, 404);

        $validated = $request->validate([
            'badge' => ['nullable', 'string', 'max:100'],
            'title_prefix' => ['nullable', 'string', 'max:100'],
            'title_highlight' => ['nullable', 'string', 'max:100'],
            'title_suffix' => ['nullable', 'string', 'max:100'],
            'subtitle' => ['nullable', 'string', 'max:500'],
            'btn_primary_text' => ['nullable', 'string', 'max:100'],
            'btn_primary_url' => ['nullable', 'string', 'max:1024'],
            'btn_secondary_text' => ['nullable', 'string', 'max:100'],
            'btn_secondary_url' => ['nullable', 'string', 'max:1024'],
            'feature_1_title' => ['nullable', 'string', 'max:100'],
            'feature_1_sub' => ['nullable', 'string', 'max:100'],
            'feature_2_title' => ['nullable', 'string', 'max:100'],
            'feature_2_sub' => ['nullable', 'string', 'max:100'],
            'feature_3_title' => ['nullable', 'string', 'max:100'],
            'feature_3_sub' => ['nullable', 'string', 'max:100'],
            'image_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,svg', 'max:10240'],
            'image_url' => ['nullable', 'string', 'max:1024'],
            'is_active' => ['nullable'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $imagePath = $banner->image_path;
        if ($request->hasFile('image_file')) {
            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = $request->file('image_file')->store("showrooms/{$showroom->id}/banners", 'public');
        }

        $banner->update([
            'badge' => $validated['badge'] ?? $banner->badge,
            'title_prefix' => $validated['title_prefix'] ?? $banner->title_prefix,
            'title_highlight' => $validated['title_highlight'] ?? $banner->title_highlight,
            'title_suffix' => $validated['title_suffix'] ?? $banner->title_suffix,
            'subtitle' => $validated['subtitle'] ?? $banner->subtitle,
            'btn_primary_text' => $validated['btn_primary_text'] ?? $banner->btn_primary_text,
            'btn_primary_url' => $validated['btn_primary_url'] ?? $banner->btn_primary_url,
            'btn_secondary_text' => $validated['btn_secondary_text'] ?? $banner->btn_secondary_text,
            'btn_secondary_url' => $validated['btn_secondary_url'] ?? $banner->btn_secondary_url,
            'feature_1_title' => $validated['feature_1_title'] ?? $banner->feature_1_title,
            'feature_1_sub' => $validated['feature_1_sub'] ?? $banner->feature_1_sub,
            'feature_2_title' => $validated['feature_2_title'] ?? $banner->feature_2_title,
            'feature_2_sub' => $validated['feature_2_sub'] ?? $banner->feature_2_sub,
            'feature_3_title' => $validated['feature_3_title'] ?? $banner->feature_3_title,
            'feature_3_sub' => $validated['feature_3_sub'] ?? $banner->feature_3_sub,
            'image_path' => $imagePath,
            'image_url' => $validated['image_url'] ?? $banner->image_url,
            'is_active' => $request->boolean('is_active', true),
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
        ]);

        return redirect()->route('showrooms.manage.banners.index', $showroom)
            ->with('success', 'Hero banner updated successfully.');
    }

    /**
     * Remove the specified banner.
     */
    public function destroy(Showroom $showroom, Banner $banner): RedirectResponse
    {
        abort_unless($banner->tenant_id === $showroom->id, 404);

        if ($banner->image_path && Storage::disk('public')->exists($banner->image_path)) {
            Storage::disk('public')->delete($banner->image_path);
        }

        $banner->delete();

        return redirect()->route('showrooms.manage.banners.index', $showroom)
            ->with('success', 'Hero banner deleted successfully.');
    }

    /**
     * Toggle the active status of the specified banner.
     */
    public function toggleActive(Showroom $showroom, Banner $banner): RedirectResponse
    {
        abort_unless($banner->tenant_id === $showroom->id, 404);

        $banner->update([
            'is_active' => ! $banner->is_active,
        ]);

        $status = $banner->is_active ? 'activated' : 'deactivated';

        return redirect()->route('showrooms.manage.banners.index', $showroom)
            ->with('success', "Banner {$status} successfully.");
    }
}
