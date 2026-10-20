<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Showroom;
use App\Models\ShowroomMembership;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ShowroomController extends Controller
{
    /**
     * Display a listing of all showrooms across the platform.
     */
    public function index(Request $request): View
    {
        $query = Showroom::with(['ownerMembership.user', 'memberships'])
            ->withCount('memberships');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $showrooms = $query->latest()->paginate(10)->withQueryString();

        $stats = [
            'total' => Showroom::count(),
            'active' => Showroom::where('status', 'active')->count(),
            'trial' => Showroom::where('status', 'trial')->count(),
            'suspended' => Showroom::where('status', 'suspended')->count(),
        ];

        return view('admin.showrooms.index', compact('showrooms', 'stats'));
    }

    /**
     * Show the form for provisioning a new showroom tenant.
     */
    public function create(): View
    {
        $existingUsers = User::orderBy('name')->get();

        return view('admin.showrooms.create', compact('existingUsers'));
    }

    /**
     * Store a newly created showroom in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:100', 'alpha_dash', 'unique:showrooms,slug'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'city' => ['required', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'address_line1' => ['nullable', 'string', 'max:255'],
            'brand_color_primary' => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'brand_color_accent' => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'status' => ['required', Rule::in(['active', 'trial', 'suspended'])],
            'owner_mode' => ['required', Rule::in(['existing', 'new'])],
            'owner_user_id' => [
                Rule::requiredIf(fn () => $request->input('owner_mode') === 'existing'),
                'nullable',
                'exists:users,id',
            ],
            'new_owner_name' => [
                Rule::requiredIf(fn () => $request->input('owner_mode') === 'new'),
                'nullable',
                'string',
                'max:255',
            ],
            'new_owner_email' => [
                Rule::requiredIf(fn () => $request->input('owner_mode') === 'new'),
                'nullable',
                'email',
                'max:255',
                'unique:users,email',
            ],
            'new_owner_password' => [
                Rule::requiredIf(fn () => $request->input('owner_mode') === 'new'),
                'nullable',
                'string',
                'min:8',
            ],
        ]);

        $slug = $validated['slug'] ? Str::slug($validated['slug']) : Str::slug($validated['name']);

        // Guarantee uniqueness if generated
        $originalSlug = $slug;
        $counter = 1;
        while (Showroom::where('slug', $slug)->exists()) {
            $slug = "{$originalSlug}-{$counter}";
            $counter++;
        }

        $showroom = DB::transaction(function () use ($validated, $slug) {
            $ownerUser = null;

            if ($validated['owner_mode'] === 'new') {
                $ownerUser = User::create([
                    'name' => $validated['new_owner_name'],
                    'email' => $validated['new_owner_email'],
                    'password' => Hash::make($validated['new_owner_password']),
                    'role' => 'user',
                    'status' => 'active',
                ]);
            } else {
                $ownerUser = User::findOrFail($validated['owner_user_id']);
            }

            $showroom = Showroom::create([
                'name' => $validated['name'],
                'slug' => $slug,
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'city' => $validated['city'],
                'state' => $validated['state'] ?? null,
                'address_line1' => $validated['address_line1'] ?? null,
                'brand_color_primary' => $validated['brand_color_primary'] ?? '#dc2626',
                'brand_color_accent' => $validated['brand_color_accent'] ?? '#0f172a',
                'status' => $validated['status'],
                'public_website_enabled' => true,
                'read_only_mode' => false,
            ]);

            // Assign Showroom Owner Membership
            ShowroomMembership::create([
                'showroom_id' => $showroom->id,
                'user_id' => $ownerUser->id,
                'role' => 'owner',
                'status' => 'active',
                'permissions' => ['all'],
            ]);

            // Log Audit Trail
            AuditLogger::log(
                action: 'showroom.created',
                auditable: $showroom,
                oldValues: null,
                newValues: [
                    'name' => $showroom->name,
                    'slug' => $showroom->slug,
                    'owner_email' => $ownerUser->email,
                    'status' => $showroom->status,
                ],
                tenantId: $showroom->id
            );

            return $showroom;
        });

        return redirect()->route('admin.showrooms.show', $showroom)
            ->with('success', "Showroom '{$showroom->name}' successfully provisioned!");
    }

    /**
     * Display the specified showroom details and management controls.
     */
    public function show(Showroom $showroom): View
    {
        $showroom->load([
            'subscription.plan',
            'memberships.user',
            'auditLogs' => fn ($q) => $q->with('user')->latest('created_at')->take(15),
        ]);

        $availableUsers = User::whereNotIn('id', $showroom->memberships->pluck('user_id'))
            ->orderBy('name')
            ->get();

        return view('admin.showrooms.show', compact('showroom', 'availableUsers'));
    }

    /**
     * Show the form for editing the specified showroom.
     */
    public function edit(Showroom $showroom): View
    {
        return view('admin.showrooms.edit', compact('showroom'));
    }

    /**
     * Update the specified showroom in storage.
     */
    public function update(Request $request, Showroom $showroom): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'city' => ['required', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'address_line1' => ['nullable', 'string', 'max:255'],
            'brand_color_primary' => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'brand_color_accent' => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'custom_domain' => ['nullable', 'string', 'max:255', Rule::unique('showrooms', 'custom_domain')->ignore($showroom->id)],
            'public_website_enabled' => ['nullable', 'boolean'],
            'read_only_mode' => ['nullable', 'boolean'],
        ]);

        $oldValues = $showroom->only([
            'name', 'email', 'phone', 'city', 'state', 'brand_color_primary', 'brand_color_accent', 'custom_domain', 'public_website_enabled', 'read_only_mode',
        ]);

        $validated['public_website_enabled'] = $request->boolean('public_website_enabled');
        $validated['read_only_mode'] = $request->boolean('read_only_mode');

        $showroom->update($validated);

        AuditLogger::log(
            action: 'showroom.updated',
            auditable: $showroom,
            oldValues: $oldValues,
            newValues: $showroom->only(array_keys($oldValues)),
            tenantId: $showroom->id
        );

        return redirect()->route('admin.showrooms.show', $showroom)
            ->with('success', "Showroom '{$showroom->name}' settings updated successfully.");
    }

    /**
     * Toggle or update showroom status (active, trial, suspended, cancelled).
     */
    public function toggleStatus(Request $request, Showroom $showroom): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['active', 'trial', 'suspended', 'cancelled'])],
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        $oldStatus = $showroom->status;
        $showroom->update(['status' => $validated['status']]);

        AuditLogger::log(
            action: 'showroom.status_changed',
            auditable: $showroom,
            oldValues: ['status' => $oldStatus],
            newValues: [
                'status' => $validated['status'],
                'reason' => $validated['reason'] ?? 'Admin action',
            ],
            tenantId: $showroom->id
        );

        return redirect()->route('admin.showrooms.show', $showroom)
            ->with('success', "Showroom status changed to {$validated['status']}.");
    }

    /**
     * Administratively override and grant/revoke specific feature access and custom quotas for a showroom.
     */
    public function updateFeatureOverrides(Request $request, Showroom $showroom): RedirectResponse
    {
        $request->validate([
            'has_careers_module' => ['nullable', 'string', 'in:inherit,grant,deny'],
            'has_scratch_cards' => ['nullable', 'string', 'in:inherit,grant,deny'],
            'has_360_viewer' => ['nullable', 'string', 'in:inherit,grant,deny'],
            'has_ai_assistant' => ['nullable', 'string', 'in:inherit,grant,deny'],
            'has_custom_domain' => ['nullable', 'string', 'in:inherit,grant,deny'],
            'has_advanced_analytics' => ['nullable', 'string', 'in:inherit,grant,deny'],
            'max_staff_accounts' => ['nullable', 'integer', 'min:1'],
            'max_bike_listings' => ['nullable', 'integer', 'min:1'],
            'max_monthly_bookings' => ['nullable', 'integer', 'min:1'],
            'media_storage_mb' => ['nullable', 'integer', 'min:100'],
            'grant_notes' => ['nullable', 'string', 'max:500'],
        ]);

        $featureKeys = [
            'has_careers_module',
            'has_scratch_cards',
            'has_360_viewer',
            'has_ai_assistant',
            'has_custom_domain',
            'has_advanced_analytics',
        ];

        $overrides = [];

        foreach ($featureKeys as $key) {
            $val = $request->input($key, 'inherit');
            if ($val === 'grant') {
                $overrides[$key] = true;
            } elseif ($val === 'deny') {
                $overrides[$key] = false;
            }
        }

        $quotaKeys = [
            'max_staff_accounts',
            'max_bike_listings',
            'max_monthly_bookings',
            'media_storage_mb',
        ];

        foreach ($quotaKeys as $quota) {
            if ($request->filled($quota)) {
                $overrides[$quota] = (int) $request->input($quota);
            }
        }

        $settings = $showroom->settings ?? [];
        $oldOverrides = $settings['feature_overrides'] ?? [];

        if (! empty($overrides)) {
            $settings['feature_overrides'] = $overrides;
        } else {
            unset($settings['feature_overrides']);
        }
        $showroom->update(['settings' => $settings]);

        // Sync with subscription if exists
        $subscription = $showroom->subscription;
        if ($subscription) {
            $subscription->update([
                'feature_overrides' => ! empty($overrides) ? $overrides : null,
            ]);
        }

        AuditLogger::log(
            action: 'showroom.feature_overrides_updated',
            auditable: $showroom,
            oldValues: ['feature_overrides' => $oldOverrides],
            newValues: ['feature_overrides' => $overrides, 'notes' => $request->input('grant_notes')],
            tenantId: $showroom->id
        );

        return redirect()->back()
            ->with('success', "Special feature access permissions updated for '{$showroom->name}'.");
    }

    /**
     * Add a user membership to the showroom.
     */
    public function addMember(Request $request, Showroom $showroom): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'role' => ['required', Rule::in(['owner', 'sales_manager', 'service_manager', 'staff'])],
        ]);

        $user = User::findOrFail($validated['user_id']);

        $membership = ShowroomMembership::updateOrCreate(
            [
                'showroom_id' => $showroom->id,
                'user_id' => $user->id,
            ],
            [
                'role' => $validated['role'],
                'status' => 'active',
                'permissions' => ['all'],
            ]
        );

        AuditLogger::log(
            action: 'showroom.member_added',
            auditable: $showroom,
            oldValues: null,
            newValues: [
                'user_email' => $user->email,
                'role' => $validated['role'],
            ],
            tenantId: $showroom->id
        );

        return redirect()->route('admin.showrooms.show', $showroom)
            ->with('success', "User '{$user->name}' assigned role '{$validated['role']}' in {$showroom->name}.");
    }

    /**
     * Remove a member from the showroom.
     */
    public function removeMember(Showroom $showroom, ShowroomMembership $membership): RedirectResponse
    {
        if ($membership->showroom_id !== $showroom->id) {
            abort(404);
        }

        $user = $membership->user;
        $role = $membership->role;

        $membership->delete();

        AuditLogger::log(
            action: 'showroom.member_removed',
            auditable: $showroom,
            oldValues: [
                'user_email' => $user?->email,
                'role' => $role,
            ],
            newValues: null,
            tenantId: $showroom->id
        );

        return redirect()->route('admin.showrooms.show', $showroom)
            ->with('success', 'Staff membership removed successfully.');
    }

    /**
     * Delete a single showroom (Admin only).
     */
    public function destroy(Showroom $showroom): RedirectResponse
    {
        $name = $showroom->name;

        DB::transaction(function () use ($showroom) {
            $showroom->memberships()->delete();
            $showroom->delete();
        });

        AuditLogger::log(
            action: 'showroom.deleted',
            auditable: null,
            oldValues: ['name' => $name, 'id' => $showroom->id],
            newValues: null
        );

        return redirect()->route('admin.showrooms.index')
            ->with('success', "Showroom \"{$name}\" was removed successfully.");
    }

    /**
     * Delete multiple selected showrooms.
     */
    public function bulkDestroy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', 'exists:showrooms,id'],
        ]);

        $count = count($validated['ids']);

        DB::transaction(function () use ($validated) {
            ShowroomMembership::whereIn('showroom_id', $validated['ids'])->delete();
            Showroom::whereIn('id', $validated['ids'])->delete();
        });

        AuditLogger::log(
            action: 'showroom.bulk_deleted',
            auditable: null,
            oldValues: ['ids' => $validated['ids']],
            newValues: null
        );

        return redirect()->route('admin.showrooms.index')
            ->with('success', "{$count} showroom(s) were removed successfully.");
    }

    /**
     * Export showrooms to CSV.
     */
    public function export(Request $request)
    {
        $query = Showroom::with(['ownerMembership.user'])->withCount('memberships');

        if ($request->filled('ids')) {
            $ids = is_array($request->input('ids'))
                ? $request->input('ids')
                : explode(',', $request->input('ids'));
            $query->whereIn('id', $ids);
        } else {
            if ($request->filled('search')) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('city', 'like', "%{$search}%");
                });
            }

            if ($request->filled('status')) {
                $query->where('status', $request->input('status'));
            }
        }

        $showrooms = $query->orderBy('name')->get();
        $filename = 'showrooms_export_'.date('Y-m-d_His').'.csv';

        return response()->streamDownload(function () use ($showrooms) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Name', 'Slug', 'Email', 'Phone', 'City', 'Status', 'Owner', 'Members', 'Created At']);

            foreach ($showrooms as $s) {
                fputcsv($handle, [
                    $s->id,
                    $s->name,
                    $s->slug,
                    $s->email ?? 'N/A',
                    $s->phone ?? 'N/A',
                    $s->city ?? 'N/A',
                    ucfirst($s->status),
                    $s->ownerMembership?->user?->name ?? 'N/A',
                    $s->memberships_count,
                    $s->created_at?->format('Y-m-d H:i') ?? 'N/A',
                ]);
            }
            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
