<?php

namespace App\Http\Controllers\Showroom;

use App\Enums\CampaignStatus;
use App\Enums\PrizeType;
use App\Http\Controllers\Controller;
use App\Models\PromotionalCampaign;
use App\Models\Showroom;
use App\Services\AuditLogger;
use App\Services\ScratchCardService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CampaignManagementController extends Controller
{
    /**
     * Display a listing of promotional campaigns and scratch card marketing metrics.
     */
    public function index(Request $request, Showroom $showroom): View
    {
        $hasModuleAccess = $showroom->canAccessFeature('has_scratch_cards');

        $search = $request->query('search');
        $status = $request->query('status');

        $query = $showroom->promotionalCampaigns()
            ->withCount(['prizes', 'participations'])
            ->search($search);

        if (! empty($status) && $status !== 'all') {
            $query->where('status', $status);
        }

        $campaigns = $query->latest()->paginate(15)->withQueryString();

        $totalCampaigns = $showroom->promotionalCampaigns()->count();
        $activeCampaigns = $showroom->activePromotionalCampaigns()->count();
        $totalParticipations = $showroom->scratchCardParticipations()->count();
        $totalScratched = $showroom->scratchCardParticipations()->where('is_scratched', true)->count();
        $totalRedeemed = $showroom->scratchCardParticipations()->where('is_redeemed', true)->count();

        $statuses = CampaignStatus::cases();

        return view('showrooms.manage.campaigns.index', compact(
            'showroom',
            'hasModuleAccess',
            'campaigns',
            'totalCampaigns',
            'activeCampaigns',
            'totalParticipations',
            'totalScratched',
            'totalRedeemed',
            'statuses',
            'search',
            'status'
        ));
    }

    /**
     * Show the form for creating a new promotional scratch card campaign.
     */
    public function create(Showroom $showroom): View|RedirectResponse
    {
        if (! $showroom->canAccessFeature('has_scratch_cards')) {
            return redirect()->route('showrooms.manage.campaigns.index', $showroom)
                ->with('error', 'Your current subscription plan does not include Promotional Scratch Cards. Please upgrade your plan.');
        }

        $prizeTypes = PrizeType::cases();

        return view('showrooms.manage.campaigns.create', compact('showroom', 'prizeTypes'));
    }

    /**
     * Store a newly created campaign and configured prize tiers.
     */
    public function store(Request $request, Showroom $showroom): RedirectResponse
    {
        if (! $showroom->canAccessFeature('has_scratch_cards')) {
            return redirect()->route('showrooms.manage.campaigns.index', $showroom)
                ->with('error', 'Your current subscription plan does not include Promotional Scratch Cards.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:1000'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'status' => ['required', 'string', Rule::in(array_column(CampaignStatus::cases(), 'value'))],
            'min_booking_amount' => ['nullable', 'numeric', 'min:0'],
            'max_participations_per_customer' => ['required', 'integer', 'min:1', 'max:20'],
            'terms_and_conditions' => ['nullable', 'string', 'max:3000'],
            'prizes' => ['required', 'array', 'min:1'],
            'prizes.*.title' => ['required', 'string', 'max:150'],
            'prizes.*.prize_type' => ['required', 'string', Rule::in(array_column(PrizeType::cases(), 'value'))],
            'prizes.*.discount_amount' => ['nullable', 'numeric', 'min:0'],
            'prizes.*.discount_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'prizes.*.total_quantity' => ['required', 'integer', 'min:0'],
            'prizes.*.probability_weight' => ['required', 'integer', 'min:1', 'max:100'],
        ]);

        /** @var PromotionalCampaign $campaign */
        $campaign = DB::transaction(function () use ($showroom, $validated) {
            $campaign = $showroom->promotionalCampaigns()->create([
                'tenant_id' => $showroom->id,
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'status' => $validated['status'],
                'min_booking_amount' => $validated['min_booking_amount'] ?? 0.00,
                'max_participations_per_customer' => $validated['max_participations_per_customer'],
                'terms_and_conditions' => $validated['terms_and_conditions'] ?? null,
            ]);

            foreach ($validated['prizes'] as $prizeData) {
                $qty = (int) $prizeData['total_quantity'];
                $campaign->prizes()->create([
                    'tenant_id' => $showroom->id,
                    'title' => $prizeData['title'],
                    'prize_type' => $prizeData['prize_type'],
                    'discount_amount' => $prizeData['discount_amount'] ?? 0.00,
                    'discount_percentage' => $prizeData['discount_percentage'] ?? 0.00,
                    'total_quantity' => $qty,
                    'remaining_quantity' => $qty,
                    'probability_weight' => (int) $prizeData['probability_weight'],
                ]);
            }

            return $campaign;
        });

        AuditLogger::log(
            'campaign.created',
            $campaign,
            null,
            $campaign->only(['name', 'status', 'start_date', 'end_date']),
            $showroom->id
        );

        return redirect()->route('showrooms.manage.campaigns.index', $showroom)
            ->with('success', "Campaign \"{$campaign->name}\" created successfully with configured prize pool.");
    }

    /**
     * Show the form for editing an existing campaign.
     */
    public function edit(Showroom $showroom, PromotionalCampaign $campaign): View
    {
        abort_if($campaign->tenant_id !== $showroom->id, 404);

        $campaign->load('prizes');
        $statuses = CampaignStatus::cases();
        $prizeTypes = PrizeType::cases();

        return view('showrooms.manage.campaigns.edit', compact('showroom', 'campaign', 'statuses', 'prizeTypes'));
    }

    /**
     * Update the campaign and its prize tiers.
     */
    public function update(Request $request, Showroom $showroom, PromotionalCampaign $campaign): RedirectResponse
    {
        abort_if($campaign->tenant_id !== $showroom->id, 404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:1000'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'status' => ['required', 'string', Rule::in(array_column(CampaignStatus::cases(), 'value'))],
            'min_booking_amount' => ['nullable', 'numeric', 'min:0'],
            'max_participations_per_customer' => ['required', 'integer', 'min:1', 'max:20'],
            'terms_and_conditions' => ['nullable', 'string', 'max:3000'],
        ]);

        $oldValues = $campaign->only(['name', 'status', 'start_date', 'end_date']);

        $campaign->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'status' => $validated['status'],
            'min_booking_amount' => $validated['min_booking_amount'] ?? 0.00,
            'max_participations_per_customer' => $validated['max_participations_per_customer'],
            'terms_and_conditions' => $validated['terms_and_conditions'] ?? null,
        ]);

        AuditLogger::log(
            'campaign.updated',
            $campaign,
            $oldValues,
            $campaign->fresh()->only(['name', 'status', 'start_date', 'end_date']),
            $showroom->id
        );

        return redirect()->route('showrooms.manage.campaigns.index', $showroom)
            ->with('success', "Campaign \"{$campaign->name}\" updated successfully.");
    }

    /**
     * Toggle the status between Active and Paused.
     */
    public function toggleStatus(Showroom $showroom, PromotionalCampaign $campaign): RedirectResponse
    {
        abort_if($campaign->tenant_id !== $showroom->id, 404);

        $newStatus = $campaign->status === CampaignStatus::Active
            ? CampaignStatus::Paused
            : CampaignStatus::Active;

        $campaign->update(['status' => $newStatus->value]);

        AuditLogger::log(
            'campaign.status_toggled',
            $campaign,
            ['status' => $campaign->getOriginal('status')],
            ['status' => $newStatus->value],
            $showroom->id
        );

        return redirect()->back()->with('success', "Campaign status changed to {$newStatus->label()}.");
    }

    /**
     * Delete the specified campaign.
     */
    public function destroy(Showroom $showroom, PromotionalCampaign $campaign): RedirectResponse
    {
        abort_if($campaign->tenant_id !== $showroom->id, 404);

        $name = $campaign->name;
        $campaign->delete();

        AuditLogger::log('campaign.deleted', $campaign, ['name' => $name], null, $showroom->id);

        return redirect()->route('showrooms.manage.campaigns.index', $showroom)
            ->with('success', "Campaign \"{$name}\" deleted.");
    }

    /**
     * Display the participations ledger for a specific campaign.
     */
    public function participations(Request $request, Showroom $showroom, PromotionalCampaign $campaign): View
    {
        abort_if($campaign->tenant_id !== $showroom->id, 404);

        $search = $request->query('search');
        $redeemed = $request->query('redeemed');

        $query = $campaign->participations()
            ->with(['prize', 'redeemedBy'])
            ->search($search);

        if ($redeemed === 'yes') {
            $query->where('is_redeemed', true);
        } elseif ($redeemed === 'no') {
            $query->where('is_redeemed', false);
        }

        $participations = $query->latest()->paginate(20)->withQueryString();

        return view('showrooms.manage.campaigns.participations', compact('showroom', 'campaign', 'participations', 'search', 'redeemed'));
    }

    /**
     * In-store physical or workshop coupon redemption tool.
     */
    public function redeemCoupon(Request $request, Showroom $showroom, ScratchCardService $service): RedirectResponse
    {
        $validated = $request->validate([
            'coupon_code' => ['required', 'string', 'max:50'],
        ]);

        $result = $service->redeemCoupon($showroom, $validated['coupon_code'], Auth::user());

        if (! $result['success']) {
            return redirect()->back()->with('error', $result['message']);
        }

        return redirect()->back()->with('success', $result['message']);
    }
}
