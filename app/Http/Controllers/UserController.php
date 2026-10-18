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
     * Redirect to authenticated user's own details page.
     */
    public function profile(): RedirectResponse
    {
        return redirect()->route('users.show', Auth::user());
    }
}
