<?php

namespace App\Http\Controllers\Showroom;

use App\Enums\BikeCategory;
use App\Http\Controllers\Controller;
use App\Models\Bike;
use App\Models\Showroom;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class PublicBikeController extends Controller
{
    /**
     * Display a public catalog of available motorcycles for this showroom.
     */
    public function index(Request $request, Showroom $showroom): View
    {
        $search = $request->query('search');
        $category = $request->query('category');
        $brand = $request->query('brand');
        $minPrice = $request->query('min_price');
        $maxPrice = $request->query('max_price');
        $sort = $request->query('sort', 'latest');

        $query = $showroom->publishedBikes()
            ->with(['images', 'variants'])
            ->search($search)
            ->filterCategory($category);

        if (! empty($brand) && $brand !== 'all') {
            $query->where('brand', $brand);
        }

        if (! empty($minPrice) && is_numeric($minPrice)) {
            $query->where('price', '>=', (float) $minPrice);
        }

        if (! empty($maxPrice) && is_numeric($maxPrice)) {
            $query->where('price', '<=', (float) $maxPrice);
        }

        match ($sort) {
            'price_asc' => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'year_desc' => $query->orderByDesc('model_year'),
            default => $query->latest(),
        };

        $bikes = $query->paginate(12)->withQueryString();

        $categories = BikeCategory::cases();
        $brands = $showroom->publishedBikes()->distinct()->pluck('brand')->filter()->values();

        return view('showrooms.bikes.index', compact(
            'showroom',
            'bikes',
            'categories',
            'brands',
            'search',
            'category',
            'brand',
            'sort'
        ));
    }

    /**
     * Display detailed specs, variants and image gallery for a specific motorcycle.
     */
    public function show(Showroom $showroom, Bike $bike): View
    {
        abort_if($bike->tenant_id !== $showroom->id || ! $bike->is_published, 404);

        $bike->load(['images', 'variants', 'frames360']);
        $has360Viewer = $showroom->canAccessFeature('has_360_viewer') && $bike->has360View();

        $relatedBikes = $showroom->publishedBikes()
            ->with(['images'])
            ->where('id', '!=', $bike->id)
            ->where('category', $bike->category)
            ->take(4)
            ->get();

        if ($relatedBikes->count() < 4) {
            $filler = $showroom->publishedBikes()
                ->with(['images'])
                ->where('id', '!=', $bike->id)
                ->whereNotIn('id', $relatedBikes->pluck('id'))
                ->take(4 - $relatedBikes->count())
                ->get();
            $relatedBikes = $relatedBikes->merge($filler);
        }

        return view('showrooms.bikes.show', compact('showroom', 'bike', 'relatedBikes', 'has360Viewer'));
    }

    /**
     * Display dedicated reservation checkout page for a specific motorcycle.
     */
    public function checkout(Request $request, Showroom $showroom, Bike $bike): View
    {
        abort_if($bike->tenant_id !== $showroom->id || ! $bike->is_published, 404);

        $bike->load(['images', 'variants']);
        $selectedVariant = $request->query('variant', $bike->variants->first()?->name ?? 'Standard');

        return view('showrooms.bikes.checkout', compact('showroom', 'bike', 'selectedVariant'));
    }
}
