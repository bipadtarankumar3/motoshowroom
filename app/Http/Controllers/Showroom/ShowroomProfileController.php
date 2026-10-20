<?php

namespace App\Http\Controllers\Showroom;

use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
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

class ShowroomProfileController extends Controller
{
    /**
     * Display the showroom owner's profile page.
     */
    public function show(Request $request, ?Showroom $showroom = null): View|RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        // If showroom is not explicitly in the route, resolve from user or tenant
        if (! $showroom || ! $showroom->exists) {
            $showroom = $user->showrooms()->first();
        }

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
            ->where(function ($q) use ($user, $userShowroomIds) {
                $q->where('user_id', $user->id)
                    ->orWhere('customer_email', $user->email);
                if (! empty($userShowroomIds)) {
                    $q->orWhereIn('tenant_id', $userShowroomIds);
                }
            })
            ->with(['bike', 'showroom'])
            ->latest()
            ->paginate(8, ['*'], 'enquiries_page')
            ->withQueryString();

        $serviceBookings = ServiceBooking::query()
            ->where(function ($q) use ($user, $userShowroomIds) {
                $q->where('user_id', $user->id)
                    ->orWhere('customer_email', $user->email);
                if (! empty($userShowroomIds)) {
                    $q->orWhereIn('tenant_id', $userShowroomIds);
                }
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
            'enquiries_count' => Enquiry::query()
                ->where(function ($q) use ($user, $userShowroomIds) {
                    $q->where('user_id', $user->id)
                        ->orWhere('customer_email', $user->email);
                    if (! empty($userShowroomIds)) {
                        $q->orWhereIn('tenant_id', $userShowroomIds);
                    }
                })->count(),
            'bookings_count' => ServiceBooking::query()
                ->where(function ($q) use ($user, $userShowroomIds) {
                    $q->where('user_id', $user->id)
                        ->orWhere('customer_email', $user->email);
                    if (! empty($userShowroomIds)) {
                        $q->orWhereIn('tenant_id', $userShowroomIds);
                    }
                })->count(),
            'showrooms_count' => $user->showrooms->count(),
            'audit_events_count' => AuditLog::where('user_id', $user->id)->count(),
        ];

        return view('showrooms.profile.show', [
            'user' => $user,
            'showroom' => $showroom,
            'isOwnProfile' => true,
            'payments' => $payments,
            'enquiries' => $enquiries,
            'serviceBookings' => $serviceBookings,
            'auditLogs' => $auditLogs,
            'telemetry' => $telemetry,
        ]);
    }

    /**
     * Show the edit form for the showroom owner.
     */
    public function edit(Request $request, ?Showroom $showroom = null): View
    {
        /** @var User $user */
        $user = Auth::user();

        if (! $showroom || ! $showroom->exists) {
            $showroom = $user->showrooms()->first();
        }

        return view('showrooms.profile.edit', [
            'user' => $user,
            'showroom' => $showroom,
            'isOwnProfile' => true,
        ]);
    }

    /**
     * Update the showroom owner's details.
     */
    public function update(UpdateUserRequest $request, ?Showroom $showroom = null): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

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

        $user->update($dataToUpdate);

        $redirectRoute = ($showroom && $showroom->exists)
            ? redirect()->route('showrooms.manage.profile', $showroom)
            : redirect()->route('profile');

        return $redirectRoute->with('success', 'Your showroom owner profile has been updated successfully.');
    }
}
