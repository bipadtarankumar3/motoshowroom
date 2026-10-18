<?php

namespace App\Http\Controllers\Showroom;

use App\Http\Controllers\Controller;
use App\Models\Bike;
use App\Models\Showroom;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class PublicBike360Controller extends Controller
{
    /**
     * Display the dedicated immersive full-screen 360 viewer for this motorcycle.
     */
    public function viewer(Showroom $showroom, Bike $bike): View|RedirectResponse
    {
        abort_if($bike->tenant_id !== $showroom->id || ! $bike->is_published, 404);

        $hasFeature = $showroom->canAccessFeature('has_360_viewer');
        $frames = $bike->frames360;

        // If showroom lacks entitlement or bike has no 360 view enabled, redirect to bike profile
        if (! $hasFeature || ! $bike->has360View()) {
            return redirect()
                ->route('showrooms.bikes.show', [$showroom, $bike])
                ->with('error', 'Interactive 360° Studio is currently unavailable for this model.');
        }

        $hotspots = $bike->hotspots_list;
        $settings = $bike->viewer_settings_or_default;

        return view('showrooms.bikes.360-fullscreen', compact(
            'showroom',
            'bike',
            'frames',
            'hotspots',
            'settings'
        ));
    }

    /**
     * Return JSON data payload for 360 turntable frames and hotspots.
     */
    public function data(Showroom $showroom, Bike $bike): JsonResponse
    {
        abort_if($bike->tenant_id !== $showroom->id || ! $bike->is_published, 404);

        $hasFeature = $showroom->canAccessFeature('has_360_viewer');
        if (! $hasFeature || ! $bike->has360View()) {
            return response()->json([
                'available' => false,
                'message' => '360° view is not active or entitled.',
            ], 404);
        }

        $frames = $bike->frames360->map(fn ($img) => [
            'id' => $img->id,
            'url' => $img->image_url,
            'angle' => $img->frame_angle,
            'order' => $img->sort_order,
        ]);

        return response()->json([
            'available' => true,
            'bike' => [
                'id' => $bike->id,
                'name' => $bike->name,
                'brand' => $bike->brand,
                'category' => $bike->category->value,
                'price' => $bike->formatted_price,
            ],
            'viewer_type' => $bike->viewer_type,
            'model_3d_path' => $bike->model_3d_path,
            'settings' => $bike->viewer_settings_or_default,
            'frames' => $frames,
            'hotspots' => $bike->hotspots_list,
        ]);
    }
}
