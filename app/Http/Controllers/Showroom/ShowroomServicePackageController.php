<?php

namespace App\Http\Controllers\Showroom;

use App\Http\Controllers\Controller;
use App\Models\Showroom;
use App\Models\ShowroomService;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ShowroomServicePackageController extends Controller
{
    /**
     * Store a newly created custom service package for the showroom.
     */
    public function store(Request $request, Showroom $showroom): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'price' => ['required', 'numeric', 'min:0', 'max:1000000'],
            'estimated_duration_minutes' => ['required', 'integer', 'min:5', 'max:1440'],
            'badge' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:2000'],
            'features' => ['nullable', 'string', 'max:2000'],
        ]);

        $featuresList = $this->parseFeatures($request->input('features'));

        $service = $showroom->services()->create([
            'name' => trim($validated['name']),
            'slug' => Str::slug($validated['name']).'-'.substr(md5(uniqid()), 0, 4),
            'price' => $validated['price'],
            'estimated_duration_minutes' => $validated['estimated_duration_minutes'],
            'is_payment_enabled' => $request->boolean('is_payment_enabled', true),
            'is_active' => $request->boolean('is_active', true),
            'badge' => $validated['badge'] ? trim($validated['badge']) : null,
            'description' => $validated['description'] ? trim($validated['description']) : null,
            'features' => $featuresList,
            'sort_order' => $showroom->services()->max('sort_order') + 1,
        ]);

        AuditLogger::log(
            'showroom_service.created',
            $service,
            null,
            [
                'service_name' => $service->name,
                'price' => $service->price,
                'is_payment_enabled' => $service->is_payment_enabled,
            ],
            $showroom->id,
            Auth::user()
        );

        return redirect()->route('showrooms.manage.services.index', [$showroom, 'tab' => 'catalog'])
            ->with('success', "Service package '{$service->name}' created successfully with amount ₹".number_format($service->price).'.');
    }

    /**
     * Update an existing service package.
     */
    public function update(Request $request, Showroom $showroom, ShowroomService $service): RedirectResponse
    {
        abort_if($service->tenant_id !== $showroom->id, 404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'price' => ['required', 'numeric', 'min:0', 'max:1000000'],
            'estimated_duration_minutes' => ['required', 'integer', 'min:5', 'max:1440'],
            'badge' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:2000'],
            'features' => ['nullable', 'string', 'max:2000'],
        ]);

        $featuresList = $this->parseFeatures($request->input('features'));

        $service->update([
            'name' => trim($validated['name']),
            'price' => $validated['price'],
            'estimated_duration_minutes' => $validated['estimated_duration_minutes'],
            'is_payment_enabled' => $request->boolean('is_payment_enabled'),
            'is_active' => $request->boolean('is_active', true),
            'badge' => $validated['badge'] ? trim($validated['badge']) : null,
            'description' => $validated['description'] ? trim($validated['description']) : null,
            'features' => $featuresList,
        ]);

        AuditLogger::log(
            'showroom_service.updated',
            $service,
            null,
            [
                'service_name' => $service->name,
                'price' => $service->price,
                'is_payment_enabled' => $service->is_payment_enabled,
            ],
            $showroom->id,
            Auth::user()
        );

        return redirect()->route('showrooms.manage.services.index', [$showroom, 'tab' => 'catalog'])
            ->with('success', "Service package '{$service->name}' updated successfully.");
    }

    /**
     * Remove the specified service package.
     */
    public function destroy(Showroom $showroom, ShowroomService $service): RedirectResponse
    {
        abort_if($service->tenant_id !== $showroom->id, 404);

        $name = $service->name;
        $service->delete();

        AuditLogger::log(
            'showroom_service.deleted',
            $service,
            null,
            ['service_name' => $name],
            $showroom->id,
            Auth::user()
        );

        return redirect()->route('showrooms.manage.services.index', [$showroom, 'tab' => 'catalog'])
            ->with('success', "Service package '{$name}' removed from active offerings.");
    }

    /**
     * Toggle online advance payment requirement for this service.
     */
    public function togglePayment(Showroom $showroom, ShowroomService $service): RedirectResponse
    {
        abort_if($service->tenant_id !== $showroom->id, 404);

        $service->is_payment_enabled = ! $service->is_payment_enabled;
        $service->save();

        $statusText = $service->is_payment_enabled ? 'Online Advance Payment Enabled' : 'Payment at Bay / Workshop Enabled';

        return redirect()->route('showrooms.manage.services.index', [$showroom, 'tab' => 'catalog'])
            ->with('success', "Payment mode for '{$service->name}' updated: {$statusText}.");
    }

    /**
     * Toggle active/inactive status for this service.
     */
    public function toggleActive(Showroom $showroom, ShowroomService $service): RedirectResponse
    {
        abort_if($service->tenant_id !== $showroom->id, 404);

        $service->is_active = ! $service->is_active;
        $service->save();

        $statusText = $service->is_active ? 'Activated on Public Booking' : 'Deactivated / Hidden from Public';

        return redirect()->route('showrooms.manage.services.index', [$showroom, 'tab' => 'catalog'])
            ->with('success', "'{$service->name}' is now {$statusText}.");
    }

    /**
     * Parse features input into an array of clean string bullet items.
     *
     * @return list<string>
     */
    protected function parseFeatures(?string $input): array
    {
        if (empty($input)) {
            return [];
        }

        $lines = preg_split('/[\r\n]+|,/', $input);
        if (! is_array($lines)) {
            return [];
        }

        $items = [];
        foreach ($lines as $line) {
            $cleaned = trim(ltrim(trim($line), '•-* '));
            if ($cleaned !== '') {
                $items[] = $cleaned;
            }
        }

        return array_values(array_unique($items));
    }
}
