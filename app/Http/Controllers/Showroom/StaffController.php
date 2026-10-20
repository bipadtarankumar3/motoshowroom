<?php

namespace App\Http\Controllers\Showroom;

use App\Http\Controllers\Controller;
use App\Models\Showroom;
use App\Models\ShowroomMembership;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StaffController extends Controller
{
    /**
     * Display the showroom staff directory, availability status, and plan quota.
     */
    public function index(Request $request, Showroom $showroom): View
    {
        $showroom->load(['subscription.plan', 'memberships.user']);

        $query = $showroom->memberships()->with('user');

        // Filter by Search Query
        if ($request->filled('q')) {
            $search = trim($request->input('q'));
            $query->where(function ($q) use ($search) {
                $q->where('designation', 'like', "%{$search}%")
                    ->orWhere('department', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by Availability Status (Available / Busy / Not Attending / Off Duty)
        if ($request->filled('status') && $request->input('status') !== 'all') {
            $query->where('availability_status', $request->input('status'));
        }

        // Filter by Department
        if ($request->filled('department') && $request->input('department') !== 'all') {
            $query->where('department', $request->input('department'));
        }

        // Filter by Role
        if ($request->filled('role') && $request->input('role') !== 'all') {
            $query->where('role', $request->input('role'));
        }

        $members = $query->latest('id')->get();

        // Compute workload metrics for each staff member
        $enquiryCounts = $showroom->enquiries()
            ->whereNotNull('assigned_to_user_id')
            ->whereIn('status', ['new', 'contacted', 'test_ride_scheduled', 'negotiation', 'booking_pending'])
            ->selectRaw('assigned_to_user_id, count(*) as count')
            ->groupBy('assigned_to_user_id')
            ->pluck('count', 'assigned_to_user_id')
            ->toArray();

        $serviceCounts = $showroom->serviceBookings()
            ->whereNotNull('assigned_to_user_id')
            ->whereIn('status', ['confirmed', 'in_progress', 'checked_in'])
            ->selectRaw('assigned_to_user_id, count(*) as count')
            ->groupBy('assigned_to_user_id')
            ->pluck('count', 'assigned_to_user_id')
            ->toArray();

        // Attach metrics to each member
        foreach ($members as $member) {
            $userId = $member->user_id;
            $member->active_enquiries_count = $enquiryCounts[$userId] ?? 0;
            $member->active_services_count = $serviceCounts[$userId] ?? 0;
        }

        // Overall Showroom Staff Telemetry & Availability Stats
        $allMembers = $showroom->memberships;
        $totalStaff = $allMembers->count();
        $availableCount = $allMembers->where('availability_status', 'available')->count();
        $busyCount = $allMembers->where('availability_status', 'busy')->count();
        $notAttendingCount = $allMembers->where('availability_status', 'not_attending')->count();
        $offDutyCount = $allMembers->where('availability_status', 'off_duty')->count();

        // Plan Quota Calculations
        $maxStaffLimit = $showroom->getMaxStaffAccounts();
        $slotsRemaining = max(0, $maxStaffLimit - $totalStaff);
        $quotaPercentage = $maxStaffLimit > 0 ? min(100, round(($totalStaff / $maxStaffLimit) * 100)) : 100;
        $canAddStaff = ! $showroom->hasReachedStaffLimit();

        // Standard Role & Department Options
        $roles = [
            'owner' => 'Showroom Owner',
            'sales_manager' => 'Sales Manager',
            'service_manager' => 'Service Manager',
            'sales_executive' => 'Sales Executive',
            'service_advisor' => 'Service Advisor',
            'technician' => 'Service Technician',
            'staff' => 'General Staff',
        ];

        $departments = [
            'Sales & Customer Relations',
            'Workshop & Service Bay',
            'Finance & Insurance',
            'Storefront & Front Desk',
            'Inventory & Logistics',
            'Management & Admin',
        ];

        return view('showrooms.manage.staff.index', compact(
            'showroom',
            'members',
            'totalStaff',
            'availableCount',
            'busyCount',
            'notAttendingCount',
            'offDutyCount',
            'maxStaffLimit',
            'slotsRemaining',
            'quotaPercentage',
            'canAddStaff',
            'roles',
            'departments'
        ));
    }

    /**
     * Store a newly created staff member in the showroom, validating plan quota.
     */
    public function store(Request $request, Showroom $showroom): RedirectResponse
    {
        // Enforce subscription plan quota
        if ($showroom->hasReachedStaffLimit()) {
            return redirect()->route('showrooms.manage.staff.index', $showroom)
                ->with('error', "Plan Quota Reached: Your current plan allows up to {$showroom->getMaxStaffAccounts()} staff members. Please upgrade your subscription to add more.")
                ->withInput();
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['nullable', 'string', 'min:8'],
            'role' => ['required', Rule::in(['sales_manager', 'service_manager', 'sales_executive', 'service_advisor', 'technician', 'staff'])],
            'department' => ['nullable', 'string', 'max:100'],
            'designation' => ['nullable', 'string', 'max:100'],
            'shift' => ['nullable', 'string', 'max:100'],
            'availability_status' => ['required', Rule::in(['available', 'busy', 'not_attending', 'off_duty'])],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $email = strtolower(trim($validated['email']));

        // Find or create the user account
        $user = User::where('email', $email)->first();

        if ($user) {
            // Check if already a member of this showroom
            if ($showroom->memberships()->where('user_id', $user->id)->exists()) {
                return redirect()->route('showrooms.manage.staff.index', $showroom)
                    ->with('error', "Staff member with email '{$email}' is already assigned to this showroom.")
                    ->withInput();
            }

            // Update user details if not filled
            if ($request->filled('phone') && empty($user->phone)) {
                $user->phone = trim($validated['phone']);
                $user->save();
            }
        } else {
            $user = User::create([
                'name' => trim($validated['name']),
                'email' => $email,
                'phone' => $validated['phone'] ? trim($validated['phone']) : null,
                'password' => Hash::make($validated['password'] ?? Str::random(12)),
                'role' => 'user',
                'status' => 'active',
            ]);
        }

        $membership = $showroom->memberships()->create([
            'user_id' => $user->id,
            'role' => $validated['role'],
            'department' => $validated['department'] ?? 'Sales & Customer Relations',
            'designation' => $validated['designation'] ?? ucwords(str_replace('_', ' ', $validated['role'])),
            'shift' => $validated['shift'] ?? 'General (9:30 AM - 6:30 PM)',
            'availability_status' => $validated['availability_status'],
            'notes' => $validated['notes'] ?? null,
            'status' => 'active',
            'permissions' => ['all'],
        ]);

        AuditLogger::log(
            action: 'showroom.staff_created',
            auditable: $showroom,
            oldValues: null,
            newValues: [
                'user_id' => $user->id,
                'email' => $user->email,
                'role' => $validated['role'],
                'availability_status' => $validated['availability_status'],
                'designation' => $membership->designation,
            ],
            tenantId: $showroom->id
        );

        return redirect()->route('showrooms.manage.staff.index', $showroom)
            ->with('success', "Staff member '{$user->name}' added successfully.");
    }

    /**
     * Update an existing staff member's details and assignments.
     */
    public function update(Request $request, Showroom $showroom, ShowroomMembership $staff): RedirectResponse
    {
        if ($staff->showroom_id !== $showroom->id) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'role' => ['required', Rule::in(['owner', 'sales_manager', 'service_manager', 'sales_executive', 'service_advisor', 'technician', 'staff'])],
            'department' => ['nullable', 'string', 'max:100'],
            'designation' => ['nullable', 'string', 'max:100'],
            'shift' => ['nullable', 'string', 'max:100'],
            'availability_status' => ['required', Rule::in(['available', 'busy', 'not_attending', 'off_duty'])],
            'notes' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', Rule::in(['active', 'suspended', 'inactive'])],
        ]);

        // Prevent demoting the showroom owner if they are the primary owner
        if ($staff->isOwner() && $validated['role'] !== 'owner') {
            return redirect()->route('showrooms.manage.staff.index', $showroom)
                ->with('error', 'The primary showroom owner role cannot be changed.');
        }

        $oldValues = $staff->only(['role', 'department', 'designation', 'shift', 'availability_status', 'notes', 'status']);

        $staff->update([
            'role' => $validated['role'],
            'department' => $validated['department'] ?? $staff->department,
            'designation' => $validated['designation'] ?? $staff->designation,
            'shift' => $validated['shift'] ?? $staff->shift,
            'availability_status' => $validated['availability_status'],
            'notes' => $validated['notes'] ?? null,
            'status' => $validated['status'],
        ]);

        // Also update user's name & phone
        if ($staff->user) {
            $staff->user->update([
                'name' => trim($validated['name']),
                'phone' => $validated['phone'] ? trim($validated['phone']) : null,
            ]);
        }

        AuditLogger::log(
            action: 'showroom.staff_updated',
            auditable: $showroom,
            oldValues: $oldValues,
            newValues: $staff->only(['role', 'department', 'designation', 'shift', 'availability_status', 'notes', 'status']),
            tenantId: $showroom->id
        );

        return redirect()->route('showrooms.manage.staff.index', $showroom)
            ->with('success', "Staff details for '{$staff->user?->name}' updated successfully.");
    }

    /**
     * Quick status toggle (Available / Busy / Not Attending / Off Duty).
     */
    public function updateStatus(Request $request, Showroom $showroom, ShowroomMembership $staff): JsonResponse|RedirectResponse
    {
        if ($staff->showroom_id !== $showroom->id) {
            abort(404);
        }

        $validated = $request->validate([
            'availability_status' => ['required', Rule::in(['available', 'busy', 'not_attending', 'off_duty'])],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $oldStatus = $staff->availability_status;

        $staff->update([
            'availability_status' => $validated['availability_status'],
            'notes' => $request->has('notes') ? $validated['notes'] : $staff->notes,
        ]);

        AuditLogger::log(
            action: 'showroom.staff_status_toggled',
            auditable: $showroom,
            oldValues: ['availability_status' => $oldStatus],
            newValues: [
                'availability_status' => $validated['availability_status'],
                'user_id' => $staff->user_id,
            ],
            tenantId: $showroom->id
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Status updated to {$validated['availability_status']}.",
                'status' => $validated['availability_status'],
            ]);
        }

        $statusLabels = [
            'available' => 'Available (On Duty)',
            'busy' => 'Busy (With Customer / In Workshop)',
            'not_attending' => 'Not Attending (On Leave / Absent)',
            'off_duty' => 'Off Duty',
        ];

        $label = $statusLabels[$validated['availability_status']] ?? $validated['availability_status'];

        return redirect()->route('showrooms.manage.staff.index', $showroom)
            ->with('success', "{$staff->user?->name}'s status changed to {$label}.");
    }

    /**
     * Remove a staff member from the showroom.
     */
    public function destroy(Showroom $showroom, ShowroomMembership $staff): RedirectResponse
    {
        if ($staff->showroom_id !== $showroom->id) {
            abort(404);
        }

        if ($staff->isOwner()) {
            return redirect()->route('showrooms.manage.staff.index', $showroom)
                ->with('error', 'Cannot remove the primary showroom owner account.');
        }

        $userName = $staff->user?->name ?? 'Staff member';
        $userEmail = $staff->user?->email;

        // Reassign or clear active leads / service bookings assigned to this user in this showroom
        $showroom->enquiries()->where('assigned_to_user_id', $staff->user_id)->update(['assigned_to_user_id' => null]);
        $showroom->serviceBookings()->where('assigned_to_user_id', $staff->user_id)->update(['assigned_to_user_id' => null]);

        $staff->delete();

        AuditLogger::log(
            action: 'showroom.staff_removed',
            auditable: $showroom,
            oldValues: [
                'user_name' => $userName,
                'email' => $userEmail,
                'role' => $staff->role,
            ],
            newValues: null,
            tenantId: $showroom->id
        );

        return redirect()->route('showrooms.manage.staff.index', $showroom)
            ->with('success', "'{$userName}' has been removed from showroom staff.");
    }
}
