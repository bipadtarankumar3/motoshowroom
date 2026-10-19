<?php

namespace App\Services;

use App\Enums\CampaignStatus;
use App\Enums\PrizeType;
use App\Models\CampaignPrize;
use App\Models\Enquiry;
use App\Models\PromotionalCampaign;
use App\Models\ScratchCardParticipation;
use App\Models\ServiceBooking;
use App\Models\Showroom;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ScratchCardService
{
    /**
     * Check if a customer is eligible to participate in the specified campaign.
     *
     * @return array{allowed: bool, reason: ?string}
     */
    public function canParticipate(PromotionalCampaign $campaign, string $customerEmail, string $customerPhone): array
    {
        if ($campaign->status !== CampaignStatus::Active) {
            return [
                'allowed' => false,
                'reason' => 'This promotional campaign is currently not active.',
            ];
        }

        if ($campaign->start_date->isFuture()) {
            return [
                'allowed' => false,
                'reason' => "This campaign opens on {$campaign->start_date->format('M d, Y')}.",
            ];
        }

        if ($campaign->end_date->isPast()) {
            return [
                'allowed' => false,
                'reason' => 'This promotional campaign has concluded.',
            ];
        }

        $existingCount = ScratchCardParticipation::where('tenant_id', $campaign->tenant_id)
            ->where('campaign_id', $campaign->id)
            ->where(function ($q) use ($customerEmail, $customerPhone): void {
                $q->where('customer_email', $customerEmail)
                    ->orWhere('customer_phone', $customerPhone);
            })
            ->count();

        if ($existingCount >= $campaign->max_participations_per_customer) {
            return [
                'allowed' => false,
                'reason' => "You have reached the maximum participation limit ({$campaign->max_participations_per_customer}) for this campaign.",
            ];
        }

        return [
            'allowed' => true,
            'reason' => null,
        ];
    }

    /**
     * Claim a scratch card with concurrency-safe probability prize allocation.
     *
     * @param  array{customer_name: string, customer_email: string, customer_phone: string}  $customerData
     */
    public function claimScratchCard(
        PromotionalCampaign $campaign,
        array $customerData,
        ?User $user = null,
        ?ServiceBooking $booking = null,
        ?Enquiry $enquiry = null,
        ?string $bookingType = null
    ): ScratchCardParticipation {
        return DB::transaction(function () use ($campaign, $customerData, $user, $booking, $enquiry, $bookingType) {
            $eligibility = $this->canParticipate(
                $campaign,
                $customerData['customer_email'],
                $customerData['customer_phone']
            );

            if (! $eligibility['allowed']) {
                throw ValidationException::withMessages([
                    'participation' => [$eligibility['reason']],
                ]);
            }

            /** @var Collection<int, CampaignPrize> $prizes */
            $prizes = $campaign->prizes()
                ->lockForUpdate()
                ->get();

            // Filter prizes that still have inventory available (or BetterLuck which is unlimited)
            $availablePrizes = $prizes->filter(fn (CampaignPrize $prize) => $prize->hasInventoryAvailable());

            $selectedPrize = null;
            $couponCode = null;

            if ($availablePrizes->isNotEmpty()) {
                $selectedPrize = $this->selectWeightedPrize($availablePrizes);

                // Decrement inventory if limited physical/discount prize
                if ($selectedPrize && $selectedPrize->prize_type !== PrizeType::BetterLuck && $selectedPrize->remaining_quantity > 0) {
                    $selectedPrize->decrement('remaining_quantity');
                }

                // Generate coupon code for winning tangible prizes
                if ($selectedPrize && $selectedPrize->prize_type->isWinningPrize()) {
                    $couponCode = $this->generateUniqueCouponCode($campaign->tenant_id);
                }
            }

            $resolvedType = $bookingType;
            if (! $resolvedType) {
                if ($booking) {
                    $resolvedType = 'service';
                } elseif ($enquiry) {
                    $resolvedType = 'bike';
                } else {
                    $resolvedType = 'direct';
                }
            }

            /** @var ScratchCardParticipation $participation */
            $participation = ScratchCardParticipation::create([
                'tenant_id' => $campaign->tenant_id,
                'campaign_id' => $campaign->id,
                'prize_id' => $selectedPrize?->id,
                'user_id' => $user?->id,
                'service_booking_id' => $booking?->id,
                'enquiry_id' => $enquiry?->id,
                'booking_type' => $resolvedType,
                'customer_name' => $customerData['customer_name'],
                'customer_email' => $customerData['customer_email'],
                'customer_phone' => $customerData['customer_phone'],
                'participation_token' => Str::random(40),
                'is_scratched' => false,
                'coupon_code' => $couponCode,
            ]);

            AuditLogger::log(
                'scratch_card.claimed',
                $participation,
                null,
                [
                    'campaign' => $campaign->name,
                    'customer_name' => $participation->customer_name,
                    'customer_email' => $participation->customer_email,
                    'has_prize' => $selectedPrize?->prize_type->isWinningPrize() ?? false,
                ],
                $campaign->tenant_id,
                $user
            );

            return $participation;
        });
    }

    /**
     * Mark a scratch card as revealed/scratched.
     */
    public function revealScratchCard(ScratchCardParticipation $participation): ScratchCardParticipation
    {
        if (! $participation->is_scratched) {
            $participation->update([
                'is_scratched' => true,
                'scratched_at' => now(),
            ]);

            AuditLogger::log(
                'scratch_card.revealed',
                $participation,
                ['is_scratched' => false],
                [
                    'is_scratched' => true,
                    'prize' => $participation->prize?->title ?? 'None',
                    'coupon_code' => $participation->coupon_code,
                ],
                $participation->tenant_id
            );
        }

        return $participation->fresh(['prize', 'campaign']);
    }

    /**
     * Redeem a customer coupon in-store.
     *
     * @return array{success: bool, message: string, participation?: ScratchCardParticipation}
     */
    public function redeemCoupon(Showroom $showroom, string $couponCode, ?User $staffUser = null): array
    {
        $cleanedCode = strtoupper(trim($couponCode));

        /** @var ?ScratchCardParticipation $participation */
        $participation = ScratchCardParticipation::where('tenant_id', $showroom->id)
            ->where('coupon_code', $cleanedCode)
            ->with(['prize', 'campaign'])
            ->first();

        if (! $participation) {
            return [
                'success' => false,
                'message' => "Coupon code \"{$couponCode}\" was not found for this showroom dealership.",
            ];
        }

        if ($participation->is_redeemed) {
            $redeemedDate = $participation->redeemed_at ? $participation->redeemed_at->format('M d, Y h:i A') : 'earlier';

            return [
                'success' => false,
                'message' => "Coupon \"{$couponCode}\" was already redeemed on {$redeemedDate}.",
                'participation' => $participation,
            ];
        }

        if (! $participation->is_scratched) {
            return [
                'success' => false,
                'message' => 'This scratch card has not yet been scratched or revealed by the customer.',
                'participation' => $participation,
            ];
        }

        $participation->update([
            'is_redeemed' => true,
            'redeemed_at' => now(),
            'redeemed_by_user_id' => $staffUser?->id,
        ]);

        AuditLogger::log(
            'scratch_card.redeemed',
            $participation,
            ['is_redeemed' => false],
            [
                'is_redeemed' => true,
                'coupon_code' => $participation->coupon_code,
                'prize' => $participation->prize?->title,
                'redeemed_by' => $staffUser?->name,
            ],
            $showroom->id,
            $staffUser
        );

        $prizeTitle = $participation->prize?->title ?? 'Promotional Reward';

        return [
            'success' => true,
            'message' => "Coupon \"{$participation->coupon_code}\" successfully redeemed for \"{$prizeTitle}\"!",
            'participation' => $participation,
        ];
    }

    /**
     * Probability-weighted prize selection engine.
     *
     * @param  Collection<int, CampaignPrize>  $availablePrizes
     */
    protected function selectWeightedPrize(Collection $availablePrizes): CampaignPrize
    {
        $totalWeight = $availablePrizes->sum('probability_weight');

        if ($totalWeight <= 0) {
            return $availablePrizes->first();
        }

        $random = random_int(1, (int) $totalWeight);
        $current = 0;

        foreach ($availablePrizes as $prize) {
            $current += $prize->probability_weight;
            if ($random <= $current) {
                return $prize;
            }
        }

        return $availablePrizes->last();
    }

    /**
     * Generate unique, human-readable coupon code.
     */
    protected function generateUniqueCouponCode(int $tenantId): string
    {
        do {
            $code = 'MOTO-'.strtoupper(Str::random(4)).'-'.strtoupper(Str::random(4));
        } while (ScratchCardParticipation::where('tenant_id', $tenantId)->where('coupon_code', $code)->exists());

        return $code;
    }

    /**
     * Find active campaign eligible for service or bike bookings.
     */
    public function getEligibleCampaignForBooking(Showroom $showroom, string $type = 'service'): ?PromotionalCampaign
    {
        if (! $showroom->canAccessFeature('has_scratch_cards')) {
            return null;
        }

        return $showroom->activePromotionalCampaigns()
            ->applicableTo($type)
            ->with(['prizes'])
            ->latest()
            ->first();
    }

    /**
     * Auto-issue a scratch card when a customer books a service appointment.
     */
    public function autoIssueForServiceBooking(ServiceBooking $booking): ?ScratchCardParticipation
    {
        $showroom = $booking->showroom;
        $campaign = $this->getEligibleCampaignForBooking($showroom, 'service');

        if (! $campaign) {
            return null;
        }

        $customerData = [
            'customer_name' => $booking->customer_name,
            'customer_email' => $booking->customer_email,
            'customer_phone' => $booking->customer_phone,
        ];

        try {
            return $this->claimScratchCard(
                $campaign,
                $customerData,
                $booking->user,
                $booking,
                null,
                'service'
            );
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Auto-issue a scratch card when a customer books or inquires about a bike.
     */
    public function autoIssueForBikeEnquiry(Enquiry $enquiry): ?ScratchCardParticipation
    {
        $showroom = $enquiry->showroom;
        $campaign = $this->getEligibleCampaignForBooking($showroom, 'bike');

        if (! $campaign) {
            return null;
        }

        $customerData = [
            'customer_name' => $enquiry->customer_name,
            'customer_email' => $enquiry->customer_email,
            'customer_phone' => $enquiry->customer_phone,
        ];

        try {
            return $this->claimScratchCard(
                $campaign,
                $customerData,
                $enquiry->user,
                null,
                $enquiry,
                'bike'
            );
        } catch (\Throwable $e) {
            return null;
        }
    }
}
