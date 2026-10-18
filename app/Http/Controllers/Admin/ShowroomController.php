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
}
