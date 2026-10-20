<?php

namespace App\Http\Controllers\Showroom;

use App\Enums\BikeCategory;
use App\Enums\BikeStatus;
use App\Http\Controllers\Controller;
use App\Models\Bike;
use App\Models\BikeImage;
use App\Models\Category;
use App\Models\Showroom;
use App\Services\AuditLogger;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class BikeController extends Controller
{
    /**
     * Display a listing of motorcycles for this showroom back-office.
     */
    public function index(Request $request, Showroom $showroom): View
    {
        $search = $request->query('search');
        $category = $request->query('category');
        $status = $request->query('status');

        $query = $showroom->bikes()
            ->with(['images', 'variants'])
            ->search($search)
            ->filterCategory($category)
            ->filterStatus($status)
            ->latest();

        $bikes = $query->paginate(12)->withQueryString();

        $totalBikes = $showroom->bikes()->count();
        $inStockBikes = $showroom->bikes()->where('status', BikeStatus::InStock->value)->count();
        $featuredBikes = $showroom->bikes()->where('is_featured', true)->count();
        $maxLimit = $showroom->subscription?->plan?->max_bike_listings;
        $quotaUsedPercent = $maxLimit ? min(100, (int) round(($totalBikes / $maxLimit) * 100)) : 0;
        $hasReachedLimit = $showroom->hasReachedLimit('max_bike_listings', $totalBikes);

        $categories = BikeCategory::cases();
        $statuses = BikeStatus::cases();

        return view('showrooms.manage.bikes.index', compact(
            'showroom',
            'bikes',
            'totalBikes',
            'inStockBikes',
            'featuredBikes',
            'maxLimit',
            'quotaUsedPercent',
            'hasReachedLimit',
            'categories',
            'statuses',
            'search',
            'category',
            'status'
        ));
    }

    /**
     * Show the form for creating a new motorcycle listing.
     */
    public function create(Showroom $showroom): View|RedirectResponse
    {
        $currentCount = $showroom->bikes()->count();
        if ($showroom->hasReachedLimit('max_bike_listings', $currentCount)) {
            $limit = $showroom->subscription?->plan?->max_bike_listings ?? 'current';

            return redirect()->route('showrooms.manage.bikes.index', $showroom)
                ->with('error', "Your plan allows a maximum of {$limit} bike listings. Please upgrade your subscription to add more motorcycles.");
        }

        if ($showroom->categories()->count() === 0) {
            Category::seedDefaultsForShowroom($showroom);
        }

        $categories = BikeCategory::cases();
        $customCategories = $showroom->categories()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
        $statuses = BikeStatus::cases();

        return view('showrooms.manage.bikes.create', compact('showroom', 'categories', 'customCategories', 'statuses'));
    }

    /**
     * Store a newly created motorcycle in storage.
     */
    public function store(Request $request, Showroom $showroom): RedirectResponse
    {
        $currentCount = $showroom->bikes()->count();
        if ($showroom->hasReachedLimit('max_bike_listings', $currentCount)) {
            $limit = $showroom->subscription?->plan?->max_bike_listings ?? 'current';

            return redirect()->route('showrooms.manage.bikes.index', $showroom)
                ->with('error', "Maximum bike listing limit of {$limit} reached. Please upgrade to list more motorcycles.");
        }

        $allowedCategories = array_unique(array_merge(
            array_column(BikeCategory::cases(), 'value'),
            $showroom->categories()->pluck('slug')->all()
        ));

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'brand' => ['required', 'string', 'max:100'],
            'model_year' => ['required', 'integer', 'min:1980', 'max:2035'],
            'category' => ['required', 'string', Rule::in($allowedCategories)],
            'category_id' => ['nullable', 'integer', Rule::exists('categories', 'id')->where('tenant_id', $showroom->id)],
            'sku' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('bikes', 'sku')->where('tenant_id', $showroom->id),
            ],
            'price' => ['required', 'numeric', 'min:0'],
            'engine_cc' => ['nullable', 'integer', 'min:0'],
            'power_bhp' => ['nullable', 'numeric', 'min:0'],
            'torque_nm' => ['nullable', 'numeric', 'min:0'],
            'fuel_type' => ['required', 'string', 'in:petrol,electric,hybrid,diesel'],
            'transmission' => ['nullable', 'string', 'max:100'],
            'mileage_kmpl' => ['nullable', 'numeric', 'min:0'],
            'top_speed_kmh' => ['nullable', 'integer', 'min:0'],
            'curb_weight_kg' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', 'string', Rule::in(array_column(BikeStatus::cases(), 'value'))],
            'is_featured' => ['nullable', 'boolean'],
            'is_published' => ['nullable', 'boolean'],
            'description' => ['nullable', 'string'],
            'spec_keys' => ['nullable', 'array'],
            'spec_values' => ['nullable', 'array'],
            'images' => ['nullable', 'array'],
            'images.*.image_url' => ['nullable', 'url'],
            'images.*.alt_text' => ['nullable', 'string', 'max:255'],
            'images.*.is_primary' => ['nullable', 'boolean'],
            'thumbnail' => ['nullable', 'file', 'image', 'max:10240'],
            'bike_files' => ['nullable', 'array'],
            'bike_files.*' => ['file', 'image', 'max:10240'],
            'has_360_view' => ['nullable', 'boolean'],
            'viewer_type' => ['nullable', 'string', 'in:image_sequence,gltf_model,external_embed,video_loop'],
            'model_3d_path' => ['nullable', 'string', 'max:1024'],
            'model_3d_file' => ['nullable', 'file', 'max:51200'],
            'video_360_file' => ['nullable', 'file', 'mimes:mp4,webm,mov', 'max:51200'],
            'frames_360' => ['nullable', 'array'],
            'frames_360.*' => ['file', 'image', 'max:10240'],
            'frames_360_urls' => ['nullable', 'string'],
            'variants' => ['nullable', 'array'],
            'variants.*.name' => ['required_with:variants', 'string', 'max:100'],
            'variants.*.color_name' => ['required_with:variants', 'string', 'max:100'],
            'variants.*.color_hex' => ['nullable', 'string', 'max:20'],
            'variants.*.price_difference' => ['nullable', 'numeric'],
            'variants.*.stock_quantity' => ['nullable', 'integer', 'min:0'],
        ]);

        // Build composite specifications JSON
        $specifications = [];
        if (! empty($validated['spec_keys']) && ! empty($validated['spec_values'])) {
            foreach ($validated['spec_keys'] as $idx => $key) {
                $trimmedKey = trim((string) $key);
                $val = trim((string) ($validated['spec_values'][$idx] ?? ''));
                if ($trimmedKey !== '' && $val !== '') {
                    $specifications[$trimmedKey] = $val;
                }
            }
        }

        // Generate unique slug within tenant
        $baseSlug = Str::slug($validated['name']);
        $slug = $baseSlug;
        $counter = 1;
        while ($showroom->bikes()->where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        // Auto-generate SKU if omitted
        $sku = ! empty($validated['sku'])
            ? strtoupper(trim($validated['sku']))
            : 'MOTO-'.strtoupper(Str::substr($validated['brand'], 0, 3)).'-'.strtoupper(Str::random(4));

        $categoryId = $validated['category_id'] ?? null;
        if (! $categoryId) {
            $matchedCat = $showroom->categories()->where('slug', $validated['category'])->first();
            if ($matchedCat) {
                $categoryId = $matchedCat->id;
            }
        }

        $categoryValue = in_array($validated['category'], array_column(BikeCategory::cases(), 'value'), true)
            ? $validated['category']
            : BikeCategory::Sports->value;

        $viewerType = $request->input('viewer_type', 'image_sequence');
        $model3dPath = $request->input('model_3d_path');

        $has360View = $request->boolean('has_360_view');

        $bike = DB::transaction(function () use ($request, $showroom, $validated, $slug, $sku, $specifications, $categoryId, $categoryValue, $has360View, $viewerType, $model3dPath) {
            $bike = $showroom->bikes()->create([
                'name' => $validated['name'],
                'slug' => $slug,
                'sku' => $sku,
                'brand' => $validated['brand'],
                'model_year' => (int) $validated['model_year'],
                'category' => $categoryValue,
                'category_id' => $categoryId,
                'price' => $validated['price'],
                'engine_cc' => $validated['engine_cc'] ?? null,
                'power_bhp' => $validated['power_bhp'] ?? null,
                'torque_nm' => $validated['torque_nm'] ?? null,
                'fuel_type' => $validated['fuel_type'],
                'transmission' => $validated['transmission'] ?? null,
                'mileage_kmpl' => $validated['mileage_kmpl'] ?? null,
                'top_speed_kmh' => $validated['top_speed_kmh'] ?? null,
                'curb_weight_kg' => $validated['curb_weight_kg'] ?? null,
                'status' => $validated['status'],
                'is_featured' => ! empty($validated['is_featured']),
                'is_published' => isset($validated['is_published']) ? (bool) $validated['is_published'] : true,
                'has_360_view' => $has360View,
                'viewer_type' => $viewerType,
                'model_3d_path' => $model3dPath,
                'description' => $validated['description'] ?? null,
                'specifications' => $specifications,
            ]);

            // Handle 3D model file upload
            if ($request->hasFile('model_3d_file')) {
                $path = $request->file('model_3d_file')->store("showrooms/{$showroom->id}/bikes/{$bike->id}/3d", 'public');
                $bike->update(['model_3d_path' => Storage::disk('public')->url($path)]);
            } elseif ($request->hasFile('video_360_file')) {
                $path = $request->file('video_360_file')->store("showrooms/{$showroom->id}/bikes/{$bike->id}/video", 'public');
                $bike->update(['model_3d_path' => Storage::disk('public')->url($path)]);
            }

            $imageIndex = 0;

            // 1. Process dedicated cover thumbnail upload
            if ($request->hasFile('thumbnail')) {
                $path = $request->file('thumbnail')->store("showrooms/{$showroom->id}/bikes/{$bike->id}/gallery", 'public');
                $url = Storage::disk('public')->url($path);

                $bike->images()->create([
                    'tenant_id' => $showroom->id,
                    'image_url' => $url,
                    'alt_text' => "{$bike->name} Cover Thumbnail",
                    'is_primary' => true,
                    'is_360_frame' => false,
                    'sort_order' => 0,
                ]);
                $imageIndex = 1;
            }

            // 2. Process uploaded normal bike gallery files
            if ($request->hasFile('bike_files')) {
                $hasPrimary = $bike->images()->where('is_primary', true)->exists();
                foreach ($request->file('bike_files') as $file) {
                    $path = $file->store("showrooms/{$showroom->id}/bikes/{$bike->id}/gallery", 'public');
                    $url = Storage::disk('public')->url($path);

                    $bike->images()->create([
                        'tenant_id' => $showroom->id,
                        'image_url' => $url,
                        'alt_text' => "{$bike->name} Photo ".($imageIndex + 1),
                        'is_primary' => ! $hasPrimary && $imageIndex === 0,
                        'is_360_frame' => false,
                        'sort_order' => $imageIndex,
                    ]);
                    $imageIndex++;
                }
            }

            // 2. Process external image URLs
            if (! empty($validated['images'])) {
                foreach ($validated['images'] as $index => $imgData) {
                    if (! empty($imgData['image_url'])) {
                        $bike->images()->create([
                            'tenant_id' => $showroom->id,
                            'image_url' => $imgData['image_url'],
                            'alt_text' => $imgData['alt_text'] ?? "{$bike->name} Photo",
                            'is_primary' => $imageIndex === 0 && (! empty($imgData['is_primary']) || $index === 0),
                            'is_360_frame' => false,
                            'sort_order' => $imageIndex,
                        ]);
                        $imageIndex++;
                    }
                }
            }

            // 3. Process uploaded 360-degree turntable frames
            if ($request->hasFile('frames_360')) {
                $frames = $request->file('frames_360');
                $totalFrames = count($frames);
                $step = $totalFrames > 0 ? 360 / $totalFrames : 1;

                foreach ($frames as $fIdx => $frameFile) {
                    $angle = (int) round($fIdx * $step);
                    $path = $frameFile->store("showrooms/{$showroom->id}/bikes/{$bike->id}/360", 'public');
                    $url = Storage::disk('public')->url($path);

                    $bike->allImages()->create([
                        'tenant_id' => $showroom->id,
                        'image_url' => $url,
                        'alt_text' => "{$bike->name} 360 Frame {$angle}°",
                        'is_primary' => false,
                        'is_360_frame' => true,
                        'frame_angle' => $angle,
                        'sort_order' => $fIdx,
                    ]);
                }
            }

            // Append 360-degree turntable frames from bulk URLs
            if ($request->filled('frames_360_urls')) {
                $lines = preg_split('/\r\n|\r|\n/', trim($request->input('frames_360_urls')));
                $validUrls = array_values(array_filter(array_map('trim', $lines ?: [])));

                if (! empty($validUrls)) {
                    $totalUrls = count($validUrls);
                    $step = 360 / $totalUrls;

                    foreach ($validUrls as $index => $url) {
                        $angle = (int) round($index * $step);
                        $bike->allImages()->create([
                            'tenant_id' => $showroom->id,
                            'image_url' => $url,
                            'alt_text' => "{$bike->name} 360 Frame {$angle}°",
                            'is_primary' => false,
                            'is_360_frame' => true,
                            'frame_angle' => $angle,
                            'sort_order' => $index,
                        ]);
                    }
                }
            }

            // Save variants
            if (! empty($validated['variants'])) {
                foreach ($validated['variants'] as $varData) {
                    if (! empty($varData['name'])) {
                        $bike->variants()->create([
                            'tenant_id' => $showroom->id,
                            'name' => $varData['name'],
                            'color_name' => $varData['color_name'] ?? 'Standard',
                            'color_hex' => $varData['color_hex'] ?? '#000000',
                            'price_difference' => $varData['price_difference'] ?? 0.00,
                            'stock_quantity' => $varData['stock_quantity'] ?? 1,
                        ]);
                    }
                }
            }

            return $bike;
        });

        AuditLogger::log(
            'bike.created',
            $bike,
            null,
            ['id' => $bike->id, 'name' => $bike->name, 'sku' => $bike->sku, 'price' => $bike->price],
            $showroom->id
        );

        return redirect()->route('showrooms.manage.bikes.index', $showroom)
            ->with('success', "Motorcycle \"{$bike->name}\" added to inventory successfully.");
    }

    /**
     * Show the form for editing the specified motorcycle.
     */
    public function edit(Showroom $showroom, Bike $bike): View
    {
        abort_if($bike->tenant_id !== $showroom->id, 404);

        $bike->load(['images', 'variants', 'categoryMaster']);

        if ($showroom->categories()->count() === 0) {
            Category::seedDefaultsForShowroom($showroom);
        }

        $categories = BikeCategory::cases();
        $customCategories = $showroom->categories()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
        $statuses = BikeStatus::cases();

        return view('showrooms.manage.bikes.edit', compact('showroom', 'bike', 'categories', 'customCategories', 'statuses'));
    }

    /**
     * Update the specified motorcycle in storage.
     */
    public function update(Request $request, Showroom $showroom, Bike $bike): RedirectResponse
    {
        abort_if($bike->tenant_id !== $showroom->id, 404);

        $allowedCategories = array_unique(array_merge(
            array_column(BikeCategory::cases(), 'value'),
            $showroom->categories()->pluck('slug')->all()
        ));

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'brand' => ['required', 'string', 'max:100'],
            'model_year' => ['required', 'integer', 'min:1980', 'max:2035'],
            'category' => ['required', 'string', Rule::in($allowedCategories)],
            'category_id' => ['nullable', 'integer', Rule::exists('categories', 'id')->where('tenant_id', $showroom->id)],
            'sku' => [
                'required',
                'string',
                'max:50',
                Rule::unique('bikes', 'sku')->where('tenant_id', $showroom->id)->ignore($bike->id),
            ],
            'price' => ['required', 'numeric', 'min:0'],
            'engine_cc' => ['nullable', 'integer', 'min:0'],
            'power_bhp' => ['nullable', 'numeric', 'min:0'],
            'torque_nm' => ['nullable', 'numeric', 'min:0'],
            'fuel_type' => ['required', 'string', 'in:petrol,electric,hybrid,diesel'],
            'transmission' => ['nullable', 'string', 'max:100'],
            'mileage_kmpl' => ['nullable', 'numeric', 'min:0'],
            'top_speed_kmh' => ['nullable', 'integer', 'min:0'],
            'curb_weight_kg' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', 'string', Rule::in(array_column(BikeStatus::cases(), 'value'))],
            'is_featured' => ['nullable', 'boolean'],
            'is_published' => ['nullable', 'boolean'],
            'description' => ['nullable', 'string'],
            'spec_keys' => ['nullable', 'array'],
            'spec_values' => ['nullable', 'array'],
            'images' => ['nullable', 'array'],
            'images.*.id' => ['nullable', 'integer'],
            'images.*.image_url' => ['required_with:images', 'url'],
            'images.*.alt_text' => ['nullable', 'string', 'max:255'],
            'images.*.is_primary' => ['nullable', 'boolean'],
            'thumbnail' => ['nullable', 'file', 'image', 'max:10240'],
            'bike_files' => ['nullable', 'array'],
            'bike_files.*' => ['file', 'image', 'max:10240'],
            'has_360_view' => ['nullable', 'boolean'],
            'viewer_type' => ['nullable', 'string', 'in:image_sequence,gltf_model,external_embed,video_loop'],
            'model_3d_path' => ['nullable', 'string', 'max:1024'],
            'model_3d_file' => ['nullable', 'file', 'max:51200'],
            'video_360_file' => ['nullable', 'file', 'mimes:mp4,webm,mov', 'max:51200'],
            'frames_360' => ['nullable', 'array'],
            'frames_360.*' => ['file', 'image', 'max:10240'],
            'frames_360_urls' => ['nullable', 'string'],
            'delete_frames' => ['nullable', 'array'],
            'delete_frames.*' => ['integer'],
            'variants' => ['nullable', 'array'],
            'variants.*.id' => ['nullable', 'integer'],
            'variants.*.name' => ['required_with:variants', 'string', 'max:100'],
            'variants.*.color_name' => ['required_with:variants', 'string', 'max:100'],
            'variants.*.color_hex' => ['nullable', 'string', 'max:20'],
            'variants.*.price_difference' => ['nullable', 'numeric'],
            'variants.*.stock_quantity' => ['nullable', 'integer', 'min:0'],
        ]);

        // Build specifications JSON
        $specifications = [];
        if (! empty($validated['spec_keys']) && ! empty($validated['spec_values'])) {
            foreach ($validated['spec_keys'] as $idx => $key) {
                $trimmedKey = trim((string) $key);
                $val = trim((string) ($validated['spec_values'][$idx] ?? ''));
                if ($trimmedKey !== '' && $val !== '') {
                    $specifications[$trimmedKey] = $val;
                }
            }
        }

        $categoryId = $validated['category_id'] ?? $bike->category_id;
        if (empty($validated['category_id'])) {
            $matchedCat = $showroom->categories()->where('slug', $validated['category'])->first();
            if ($matchedCat) {
                $categoryId = $matchedCat->id;
            }
        }

        $categoryValue = in_array($validated['category'], array_column(BikeCategory::cases(), 'value'), true)
            ? $validated['category']
            : BikeCategory::Sports->value;

        $oldValues = $bike->only(['name', 'sku', 'price', 'status', 'is_featured', 'is_published']);

        $viewerType = $request->input('viewer_type', $bike->viewer_type ?? 'image_sequence');
        $model3dPath = $request->input('model_3d_path', $bike->model_3d_path);

        if ($request->hasFile('model_3d_file')) {
            $path = $request->file('model_3d_file')->store("showrooms/{$showroom->id}/bikes/{$bike->id}/3d", 'public');
            $model3dPath = Storage::disk('public')->url($path);
        } elseif ($request->hasFile('video_360_file')) {
            $path = $request->file('video_360_file')->store("showrooms/{$showroom->id}/bikes/{$bike->id}/video", 'public');
            $model3dPath = Storage::disk('public')->url($path);
        }

        $has360View = $request->boolean('has_360_view');

        DB::transaction(function () use ($request, $bike, $showroom, $validated, $specifications, $categoryId, $categoryValue, $has360View, $viewerType, $model3dPath) {
            $bike->update([
                'name' => $validated['name'],
                'sku' => strtoupper(trim($validated['sku'])),
                'brand' => $validated['brand'],
                'model_year' => (int) $validated['model_year'],
                'category' => $categoryValue,
                'category_id' => $categoryId,
                'price' => $validated['price'],
                'engine_cc' => $validated['engine_cc'] ?? null,
                'power_bhp' => $validated['power_bhp'] ?? null,
                'torque_nm' => $validated['torque_nm'] ?? null,
                'fuel_type' => $validated['fuel_type'],
                'transmission' => $validated['transmission'] ?? null,
                'mileage_kmpl' => $validated['mileage_kmpl'] ?? null,
                'top_speed_kmh' => $validated['top_speed_kmh'] ?? null,
                'curb_weight_kg' => $validated['curb_weight_kg'] ?? null,
                'status' => $validated['status'],
                'is_featured' => ! empty($validated['is_featured']),
                'is_published' => ! empty($validated['is_published']),
                'has_360_view' => $has360View,
                'viewer_type' => $viewerType,
                'model_3d_path' => $model3dPath,
                'description' => $validated['description'] ?? null,
                'specifications' => $specifications,
            ]);

            // Sync or update gallery images
            if (isset($validated['images'])) {
                $bike->images()->delete();
                $sort = 0;
                foreach ($validated['images'] as $index => $imgData) {
                    if (! empty($imgData['image_url'])) {
                        $bike->images()->create([
                            'tenant_id' => $showroom->id,
                            'image_url' => $imgData['image_url'],
                            'alt_text' => $imgData['alt_text'] ?? "{$bike->name} Photo",
                            'is_primary' => ! empty($imgData['is_primary']) || $sort === 0,
                            'is_360_frame' => false,
                            'sort_order' => $sort,
                        ]);
                        $sort++;
                    }
                }
            }

            // Dedicated cover thumbnail update
            if ($request->hasFile('thumbnail')) {
                $bike->images()->where('is_primary', true)->update(['is_primary' => false]);
                $path = $request->file('thumbnail')->store("showrooms/{$showroom->id}/bikes/{$bike->id}/gallery", 'public');
                $url = Storage::disk('public')->url($path);

                $bike->images()->create([
                    'tenant_id' => $showroom->id,
                    'image_url' => $url,
                    'alt_text' => "{$bike->name} Cover Thumbnail",
                    'is_primary' => true,
                    'is_360_frame' => false,
                    'sort_order' => 0,
                ]);
            }

            // Append newly uploaded normal gallery files
            if ($request->hasFile('bike_files')) {
                $hasExistingPrimary = $bike->images()->where('is_primary', true)->exists();
                $currentMaxSort = (int) ($bike->images()->max('sort_order') ?? 0);

                foreach ($request->file('bike_files') as $idx => $file) {
                    $path = $file->store("showrooms/{$showroom->id}/bikes/{$bike->id}/gallery", 'public');
                    $url = Storage::disk('public')->url($path);

                    $bike->images()->create([
                        'tenant_id' => $showroom->id,
                        'image_url' => $url,
                        'alt_text' => "{$bike->name} Photo ".($currentMaxSort + $idx + 1),
                        'is_primary' => (! $hasExistingPrimary && $idx === 0),
                        'is_360_frame' => false,
                        'sort_order' => $currentMaxSort + $idx + 1,
                    ]);
                }
            }

            // Upload additional 360-degree turntable frames
            if ($request->hasFile('frames_360')) {
                $frames = $request->file('frames_360');
                $totalFrames = count($frames);
                $step = $totalFrames > 0 ? 360 / $totalFrames : 1;

                foreach ($frames as $fIdx => $frameFile) {
                    $angle = (int) round($fIdx * $step);
                    $path = $frameFile->store("showrooms/{$showroom->id}/bikes/{$bike->id}/360", 'public');
                    $url = Storage::disk('public')->url($path);

                    $bike->allImages()->create([
                        'tenant_id' => $showroom->id,
                        'image_url' => $url,
                        'alt_text' => "{$bike->name} 360 Frame {$angle}°",
                        'is_primary' => false,
                        'is_360_frame' => true,
                        'frame_angle' => $angle,
                        'sort_order' => $fIdx,
                    ]);
                }
            }

            // Append 360-degree turntable frames from bulk URLs
            if ($request->filled('frames_360_urls')) {
                $lines = preg_split('/\r\n|\r|\n/', trim($request->input('frames_360_urls')));
                $validUrls = array_values(array_filter(array_map('trim', $lines ?: [])));

                if (! empty($validUrls)) {
                    $totalUrls = count($validUrls);
                    $step = 360 / $totalUrls;

                    foreach ($validUrls as $index => $url) {
                        $angle = (int) round($index * $step);
                        $bike->allImages()->create([
                            'tenant_id' => $showroom->id,
                            'image_url' => $url,
                            'alt_text' => "{$bike->name} 360 Frame {$angle}°",
                            'is_primary' => false,
                            'is_360_frame' => true,
                            'frame_angle' => $angle,
                            'sort_order' => $index,
                        ]);
                    }
                }
            }

            // Delete removed 360 frames
            if ($request->has('delete_frames')) {
                $bike->frames360()->whereIn('id', (array) $request->input('delete_frames'))->delete();
            }

            // Sync variants
            if (isset($validated['variants'])) {
                $bike->variants()->delete();
                foreach ($validated['variants'] as $varData) {
                    if (! empty($varData['name'])) {
                        $bike->variants()->create([
                            'tenant_id' => $showroom->id,
                            'name' => $varData['name'],
                            'color_name' => $varData['color_name'] ?? 'Standard',
                            'color_hex' => $varData['color_hex'] ?? '#000000',
                            'price_difference' => $varData['price_difference'] ?? 0.00,
                            'stock_quantity' => $varData['stock_quantity'] ?? 1,
                        ]);
                    }
                }
            }
        });

        AuditLogger::log(
            'bike.updated',
            $bike,
            $oldValues,
            $bike->fresh()->only(['name', 'sku', 'price', 'status', 'is_featured', 'is_published']),
            $showroom->id
        );

        return redirect()->route('showrooms.manage.bikes.index', $showroom)
            ->with('success', "Motorcycle \"{$bike->name}\" updated successfully.");
    }

    /**
     * Remove the specified motorcycle from inventory (soft delete).
     */
    public function destroy(Showroom $showroom, Bike $bike): RedirectResponse
    {
        abort_if($bike->tenant_id !== $showroom->id, 404);

        $name = $bike->name;
        $oldValues = ['id' => $bike->id, 'name' => $name, 'sku' => $bike->sku];

        $bike->delete();

        AuditLogger::log('bike.deleted', $bike, $oldValues, null, $showroom->id);

        return redirect()->route('showrooms.manage.bikes.index', $showroom)
            ->with('success', "Motorcycle \"{$name}\" removed from inventory.");
    }

    /**
     * Quick toggle for featured status.
     */
    public function toggleFeatured(Showroom $showroom, Bike $bike): RedirectResponse
    {
        abort_if($bike->tenant_id !== $showroom->id, 404);

        $bike->update(['is_featured' => ! $bike->is_featured]);

        $statusText = $bike->is_featured ? 'marked as Featured' : 'unmarked from Featured';

        return back()->with('success', "\"{$bike->name}\" {$statusText}.");
    }

    /**
     * Quick toggle for published status.
     */
    public function togglePublished(Showroom $showroom, Bike $bike): RedirectResponse
    {
        abort_if($bike->tenant_id !== $showroom->id, 404);

        $bike->update(['is_published' => ! $bike->is_published]);

        $statusText = $bike->is_published ? 'published on storefront' : 'hidden from storefront';

        return back()->with('success', "\"{$bike->name}\" {$statusText}.");
    }

    /**
     * Delete multiple selected motorcycles.
     */
    public function bulkDestroy(Request $request, Showroom $showroom): RedirectResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', 'exists:bikes,id'],
        ]);

        $count = $showroom->bikes()->whereIn('id', $validated['ids'])->delete();

        AuditLogger::log('bike.bulk_deleted', null, ['ids' => $validated['ids']], null, $showroom->id);

        return redirect()->route('showrooms.manage.bikes.index', $showroom)
            ->with('success', "{$count} motorcycle(s) removed from inventory.");
    }

    /**
     * Export showroom motorcycles to CSV.
     */
    public function export(Request $request, Showroom $showroom)
    {
        $query = $showroom->bikes()->with(['variants']);

        if ($request->filled('ids')) {
            $ids = is_array($request->input('ids'))
                ? $request->input('ids')
                : explode(',', $request->input('ids'));
            $query->whereIn('id', $ids);
        } else {
            if ($request->filled('search')) {
                $query->search($request->input('search'));
            }
            if ($request->filled('category')) {
                $query->filterCategory($request->input('category'));
            }
            if ($request->filled('status')) {
                $query->filterStatus($request->input('status'));
            }
        }

        $bikes = $query->latest()->get();
        $filename = Str::slug($showroom->name).'_bikes_'.date('Y-m-d_His').'.csv';

        return response()->streamDownload(function () use ($bikes) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Name', 'SKU', 'Brand', 'Model Year', 'Category', 'Price', 'Status', 'Featured', 'Published', 'Engine CC', 'Power BHP']);

            foreach ($bikes as $b) {
                fputcsv($handle, [
                    $b->id,
                    $b->name,
                    $b->sku,
                    $b->brand,
                    $b->model_year,
                    $b->category?->value ?? $b->category,
                    $b->price,
                    $b->status?->value ?? $b->status,
                    $b->is_featured ? 'Yes' : 'No',
                    $b->is_published ? 'Yes' : 'No',
                    $b->engine_cc ?? 'N/A',
                    $b->power_bhp ?? 'N/A',
                ]);
            }
            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Delete an individual gallery image.
     */
    public function destroyImage(Request $request, Showroom $showroom, Bike $bike, BikeImage $image): RedirectResponse|JsonResponse
    {
        abort_if($bike->tenant_id !== $showroom->id || $image->bike_id !== $bike->id || $image->tenant_id !== $showroom->id, 404);

        $wasPrimary = $image->is_primary;
        $image->delete();

        // If primary was deleted, promote first remaining gallery image as primary
        if ($wasPrimary) {
            $nextPrimary = $bike->images()->first();
            if ($nextPrimary) {
                $nextPrimary->update(['is_primary' => true]);
            }
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Photo removed successfully.',
            ]);
        }

        return back()->with('success', 'Photo removed successfully.');
    }
}
