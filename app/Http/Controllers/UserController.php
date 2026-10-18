<?php

namespace App\Http\Controllers;

use App\Enums\PaymentStatus;
use App\Enums\SubscriptionStatus;
use App\Http\Requests\AdminCreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\AuditLog;
use App\Models\Enquiry;
use App\Models\Payment;
use App\Models\ServiceBooking;
use App\Models\Showroom;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display a listing of users for administrators with rich filters and metrics.
     */
    public function index(Request $request): View
    {
        $query = User::query()
            ->with([
                'showrooms.subscription.plan',
                'memberships.showroom',
                'payments' => fn ($p) => $p->latest()->limit(1),
            ]);

        // Search text (name, email, phone, city, or showroom name)
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%")
                    ->orWhereHas('showrooms', fn ($s) => $s->where('name', 'like', "%{$search}%"));
            });
        }

        // Role filter
        if ($request->filled('role')) {
            $query->where('role', $request->input('role'));
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Specific Showroom Association filter
        if ($request->filled('showroom_id')) {
            $showroomFilterId = $request->input('showroom_id');
            $query->whereHas('showrooms', fn ($s) => $s->where('showrooms.id', $showroomFilterId));
        }

        // Quick Preset Filters (today, week, month, expired, recently_paid, etc.)
        $preset = $request->input('preset');
        if ($preset) {
            match ($preset) {
                'today' => $query->whereDate('created_at', today()),
                'week' => $query->where('created_at', '>=', now()->subDays(7)),
                'month' => $query->where('created_at', '>=', now()->subDays(30)),
                'expired' => $query->where(function ($q) {
                    $q->whereIn('status', ['inactive', 'suspended'])
                        ->orWhereHas('showrooms.subscriptions', function ($sub) {
                            $sub->whereIn('status', [
                                SubscriptionStatus::PastDue->value,
                                SubscriptionStatus::Expired->value,
                                SubscriptionStatus::Cancelled->value,
                            ])->orWhere('current_period_end', '<', now());
                        });
                }),
                'recently_paid' => $query->where(function ($q) {
                    $q->whereHas('payments', function ($p) {
                        $p->where('status', PaymentStatus::Captured->value)
                            ->where('created_at', '>=', now()->subDays(30));
                    })->orWhereHas('showrooms.subscriptions', function ($s) {
                        $s->where('status', SubscriptionStatus::Active->value)
                            ->where('created_at', '>=', now()->subDays(30));
                    });
                }),
                'owners' => $query->whereHas('memberships', fn ($m) => $m->where('role', 'owner')),
                'staff' => $query->whereHas('memberships', fn ($m) => $m->where('role', '!=', 'owner')),
                'admins' => $query->where('role', 'admin'),
                'verified' => $query->whereNotNull('email_verified_at'),
                'unverified' => $query->whereNull('email_verified_at'),
                default => null,
            };
        }

        // Sorting
        $sort = $request->input('sort', 'latest');
        match ($sort) {
            'oldest' => $query->oldest(),
            'name_asc' => $query->orderBy('name', 'asc'),
            'name_desc' => $query->orderBy('name', 'desc'),
            default => $query->latest(),
        };

        $users = $query->paginate(12)->withQueryString();

        $stats = [
            'total' => User::count(),
            'registered_today' => User::whereDate('created_at', today())->count(),
            'registered_this_week' => User::where('created_at', '>=', now()->subDays(7))->count(),
            'registered_this_month' => User::where('created_at', '>=', now()->subDays(30))->count(),
            'expired_or_suspended' => User::where(function ($q) {
                $q->whereIn('status', ['inactive', 'suspended'])
                    ->orWhereHas('showrooms.subscriptions', function ($sub) {
                        $sub->whereIn('status', [
                            SubscriptionStatus::PastDue->value,
                            SubscriptionStatus::Expired->value,
                            SubscriptionStatus::Cancelled->value,
                        ])->orWhere('current_period_end', '<', now());
                    });
            })->count(),
            'recently_paid' => User::where(function ($q) {
                $q->whereHas('payments', function ($p) {
                    $p->where('status', PaymentStatus::Captured->value)
                        ->where('created_at', '>=', now()->subDays(30));
                })->orWhereHas('showrooms.subscriptions', function ($s) {
                    $s->where('status', SubscriptionStatus::Active->value)
                        ->where('created_at', '>=', now()->subDays(30));
                });
            })->count(),
            'admins' => User::where('role', 'admin')->count(),
            'regular_users' => User::where('role', 'user')->count(),
            'owners' => User::whereHas('memberships', fn ($m) => $m->where('role', 'owner'))->count(),
            'active' => User::where('status', 'active')->count(),
        ];

        $allShowrooms = Showroom::orderBy('name')->get(['id', 'name', 'slug']);

        return view('admin.users.index', compact('users', 'stats', 'allShowrooms'));
    }

    /**
     * Show the form for creating a new user (Admin only).
     */
    public function create(): View
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created user in storage (Admin action).
     */
    public function store(AdminCreateUserRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => $validated['role'],
            'phone' => $validated['phone'] ?? null,
            'status' => $validated['status'] ?? 'active',
            'bio' => $validated['bio'] ?? null,
            'address' => $validated['address'] ?? null,
            'city' => $validated['city'] ?? null,
        ]);

        return redirect()->route('users.show', $user)->with('success', "User '{$user->name}' was created successfully!");
    }

    /**
     * Display the specified user's detail page with multi-dimensional telemetry,
     * showroom subscriptions, payment ledger, enquiries, and service bookings.
     */
    public function show(User $user): View|RedirectResponse
    {
        $currentUser = Auth::user();

        // Security check: non-admins can only view their own user details page
        if (! $currentUser->isAdmin() && $currentUser->id !== $user->id) {
            abort(403, 'Unauthorized access to user profile.');
        }

        $isOwnProfile = $currentUser->id === $user->id;

        $user->load([
            'showrooms.subscription.plan',
            'showrooms.bikes',
            'memberships.showroom.subscription.plan',
            'socialAccounts',
        ]);

        $userShowroomIds = $user->showrooms->pluck('id')->all();

        $payments = Payment::query()
            ->where(function ($q) use ($user, $userShowroomIds) {
                $q->where('user_id', $user->id);
                if (! empty($userShowroomIds)) {
                    $q->orWhereIn('tenant_id', $userShowroomIds);
                }
            })
            ->with('showroom')
            ->latest()
            ->paginate(8, ['*'], 'payments_page')
            ->withQueryString();

        $enquiries = Enquiry::query()
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->orWhere('customer_email', $user->email);
            })
            ->with(['bike', 'showroom'])
            ->latest()
            ->paginate(8, ['*'], 'enquiries_page')
            ->withQueryString();

        $serviceBookings = ServiceBooking::query()
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->orWhere('customer_email', $user->email);
            })
            ->with(['bike', 'showroom'])
            ->latest()
            ->paginate(8, ['*'], 'bookings_page')
            ->withQueryString();

        $auditLogs = AuditLog::query()
            ->where('user_id', $user->id)
            ->latest()
            ->limit(15)
            ->get();

        $telemetry = [
            'total_spend' => (float) Payment::query()
                ->where(function ($q) use ($user, $userShowroomIds) {
                    $q->where('user_id', $user->id);
                    if (! empty($userShowroomIds)) {
                        $q->orWhereIn('tenant_id', $userShowroomIds);
                    }
                })
                ->where('status', PaymentStatus::Captured->value)
                ->sum('amount'),
            'captured_payments_count' => Payment::query()
                ->where(function ($q) use ($user, $userShowroomIds) {
                    $q->where('user_id', $user->id);
                    if (! empty($userShowroomIds)) {
                        $q->orWhereIn('tenant_id', $userShowroomIds);
                    }
                })
                ->where('status', PaymentStatus::Captured->value)
                ->count(),
            'last_payment' => Payment::query()
                ->where(function ($q) use ($user, $userShowroomIds) {
                    $q->where('user_id', $user->id);
                    if (! empty($userShowroomIds)) {
                        $q->orWhereIn('tenant_id', $userShowroomIds);
                    }
                })
                ->latest()
                ->first(),
            'enquiries_count' => Enquiry::query()
                ->where(function ($q) use ($user) {
                    $q->where('user_id', $user->id)
                        ->orWhere('customer_email', $user->email);
                })->count(),
            'bookings_count' => ServiceBooking::query()
                ->where(function ($q) use ($user) {
                    $q->where('user_id', $user->id)
                        ->orWhere('customer_email', $user->email);
                })->count(),
            'showrooms_count' => $user->showrooms->count(),
            'audit_events_count' => AuditLog::where('user_id', $user->id)->count(),
        ];

        return view('admin.users.show', compact(
            'user',
            'isOwnProfile',
            'payments',
            'enquiries',
            'serviceBookings',
            'auditLogs',
            'telemetry'
        ));
    }

    /**
     * Show the edit details form for the specified user.
     */
    public function edit(User $user): View
    {
        $currentUser = Auth::user();

        if (! $currentUser->isAdmin() && $currentUser->id !== $user->id) {
            abort(403, 'Unauthorized to edit this user details.');
        }

        $isOwnProfile = $currentUser->id === $user->id;

        return view('admin.users.edit', compact('user', 'isOwnProfile'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $validated = $request->validated();

        $dataToUpdate = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'bio' => $validated['bio'] ?? null,
            'address' => $validated['address'] ?? null,
            'city' => $validated['city'] ?? null,
        ];

        if (! empty($validated['password'])) {
            $dataToUpdate['password'] = $validated['password'];
        }

        // Only admins can alter role and status
        if ($request->user()?->isAdmin()) {
            if (isset($validated['role'])) {
                $dataToUpdate['role'] = $validated['role'];
            }
            if (isset($validated['status'])) {
                $dataToUpdate['status'] = $validated['status'];
            }
        }

        $user->update($dataToUpdate);

        return redirect()->route('users.show', $user)->with('success', 'User profile details updated successfully.');
    }

    /**
     * Delete a single user (Admin only).
     */
    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot delete your own administrator account.');
        }

        $userName = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', "User \"{$userName}\" was deleted successfully.");
    }

    /**
     * Delete multiple selected users (Admin only).
     */
    public function bulkDestroy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', 'exists:users,id'],
        ]);

        $idsToDelete = array_diff($validated['ids'], [Auth::id()]);
        $count = count($idsToDelete);

        if ($count === 0) {
            return back()->with('error', 'No valid users to delete (you cannot delete yourself).');
        }

        User::whereIn('id', $idsToDelete)->delete();

        return redirect()->route('admin.users.index')
            ->with('success', "{$count} user(s) were deleted successfully.");
    }

    /**
     * Export users matching filters or selected IDs to CSV.
     */
    public function export(Request $request)
    {
        $query = User::query()->with(['showrooms.subscription.plan']);

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
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('city', 'like', "%{$search}%")
                        ->orWhereHas('showrooms', fn ($s) => $s->where('name', 'like', "%{$search}%"));
                });
            }

            if ($request->filled('role')) {
                $query->where('role', $request->input('role'));
            }

            if ($request->filled('status')) {
                $query->where('status', $request->input('status'));
            }

            if ($request->filled('showroom_id')) {
                $query->whereHas('showrooms', fn ($s) => $s->where('showrooms.id', $request->input('showroom_id')));
            }

            $preset = $request->input('preset');
            if ($preset) {
                match ($preset) {
                    'today' => $query->whereDate('created_at', today()),
                    'week' => $query->where('created_at', '>=', now()->subDays(7)),
                    'month' => $query->where('created_at', '>=', now()->subDays(30)),
                    'expired' => $query->where(function ($q) {
                        $q->whereIn('status', ['inactive', 'suspended'])
                            ->orWhereHas('showrooms.subscriptions', function ($sub) {
                                $sub->whereIn('status', [
                                    SubscriptionStatus::PastDue->value,
                                    SubscriptionStatus::Expired->value,
                                    SubscriptionStatus::Cancelled->value,
                                ])->orWhere('current_period_end', '<', now());
                            });
                    }),
                    'recently_paid' => $query->where(function ($q) {
                        $q->whereHas('payments', function ($p) {
                            $p->where('status', PaymentStatus::Captured->value)
                                ->where('created_at', '>=', now()->subDays(30));
                        })->orWhereHas('showrooms.subscriptions', function ($s) {
                            $s->where('status', SubscriptionStatus::Active->value)
                                ->where('created_at', '>=', now()->subDays(30));
                        });
                    }),
                    'owners' => $query->whereHas('memberships', fn ($m) => $m->where('role', 'owner')),
                    'staff' => $query->whereHas('memberships', fn ($m) => $m->where('role', '!=', 'owner')),
                    'admins' => $query->where('role', 'admin'),
                    default => null,
                };
            }
        }

        $users = $query->orderBy('name')->get();

        $filename = 'users_export_'.date('Y-m-d_His').'.csv';

        return response()->streamDownload(function () use ($users) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Name', 'Email', 'Role', 'Status', 'Phone', 'City', 'Showroom(s)', 'Subscription Plan', 'Joined Date']);

            foreach ($users as $user) {
                $showrooms = $user->showrooms->pluck('name')->implode(', ');
                $planNames = $user->showrooms->map(fn ($s) => $s->subscription?->plan?->name)->filter()->implode(', ');

                fputcsv($handle, [
                    $user->id,
                    $user->name,
                    $user->email,
                    strtoupper($user->role),
                    ucfirst($user->status),
                    $user->phone ?? 'N/A',
                    $user->city ?? 'N/A',
                    $showrooms ?: 'None',
                    $planNames ?: 'N/A',
                    $user->created_at?->format('Y-m-d H:i') ?? 'N/A',
                ]);
            }
            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Admin visits/impersonates a user's dashboard.
     */
    public function impersonate(User $user): RedirectResponse
    {
        abort_unless(Auth::user()->isAdmin(), 403, 'Unauthorized.');

        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot impersonate your own account.');
        }

        // Store original admin ID in session
        session(['admin_impersonator_id' => Auth::id()]);

        // Login as the target user
        Auth::login($user);

        // Redirect to target user's dashboard (showroom back-office or personal details)
        $showroom = $user->showrooms()->first();
        if ($showroom) {
            return redirect()->route('showrooms.manage', $showroom)
                ->with('success', "Now navigating as {$user->name}. You have access to their showroom dashboard.");
        }

        return redirect()->route('users.show', $user)
            ->with('success', "Now viewing dashboard as {$user->name}.");
    }

    /**
     * Return back to super admin dashboard from an impersonation session.
     */
    public function leaveImpersonate(): RedirectResponse
    {
        if (! session()->has('admin_impersonator_id')) {
            return redirect()->route('dashboard');
        }

        $adminId = session('admin_impersonator_id');
        session()->forget('admin_impersonator_id');

        Auth::loginUsingId($adminId);

        return redirect()->route('admin.users.index')
            ->with('success', 'Returned to Super Admin dashboard successfully.');
    }

    /**
     * Redirect to authenticated user's primary dashboard (Admin overview or Showroom Manage).
     */
    public function dashboard(): RedirectResponse
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        $showroom = $user->showrooms()->first();
        if ($showroom) {
            return redirect()->route('showrooms.manage', $showroom);
        }

        return redirect()->route('users.show', $user);
    }

    /**
     * Redirect to authenticated user's own details page.
     */
    public function profile(): RedirectResponse
    {
        return redirect()->route('users.show', Auth::user());
    }
}
