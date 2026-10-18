<?php

namespace App\Http\Controllers\Showroom;

use App\Http\Controllers\Controller;
use App\Models\PromotionalCampaign;
use App\Models\ScratchCardParticipation;
use App\Models\Showroom;
use App\Services\ScratchCardService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PublicScratchCardController extends Controller
{
    /**
     * Display public promotions portal showing active campaigns for this showroom.
     */
    public function index(Showroom $showroom): View
    {
        $campaigns = $showroom->activePromotionalCampaigns()
            ->with(['prizes'])
            ->latest()
            ->get();

        return view('showrooms.promotions.index', compact('showroom', 'campaigns'));
    }

    /**
     * Display campaign details and customer scratch card claim form.
     */
    public function show(Showroom $showroom, PromotionalCampaign $campaign): View
    {
        abort_if($campaign->tenant_id !== $showroom->id, 404);
        abort_if(! $campaign->isCurrentlyActive(), 404);

        $campaign->load(['prizes']);

        return view('showrooms.promotions.show', compact('showroom', 'campaign'));
    }

    /**
     * Customer claims a scratch card for this promotional campaign.
     */
    public function claim(
        Request $request,
        Showroom $showroom,
        PromotionalCampaign $campaign,
        ScratchCardService $service
    ): RedirectResponse {
        abort_if($campaign->tenant_id !== $showroom->id, 404);
        abort_if(! $campaign->isCurrentlyActive(), 404);

        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:150'],
            'customer_email' => ['required', 'email', 'max:150'],
            'customer_phone' => ['required', 'string', 'max:30'],
        ]);

        $participation = $service->claimScratchCard(
            $campaign,
            $validated,
            Auth::user()
        );

        return redirect()->route('showrooms.scratch.play', [
            'showroom' => $showroom,
            'participation' => $participation,
        ]);
    }

    /**
     * Display the interactive scratch card reveal view.
     */
    public function play(Showroom $showroom, ScratchCardParticipation $participation): View
    {
        abort_if($participation->tenant_id !== $showroom->id, 404);

        $participation->load(['campaign', 'prize']);

        return view('showrooms.promotions.play', compact('showroom', 'participation'));
    }

    /**
     * AJAX or form post to reveal the scratch card.
     */
    public function reveal(
        Request $request,
        Showroom $showroom,
        ScratchCardParticipation $participation,
        ScratchCardService $service
    ): JsonResponse|RedirectResponse {
        abort_if($participation->tenant_id !== $showroom->id, 404);

        $scratched = $service->revealScratchCard($participation);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'is_winning' => $scratched->prize?->prize_type->isWinningPrize() ?? false,
                'prize' => $scratched->prize ? [
                    'title' => $scratched->prize->title,
                    'type' => $scratched->prize->prize_type->value,
                    'label' => $scratched->prize->prize_type->label(),
                ] : null,
                'coupon_code' => $scratched->coupon_code,
            ]);
        }

        return redirect()->back()->with('success', 'Card revealed!');
    }
}
