<?php

namespace App\Http\Controllers\Showroom;

use App\Http\Controllers\Controller;
use App\Models\AiConversation;
use App\Models\Showroom;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

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
            'bot_name' => ['required', 'string', 'max:100'],
            'welcome_message' => ['nullable', 'string', 'max:1000'],
            'tone' => ['required', 'string', 'in:friendly,professional,enthusiastic'],
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
        $setting->update([
            'bot_name' => $validated['bot_name'],
            'welcome_message' => $validated['welcome_message'] ?? null,
            'tone' => $validated['tone'],
            'is_enabled' => $request->boolean('is_enabled'),
            'suggested_prompts' => ! empty($promptsArray) ? $promptsArray : null,
            'custom_faqs' => $faqsArray,
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
