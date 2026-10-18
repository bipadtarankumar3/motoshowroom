<?php

namespace Tests\Feature;

use App\Enums\CampaignStatus;
use App\Enums\PrizeType;
use App\Models\CampaignPrize;
use App\Models\PromotionalCampaign;
use App\Models\ScratchCardParticipation;
use App\Models\Showroom;
use App\Models\ShowroomMembership;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Services\ScratchCardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PromotionalScratchCardTest extends TestCase
{
    use RefreshDatabase;

    protected User $ownerA;

    protected User $ownerB;

    protected Showroom $showroomA;

    protected Showroom $showroomB;

    protected SubscriptionPlan $basicPlan;

    protected SubscriptionPlan $proPlan;

    protected PromotionalCampaign $campaignA;

    protected PromotionalCampaign $campaignB;

    protected CampaignPrize $prizeAccessory;

    protected CampaignPrize $prizeDiscount;

    protected CampaignPrize $prizeBetterLuck;

    protected function setUp(): void
    {
        parent::setUp();

        $this->basicPlan = SubscriptionPlan::create([
            'name' => 'Starter Tier',
            'slug' => 'starter-tier',
            'monthly_price' => 2999.00,
            'annual_price' => 29990.00,
            'currency' => 'INR',
            'has_scratch_cards' => false,
            'is_active' => true,
        ]);

        $this->proPlan = SubscriptionPlan::create([
            'name' => 'Professional Enterprise',
            'slug' => 'pro-enterprise',
            'monthly_price' => 7999.00,
            'annual_price' => 79990.00,
            'currency' => 'INR',
            'has_scratch_cards' => true,
            'is_active' => true,
        ]);

        $this->ownerA = User::create([
            'name' => 'Rajesh Sharma',
            'email' => 'rajesh@apex.example',
            'password' => 'Password123!',
            'role' => 'user',
            'status' => 'active',
        ]);

        $this->ownerB = User::create([
            'name' => 'Vikram Malhotra',
            'email' => 'vikram@velocity.example',
            'password' => 'Password123!',
            'role' => 'user',
            'status' => 'active',
        ]);

        $this->showroomA = Showroom::create([
            'name' => 'Apex Moto Hub',
            'slug' => 'apex-moto',
            'email' => 'contact@apex.example',
            'phone' => '+91 8023456789',
            'city' => 'Bangalore',
            'state' => 'Karnataka',
            'status' => 'active',
            'public_website_enabled' => true,
        ]);

        $this->showroomB = Showroom::create([
            'name' => 'Velocity Superbikes',
            'slug' => 'velocity-superbikes',
            'email' => 'contact@velocity.example',
            'phone' => '+91 1123456789',
            'city' => 'New Delhi',
            'state' => 'Delhi',
            'status' => 'active',
            'public_website_enabled' => true,
        ]);

        ShowroomMembership::create([
            'showroom_id' => $this->showroomA->id,
            'user_id' => $this->ownerA->id,
            'role' => 'owner',
            'status' => 'active',
        ]);

        ShowroomMembership::create([
            'showroom_id' => $this->showroomB->id,
            'user_id' => $this->ownerB->id,
            'role' => 'owner',
            'status' => 'active',
        ]);

        Subscription::create([
            'showroom_id' => $this->showroomA->id,
            'plan_id' => $this->proPlan->id,
            'status' => 'active',
            'billing_interval' => 'monthly',
            'current_period_start' => now()->subDay(),
            'current_period_end' => now()->addMonth(),
        ]);

        Subscription::create([
            'showroom_id' => $this->showroomB->id,
            'plan_id' => $this->proPlan->id,
            'status' => 'active',
            'billing_interval' => 'monthly',
            'current_period_start' => now()->subDay(),
            'current_period_end' => now()->addMonth(),
        ]);

        $this->campaignA = PromotionalCampaign::create([
            'tenant_id' => $this->showroomA->id,
            'name' => 'Apex Monsoon Superbike Scratch & Win',
            'slug' => 'apex-monsoon-superbike-scratch-win',
            'description' => 'Win free riding jackets, service vouchers, or cash discounts.',
            'start_date' => now()->subDays(2),
            'end_date' => now()->addDays(20),
            'status' => CampaignStatus::Active->value,
            'min_booking_amount' => 0.00,
            'max_participations_per_customer' => 1,
            'terms_and_conditions' => 'Redeemable exclusively at Apex Moto Hub workshop.',
        ]);

        $this->prizeAccessory = CampaignPrize::create([
            'tenant_id' => $this->showroomA->id,
            'campaign_id' => $this->campaignA->id,
            'title' => 'Free Alpinestars Riding Gloves',
            'prize_type' => PrizeType::FreeAccessory->value,
            'total_quantity' => 5,
            'remaining_quantity' => 5,
            'probability_weight' => 20,
        ]);

        $this->prizeDiscount = CampaignPrize::create([
            'tenant_id' => $this->showroomA->id,
            'campaign_id' => $this->campaignA->id,
            'title' => 'Flat ₹1,000 Service Voucher',
            'prize_type' => PrizeType::ServiceDiscount->value,
            'discount_amount' => 1000.00,
            'total_quantity' => 20,
            'remaining_quantity' => 20,
            'probability_weight' => 40,
        ]);

        $this->prizeBetterLuck = CampaignPrize::create([
            'tenant_id' => $this->showroomA->id,
            'campaign_id' => $this->campaignA->id,
            'title' => 'Better Luck Next Time',
            'prize_type' => PrizeType::BetterLuck->value,
            'total_quantity' => 999,
            'remaining_quantity' => 999,
            'probability_weight' => 40,
        ]);

        $this->campaignB = PromotionalCampaign::create([
            'tenant_id' => $this->showroomB->id,
            'name' => 'Velocity Delhi Festive Bonanza',
            'slug' => 'velocity-delhi-festive-bonanza',
            'start_date' => now()->subDays(2),
            'end_date' => now()->addDays(20),
            'status' => CampaignStatus::Active->value,
            'max_participations_per_customer' => 1,
        ]);
    }

    public function test_public_visitor_can_browse_active_promotions(): void
    {
        PromotionalCampaign::create([
            'tenant_id' => $this->showroomA->id,
            'name' => 'Hidden Draft Promo',
            'slug' => 'hidden-draft-promo',
            'start_date' => now()->subDay(),
            'end_date' => now()->addDays(10),
            'status' => CampaignStatus::Draft->value,
        ]);

        $response = $this->get(route('showrooms.promotions.index', $this->showroomA));

        $response->assertStatus(200);
        $response->assertSee('Exclusive Rewards');
        $response->assertSee('Scratch-and-Win');
        $response->assertSee('Apex Monsoon Superbike Scratch');
        $response->assertDontSee('Hidden Draft Promo');
        $response->assertDontSee('Velocity Delhi Festive Bonanza'); // Showroom B's campaign
    }

    public function test_public_visitor_can_view_campaign_details_and_prizes(): void
    {
        $response = $this->get(route('showrooms.promotions.show', [
            'showroom' => $this->showroomA,
            'campaign' => $this->campaignA,
        ]));

        $response->assertStatus(200);
        $response->assertSee('Apex Monsoon Superbike Scratch');
        $response->assertSee('Free Alpinestars Riding Gloves');
        $response->assertSee('Claim Your Scratch Card');
    }

    public function test_customer_can_claim_scratch_card(): void
    {
        $payload = [
            'customer_name' => 'Rohan Mehra',
            'customer_email' => 'rohan@example.com',
            'customer_phone' => '+91 98888 77777',
        ];

        $response = $this->post(route('showrooms.promotions.claim', [
            'showroom' => $this->showroomA,
            'campaign' => $this->campaignA,
        ]), $payload);

        $this->assertDatabaseHas('scratch_card_participations', [
            'tenant_id' => $this->showroomA->id,
            'campaign_id' => $this->campaignA->id,
            'customer_name' => 'Rohan Mehra',
            'customer_email' => 'rohan@example.com',
            'is_scratched' => false,
        ]);

        /** @var ScratchCardParticipation $participation */
        $participation = ScratchCardParticipation::where('customer_email', 'rohan@example.com')->firstOrFail();
        $this->assertNotNull($participation->participation_token);
        $this->assertNotNull($participation->prize_id);

        $response->assertRedirect(route('showrooms.scratch.play', [
            'showroom' => $this->showroomA,
            'participation' => $participation,
        ]));

        $this->assertDatabaseHas('audit_logs', [
            'tenant_id' => $this->showroomA->id,
            'action' => 'scratch_card.claimed',
        ]);
    }

    public function test_customer_participation_limit_enforced(): void
    {
        $payload = [
            'customer_name' => 'Rohan Mehra',
            'customer_email' => 'rohan@example.com',
            'customer_phone' => '+91 98888 77777',
        ];

        // First claim succeeds
        $this->post(route('showrooms.promotions.claim', [
            'showroom' => $this->showroomA,
            'campaign' => $this->campaignA,
        ]), $payload);

        // Second claim with same email/phone is blocked by max_participations_per_customer (1)
        $responseSecond = $this->post(route('showrooms.promotions.claim', [
            'showroom' => $this->showroomA,
            'campaign' => $this->campaignA,
        ]), $payload);

        $responseSecond->assertSessionHasErrors('participation');
        $this->assertEquals(1, ScratchCardParticipation::where('customer_email', 'rohan@example.com')->count());
    }

    public function test_customer_can_reveal_scratch_card_via_ajax(): void
    {
        /** @var ScratchCardParticipation $participation */
        $participation = ScratchCardParticipation::create([
            'tenant_id' => $this->showroomA->id,
            'campaign_id' => $this->campaignA->id,
            'prize_id' => $this->prizeAccessory->id,
            'customer_name' => 'Deepak Verma',
            'customer_email' => 'deepak@example.com',
            'customer_phone' => '+91 97777 66666',
            'participation_token' => 'token-test-1234567890-unique',
            'is_scratched' => false,
            'coupon_code' => 'MOTO-TEST-GLOVES',
        ]);

        $response = $this->postJson(route('showrooms.scratch.reveal', [
            'showroom' => $this->showroomA,
            'participation' => $participation,
        ]));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'is_winning' => true,
            'prize' => [
                'title' => 'Free Alpinestars Riding Gloves',
                'type' => 'free_accessory',
            ],
            'coupon_code' => 'MOTO-TEST-GLOVES',
        ]);

        $this->assertTrue($participation->fresh()->is_scratched);
        $this->assertNotNull($participation->fresh()->scratched_at);

        $this->assertDatabaseHas('audit_logs', [
            'tenant_id' => $this->showroomA->id,
            'action' => 'scratch_card.revealed',
        ]);
    }

    public function test_back_office_campaigns_index_displays_metrics_and_entitlement_status(): void
    {
        ScratchCardParticipation::create([
            'tenant_id' => $this->showroomA->id,
            'campaign_id' => $this->campaignA->id,
            'prize_id' => $this->prizeDiscount->id,
            'customer_name' => 'Amit Shah',
            'customer_email' => 'amit@example.com',
            'customer_phone' => '+91 95555 12345',
            'participation_token' => 'token-metric-check-1',
            'is_scratched' => true,
            'scratched_at' => now(),
            'coupon_code' => 'MOTO-AMIT-DISC',
            'is_redeemed' => true,
            'redeemed_at' => now(),
        ]);

        $response = $this->actingAs($this->ownerA)->get(route('showrooms.manage.campaigns.index', $this->showroomA));

        $response->assertStatus(200);
        $response->assertSee('Promotional Scratch Cards');
        $response->assertSee('In-Store Coupon Redemption Desk');
        $response->assertSee('Apex Monsoon Superbike Scratch &amp; Win', false);
        $response->assertSee('Total Campaigns');
    }

    public function test_back_office_campaign_create_is_guarded_by_has_scratch_cards_plan_entitlement(): void
    {
        // Downgrade showroom A to starter tier without scratch card feature
        $this->showroomA->subscription()->update([
            'plan_id' => $this->basicPlan->id,
        ]);

        $responseCreate = $this->actingAs($this->ownerA)->get(route('showrooms.manage.campaigns.create', $this->showroomA));
        $responseCreate->assertRedirect(route('showrooms.manage.campaigns.index', $this->showroomA));
        $responseCreate->assertSessionHas('error');

        $responseStore = $this->actingAs($this->ownerA)->post(route('showrooms.manage.campaigns.store', $this->showroomA), [
            'name' => 'Blocked Campaign',
            'start_date' => now()->format('Y-m-d'),
            'end_date' => now()->addMonth()->format('Y-m-d'),
            'status' => 'active',
            'max_participations_per_customer' => 1,
            'prizes' => [
                [
                    'title' => 'Discount',
                    'prize_type' => 'service_discount',
                    'total_quantity' => 10,
                    'probability_weight' => 50,
                ],
            ],
        ]);

        $responseStore->assertRedirect(route('showrooms.manage.campaigns.index', $this->showroomA));
        $responseStore->assertSessionHas('error');
        $this->assertDatabaseMissing('promotional_campaigns', ['name' => 'Blocked Campaign']);
    }

    public function test_back_office_owner_can_create_campaign_with_multiple_prize_tiers(): void
    {
        $payload = [
            'name' => 'Winter Superbike Festival',
            'description' => 'Festive scratch and win celebration.',
            'start_date' => now()->format('Y-m-d'),
            'end_date' => now()->addMonth()->format('Y-m-d'),
            'status' => CampaignStatus::Active->value,
            'max_participations_per_customer' => 1,
            'prizes' => [
                [
                    'title' => 'Free Motul Chain Lube & Cleaner Kit',
                    'prize_type' => PrizeType::FreeAccessory->value,
                    'total_quantity' => 15,
                    'probability_weight' => 30,
                ],
                [
                    'title' => '15% Off Brake Pad Overhaul',
                    'prize_type' => PrizeType::PercentageDiscount->value,
                    'discount_percentage' => 15.00,
                    'total_quantity' => 30,
                    'probability_weight' => 40,
                ],
                [
                    'title' => 'Better Luck Next Time',
                    'prize_type' => PrizeType::BetterLuck->value,
                    'total_quantity' => 500,
                    'probability_weight' => 30,
                ],
            ],
        ];

        $response = $this->actingAs($this->ownerA)->post(route('showrooms.manage.campaigns.store', $this->showroomA), $payload);

        $response->assertSessionHas('success');
        $response->assertRedirect(route('showrooms.manage.campaigns.index', $this->showroomA));

        $this->assertDatabaseHas('promotional_campaigns', [
            'tenant_id' => $this->showroomA->id,
            'name' => 'Winter Superbike Festival',
            'status' => CampaignStatus::Active->value,
        ]);

        $this->assertDatabaseHas('campaign_prizes', [
            'tenant_id' => $this->showroomA->id,
            'title' => 'Free Motul Chain Lube & Cleaner Kit',
            'total_quantity' => 15,
            'remaining_quantity' => 15,
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'tenant_id' => $this->showroomA->id,
            'action' => 'campaign.created',
        ]);
    }

    public function test_back_office_owner_can_update_and_toggle_campaign_status(): void
    {
        $responseUpdate = $this->actingAs($this->ownerA)->put(route('showrooms.manage.campaigns.update', [
            'showroom' => $this->showroomA,
            'campaign' => $this->campaignA,
        ]), [
            'name' => 'Apex Monsoon Superbike Scratch & Win (Extended)',
            'start_date' => $this->campaignA->start_date->format('Y-m-d'),
            'end_date' => now()->addDays(40)->format('Y-m-d'),
            'status' => CampaignStatus::Active->value,
            'max_participations_per_customer' => 2,
        ]);

        $responseUpdate->assertSessionHas('success');
        $this->assertDatabaseHas('promotional_campaigns', [
            'id' => $this->campaignA->id,
            'name' => 'Apex Monsoon Superbike Scratch & Win (Extended)',
            'max_participations_per_customer' => 2,
        ]);

        // Toggle to Paused
        $this->actingAs($this->ownerA)->post(route('showrooms.manage.campaigns.toggle-status', [
            'showroom' => $this->showroomA,
            'campaign' => $this->campaignA,
        ]));

        $this->assertEquals(CampaignStatus::Paused, $this->campaignA->fresh()->status);
    }

    public function test_back_office_owner_can_soft_delete_campaign(): void
    {
        $response = $this->actingAs($this->ownerA)->delete(route('showrooms.manage.campaigns.destroy', [
            'showroom' => $this->showroomA,
            'campaign' => $this->campaignA,
        ]));

        $response->assertSessionHas('success');
        $response->assertRedirect(route('showrooms.manage.campaigns.index', $this->showroomA));

        $this->assertSoftDeleted('promotional_campaigns', [
            'id' => $this->campaignA->id,
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'tenant_id' => $this->showroomA->id,
            'action' => 'campaign.deleted',
        ]);
    }

    public function test_back_office_staff_can_redeem_valid_coupon(): void
    {
        /** @var ScratchCardParticipation $participation */
        $participation = ScratchCardParticipation::create([
            'tenant_id' => $this->showroomA->id,
            'campaign_id' => $this->campaignA->id,
            'prize_id' => $this->prizeDiscount->id,
            'customer_name' => 'Karan Singhania',
            'customer_email' => 'karan@example.com',
            'customer_phone' => '+91 94444 33333',
            'participation_token' => 'token-redeem-test-1',
            'is_scratched' => true,
            'scratched_at' => now(),
            'coupon_code' => 'MOTO-KARAN-500',
            'is_redeemed' => false,
        ]);

        $response = $this->actingAs($this->ownerA)->post(route('showrooms.manage.coupons.redeem', $this->showroomA), [
            'coupon_code' => 'MOTO-KARAN-500',
        ]);

        $response->assertSessionHas('success');

        $this->assertTrue($participation->fresh()->is_redeemed);
        $this->assertNotNull($participation->fresh()->redeemed_at);
        $this->assertEquals($this->ownerA->id, $participation->fresh()->redeemed_by_user_id);

        $this->assertDatabaseHas('audit_logs', [
            'tenant_id' => $this->showroomA->id,
            'action' => 'scratch_card.redeemed',
        ]);
    }

    public function test_duplicate_coupon_redemption_is_rejected(): void
    {
        ScratchCardParticipation::create([
            'tenant_id' => $this->showroomA->id,
            'campaign_id' => $this->campaignA->id,
            'prize_id' => $this->prizeDiscount->id,
            'customer_name' => 'Karan Singhania',
            'customer_email' => 'karan@example.com',
            'customer_phone' => '+91 94444 33333',
            'participation_token' => 'token-redeem-duplicate',
            'is_scratched' => true,
            'scratched_at' => now(),
            'coupon_code' => 'MOTO-ALREADY-USED',
            'is_redeemed' => true,
            'redeemed_at' => now()->subHour(),
        ]);

        $response = $this->actingAs($this->ownerA)->post(route('showrooms.manage.coupons.redeem', $this->showroomA), [
            'coupon_code' => 'MOTO-ALREADY-USED',
        ]);

        $response->assertSessionHas('error');
    }

    public function test_unscratched_card_coupon_redemption_is_rejected(): void
    {
        ScratchCardParticipation::create([
            'tenant_id' => $this->showroomA->id,
            'campaign_id' => $this->campaignA->id,
            'prize_id' => $this->prizeDiscount->id,
            'customer_name' => 'Prem Kumar',
            'customer_email' => 'prem@example.com',
            'customer_phone' => '+91 93333 22222',
            'participation_token' => 'token-unscratched-redeem',
            'is_scratched' => false,
            'coupon_code' => 'MOTO-NOT-SCRATCHED',
            'is_redeemed' => false,
        ]);

        $response = $this->actingAs($this->ownerA)->post(route('showrooms.manage.coupons.redeem', $this->showroomA), [
            'coupon_code' => 'MOTO-NOT-SCRATCHED',
        ]);

        $response->assertSessionHas('error');
    }

    public function test_pessimistic_lock_prevents_overselling_zero_quantity_prizes(): void
    {
        // Create single prize campaign with exactly 1 available quantity
        $exclusiveCampaign = PromotionalCampaign::create([
            'tenant_id' => $this->showroomA->id,
            'name' => 'Exclusive 1-Quantity Prize Promo',
            'slug' => 'exclusive-1-quantity-prize-promo',
            'start_date' => now()->subDay(),
            'end_date' => now()->addDays(5),
            'status' => CampaignStatus::Active->value,
            'max_participations_per_customer' => 2,
        ]);

        $limitedPrize = CampaignPrize::create([
            'tenant_id' => $this->showroomA->id,
            'campaign_id' => $exclusiveCampaign->id,
            'title' => 'Only One Gold Voucher Available',
            'prize_type' => PrizeType::FixedCoupon->value,
            'total_quantity' => 1,
            'remaining_quantity' => 1,
            'probability_weight' => 100,
        ]);

        $fallbackPrize = CampaignPrize::create([
            'tenant_id' => $this->showroomA->id,
            'campaign_id' => $exclusiveCampaign->id,
            'title' => 'Better Luck Fallback',
            'prize_type' => PrizeType::BetterLuck->value,
            'total_quantity' => 999,
            'remaining_quantity' => 999,
            'probability_weight' => 1,
        ]);

        $service = app(ScratchCardService::class);

        // Claim 1
        $claim1 = $service->claimScratchCard($exclusiveCampaign, [
            'customer_name' => 'Rider 1',
            'customer_email' => 'rider1@example.com',
            'customer_phone' => '+91 90000 00001',
        ]);

        $this->assertEquals($limitedPrize->id, $claim1->prize_id);
        $this->assertEquals(0, $limitedPrize->fresh()->remaining_quantity);

        // Claim 2 - must NOT oversell the limited prize; must fall back to the available fallback prize
        $claim2 = $service->claimScratchCard($exclusiveCampaign, [
            'customer_name' => 'Rider 2',
            'customer_email' => 'rider2@example.com',
            'customer_phone' => '+91 90000 00002',
        ]);

        $this->assertEquals($fallbackPrize->id, $claim2->prize_id);
        $this->assertEquals(0, $limitedPrize->fresh()->remaining_quantity);
    }

    public function test_multi_tenant_isolation_prevents_cross_dealership_tampering(): void
    {
        // Owner B attempts to edit Showroom A's campaign
        $responseEdit = $this->actingAs($this->ownerB)->get(route('showrooms.manage.campaigns.edit', [
            'showroom' => $this->showroomB,
            'campaign' => $this->campaignA,
        ]));
        $responseEdit->assertStatus(404);

        // Owner B attempts to update Showroom A's campaign
        $responseUpdate = $this->actingAs($this->ownerB)->put(route('showrooms.manage.campaigns.update', [
            'showroom' => $this->showroomB,
            'campaign' => $this->campaignA,
        ]), [
            'name' => 'Malicious Campaign Hijack',
            'start_date' => now()->format('Y-m-d'),
            'end_date' => now()->addMonth()->format('Y-m-d'),
            'status' => CampaignStatus::Active->value,
            'max_participations_per_customer' => 1,
        ]);
        $responseUpdate->assertStatus(404);

        // Owner B attempts to redeem a Showroom A coupon inside Showroom B
        $participationA = ScratchCardParticipation::create([
            'tenant_id' => $this->showroomA->id,
            'campaign_id' => $this->campaignA->id,
            'prize_id' => $this->prizeDiscount->id,
            'customer_name' => 'Cross Tenant Test',
            'customer_email' => 'crosstenant@example.com',
            'customer_phone' => '+91 98888 12345',
            'participation_token' => 'token-cross-tenant-1',
            'is_scratched' => true,
            'scratched_at' => now(),
            'coupon_code' => 'MOTO-SHOWROOM-A-CODE',
            'is_redeemed' => false,
        ]);

        $responseRedeem = $this->actingAs($this->ownerB)->post(route('showrooms.manage.coupons.redeem', $this->showroomB), [
            'coupon_code' => 'MOTO-SHOWROOM-A-CODE',
        ]);

        $responseRedeem->assertSessionHas('error');
        $this->assertFalse($participationA->fresh()->is_redeemed);
    }
}
