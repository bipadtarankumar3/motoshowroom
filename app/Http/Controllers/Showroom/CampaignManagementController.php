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
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
        $preset = $request->query('preset');
        $applicableTo = $request->query('applicable_to');
        $dateType = $request->query('date_type', 'schedule');
        $fromDate = $request->query('from_date');
        $toDate = $request->query('to_date');

        $query = $showroom->promotionalCampaigns()
            ->withCount(['prizes', 'participations'])
            ->with(['prizes'])
            ->search($search);

        // Quick Preset Filters
        if ($preset === 'active') {
            $query->where('status', CampaignStatus::Active->value)
                ->whereDate('start_date', '<=', today())
                ->whereDate('end_date', '>=', today());
        } elseif ($preset === 'upcoming') {
            $query->whereDate('start_date', '>', today());
        } elseif ($preset === 'expired') {
            $query->where(function ($q) {
                $q->whereDate('end_date', '<', today())
                    ->orWhere('status', CampaignStatus::Expired->value);
            });
        } elseif ($preset === 'drafts') {
            $query->where('status', CampaignStatus::Draft->value);
        } elseif ($preset === 'service') {
            $query->where('applicable_to', 'service');
        } elseif ($preset === 'bike') {
            $query->where('applicable_to', 'bike');
        } elseif ($preset === 'today') {
            $query->whereDate('created_at', today());
        }

        if (! empty($status) && $status !== 'all') {
            $query->where('status', $status);
        }

        if (! empty($applicableTo) && $applicableTo !== 'all') {
            $query->where('applicable_to', $applicableTo);
        }

        if (! empty($fromDate)) {
            if ($dateType === 'created') {
                $query->whereDate('created_at', '>=', $fromDate);
            } else {
                $query->whereDate('start_date', '>=', $fromDate);
            }
        }

        if (! empty($toDate)) {
            if ($dateType === 'created') {
                $query->whereDate('created_at', '<=', $toDate);
            } else {
                $query->whereDate('end_date', '<=', $toDate);
            }
        }

        $campaigns = $query->latest()->paginate(15)->withQueryString();

        // 8 Key KPI Counts for Metric Cards & Filter Badges
        $totalCampaigns = $showroom->promotionalCampaigns()->count();
        $activeCampaigns = $showroom->promotionalCampaigns()
            ->where('status', CampaignStatus::Active->value)
            ->whereDate('start_date', '<=', today())
            ->whereDate('end_date', '>=', today())
            ->count();
        $upcomingCampaigns = $showroom->promotionalCampaigns()->whereDate('start_date', '>', today())->count();
        $expiredCampaigns = $showroom->promotionalCampaigns()
            ->where(function ($q) {
                $q->whereDate('end_date', '<', today())
                    ->orWhere('status', CampaignStatus::Expired->value);
            })->count();
        $draftCampaigns = $showroom->promotionalCampaigns()->where('status', CampaignStatus::Draft->value)->count();
        $serviceCampaigns = $showroom->promotionalCampaigns()->where('applicable_to', 'service')->count();
        $bikeCampaigns = $showroom->promotionalCampaigns()->where('applicable_to', 'bike')->count();

        // Scratch Cards & Participations Telemetry
        $participationsQuery = $showroom->scratchCardParticipations();
        $totalParticipations = (clone $participationsQuery)->count();
        $participationsToday = (clone $participationsQuery)->whereDate('created_at', today())->count();

        $totalScratched = (clone $participationsQuery)->where('is_scratched', true)->count();
        $scratchedToday = (clone $participationsQuery)->where('is_scratched', true)->whereDate('scratched_at', today())->count();

        $totalRedeemed = (clone $participationsQuery)->where('is_redeemed', true)->count();
        $redeemedToday = (clone $participationsQuery)->where('is_redeemed', true)->whereDate('redeemed_at', today())->count();

        $pendingRedemption = (clone $participationsQuery)->where('is_scratched', true)->where('is_redeemed', false)->whereNotNull('coupon_code')->count();

        $totalPrizeValue = (float) DB::table('scratch_card_participations')
            ->join('campaign_prizes', 'scratch_card_participations.prize_id', '=', 'campaign_prizes.id')
            ->where('scratch_card_participations.tenant_id', $showroom->id)
            ->where('scratch_card_participations.is_scratched', true)
            ->whereNull('scratch_card_participations.deleted_at')
            ->sum('campaign_prizes.discount_amount');

        $wonPrizesCount = DB::table('scratch_card_participations')
            ->join('campaign_prizes', 'scratch_card_participations.prize_id', '=', 'campaign_prizes.id')
            ->where('scratch_card_participations.tenant_id', $showroom->id)
            ->where('scratch_card_participations.is_scratched', true)
            ->where('campaign_prizes.prize_type', '!=', PrizeType::BetterLuck->value)
            ->whereNull('scratch_card_participations.deleted_at')
            ->count();

        $statuses = CampaignStatus::cases();

        return view('showrooms.manage.campaigns.index', compact(
            'showroom',
            'hasModuleAccess',
            'campaigns',
            'totalCampaigns',
            'activeCampaigns',
            'upcomingCampaigns',
            'expiredCampaigns',
            'draftCampaigns',
            'serviceCampaigns',
            'bikeCampaigns',
            'totalParticipations',
            'participationsToday',
            'totalScratched',
            'scratchedToday',
            'totalRedeemed',
            'redeemedToday',
            'pendingRedemption',
            'totalPrizeValue',
            'wonPrizesCount',
            'statuses',
            'search',
            'status',
            'preset',
            'applicableTo',
            'dateType',
            'fromDate',
            'toDate'
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
            'applicable_to' => ['nullable', 'string', Rule::in(['all', 'service', 'bike'])],
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
                'applicable_to' => $validated['applicable_to'] ?? 'all',
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
            'applicable_to' => ['nullable', 'string', Rule::in(['all', 'service', 'bike'])],
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
            'applicable_to' => $validated['applicable_to'] ?? 'all',
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
     * Display the participations ledger for a specific campaign with telemetry metrics and advanced filtering.
     */
    public function participations(Request $request, Showroom $showroom, PromotionalCampaign $campaign): View
    {
        abort_if($campaign->tenant_id !== $showroom->id, 404);

        // Telemetry Metrics Calculation across all campaign participations
        $allParticipations = $campaign->participations()->with('prize')->get();

        $totalCount = $allParticipations->count();
        $todayCount = $allParticipations->where('created_at', '>=', now()->startOfDay())->count();

        $scratchedCount = $allParticipations->where('is_scratched', true)->count();
        $scratchedTodayCount = $allParticipations->filter(fn ($p) => $p->is_scratched && $p->scratched_at && $p->scratched_at->gte(now()->startOfDay()))->count();

        $unscratchedCount = $allParticipations->where('is_scratched', false)->count();

        $redeemedCount = $allParticipations->where('is_redeemed', true)->count();
        $redeemedTodayCount = $allParticipations->filter(fn ($p) => $p->is_redeemed && $p->redeemed_at && $p->redeemed_at->gte(now()->startOfDay()))->count();

        $unredeemedCount = $allParticipations->where('is_scratched', true)->where('is_redeemed', false)->count();
        $wonPrizesCount = $allParticipations->whereNotNull('prize_id')->count();

        $totalWonValue = (float) $allParticipations->filter(fn ($p) => $p->is_scratched && $p->prize)->sum(function ($p) {
            return (float) ($p->prize?->discount_amount ?? 0);
        });

        $stats = [
            'total' => $totalCount,
            'today' => $todayCount,
            'scratched' => $scratchedCount,
            'scratched_today' => $scratchedTodayCount,
            'unscratched' => $unscratchedCount,
            'redeemed' => $redeemedCount,
            'redeemed_today' => $redeemedTodayCount,
            'pending_redemption' => $unredeemedCount,
            'won_prizes' => $wonPrizesCount,
            'total_prize_value' => $totalWonValue,
        ];

        $prizes = $campaign->prizes()->orderBy('title')->get();

        $query = $this->buildParticipationsQuery($request, $campaign);

        // Sorting
        $sort = $request->query('sort', 'latest');
        match ($sort) {
            'oldest' => $query->oldest('created_at'),
            'scratched_desc' => $query->orderBy('scratched_at', 'desc'),
            'redeemed_desc' => $query->orderBy('redeemed_at', 'desc'),
            'customer_asc' => $query->orderBy('customer_name', 'asc'),
            default => $query->latest('created_at'),
        };

        $participations = $query->paginate(20)->withQueryString();

        return view('showrooms.manage.campaigns.participations', compact('showroom', 'campaign', 'participations', 'stats', 'prizes'));
    }

    /**
     * Export campaign participations (selected rows or all matching filters) to CSV.
     */
    public function exportParticipations(Request $request, Showroom $showroom, PromotionalCampaign $campaign): StreamedResponse
    {
        abort_if($campaign->tenant_id !== $showroom->id, 404);

        $query = $this->buildParticipationsQuery($request, $campaign);

        // Check if specific rows were selected (via array selected_ids[] or comma-delimited string selected_ids)
        $selectedIds = $request->input('selected_ids');
        if (is_string($selectedIds) && strlen(trim($selectedIds)) > 0) {
            $selectedIds = array_filter(array_map('trim', explode(',', $selectedIds)));
        }

        if (! empty($selectedIds) && is_array($selectedIds)) {
            $query->whereIn('scratch_card_participations.id', $selectedIds);
        }

        // Apply sort order
        $sort = $request->input('sort', 'latest');
        match ($sort) {
            'oldest' => $query->oldest('created_at'),
            'scratched_desc' => $query->orderBy('scratched_at', 'desc'),
            'redeemed_desc' => $query->orderBy('redeemed_at', 'desc'),
            'customer_asc' => $query->orderBy('customer_name', 'asc'),
            default => $query->latest('created_at'),
        };

        $participations = $query->get();

        $filename = 'participations_'.Str::slug($campaign->name).'_'.date('Y-m-d_His').'.csv';

        return response()->streamDownload(function () use ($participations, $campaign) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM for Microsoft Excel compatibility
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, [
                'Participation ID',
                'Campaign Name',
                'Customer Name',
                'Customer Email',
                'Customer Phone',
                'Participation Token',
                'Scratch State',
                'Scratched At',
                'Prize Title',
                'Prize Type',
                'Discount Amount / Value',
                'Coupon Code',
                'Redemption Status',
                'Redeemed At',
                'Redeemed By Staff',
                'Claim Date',
            ]);

            foreach ($participations as $part) {
                $prize = $part->prize;
                $redeemedBy = $part->redeemedBy;

                $prizeReward = 'None';
                if ($prize) {
                    if ($prize->discount_amount > 0) {
                        $prizeReward = '₹'.number_format($prize->discount_amount, 2);
                    } elseif ($prize->discount_percentage > 0) {
                        $prizeReward = $prize->discount_percentage.'% Off';
                    } else {
                        $prizeReward = $prize->title;
                    }
                }

                fputcsv($handle, [
                    $part->id,
                    $campaign->name,
                    $part->customer_name,
                    $part->customer_email,
                    $part->customer_phone,
                    $part->participation_token,
                    $part->is_scratched ? 'Revealed / Scratched' : 'Unscratched',
                    $part->scratched_at ? $part->scratched_at->format('Y-m-d H:i') : 'N/A',
                    $prize?->title ?? 'No Prize',
                    $prize?->prize_type?->label() ?? 'N/A',
                    $prizeReward,
                    $part->coupon_code ?: 'N/A',
                    $part->is_redeemed ? 'Redeemed In-Store' : ($part->is_scratched ? 'Pending Redemption' : 'Unrevealed'),
                    $part->redeemed_at ? $part->redeemed_at->format('Y-m-d H:i') : 'N/A',
                    $redeemedBy?->name ?? 'N/A',
                    $part->created_at ? $part->created_at->format('Y-m-d H:i') : 'N/A',
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Build filtered participations query.
     */
    protected function buildParticipationsQuery(Request $request, PromotionalCampaign $campaign)
    {
        $query = $campaign->participations()
            ->with(['prize', 'redeemedBy']);

        // Global Search (Name, email, phone, coupon code)
        if ($request->filled('search')) {
            $query->search($request->input('search'));
        }

        // Scratch State filter
        if ($request->filled('scratched')) {
            if ($request->input('scratched') === 'yes') {
                $query->where('is_scratched', true);
            } elseif ($request->input('scratched') === 'no') {
                $query->where('is_scratched', false);
            }
        }

        // Redemption State filter
        if ($request->filled('redeemed')) {
            if ($request->input('redeemed') === 'yes') {
                $query->where('is_redeemed', true);
            } elseif ($request->input('redeemed') === 'no') {
                $query->where('is_redeemed', false);
            }
        }

        // Prize Filter
        if ($request->filled('prize_id')) {
            if ($request->input('prize_id') === 'none') {
                $query->whereNull('prize_id');
            } elseif ($request->input('prize_id') === 'any') {
                $query->whereNotNull('prize_id');
            } else {
                $query->where('prize_id', $request->input('prize_id'));
            }
        }

        // Quick Preset Filters
        $preset = $request->input('preset');
        if ($preset) {
            match ($preset) {
                'today' => $query->whereDate('created_at', today()),
                'scratched_today' => $query->where('is_scratched', true)->whereDate('scratched_at', today()),
                'scratched' => $query->where('is_scratched', true),
                'unscratched' => $query->where('is_scratched', false),
                'redeemed' => $query->where('is_redeemed', true),
                'redeemed_today' => $query->where('is_redeemed', true)->whereDate('redeemed_at', today()),
                'pending' => $query->where('is_scratched', true)->where('is_redeemed', false),
                'won' => $query->whereNotNull('prize_id'),
                default => null,
            };
        }

        // Date-Wise Search (from_date, to_date, date_type)
        if ($request->filled('from_date') || $request->filled('to_date')) {
            $dateColumn = match ($request->input('date_type')) {
                'scratched' => 'scratched_at',
                'redeemed' => 'redeemed_at',
                default => 'created_at',
            };

            if ($request->filled('from_date')) {
                $query->whereDate($dateColumn, '>=', $request->input('from_date'));
            }
            if ($request->filled('to_date')) {
                $query->whereDate($dateColumn, '<=', $request->input('to_date'));
            }
        }

        return $query;
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
