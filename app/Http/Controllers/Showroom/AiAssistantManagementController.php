<?php

namespace App\Http\Controllers\Showroom;

use App\Http\Controllers\Controller;
use App\Models\AiConversation;
use App\Models\Showroom;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
            'remove_custom_avatar' => ['nullable', 'boolean'],
            'remove_custom_audio' => ['nullable', 'boolean'],
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

        $chosenAvatarType = $validated['avatar_type'] ?? $setting->avatar_type ?? 'male_tvs';
        $defaultGender = $chosenAvatarType === 'female_saree' ? 'female' : 'male';
        $chosenGender = $validated['voice_gender'] ?? $defaultGender;
        $defaultPitch = $chosenGender === 'female' ? 1.22 : 0.88;
        $chosenPitch = isset($validated['voice_pitch']) && $validated['voice_pitch'] !== null && $validated['voice_pitch'] !== ''
            ? (float) $validated['voice_pitch']
            : ($setting->voice_pitch ?: $defaultPitch);

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
            'is_enabled' => $request->boolean('is_enabled'),
            'suggested_prompts' => ! empty($promptsArray) ? $promptsArray : $setting->suggested_prompts,
            'custom_faqs' => ! empty($faqsArray) ? $faqsArray : $setting->custom_faqs,
        ]);

        return redirect()
            ->route('showrooms.manage.ai.index', $showroom)
            ->with('success', 'AI Assistant settings and custom FAQs updated successfully.');
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
