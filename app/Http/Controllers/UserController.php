<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminCreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display a listing of users for administrators.
     */
    public function index(Request $request): View
    {
        $query = User::query()->latest();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->input('role'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $users = $query->paginate(12)->withQueryString();

        $stats = [
            'total' => User::count(),
            'admins' => User::where('role', 'admin')->count(),
            'regular_users' => User::where('role', 'user')->count(),
            'active' => User::where('status', 'active')->count(),
        ];

        return view('users.index', compact('users', 'stats'));
    }

    /**
     * Show the form for creating a new user (Admin only).
     */
    public function create(): View
    {
        return view('users.create');
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
     * Display the specified user's detail page.
     * Every user has their own page with details.
     */
    public function show(User $user): View|RedirectResponse
    {
        $currentUser = Auth::user();

        // Security check: non-admins can only view their own user details page
        if (! $currentUser->isAdmin() && $currentUser->id !== $user->id) {
            abort(403, 'Unauthorized access to user profile.');
        }

        $isOwnProfile = $currentUser->id === $user->id;

        return view('users.show', compact('user', 'isOwnProfile'));
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

        return view('users.edit', compact('user', 'isOwnProfile'));
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
        $query = User::query()->with('showrooms');

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
                        ->orWhere('city', 'like', "%{$search}%");
                });
            }

            if ($request->filled('role')) {
                $query->where('role', $request->input('role'));
            }

            if ($request->filled('status')) {
                $query->where('status', $request->input('status'));
            }
        }

        $users = $query->orderBy('name')->get();

        $filename = 'users_export_'.date('Y-m-d_His').'.csv';

        return response()->streamDownload(function () use ($users) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Name', 'Email', 'Role', 'Status', 'Phone', 'City', 'Showroom(s)', 'Joined Date']);

            foreach ($users as $user) {
                $showrooms = $user->showrooms->pluck('name')->implode(', ');
                fputcsv($handle, [
                    $user->id,
                    $user->name,
                    $user->email,
                    strtoupper($user->role),
                    ucfirst($user->status),
                    $user->phone ?? 'N/A',
                    $user->city ?? 'N/A',
                    $showrooms ?: 'None',
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
