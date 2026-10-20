<?php

namespace App\Http\Controllers\Showroom;

use App\Http\Controllers\Controller;
use App\Models\Bike;
use App\Models\BikeImage;
use App\Models\Showroom;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Bike360ViewerController extends Controller
{
    /**
     * Display the 360-Degree Studio management workbench for this motorcycle.
     */
    public function index(Showroom $showroom, Bike $bike): View
    {
        abort_if($bike->tenant_id !== $showroom->id, 404);

        $hasFeature = $showroom->canAccessFeature('has_360_viewer');
        $frames = $bike->frames360;
        $hotspots = $bike->hotspots_list;
        $settings = $bike->viewer_settings_or_default;

        return view('showrooms.manage.bikes.360', compact(
            'showroom',
            'bike',
            'hasFeature',
            'frames',
            'hotspots',
            'settings'
        ));
    }

    /**
     * Update 360 viewer general configuration and settings.
     */
    public function updateSettings(Request $request, Showroom $showroom, Bike $bike): RedirectResponse
    {
        abort_if($bike->tenant_id !== $showroom->id, 404);

        if (! $showroom->canAccessFeature('has_360_viewer')) {
            abort(403, 'Your current subscription plan does not include the Interactive 360° Motorcycle Viewer. Please upgrade your plan to unlock this feature.');
        }

        $validated = $request->validate([
            'has_360_view' => ['nullable', 'boolean'],
            'viewer_type' => ['required', 'string', 'in:image_sequence,gltf_model,video_loop'],
            'model_3d_path' => ['nullable', 'string', 'max:1024'],
            'auto_rotate' => ['nullable', 'boolean'],
            'rotation_speed' => ['nullable', 'integer', 'min:1', 'max:10'],
            'reverse_direction' => ['nullable', 'boolean'],
            'enable_zoom' => ['nullable', 'boolean'],
            'default_angle' => ['nullable', 'integer', 'min:0', 'max:359'],
        ]);

        $bike->update([
            'has_360_view' => $request->boolean('has_360_view'),
            'viewer_type' => $validated['viewer_type'],
            'model_3d_path' => $validated['model_3d_path'] ?? null,
            'viewer_settings' => [
                'auto_rotate' => $request->boolean('auto_rotate', true),
                'rotation_speed' => (int) ($validated['rotation_speed'] ?? 3),
                'reverse_direction' => $request->boolean('reverse_direction'),
                'enable_zoom' => $request->boolean('enable_zoom', true),
                'default_angle' => (int) ($validated['default_angle'] ?? 0),
            ],
        ]);

        return redirect()
            ->route('showrooms.manage.bikes.360', [$showroom, $bike])
            ->with('success', '360° Studio settings updated successfully.');
    }

    /**
     * Upload or register 360-degree turntable frames.
     */
    public function uploadFrames(Request $request, Showroom $showroom, Bike $bike): RedirectResponse
    {
        abort_if($bike->tenant_id !== $showroom->id, 404);

        if (! $showroom->canAccessFeature('has_360_viewer')) {
            abort(403, 'Your current subscription plan does not include the Interactive 360° Motorcycle Viewer.');
        }

        $validated = $request->validate([
            'bulk_urls' => ['nullable', 'string'],
            'frame_files' => ['nullable', 'array'],
            'frame_files.*' => ['file', 'image', 'max:10240'],
            'single_url' => ['nullable', 'string', 'max:1024'],
            'single_angle' => ['nullable', 'integer', 'min:0', 'max:359'],
        ]);

        $addedCount = 0;

        // Mode 1: Single image with explicit angle
        if (! empty($validated['single_url'])) {
            $angle = (int) ($validated['single_angle'] ?? 0);
            BikeImage::create([
                'tenant_id' => $showroom->id,
                'bike_id' => $bike->id,
                'image_url' => trim($validated['single_url']),
                'alt_text' => "{$bike->name} 360 View Frame {$angle}°",
                'is_primary' => false,
                'is_360_frame' => true,
                'frame_angle' => $angle,
                'sort_order' => $angle,
            ]);
            $addedCount++;
        }

        // Mode 2: Bulk image URLs (one per line) with auto-calculated angles (0° to 359°)
        if (! empty($validated['bulk_urls'])) {
            $lines = preg_split('/\r\n|\r|\n/', trim($validated['bulk_urls']));
            $validUrls = array_values(array_filter(array_map('trim', $lines ?: [])));

            if (! empty($validUrls)) {
                $totalUrls = count($validUrls);
                $step = 360 / $totalUrls;

                foreach ($validUrls as $index => $url) {
                    $angle = (int) round($index * $step);
                    BikeImage::create([
                        'tenant_id' => $showroom->id,
                        'bike_id' => $bike->id,
                        'image_url' => $url,
                        'alt_text' => "{$bike->name} 360 Frame {$angle}°",
                        'is_primary' => false,
                        'is_360_frame' => true,
                        'frame_angle' => $angle,
                        'sort_order' => $index,
                    ]);
                    $addedCount++;
                }
            }
        }

        // Mode 3: Uploaded file frames
        if ($request->hasFile('frame_files')) {
            $files = $request->file('frame_files');
            $totalFiles = count($files);
            $step = $totalFiles > 0 ? 360 / $totalFiles : 1;

            foreach ($files as $index => $file) {
                $angle = (int) round($index * $step);
                $path = $file->store("showrooms/{$showroom->id}/bikes/{$bike->id}/360", 'public');
                $url = Storage::disk('public')->url($path);

                BikeImage::create([
                    'tenant_id' => $showroom->id,
                    'bike_id' => $bike->id,
                    'image_url' => $url,
                    'alt_text' => "{$bike->name} 360 Frame {$angle}°",
                    'is_primary' => false,
                    'is_360_frame' => true,
                    'frame_angle' => $angle,
                    'sort_order' => $index,
                ]);
                $addedCount++;
            }
        }

        if ($addedCount > 0 && ! $bike->has_360_view) {
            $bike->update(['has_360_view' => true]);
        }

        return redirect()
            ->route('showrooms.manage.bikes.360', [$showroom, $bike])
            ->with('success', "{$addedCount} 360° frames added successfully.");
    }

    /**
     * Delete an individual 360-degree frame.
     */
    public function deleteFrame(Showroom $showroom, Bike $bike, BikeImage $image): RedirectResponse|JsonResponse
    {
        abort_if($bike->tenant_id !== $showroom->id || $image->bike_id !== $bike->id || $image->tenant_id !== $showroom->id, 404);

        if (! $showroom->canAccessFeature('has_360_viewer')) {
            abort(403, 'Your current subscription plan does not include the Interactive 360° Motorcycle Viewer.');
        }

        $image->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()
            ->route('showrooms.manage.bikes.360', [$showroom, $bike])
            ->with('success', '360° frame deleted successfully.');
    }

    /**
     * Clear all 360-degree turntable frames for this motorcycle.
     */
    public function clearAllFrames(Showroom $showroom, Bike $bike): RedirectResponse
    {
        abort_if($bike->tenant_id !== $showroom->id, 404);

        if (! $showroom->canAccessFeature('has_360_viewer')) {
            abort(403, 'Your current subscription plan does not include the Interactive 360° Motorcycle Viewer.');
        }

        $count = $bike->frames360()->count();
        $bike->frames360()->delete();

        return redirect()
            ->route('showrooms.manage.bikes.360', [$showroom, $bike])
            ->with('success', "Cleared all {$count} 360° frames.");
    }

    /**
     * Update interactive points of interest (hotspots).
     */
    public function updateHotspots(Request $request, Showroom $showroom, Bike $bike): RedirectResponse
    {
        abort_if($bike->tenant_id !== $showroom->id, 404);

        if (! $showroom->canAccessFeature('has_360_viewer')) {
            abort(403, 'Your current subscription plan does not include the Interactive 360° Motorcycle Viewer.');
        }

        $validated = $request->validate([
            'hotspots' => ['nullable', 'array'],
            'hotspots.*.title' => ['required_with:hotspots', 'string', 'max:100'],
            'hotspots.*.description' => ['nullable', 'string', 'max:500'],
            'hotspots.*.frame_angle' => ['required_with:hotspots', 'integer', 'min:0', 'max:359'],
            'hotspots.*.x_pct' => ['required_with:hotspots', 'numeric', 'min:0', 'max:100'],
            'hotspots.*.y_pct' => ['required_with:hotspots', 'numeric', 'min:0', 'max:100'],
        ]);

        $rawHotspots = $validated['hotspots'] ?? [];
        $cleanedHotspots = [];

        foreach ($rawHotspots as $h) {
            if (empty($h['title'])) {
                continue;
            }
            $cleanedHotspots[] = [
                'id' => $h['id'] ?? (string) Str::uuid(),
                'title' => trim($h['title']),
                'description' => trim($h['description'] ?? ''),
                'frame_angle' => (int) ($h['frame_angle'] ?? 0),
                'x_pct' => round((float) ($h['x_pct'] ?? 50), 2),
                'y_pct' => round((float) ($h['y_pct'] ?? 50), 2),
            ];
        }

        $bike->update(['hotspots' => $cleanedHotspots]);

        return redirect()
            ->route('showrooms.manage.bikes.360', [$showroom, $bike])
            ->with('success', 'Interactive hotspots saved successfully.');
    }
}
