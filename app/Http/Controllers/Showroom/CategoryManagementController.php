<?php

namespace App\Http\Controllers\Showroom;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Showroom;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CategoryManagementController extends Controller
{
    /**
     * Display a listing of custom motorcycle categories.
     */
    public function index(Request $request, Showroom $showroom): View
    {
        $categories = $showroom->categories()
            ->withCount('bikes')
            ->get();

        return view('showrooms.manage.categories.index', compact('showroom', 'categories'));
    }

    /**
     * Store a newly created category.
     */
    public function store(Request $request, Showroom $showroom): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'slug' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('categories', 'slug')->where('tenant_id', $showroom->id),
            ],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'image_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,svg', 'max:5120'],
            'image_url' => ['nullable', 'string', 'max:1024'],
            'is_active' => ['nullable'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $slug = ! empty($validated['slug'])
            ? Str::slug($validated['slug'])
            : Str::slug($validated['name']);

        // Ensure unique slug for this tenant
        $baseSlug = $slug;
        $counter = 1;
        while ($showroom->categories()->where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        $imagePath = null;
        if ($request->hasFile('image_file')) {
            $imagePath = $request->file('image_file')->store("showrooms/{$showroom->id}/categories", 'public');
        }

        $showroom->categories()->create([
            'name' => $validated['name'],
            'slug' => $slug,
            'subtitle' => $validated['subtitle'] ?? null,
            'description' => $validated['description'] ?? null,
            'image_path' => $imagePath,
            'image_url' => $validated['image_url'] ?? null,
            'is_active' => $request->boolean('is_active', true),
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
        ]);

        return redirect()->route('showrooms.manage.categories.index', $showroom)
            ->with('success', "Category '{$validated['name']}' created successfully.");
    }

    /**
     * Update the specified category.
     */
    public function update(Request $request, Showroom $showroom, Category $category): RedirectResponse
    {
        abort_unless($category->tenant_id === $showroom->id, 404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'slug' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('categories', 'slug')
                    ->where('tenant_id', $showroom->id)
                    ->ignore($category->id),
            ],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'image_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,svg', 'max:5120'],
            'image_url' => ['nullable', 'string', 'max:1024'],
            'is_active' => ['nullable'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $slug = ! empty($validated['slug'])
            ? Str::slug($validated['slug'])
            : $category->slug;

        $imagePath = $category->image_path;
        if ($request->hasFile('image_file')) {
            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = $request->file('image_file')->store("showrooms/{$showroom->id}/categories", 'public');
        }

        $category->update([
            'name' => $validated['name'],
            'slug' => $slug,
            'subtitle' => $validated['subtitle'] ?? null,
            'description' => $validated['description'] ?? null,
            'image_path' => $imagePath,
            'image_url' => $validated['image_url'] ?? $category->image_url,
            'is_active' => $request->boolean('is_active', true),
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
        ]);

        return redirect()->route('showrooms.manage.categories.index', $showroom)
            ->with('success', "Category '{$category->name}' updated successfully.");
    }

    /**
     * Remove the specified category.
     */
    public function destroy(Showroom $showroom, Category $category): RedirectResponse
    {
        abort_unless($category->tenant_id === $showroom->id, 404);

        $name = $category->name;

        // Disassociate any bikes linked to this category
        $category->bikes()->update(['category_id' => null]);

        if ($category->image_path && Storage::disk('public')->exists($category->image_path)) {
            Storage::disk('public')->delete($category->image_path);
        }

        $category->delete();

        return redirect()->route('showrooms.manage.categories.index', $showroom)
            ->with('success', "Category '{$name}' deleted successfully.");
    }

    /**
     * Toggle the active status of the specified category.
     */
    public function toggleActive(Showroom $showroom, Category $category): RedirectResponse
    {
        abort_unless($category->tenant_id === $showroom->id, 404);

        $category->update([
            'is_active' => ! $category->is_active,
        ]);

        $status = $category->is_active ? 'activated' : 'deactivated';

        return redirect()->route('showrooms.manage.categories.index', $showroom)
            ->with('success', "Category '{$category->name}' {$status}.");
    }
}
