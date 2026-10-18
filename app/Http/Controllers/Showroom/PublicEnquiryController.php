<?php

namespace App\Http\Controllers\Showroom;

use App\Enums\EnquiryStatus;
use App\Enums\EnquiryType;
use App\Http\Controllers\Controller;
use App\Models\Enquiry;
use App\Models\Showroom;
use App\Services\AuditLogger;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class PublicEnquiryController extends Controller
{
    /**
     * Display public enquiry and test-ride booking form for this showroom.
     */
    public function create(Request $request, Showroom $showroom): View
    {
        $bikeSlug = $request->query('bike');
        $selectedBike = null;
        if (! empty($bikeSlug)) {
            $selectedBike = $showroom->publishedBikes()->where('slug', $bikeSlug)->first();
        }

        $type = $request->query('type', EnquiryType::Purchase->value);
        if (! in_array($type, array_column(EnquiryType::cases(), 'value'), true)) {
            $type = EnquiryType::Purchase->value;
        }

        $bikes = $showroom->publishedBikes()->orderBy('name')->get();

        return view('showrooms.enquiries.create', compact('showroom', 'selectedBike', 'bikes', 'type'));
    }

    /**
     * Store a newly submitted customer enquiry or test-ride request.
     */
    public function store(Request $request, Showroom $showroom): RedirectResponse
    {
        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:150'],
            'customer_email' => ['required', 'email', 'max:150'],
            'customer_phone' => ['required', 'string', 'max:30'],
            'type' => ['required', 'string', Rule::in(array_column(EnquiryType::cases(), 'value'))],
            'bike_id' => [
                'nullable',
                Rule::exists('bikes', 'id')->where('tenant_id', $showroom->id),
            ],
            'preferred_date' => ['nullable', 'date', 'after_or_equal:today'],
            'preferred_time_slot' => ['nullable', 'string', 'max:50'],
            'driving_license_number' => ['nullable', 'string', 'max:50'],
            'customer_notes' => ['nullable', 'string', 'max:1500'],
        ]);

        $enquiry = $showroom->enquiries()->create([
            'user_id' => Auth::id(),
            'bike_id' => $validated['bike_id'] ?? null,
            'type' => $validated['type'],
            'customer_name' => $validated['customer_name'],
            'customer_email' => $validated['customer_email'],
            'customer_phone' => $validated['customer_phone'],
            'preferred_date' => $validated['preferred_date'] ?? null,
            'preferred_time_slot' => $validated['preferred_time_slot'] ?? null,
            'driving_license_number' => $validated['driving_license_number'] ?? null,
            'customer_notes' => $validated['customer_notes'] ?? null,
            'status' => EnquiryStatus::New->value,
        ]);

        AuditLogger::log(
            'enquiry.created',
            $enquiry,
            null,
            [
                'enquiry_number' => $enquiry->enquiry_number,
                'customer_name' => $enquiry->customer_name,
                'type' => $enquiry->type->value,
                'bike_id' => $enquiry->bike_id,
            ],
            $showroom->id,
            Auth::user()
        );

        $typeLabel = $enquiry->type->label();

        return redirect()->back()->with(
            'success',
            "Thank you, {$enquiry->customer_name}! Your {$typeLabel} (#{$enquiry->enquiry_number}) has been registered. Our dealership team will get in touch with you shortly."
        );
    }
}
