<?php

namespace App\Http\Controllers\Showroom;

use App\Http\Controllers\Controller;
use App\Models\AiConversation;
use App\Models\Showroom;
use App\Services\AiAssistantService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class PublicAiAssistantController extends Controller
{
    public function __construct(
        protected AiAssistantService $aiService
    ) {}

    /**
     * Send customer message to AI Assistant and receive intelligent reply.
     */
    public function chat(Request $request, Showroom $showroom): JsonResponse
    {
        abort_if(! $showroom->isWebsiteAccessible(), 404);

        if (! $showroom->canAccessFeature('has_ai_assistant')) {
            return response()->json([
                'success' => false,
                'message' => 'AI Virtual Assistant is not enabled for this showroom subscription.',
            ], 403);
        }

        $setting = $showroom->getOrCreateAiSetting();
        if (! $setting->is_enabled) {
            return response()->json([
                'success' => false,
                'message' => 'The AI Concierge is currently offline for maintenance.',
            ], 503);
        }

        // Rate limit: 15 queries per minute per IP / session
        $throttleKey = 'ai_chat:'.$showroom->id.':'.($request->input('session_id') ?? $request->ip());
        if (RateLimiter::tooManyAttempts($throttleKey, 15)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            return response()->json([
                'success' => false,
                'message' => "Too many inquiries sent. Please wait {$seconds} seconds before sending another message.",
            ], 429);
        }
        RateLimiter::hit($throttleKey, 60);

        $validated = $request->validate([
            'message' => ['required', 'string', 'max:1000'],
            'session_id' => ['required', 'string', 'max:100'],
            'customer_name' => ['nullable', 'string', 'max:255'],
            'customer_email' => ['nullable', 'email', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:50'],
            'bike_id' => ['nullable', 'integer'],
        ]);

        $metadata = [
            'customer_name' => $validated['customer_name'] ?? null,
            'customer_email' => $validated['customer_email'] ?? null,
            'customer_phone' => $validated['customer_phone'] ?? null,
            'bike_id' => $validated['bike_id'] ?? null,
        ];

        $result = $this->aiService->processMessage(
            $showroom,
            $validated['session_id'],
            $validated['message'],
            $request->user(),
            $metadata
        );

        return response()->json([
            'success' => true,
            'bot_name' => $setting->bot_name,
            'conversation_id' => $result['conversation_id'],
            'detected_intent' => $result['detected_intent'],
            'reply' => $result['reply'],
            'structured_payload' => $result['structured_payload'],
            'lead_converted' => $result['lead_converted'],
        ]);
    }

    /**
     * Retrieve chat history for the current visitor session.
     */
    public function history(Request $request, Showroom $showroom): JsonResponse
    {
        abort_if(! $showroom->isWebsiteAccessible(), 404);

        $sessionId = $request->query('session_id');
        if (! $sessionId) {
            return response()->json(['messages' => []]);
        }

        $conversation = AiConversation::where('tenant_id', $showroom->id)
            ->where('session_id', $sessionId)
            ->with(['messages'])
            ->first();

        if (! $conversation) {
            return response()->json(['messages' => []]);
        }

        $messages = $conversation->messages->map(fn ($m) => [
            'id' => $m->id,
            'sender' => $m->sender,
            'message' => $m->message,
            'structured_payload' => $m->structured_payload,
            'created_at' => $m->created_at->toIso8601String(),
        ]);

        return response()->json([
            'conversation_id' => $conversation->id,
            'lead_converted' => $conversation->lead_converted,
            'messages' => $messages,
        ]);
    }

    /**
     * Get AI Assistant configuration & suggested prompts for public widget.
     */
    public function settings(Showroom $showroom): JsonResponse
    {
        abort_if(! $showroom->isWebsiteAccessible(), 404);

        $hasFeature = $showroom->canAccessFeature('has_ai_assistant');
        $setting = $showroom->getOrCreateAiSetting();

        return response()->json([
            'is_available' => $hasFeature && $setting->is_enabled,
            'bot_name' => $setting->bot_name,
            'avatar_type' => $setting->avatar_type,
            'avatar_url' => $setting->avatar_url,
            'voice_enabled' => (bool) $setting->voice_enabled,
            'voice_language' => $setting->voice_language,
            'voice_gender' => $setting->effective_voice_gender,
            'voice_pitch' => $setting->effective_voice_pitch,
            'voice_welcome_text' => $setting->voice_welcome_text_or_default,
            'voice_audio_url' => $setting->voice_audio_url,
            'welcome_message' => $setting->welcome_message_or_default,
            'tone' => $setting->tone,
            'suggested_prompts' => $setting->suggested_prompts ?? [
                'Recommend a sports bike under ₹15 Lakhs',
                'What electric motorcycles do you have?',
                'How do I schedule a test ride?',
                'What are your workshop timings today?',
                'Any active promotions or scratch cards?',
            ],
        ]);
    }
}
