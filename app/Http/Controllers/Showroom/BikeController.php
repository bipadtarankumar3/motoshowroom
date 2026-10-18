<?php

namespace App\Http\Controllers\Showroom;

use App\Enums\BikeCategory;
use App\Enums\BikeStatus;
use App\Http\Controllers\Controller;
use App\Models\Bike;
use App\Models\Showroom;
use App\Services\AuditLogger;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

        $categories = BikeCategory::cases();
        $statuses = BikeStatus::cases();

        return view('showrooms.manage.bikes.create', compact('showroom', 'categories', 'statuses'));
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

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'brand' => ['required', 'string', 'max:100'],
            'model_year' => ['required', 'integer', 'min:1980', 'max:2035'],
            'category' => ['required', 'string', Rule::in(array_column(BikeCategory::cases(), 'value'))],
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
            'images.*.image_url' => ['required_with:images', 'url'],
            'images.*.alt_text' => ['nullable', 'string', 'max:255'],
            'images.*.is_primary' => ['nullable', 'boolean'],
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

        $bike = DB::transaction(function () use ($showroom, $validated, $slug, $sku, $specifications) {
            $bike = $showroom->bikes()->create([
                'name' => $validated['name'],
                'slug' => $slug,
                'sku' => $sku,
                'brand' => $validated['brand'],
                'model_year' => (int) $validated['model_year'],
                'category' => $validated['category'],
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
                'description' => $validated['description'] ?? null,
                'specifications' => $specifications,
            ]);

            // Save images
            if (! empty($validated['images'])) {
                foreach ($validated['images'] as $index => $imgData) {
                    if (! empty($imgData['image_url'])) {
                        $bike->images()->create([
                            'tenant_id' => $showroom->id,
                            'image_url' => $imgData['image_url'],
                            'alt_text' => $imgData['alt_text'] ?? "{$bike->name} Photo",
                            'is_primary' => ! empty($imgData['is_primary']) || $index === 0,
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

        $bike->load(['images', 'variants']);
        $categories = BikeCategory::cases();
        $statuses = BikeStatus::cases();

        return view('showrooms.manage.bikes.edit', compact('showroom', 'bike', 'categories', 'statuses'));
    }

    /**
     * Update the specified motorcycle in storage.
     */
    public function update(Request $request, Showroom $showroom, Bike $bike): RedirectResponse
    {
        abort_if($bike->tenant_id !== $showroom->id, 404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'brand' => ['required', 'string', 'max:100'],
            'model_year' => ['required', 'integer', 'min:1980', 'max:2035'],
            'category' => ['required', 'string', Rule::in(array_column(BikeCategory::cases(), 'value'))],
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

        $oldValues = $bike->only(['name', 'sku', 'price', 'status', 'is_featured', 'is_published']);

        DB::transaction(function () use ($bike, $showroom, $validated, $specifications) {
            $bike->update([
                'name' => $validated['name'],
                'sku' => strtoupper(trim($validated['sku'])),
                'brand' => $validated['brand'],
                'model_year' => (int) $validated['model_year'],
                'category' => $validated['category'],
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
                'description' => $validated['description'] ?? null,
                'specifications' => $specifications,
            ]);

            // Sync images
            if (isset($validated['images'])) {
                $bike->images()->delete();
                foreach ($validated['images'] as $index => $imgData) {
                    if (! empty($imgData['image_url'])) {
                        $bike->images()->create([
                            'tenant_id' => $showroom->id,
                            'image_url' => $imgData['image_url'],
                            'alt_text' => $imgData['alt_text'] ?? "{$bike->name} Photo",
                            'is_primary' => ! empty($imgData['is_primary']) || $index === 0,
                            'sort_order' => $index,
                        ]);
                    }
                }
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
}
