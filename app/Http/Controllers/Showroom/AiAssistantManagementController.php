<?php

namespace App\Http\Controllers\Showroom;

use App\Http\Controllers\Controller;
use App\Models\AiConversation;
use App\Models\Showroom;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class AiAssistantManagementController extends Controller
{
    /**
     * Display AI Virtual Assistant configuration and customer conversation ledger.
     */
    public function index(Request $request, Showroom $showroom): View
    {
        $hasFeature = $showroom->canAccessFeature('has_ai_assistant');
        $setting = $showroom->getOrCreateAiSetting();

        $search = $request->query('search');
        $intent = $request->query('intent');
        $converted = $request->query('converted');

        $query = $showroom->aiConversations()->with(['messages', 'enquiry']);

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('customer_name', 'LIKE', "%{$search}%")
                    ->orWhere('customer_phone', 'LIKE', "%{$search}%")
                    ->orWhere('customer_email', 'LIKE', "%{$search}%")
                    ->orWhere('session_id', 'LIKE', "%{$search}%");
            });
        }

        if (! empty($intent) && $intent !== 'all') {
            $query->where('detected_intent', $intent);
        }

        if ($converted === '1') {
            $query->where('lead_converted', true);
        } elseif ($converted === '0') {
            $query->where('lead_converted', false);
        }

        $conversations = $query->paginate(15)->withQueryString();

        $totalConversations = $showroom->aiConversations()->count();
        $totalConvertedLeads = $showroom->aiConversations()->where('lead_converted', true)->count();
        $todayInquiries = $showroom->aiConversations()->whereDate('created_at', today())->count();

        return view('showrooms.manage.ai.index', compact(
            'showroom',
            'hasFeature',
            'setting',
            'conversations',
            'totalConversations',
            'totalConvertedLeads',
            'todayInquiries',
            'search',
            'intent',
            'converted'
        ));
    }

    /**
     * Update AI Assistant bot configuration and custom FAQs.
     */
    public function updateSettings(Request $request, Showroom $showroom): RedirectResponse
    {
        if (! $showroom->canAccessFeature('has_ai_assistant')) {
            abort(403, 'Your subscription plan does not include the AI Virtual Assistant. Please upgrade to Pro or Enterprise.');
        }

        $validated = $request->validate([
            'bot_name' => ['nullable', 'string', 'max:100'],
            'welcome_message' => ['nullable', 'string', 'max:1000'],
            'tone' => ['nullable', 'string', 'in:friendly,professional,enthusiastic'],
            'avatar_type' => ['nullable', 'string', 'in:male_tvs,female_saree,custom'],
            'avatar_custom' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'voice_enabled' => ['nullable', 'boolean'],
            'voice_language' => ['nullable', 'string', 'in:hi-IN,en-IN,en-US'],
            'voice_gender' => ['nullable', 'string', 'in:male,female'],
            'voice_pitch' => ['nullable', 'numeric', 'min:0.5', 'max:2.0'],
            'voice_name' => ['nullable', 'string', 'max:100'],
            'voice_welcome_text' => ['nullable', 'string', 'max:1000'],
            'voice_audio' => ['nullable', 'file', 'mimes:mp3,wav,ogg,m4a', 'max:10240'],
            'welcome_video' => ['nullable', 'file', 'mimes:mp4,webm,mov,ogg', 'max:51200'],
            'welcome_video_url' => ['nullable', 'url', 'max:1000'],
            'gemini_api_key' => ['nullable', 'string', 'max:500'],
            'presenter_mode' => ['nullable', 'string', 'in:avatar,video'],
            'remove_custom_avatar' => ['nullable', 'boolean'],
            'remove_custom_audio' => ['nullable', 'boolean'],
            'remove_welcome_video' => ['nullable', 'boolean'],
            'is_enabled' => ['nullable', 'boolean'],
            'suggested_prompts' => ['nullable', 'string'],
            'faqs' => ['nullable', 'array'],
            'faqs.*.question' => ['nullable', 'string', 'max:255'],
            'faqs.*.answer' => ['nullable', 'string', 'max:1000'],
        ]);

        $promptsArray = [];
        if (! empty($validated['suggested_prompts'])) {
            $lines = preg_split('/\r\n|\r|\n/', trim($validated['suggested_prompts']));
            $promptsArray = array_values(array_filter(array_map('trim', $lines ?: [])));
        }

        $faqsArray = [];
        if (! empty($validated['faqs'])) {
            foreach ($validated['faqs'] as $faq) {
                $q = trim((string) ($faq['question'] ?? ''));
                $a = trim((string) ($faq['answer'] ?? ''));
                if ($q !== '' && $a !== '') {
                    $faqsArray[] = [
                        'question' => $q,
                        'answer' => $a,
                    ];
                }
            }
        }

        $setting = $showroom->getOrCreateAiSetting();

        $customAvatarUrl = $setting->avatar_custom_url;
        if ($request->boolean('remove_custom_avatar')) {
            if ($customAvatarUrl && ! str_starts_with($customAvatarUrl, 'http')) {
                Storage::disk('public')->delete($customAvatarUrl);
            }
            $customAvatarUrl = null;
        } elseif ($request->hasFile('avatar_custom')) {
            if ($customAvatarUrl && ! str_starts_with($customAvatarUrl, 'http')) {
                Storage::disk('public')->delete($customAvatarUrl);
            }
            $customAvatarUrl = $request->file('avatar_custom')->store("ai-avatars/{$showroom->id}", 'public');
        }

        $voiceAudioPath = $setting->voice_audio_path;
        if ($request->boolean('remove_custom_audio')) {
            if ($voiceAudioPath && ! str_starts_with($voiceAudioPath, 'http')) {
                Storage::disk('public')->delete($voiceAudioPath);
            }
            $voiceAudioPath = null;
        } elseif ($request->hasFile('voice_audio')) {
            if ($voiceAudioPath && ! str_starts_with($voiceAudioPath, 'http')) {
                Storage::disk('public')->delete($voiceAudioPath);
            }
            $voiceAudioPath = $request->file('voice_audio')->store("ai-audio/{$showroom->id}", 'public');
        }

        $welcomeVideoPath = $setting->welcome_video_path;
        $welcomeVideoType = $setting->welcome_video_type ?? 'none';
        if ($request->boolean('remove_welcome_video')) {
            if ($welcomeVideoPath && ! str_starts_with($welcomeVideoPath, 'http')) {
                Storage::disk('public')->delete($welcomeVideoPath);
            }
            $welcomeVideoPath = null;
            $welcomeVideoType = 'none';
        } elseif ($request->hasFile('welcome_video')) {
            if ($welcomeVideoPath && ! str_starts_with($welcomeVideoPath, 'http')) {
                Storage::disk('public')->delete($welcomeVideoPath);
            }
            $welcomeVideoPath = $request->file('welcome_video')->store("ai-video/{$showroom->id}", 'public');
            $welcomeVideoType = 'upload';
        } elseif ($request->filled('welcome_video_url')) {
            if ($welcomeVideoPath && ! str_starts_with($welcomeVideoPath, 'http')) {
                Storage::disk('public')->delete($welcomeVideoPath);
            }
            $welcomeVideoPath = trim((string) $request->input('welcome_video_url'));
            $welcomeVideoType = 'url';
        }

        $chosenAvatarType = $validated['avatar_type'] ?? $setting->avatar_type ?? 'male_tvs';
        $defaultGender = $chosenAvatarType === 'female_saree' ? 'female' : 'male';
        $chosenGender = $validated['voice_gender'] ?? $defaultGender;
        $defaultPitch = $chosenGender === 'female' ? 1.22 : 0.88;
        $chosenPitch = isset($validated['voice_pitch']) && $validated['voice_pitch'] !== null && $validated['voice_pitch'] !== ''
            ? (float) $validated['voice_pitch']
            : ($setting->voice_pitch ?: $defaultPitch);

        $presenterMode = $validated['presenter_mode'] ?? $setting->presenter_mode ?? 'avatar';
        if ($request->boolean('remove_welcome_video')) {
            $presenterMode = 'avatar';
        } elseif ($request->hasFile('welcome_video') || $request->filled('welcome_video_url')) {
            $presenterMode = 'video';
        }

        $setting->update([
            'bot_name' => $validated['bot_name'] ?? $setting->bot_name ?? 'Showroom AI Advisor',
            'welcome_message' => $validated['welcome_message'] ?? $setting->welcome_message,
            'tone' => $validated['tone'] ?? $setting->tone ?? 'friendly',
            'avatar_type' => $chosenAvatarType,
            'avatar_custom_url' => $customAvatarUrl,
            'voice_enabled' => $request->has('voice_enabled') ? $request->boolean('voice_enabled') : (bool) ($setting->voice_enabled ?? true),
            'voice_language' => $validated['voice_language'] ?? $setting->voice_language ?? 'hi-IN',
            'voice_gender' => $chosenGender,
            'voice_pitch' => $chosenPitch,
            'voice_name' => $validated['voice_name'] ?? $setting->voice_name,
            'voice_welcome_text' => $validated['voice_welcome_text'] ?? null,
            'voice_audio_path' => $voiceAudioPath,
            'welcome_video_path' => $welcomeVideoPath,
            'welcome_video_type' => $welcomeVideoType,
            'gemini_api_key' => $request->filled('gemini_api_key') ? trim((string) $request->input('gemini_api_key')) : $setting->gemini_api_key,
            'presenter_mode' => $presenterMode,
            'is_enabled' => $request->boolean('is_enabled'),
            'suggested_prompts' => ! empty($promptsArray) ? $promptsArray : $setting->suggested_prompts,
            'custom_faqs' => ! empty($faqsArray) ? $faqsArray : $setting->custom_faqs,
        ]);

        return redirect()
            ->route('showrooms.manage.ai.index', $showroom)
            ->with('success', 'AI Assistant settings and custom FAQs updated successfully.');
    }

    /**
     * Initiate video generation using Google Gemini (Veo).
     */
    public function generateGeminiVideo(Request $request, Showroom $showroom): JsonResponse
    {
        if (! $showroom->canAccessFeature('has_ai_assistant')) {
            return response()->json(['success' => false, 'message' => 'Subscription plan does not include AI Assistant.'], 403);
        }

        $validated = $request->validate([
            'prompt' => ['required', 'string', 'max:2000'],
            'gemini_api_key' => ['nullable', 'string', 'max:500'],
            'duration' => ['nullable', 'integer', 'min:4', 'max:16'],
            'resolution' => ['nullable', 'string', 'in:720p,1080p'],
        ]);

        $setting = $showroom->getOrCreateAiSetting();
        $apiKey = trim((string) ($validated['gemini_api_key'] ?? '')) ?: $setting->effective_gemini_api_key;

        if (empty($apiKey)) {
            return response()->json([
                'success' => false,
                'message' => 'Please enter a Gemini API Key to generate the video.',
            ], 422);
        }

        if (! empty($validated['gemini_api_key'])) {
            $setting->update(['gemini_api_key' => $validated['gemini_api_key']]);
        }

        $duration = (int) ($validated['duration'] ?? 8);
        $resolution = $validated['resolution'] ?? '720p';

        $modelsToTry = ['veo-3.1-fast-generate-preview', 'veo-2.0-generate-001', 'veo-3.1-generate-preview'];
        $lastError = null;
        $operationName = null;

        foreach ($modelsToTry as $model) {
            $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:predictLongRunning";
            try {
                $response = Http::withHeaders([
                    'x-goog-api-key' => $apiKey,
                    'Content-Type' => 'application/json',
                ])->timeout(30)->post($url, [
                    'instances' => [
                        ['prompt' => $validated['prompt']],
                    ],
                    'parameters' => [
                        'sampleCount' => 1,
                        'resolution' => $resolution,
                        'aspectRatio' => '16:9',
                        'durationSeconds' => $duration,
                    ],
                ]);

                if ($response->successful() && ! empty($response->json('name'))) {
                    $operationName = $response->json('name');
                    break;
                }

                $lastError = $response->json('error.message') ?? $response->body();
            } catch (\Throwable $e) {
                $lastError = $e->getMessage();
            }
        }

        if (empty($operationName)) {
            return response()->json([
                'success' => false,
                'message' => 'Gemini Veo API error: '.($lastError ?: 'Unable to initiate video generation.'),
            ], 400);
        }

        return response()->json([
            'success' => true,
            'operation_name' => $operationName,
            'message' => 'Video generation initiated on Gemini. Polling for completion...',
        ]);
    }

    /**
     * Poll Gemini Long-Running Operation status and auto-save completed video.
     */
    public function checkGeminiVideo(Request $request, Showroom $showroom): JsonResponse
    {
        if (! $showroom->canAccessFeature('has_ai_assistant')) {
            return response()->json(['success' => false, 'message' => 'Subscription plan does not include AI Assistant.'], 403);
        }

        $validated = $request->validate([
            'operation_name' => ['required', 'string', 'max:500'],
            'gemini_api_key' => ['nullable', 'string', 'max:500'],
        ]);

        $setting = $showroom->getOrCreateAiSetting();
        $apiKey = trim((string) ($validated['gemini_api_key'] ?? '')) ?: $setting->effective_gemini_api_key;

        if (empty($apiKey)) {
            return response()->json(['success' => false, 'message' => 'Gemini API Key missing.'], 422);
        }

        $opName = ltrim($validated['operation_name'], '/');
        $url = "https://generativelanguage.googleapis.com/v1beta/{$opName}";

        try {
            $response = Http::withHeaders([
                'x-goog-api-key' => $apiKey,
            ])->timeout(30)->get($url);

            if (! $response->successful()) {
                return response()->json([
                    'done' => true,
                    'success' => false,
                    'message' => $response->json('error.message') ?? 'Status check failed: '.$response->body(),
                ], 400);
            }

            $data = $response->json();

            if (empty($data['done'])) {
                return response()->json([
                    'done' => false,
                    'message' => 'AI is generating and rendering video frames...',
                ]);
            }

            if (! empty($data['error'])) {
                return response()->json([
                    'done' => true,
                    'success' => false,
                    'message' => $data['error']['message'] ?? 'Gemini generation encountered an error.',
                ]);
            }

            $videoUri = $data['response']['generatedVideos'][0]['video']['uri']
                ?? $data['response']['generateVideoResponse']['generatedSamples'][0]['video']['uri']
                ?? null;

            $base64Data = $data['response']['generatedVideos'][0]['video']['bytesBase64Encoded']
                ?? $data['response']['generateVideoResponse']['generatedSamples'][0]['video']['bytesBase64Encoded']
                ?? null;

            $videoBytes = null;
            if (! empty($videoUri)) {
                $dlResponse = Http::withHeaders(['x-goog-api-key' => $apiKey])->timeout(60)->get($videoUri);
                if ($dlResponse->successful()) {
                    $videoBytes = $dlResponse->body();
                }
            } elseif (! empty($base64Data)) {
                $videoBytes = base64_decode($base64Data);
            }

            if (empty($videoBytes)) {
                if (! empty($videoUri)) {
                    $setting->update([
                        'welcome_video_path' => $videoUri,
                        'welcome_video_type' => 'url',
                        'presenter_mode' => 'video',
                    ]);

                    return response()->json([
                        'done' => true,
                        'success' => true,
                        'video_url' => $setting->welcome_video_url,
                        'message' => 'Video generated and activated successfully via direct URI!',
                    ]);
                }

                return response()->json([
                    'done' => true,
                    'success' => false,
                    'message' => 'Generation finished but could not extract video file.',
                ]);
            }

            if ($setting->welcome_video_path && ! str_starts_with($setting->welcome_video_path, 'http')) {
                Storage::disk('public')->delete($setting->welcome_video_path);
            }

            $fileName = 'gemini_welcome_'.time().'.mp4';
            $path = "ai-video/{$showroom->id}/{$fileName}";
            Storage::disk('public')->put($path, $videoBytes);

            $setting->update([
                'welcome_video_path' => $path,
                'welcome_video_type' => 'upload',
                'presenter_mode' => 'video',
            ]);

            return response()->json([
                'done' => true,
                'success' => true,
                'video_url' => $setting->welcome_video_url,
                'message' => 'AI Welcome Video generated, downloaded, and activated on your showroom!',
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'done' => true,
                'success' => false,
                'message' => 'Error checking video status: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * View detailed transcript of a customer chat conversation.
     */
    public function showConversation(Showroom $showroom, AiConversation $conversation): View
    {
        abort_if($conversation->tenant_id !== $showroom->id, 404);

        $conversation->load(['messages', 'enquiry']);

        return view('showrooms.manage.ai.show', compact('showroom', 'conversation'));
    }

    /**
     * Delete an individual customer chat conversation.
     */
    public function destroyConversation(Showroom $showroom, AiConversation $conversation): RedirectResponse
    {
        abort_if($conversation->tenant_id !== $showroom->id, 404);

        $conversation->delete();

        return redirect()
            ->route('showrooms.manage.ai.index', $showroom)
            ->with('success', 'Conversation record deleted successfully.');
    }
}
