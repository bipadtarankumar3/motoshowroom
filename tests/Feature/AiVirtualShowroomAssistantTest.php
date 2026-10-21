<?php

namespace Tests\Feature;

use App\Enums\BikeCategory;
use App\Enums\BikeStatus;
use App\Enums\CampaignStatus;
use App\Models\AiConversation;
use App\Models\Bike;
use App\Models\Enquiry;
use App\Models\PromotionalCampaign;
use App\Models\Showroom;
use App\Models\ShowroomMembership;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Services\SubscriptionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AiVirtualShowroomAssistantTest extends TestCase
{
    use RefreshDatabase;

    protected User $ownerA;

    protected User $ownerB;

    protected Showroom $showroomA;

    protected Showroom $showroomB;

    protected SubscriptionPlan $starterPlan;

    protected SubscriptionPlan $proPlan;

    protected Bike $bikeA;

    protected function setUp(): void
    {
        parent::setUp();
        RateLimiter::clear('ai_chat:*');

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

        // Starter Plan (no AI Assistant)
        $this->starterPlan = SubscriptionPlan::create([
            'name' => 'Starter Dealership',
            'slug' => 'starter',
            'description' => 'Entry tier without AI Assistant.',
            'monthly_price' => 999.00,
            'annual_price' => 9990.00,
            'currency' => 'INR',
            'trial_days' => 7,
            'max_staff_accounts' => 3,
            'max_bike_listings' => 10,
            'max_monthly_bookings' => 50,
            'media_storage_mb' => 1024,
            'has_ai_assistant' => false,
            'is_active' => true,
        ]);

        // Pro Plan (with AI Assistant)
        $this->proPlan = SubscriptionPlan::create([
            'name' => 'Pro Superbike Dealer',
            'slug' => 'pro-superbike',
            'description' => 'Includes AI Virtual Showroom Concierge.',
            'monthly_price' => 2999.00,
            'annual_price' => 29990.00,
            'currency' => 'INR',
            'trial_days' => 14,
            'max_staff_accounts' => 10,
            'max_bike_listings' => 50,
            'max_monthly_bookings' => 200,
            'media_storage_mb' => 5120,
            'has_ai_assistant' => true,
            'is_active' => true,
        ]);

        $subService = app(SubscriptionService::class);
        $subService->subscribe($this->showroomA, $this->proPlan, 'monthly');
        $subService->subscribe($this->showroomB, $this->starterPlan, 'monthly');

        $this->bikeA = Bike::create([
            'tenant_id' => $this->showroomA->id,
            'name' => 'Ducati Panigale V4 S',
            'slug' => 'ducati-panigale-v4-s',
            'sku' => 'MOTO-DUC-0001',
            'brand' => 'Ducati',
            'model_year' => 2026,
            'category' => BikeCategory::Sports->value,
            'price' => 2850000.00,
            'fuel_type' => 'petrol',
            'status' => BikeStatus::InStock->value,
            'is_featured' => true,
            'is_published' => true,
        ]);
    }

    public function test_back_office_staff_can_view_ai_concierge_dashboard_when_entitled(): void
    {
        $response = $this->actingAs($this->ownerA)
            ->get(route('showrooms.manage.ai.index', $this->showroomA));

        $response->assertStatus(200);
        $response->assertSee('AI Virtual Showroom Advisor');
        $response->assertSee('Concierge Settings');
        $response->assertSee('Showroom Presenter Mode');
        $response->assertDontSee('Customer Conversation History');
        $response->assertDontSee('AI Assistant Gated Feature');
    }

    public function test_ai_dashboard_displays_upgrade_prompt_when_plan_lacks_has_ai_assistant(): void
    {
        $response = $this->actingAs($this->ownerB)
            ->get(route('showrooms.manage.ai.index', $this->showroomB));

        $response->assertStatus(200);
        $response->assertSee('AI Assistant Gated Feature');
        $response->assertSee('Upgrade Subscription');
    }

    public function test_staff_can_update_ai_settings_and_custom_faqs(): void
    {
        $response = $this->actingAs($this->ownerA)
            ->put(route('showrooms.manage.ai.settings', $this->showroomA), [
                'bot_name' => 'Apex Cyber Concierge',
                'tone' => 'enthusiastic',
                'welcome_message' => 'Welcome to Apex Moto Hub! Ready to feel the adrenaline?',
                'is_enabled' => 1,
                'suggested_prompts' => "What is the top speed of Panigale V4?\nDo you offer home delivery?",
                'faqs' => [
                    [
                        'question' => 'Do you offer doorstep test rides?',
                        'answer' => 'Yes, within 25km radius of our Bangalore showroom.',
                    ],
                ],
            ]);

        $response->assertRedirect(route('showrooms.manage.ai.index', $this->showroomA));
        $response->assertSessionHas('success', 'AI Assistant settings and custom FAQs updated successfully.');

        $setting = $this->showroomA->aiSetting;
        $this->assertNotNull($setting);
        $this->assertSame('Apex Cyber Concierge', $setting->bot_name);
        $this->assertSame('enthusiastic', $setting->tone);
        $this->assertCount(2, $setting->suggested_prompts);
        $this->assertCount(1, $setting->faqs_list);
        $this->assertSame('Do you offer doorstep test rides?', $setting->faqs_list[0]['question']);
    }

    public function test_staff_cannot_update_ai_settings_if_plan_lacks_entitlement(): void
    {
        $response = $this->actingAs($this->ownerB)
            ->put(route('showrooms.manage.ai.settings', $this->showroomB), [
                'bot_name' => 'Hacker Bot',
                'tone' => 'friendly',
            ]);

        $response->assertStatus(403);
    }

    public function test_public_visitor_can_retrieve_ai_settings_for_widget(): void
    {
        $setting = $this->showroomA->getOrCreateAiSetting();
        $setting->update([
            'bot_name' => 'Apex AI Bot',
            'is_enabled' => true,
        ]);

        $response = $this->getJson(route('showrooms.ai.settings', $this->showroomA));

        $response->assertStatus(200);
        $response->assertJson([
            'is_available' => true,
            'bot_name' => 'Apex AI Bot',
        ]);
        $response->assertJsonStructure(['is_available', 'bot_name', 'welcome_message', 'suggested_prompts']);
    }

    public function test_public_visitor_can_chat_and_receive_motorcycle_recommendations(): void
    {
        $response = $this->postJson(route('showrooms.ai.chat', $this->showroomA), [
            'message' => 'Can you recommend a sports bike under 30 lakhs?',
            'session_id' => 'sess_test_recom_123',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'detected_intent' => 'bike_recommendation',
        ]);

        $this->assertStringContainsString('Panigale V4', $response->json('reply'));

        $payload = $response->json('structured_payload');
        $this->assertNotNull($payload);
        $this->assertSame('bike_recommendations', $payload['type']);
        $this->assertNotEmpty($payload['bikes']);
        $this->assertSame($this->bikeA->id, $payload['bikes'][0]['id']);

        // Verify conversation and message records in DB
        $this->assertDatabaseHas('ai_conversations', [
            'tenant_id' => $this->showroomA->id,
            'session_id' => 'sess_test_recom_123',
            'detected_intent' => 'bike_recommendation',
        ]);

        $this->assertDatabaseHas('ai_messages', [
            'tenant_id' => $this->showroomA->id,
            'sender' => 'user',
            'message' => 'Can you recommend a sports bike under 30 lakhs?',
        ]);

        $this->assertDatabaseHas('ai_messages', [
            'tenant_id' => $this->showroomA->id,
            'sender' => 'assistant',
        ]);
    }

    public function test_ai_assistant_matches_and_answers_custom_faqs(): void
    {
        $setting = $this->showroomA->getOrCreateAiSetting();
        $setting->update([
            'custom_faqs' => [
                [
                    'question' => 'Do you offer exchange bonus on old motorcycles?',
                    'answer' => 'Yes, we provide instant valuation and up to ₹50,000 exchange bonus on superbikes!',
                ],
            ],
        ]);

        $response = $this->postJson(route('showrooms.ai.chat', $this->showroomA), [
            'message' => 'Do you offer exchange bonus on old motorcycles?',
            'session_id' => 'sess_test_faq_123',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'detected_intent' => 'custom_faq',
        ]);

        $this->assertStringContainsString('instant valuation and up to ₹50,000', $response->json('reply'));
    }

    public function test_ai_assistant_answers_showroom_timings_and_location(): void
    {
        $response = $this->postJson(route('showrooms.ai.chat', $this->showroomA), [
            'message' => 'What are your showroom timings and location?',
            'session_id' => 'sess_test_hours_123',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'detected_intent' => 'general',
        ]);

        $this->assertStringContainsString('Bangalore', $response->json('reply'));
        $this->assertStringContainsString('Karnataka', $response->json('reply'));
    }

    public function test_ai_assistant_answers_service_and_workshop_inquiry(): void
    {
        $response = $this->postJson(route('showrooms.ai.chat', $this->showroomA), [
            'message' => 'I need general maintenance and oil change workshop service.',
            'session_id' => 'sess_test_service_123',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'detected_intent' => 'service_inquiry',
        ]);

        $this->assertStringContainsString('workshop', strtolower($response->json('reply')));
        $payload = $response->json('structured_payload');
        $this->assertNotNull($payload);
        $this->assertSame('service_options', $payload['type']);
        $this->assertNotEmpty($payload['book_url']);
    }

    public function test_ai_assistant_answers_promotions_and_scratch_cards_inquiry(): void
    {
        PromotionalCampaign::create([
            'tenant_id' => $this->showroomA->id,
            'name' => 'Diwali Superbike Scratch & Win',
            'slug' => 'diwali-scratch-win',
            'description' => 'Win riding jackets and free maintenance.',
            'start_date' => now()->subDay(),
            'end_date' => now()->addDays(15),
            'status' => CampaignStatus::Active->value,
        ]);

        $response = $this->postJson(route('showrooms.ai.chat', $this->showroomA), [
            'message' => 'Are there any active discounts or scratch cards?',
            'session_id' => 'sess_test_promos_123',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'detected_intent' => 'promotions',
        ]);

        $this->assertStringContainsString('Diwali Superbike Scratch', $response->json('reply'));
        $payload = $response->json('structured_payload');
        $this->assertSame('promotional_campaigns', $payload['type']);
    }

    public function test_ai_assistant_automatically_converts_lead_when_phone_provided_for_test_ride(): void
    {
        $response = $this->postJson(route('showrooms.ai.chat', $this->showroomA), [
            'message' => 'I want to schedule a test ride, my phone is 9876543210 and name is Arjun Mehta',
            'session_id' => 'sess_test_lead_123',
            'customer_name' => 'Arjun Mehta',
            'customer_phone' => '9876543210',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'detected_intent' => 'test_ride',
            'lead_converted' => true,
        ]);

        $this->assertStringContainsString('booked your test ride request', $response->json('reply'));

        // Verify Enquiry created
        $enquiry = Enquiry::where('tenant_id', $this->showroomA->id)
            ->where('customer_phone', '9876543210')
            ->first();

        $this->assertNotNull($enquiry);
        $this->assertSame('test_ride', $enquiry->type->value ?? (string) $enquiry->type);
        $this->assertSame('Arjun Mehta', $enquiry->customer_name);

        // Verify conversation is linked to enquiry
        $conversation = AiConversation::where('session_id', 'sess_test_lead_123')->first();
        $this->assertNotNull($conversation);
        $this->assertTrue($conversation->lead_converted);
        $this->assertSame($enquiry->id, $conversation->enquiry_id);
    }

    public function test_visitor_can_retrieve_chat_history(): void
    {
        $sessionId = 'sess_history_flow_999';

        $this->postJson(route('showrooms.ai.chat', $this->showroomA), [
            'message' => 'Hello there',
            'session_id' => $sessionId,
        ]);

        $this->postJson(route('showrooms.ai.chat', $this->showroomA), [
            'message' => 'Where are you located?',
            'session_id' => $sessionId,
        ]);

        $response = $this->getJson(route('showrooms.ai.history', [
            'showroom' => $this->showroomA,
            'session_id' => $sessionId,
        ]));

        $response->assertStatus(200);
        $response->assertJsonCount(4, 'messages'); // 2 user messages + 2 bot responses
    }

    public function test_rate_limiting_enforces_15_queries_per_minute(): void
    {
        $sessionId = 'sess_throttle_test_111';

        // 15 requests succeed
        for ($i = 0; $i < 15; $i++) {
            $resp = $this->postJson(route('showrooms.ai.chat', $this->showroomA), [
                'message' => "Query number {$i}",
                'session_id' => $sessionId,
            ]);
            $resp->assertStatus(200);
        }

        // 16th request is rate-limited (HTTP 429)
        $rateLimitedResp = $this->postJson(route('showrooms.ai.chat', $this->showroomA), [
            'message' => 'Excess inquiry beyond rate limit',
            'session_id' => $sessionId,
        ]);

        $rateLimitedResp->assertStatus(429);
        $this->assertStringContainsString('Too many inquiries sent', $rateLimitedResp->json('message'));
    }

    public function test_public_chat_blocked_when_showroom_lacks_ai_entitlement(): void
    {
        $response = $this->postJson(route('showrooms.ai.chat', $this->showroomB), [
            'message' => 'Hello, are you there?',
            'session_id' => 'sess_unentitled_123',
        ]);

        $response->assertStatus(403);
    }

    public function test_multi_tenant_isolation_staff_cannot_view_or_delete_another_showroom_conversation(): void
    {
        $conversationA = AiConversation::create([
            'tenant_id' => $this->showroomA->id,
            'session_id' => 'sess_secret_apex',
            'customer_name' => 'Confidential Lead',
        ]);

        // Showroom B staff tries to view Showroom A's conversation transcript
        $response = $this->actingAs($this->ownerB)
            ->get(route('showrooms.manage.ai.conversations.show', [$this->showroomA, $conversationA]));

        $response->assertStatus(403);

        // Showroom B staff tries to delete Showroom A's conversation under Showroom B route
        $response2 = $this->actingAs($this->ownerB)
            ->delete(route('showrooms.manage.ai.conversations.destroy', [$this->showroomB, $conversationA]));

        $response2->assertStatus(404);
    }

    public function test_staff_can_delete_conversation_history(): void
    {
        $conversation = AiConversation::create([
            'tenant_id' => $this->showroomA->id,
            'session_id' => 'sess_to_delete',
            'customer_name' => 'Test Lead',
        ]);

        $response = $this->actingAs($this->ownerA)
            ->delete(route('showrooms.manage.ai.conversations.destroy', [$this->showroomA, $conversation]));

        $response->assertRedirect(route('showrooms.manage.ai.index', $this->showroomA));
        $this->assertSoftDeleted('ai_conversations', ['id' => $conversation->id]);
    }

    public function test_staff_can_configure_avatar_and_voice_settings(): void
    {
        $response = $this->actingAs($this->ownerA)
            ->put(route('showrooms.manage.ai.settings', $this->showroomA), [
                'bot_name' => 'Sawaranti TVS Advisor',
                'tone' => 'friendly',
                'is_enabled' => '1',
                'avatar_type' => 'female_saree',
                'voice_enabled' => '1',
                'voice_language' => 'hi-IN',
                'voice_welcome_text' => 'नमस्ते! सवरंती टीवीएस में आपका स्वागत है।',
            ]);

        $response->assertRedirect(route('showrooms.manage.ai.index', $this->showroomA));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('ai_assistant_settings', [
            'tenant_id' => $this->showroomA->id,
            'avatar_type' => 'female_saree',
            'voice_enabled' => true,
            'voice_language' => 'hi-IN',
            'voice_welcome_text' => 'नमस्ते! सवरंती टीवीएस में आपका स्वागत है।',
        ]);
    }

    public function test_public_ai_settings_endpoint_exposes_avatar_and_voice_data(): void
    {
        $setting = $this->showroomA->getOrCreateAiSetting();
        $setting->update([
            'avatar_type' => 'male_tvs',
            'voice_enabled' => true,
            'voice_language' => 'hi-IN',
            'voice_welcome_text' => 'नमस्ते! सवरंती टीवीएस में आपका स्वागत है।',
        ]);

        $response = $this->getJson(route('showrooms.ai.settings', $this->showroomA));

        $response->assertOk()
            ->assertJson([
                'is_available' => true,
                'avatar_type' => 'male_tvs',
                'voice_enabled' => true,
                'voice_language' => 'hi-IN',
                'voice_welcome_text' => 'नमस्ते! सवरंती टीवीएस में आपका स्वागत है।',
            ]);

        $this->assertStringContainsString('avatar-male-tvs-cutout.png', $response->json('avatar_url'));
    }

    public function test_ai_assistant_setting_avatar_url_and_voice_accessors(): void
    {
        $setting = $this->showroomA->getOrCreateAiSetting();

        $setting->update(['avatar_type' => 'male_tvs']);
        $this->assertStringContainsString('avatar-male-tvs-cutout.png', $setting->avatar_url);

        $setting->update(['avatar_type' => 'female_saree']);
        $this->assertStringContainsString('avatar-female-saree-cutout.png', $setting->avatar_url);

        $setting->update([
            'avatar_type' => 'custom',
            'avatar_custom_url' => 'https://example.com/custom-avatar.png',
        ]);
        $this->assertSame('https://example.com/custom-avatar.png', $setting->avatar_url);

        $setting->update([
            'voice_language' => 'hi-IN',
            'voice_welcome_text' => null,
            'avatar_type' => 'female_saree',
            'voice_gender' => 'female',
            'voice_pitch' => null,
        ]);
        $this->assertStringContainsString('नमस्ते', $setting->voice_welcome_text_or_default);
        $this->assertSame('female', $setting->effective_voice_gender);
        $this->assertEquals(1.22, $setting->effective_voice_pitch);

        $setting->update([
            'avatar_type' => 'male_tvs',
            'voice_gender' => 'male',
            'voice_pitch' => null,
        ]);
        $this->assertSame('male', $setting->effective_voice_gender);
        $this->assertEquals(0.88, $setting->effective_voice_pitch);
    }

    public function test_ai_assistant_setting_welcome_video_accessors(): void
    {
        $setting = $this->showroomA->getOrCreateAiSetting();

        $this->assertFalse($setting->has_welcome_video);
        $this->assertNull($setting->welcome_video_url);

        $setting->update([
            'welcome_video_path' => 'https://cdn.example.com/namaste-welcome.mp4',
            'welcome_video_type' => 'url',
        ]);

        $this->assertTrue($setting->has_welcome_video);
        $this->assertSame('https://cdn.example.com/namaste-welcome.mp4', $setting->welcome_video_url);

        $setting->update([
            'welcome_video_path' => 'ai-video/1/video.mp4',
            'welcome_video_type' => 'upload',
        ]);

        $this->assertTrue($setting->has_welcome_video);
        $this->assertStringContainsString('storage/ai-video/1/video.mp4', $setting->welcome_video_url);
    }

    public function test_showroom_owner_can_update_and_remove_welcome_video_via_url(): void
    {
        $this->actingAs($this->ownerA);

        $response = $this->put(route('showrooms.manage.ai.settings', $this->showroomA), [
            'welcome_video_url' => 'https://cdn.example.com/veo-namaste.mp4',
            'is_enabled' => '1',
            'voice_enabled' => '1',
        ]);

        $response->assertRedirect(route('showrooms.manage.ai.index', $this->showroomA));

        $setting = $this->showroomA->fresh()->getOrCreateAiSetting();
        $this->assertSame('https://cdn.example.com/veo-namaste.mp4', $setting->welcome_video_path);
        $this->assertSame('url', $setting->welcome_video_type);
        $this->assertTrue($setting->has_welcome_video);

        // Now remove video
        $removeResponse = $this->put(route('showrooms.manage.ai.settings', $this->showroomA), [
            'remove_welcome_video' => '1',
            'is_enabled' => '1',
            'voice_enabled' => '1',
        ]);

        $removeResponse->assertRedirect(route('showrooms.manage.ai.index', $this->showroomA));

        $setting->refresh();
        $this->assertNull($setting->welcome_video_path);
        $this->assertSame('none', $setting->welcome_video_type);
        $this->assertFalse($setting->has_welcome_video);
    }

    public function test_showroom_owner_can_upload_welcome_video_file(): void
    {
        Storage::fake('public');
        $this->actingAs($this->ownerA);

        $videoFile = UploadedFile::fake()->create('namaste-welcome.mp4', 2048, 'video/mp4');

        $response = $this->put(route('showrooms.manage.ai.settings', $this->showroomA), [
            'welcome_video' => $videoFile,
            'is_enabled' => '1',
            'voice_enabled' => '1',
        ]);

        $response->assertRedirect(route('showrooms.manage.ai.index', $this->showroomA));

        $setting = $this->showroomA->fresh()->getOrCreateAiSetting();
        $this->assertNotNull($setting->welcome_video_path);
        $this->assertSame('upload', $setting->welcome_video_type);
        Storage::disk('public')->assertExists($setting->welcome_video_path);
    }

    public function test_showroom_owner_can_generate_gemini_video_and_poll_completion(): void
    {
        Storage::fake('public');
        $this->actingAs($this->ownerA);

        Http::fake([
            'https://generativelanguage.googleapis.com/v1beta/models/veo-3.1-fast-generate-preview:predictLongRunning*' => Http::response([
                'name' => 'operations/test-op-123',
            ], 200),
            'https://generativelanguage.googleapis.com/v1beta/operations/test-op-123' => Http::response([
                'done' => true,
                'response' => [
                    'generatedVideos' => [
                        [
                            'video' => [
                                'bytesBase64Encoded' => base64_encode('fake-video-bytes'),
                            ],
                        ],
                    ],
                ],
            ], 200),
        ]);

        // 1. Initiate Generation
        $initResponse = $this->postJson(route('showrooms.manage.ai.generate-video', $this->showroomA), [
            'gemini_api_key' => 'AIzaSyTestApiKey123',
            'prompt' => 'A woman saying namaste in showroom',
            'duration' => 8,
            'resolution' => '720p',
        ]);

        $initResponse->assertOk()
            ->assertJson([
                'success' => true,
                'operation_name' => 'operations/test-op-123',
            ]);

        // 2. Poll for completion
        $pollResponse = $this->postJson(route('showrooms.manage.ai.check-video', $this->showroomA), [
            'operation_name' => 'operations/test-op-123',
            'gemini_api_key' => 'AIzaSyTestApiKey123',
        ]);

        $pollResponse->assertOk()
            ->assertJson([
                'done' => true,
                'success' => true,
            ]);

        $setting = $this->showroomA->fresh()->getOrCreateAiSetting();
        $this->assertNotNull($setting->welcome_video_path);
        $this->assertTrue($setting->has_welcome_video);
        $this->assertSame('upload', $setting->welcome_video_type);
        $this->assertSame('AIzaSyTestApiKey123', $setting->gemini_api_key);
        Storage::disk('public')->assertExists($setting->welcome_video_path);
    }

    public function test_gemini_video_generation_requires_api_key(): void
    {
        $this->actingAs($this->ownerA);

        $response = $this->postJson(route('showrooms.manage.ai.generate-video', $this->showroomA), [
            'prompt' => 'A woman saying namaste in showroom',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);
    }

    public function test_showroom_owner_can_toggle_between_avatar_and_video_presenter_modes(): void
    {
        $this->actingAs($this->ownerA);
        $setting = $this->showroomA->getOrCreateAiSetting();
        $setting->update([
            'is_enabled' => true,
            'welcome_video_path' => 'https://cdn.example.com/welcome.mp4',
            'welcome_video_type' => 'url',
            'presenter_mode' => 'video',
        ]);

        $this->assertSame('video', $setting->fresh()->effective_presenter_mode);

        // Render widget with video mode active
        $viewVideo = $this->blade('<x-ai-assistant-widget :showroom="$showroom" />', ['showroom' => $this->showroomA->fresh()]);
        $viewVideo->assertSee('ai-welcome-video-player');
        $viewVideo->assertDontSee('ai-living-character');

        // Toggle back to avatar mode
        $response = $this->put(route('showrooms.manage.ai.settings', $this->showroomA), [
            'presenter_mode' => 'avatar',
            'is_enabled' => '1',
            'voice_enabled' => '1',
        ]);

        $response->assertRedirect(route('showrooms.manage.ai.index', $this->showroomA));
        $this->assertSame('avatar', $this->showroomA->fresh()->aiSetting->effective_presenter_mode);

        // Render widget with avatar mode active
        $viewAvatar = $this->blade('<x-ai-assistant-widget :showroom="$showroom" />', ['showroom' => $this->showroomA->fresh()]);
        $viewAvatar->assertSee('ai-living-character');
        $viewAvatar->assertDontSee('ai-welcome-video-player');
    }

    public function test_showroom_owner_can_transliterate_hinglish_to_hindi(): void
    {
        $this->actingAs($this->ownerA);

        Http::fake([
            'inputtools.google.com/*' => Http::response([
                'SUCCESS',
                [
                    ['Namaste royal bike', ['नमस्ते रॉयल बाइक'], [], ['candidate_type' => [0]]],
                    [' apka swagat hai', [' आपका स्वागत है'], [], ['candidate_type' => [0]]],
                ],
            ], 200),
        ]);

        $response = $this->postJson(route('showrooms.manage.ai.transliterate', $this->showroomA), [
            'text' => 'Namaste royal bike apka swagat hai',
        ]);

        $response->assertOk()
            ->assertJson([
                'status' => 'success',
                'transliterated' => 'नमस्ते रॉयल बाइक आपका स्वागत है',
            ]);
    }

    public function test_transliterate_hindi_handles_empty_input(): void
    {
        $this->actingAs($this->ownerA);

        $response = $this->postJson(route('showrooms.manage.ai.transliterate', $this->showroomA), [
            'text' => '   ',
        ]);

        $response->assertOk()
            ->assertJson([
                'status' => 'empty',
                'transliterated' => '',
            ]);
    }
}
