@extends('layouts.app')

@section('title', 'AI Virtual Concierge - ' . $showroom->name)

@section('content')
<div class="min-h-screen bg-slate-50 text-slate-900 flex flex-col font-sans selection:bg-rose-500 selection:text-white">
    @include('showrooms.partials.manage-nav')

    <main class="flex-1 w-full px-4 sm:px-6 lg:px-8 xl:px-10 py-8">

        <!-- Top Header & Breadcrumbs -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 mb-1">
                    <a href="{{ route('showrooms.manage', $showroom) }}" class="hover:text-slate-900 transition">Dashboard</a>
                    <span>/</span>
                    <span class="text-rose-600 font-semibold">AI Concierge</span>
                </div>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl sm:text-3xl font-black font-display text-slate-900 tracking-tight">AI Virtual Showroom Advisor</h1>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-rose-50 text-rose-700 border border-rose-200">
                        24/7 Concierge
                    </span>
                </div>
            </div>

            <div class="flex items-center gap-3">
                @if($showroom->isWebsiteAccessible())
                    <a href="{{ route('showrooms.show', $showroom) }}" target="_blank"
                       class="px-4 py-2 rounded-xl bg-white hover:bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-700 hover:text-slate-900 shadow-xs transition flex items-center gap-2">
                        <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        <span>View Storefront Widget</span>
                    </a>
                @endif
            </div>
        </div>

        <!-- Plan Gated Banner (if plan lacks AI feature) -->
        @if(! $hasFeature)
            <div class="mb-8 rounded-3xl bg-amber-50 border border-amber-200 p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-xs">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-amber-100 border border-amber-300 flex items-center justify-center text-amber-700 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">AI Assistant Gated Feature</h3>
                        <p class="text-xs text-amber-800 mt-0.5">
                            Your current subscription plan does not include the AI Virtual Showroom Concierge. Upgrade to Pro or Enterprise to enable 24/7 intelligent sales inquiries, automatic lead generation, and custom FAQs.
                        </p>
                    </div>
                </div>
                <a href="{{ route('showrooms.billing.index', $showroom) }}"
                   class="px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs shrink-0 transition shadow-xs">
                    Upgrade Subscription
                </a>
            </div>
        @endif

        <!-- Metric KPI Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-8">
            <div class="bg-white rounded-3xl p-5 border border-slate-200 flex items-center justify-between shadow-xs">
                <div>
                    <span class="text-xs text-slate-500 font-bold uppercase tracking-wider block">Total Conversations</span>
                    <span class="text-2xl font-black text-slate-900 mt-1 block">{{ number_format($totalConversations) }}</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-rose-50 border border-rose-200 flex items-center justify-center text-rose-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                </div>
            </div>

            <div class="bg-white rounded-3xl p-5 border border-slate-200 flex items-center justify-between shadow-xs">
                <div>
                    <span class="text-xs text-slate-500 font-bold uppercase tracking-wider block">Leads Converted</span>
                    <span class="text-2xl font-black text-emerald-600 mt-1 block">{{ number_format($totalConvertedLeads) }}</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 border border-emerald-200 flex items-center justify-center text-emerald-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>

            <div class="bg-white rounded-3xl p-5 border border-slate-200 flex items-center justify-between shadow-xs">
                <div>
                    <span class="text-xs text-slate-500 font-bold uppercase tracking-wider block">Today's Inquiries</span>
                    <span class="text-2xl font-black text-cyan-700 mt-1 block">{{ number_format($todayInquiries) }}</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-cyan-50 border border-cyan-200 flex items-center justify-center text-cyan-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

            <!-- Left Column: Bot Settings & Custom FAQs (5 cols) -->
            <div class="lg:col-span-5 space-y-6">

                <!-- Bot Configuration Form -->
                <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-xs">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-slate-900 mb-4 flex items-center gap-2">
                        <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span>Concierge Settings</span>
                    </h3>

                    <form method="POST" action="{{ route('showrooms.manage.ai.settings', $showroom) }}" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <!-- Enable/Disable Switch -->
                        <div class="flex items-center justify-between p-3.5 rounded-2xl bg-slate-50 border border-slate-200">
                            <div>
                                <span class="text-xs font-bold text-slate-900 block">Enable Public AI Assistant</span>
                                <span class="text-[11px] text-slate-500">Display floating widget on showroom website.</span>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_enabled" value="1" {{ old('is_enabled', $setting->is_enabled) ? 'checked' : '' }}
                                       @disabled(! $hasFeature) class="sr-only peer">
                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-rose-600"></div>
                            </label>
                        </div>

                        <!-- Bot Name -->
                        <div>
                            <label for="bot_name" class="block text-xs font-bold uppercase text-slate-700 mb-1">Concierge Name</label>
                            <input type="text" name="bot_name" id="bot_name" value="{{ old('bot_name', $setting->bot_name) }}" required
                                   @disabled(! $hasFeature)
                                   class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20">
                        </div>

                        <!-- Tone -->
                        <div>
                            <label for="tone" class="block text-xs font-bold uppercase text-slate-700 mb-1">Conversational Tone</label>
                            <select name="tone" id="tone" @disabled(! $hasFeature)
                                    class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20">
                                <option value="friendly" {{ old('tone', $setting->tone) === 'friendly' ? 'selected' : '' }}>Friendly & Welcoming</option>
                                <option value="professional" {{ old('tone', $setting->tone) === 'professional' ? 'selected' : '' }}>Professional & Direct</option>
                                <option value="enthusiastic" {{ old('tone', $setting->tone) === 'enthusiastic' ? 'selected' : '' }}>Enthusiastic High-Energy Superbike Specialist</option>
                            </select>
                        </div>

                        <!-- Welcome Message -->
                        <div>
                            <label for="welcome_message" class="block text-xs font-bold uppercase text-slate-700 mb-1">Welcome Greeting</label>
                            <textarea name="welcome_message" id="welcome_message" rows="3" @disabled(! $hasFeature)
                                      class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20 leading-relaxed">{{ old('welcome_message', $setting->welcome_message) }}</textarea>
                            <span class="text-[10px] text-slate-500 mt-1 block">Leave empty to use automatic showroom greeting.</span>
                        </div>

                        <!-- Suggested Prompts -->
                        <div>
                            <label for="suggested_prompts" class="block text-xs font-bold uppercase text-slate-700 mb-1">Suggested Prompt Chips (One per line)</label>
                            <textarea name="suggested_prompts" id="suggested_prompts" rows="4" @disabled(! $hasFeature)
                                      class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20 font-mono">{{ old('suggested_prompts', is_array($setting->suggested_prompts) ? implode("\n", $setting->suggested_prompts) : '') }}</textarea>
                        </div>

                        <!-- Custom FAQs Builder -->
                        <div class="pt-4 border-t border-slate-200">
                            <div class="flex items-center justify-between mb-3">
                                <div>
                                    <span class="text-xs font-bold uppercase text-slate-900 block">Custom FAQs Knowledge</span>
                                    <span class="text-[10px] text-slate-500">Add shop-specific answers (financing, trade-in, insurance).</span>
                                </div>
                                <button type="button" id="btn-add-faq" @disabled(! $hasFeature)
                                        class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 border border-slate-300 text-xs text-slate-700 font-semibold transition">
                                    + Add FAQ
                                </button>
                            </div>

                            <div id="faqs-list-container" class="space-y-3">
                                @forelse($setting->faqs_list as $fIndex => $faq)
                                    <div class="faq-row p-3 rounded-xl bg-slate-50 border border-slate-200 space-y-2">
                                        <div class="flex items-center justify-between gap-2">
                                            <input type="text" name="faqs[{{ $fIndex }}][question]" value="{{ $faq['question'] ?? '' }}" placeholder="Customer question (e.g. Do you accept trade-ins?)"
                                                   @disabled(! $hasFeature)
                                                   class="flex-1 px-2.5 py-1.5 rounded-lg bg-white border border-slate-300 text-xs text-slate-900 focus:outline-none focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20">
                                            <button type="button" onclick="this.closest('.faq-row').remove()" class="text-rose-600 hover:text-rose-800 p-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </button>
                                        </div>
                                        <textarea name="faqs[{{ $fIndex }}][answer]" rows="2" placeholder="Showroom answer provided by AI"
                                                  @disabled(! $hasFeature)
                                                  class="w-full px-2.5 py-1.5 rounded-lg bg-white border border-slate-300 text-xs text-slate-900 focus:outline-none focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20 leading-snug">{{ $faq['answer'] ?? '' }}</textarea>
                                    </div>
                                @empty
                                    <p id="no-faqs-text" class="text-xs text-slate-500 py-2 text-center">No custom FAQs defined yet.</p>
                                @endforelse
                            </div>
                        </div>

                        <button type="submit" @disabled(! $hasFeature)
                                class="w-full py-2.5 rounded-xl btn-brand-primary font-bold text-xs transition shadow-xs disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer"
                                style="box-shadow: 0 4px 14px 0 {{ $showroom->primary_color }}33;">
                            Save Assistant Configuration
                        </button>
                    </form>
                </div>

            </div>

            <!-- Right Column: Customer Conversations & Leads Ledger (7 cols) -->
            <div class="lg:col-span-7 space-y-6">

                <!-- Conversations Ledger Card -->
                <div class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-xs">
                    <div class="p-6 border-b border-slate-200">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div>
                                <h3 class="text-base font-bold text-slate-900">Customer Conversation History</h3>
                                <p class="text-xs text-slate-500 mt-0.5">Live transcripts of visitor inquiries, recommended bikes, and captured sales leads.</p>
                            </div>
                        </div>

                        <!-- Filters -->
                        <form method="GET" action="{{ route('showrooms.manage.ai.index', $showroom) }}" class="mt-4 grid grid-cols-1 sm:grid-cols-12 gap-2">
                            <div class="sm:col-span-6">
                                <input type="text" name="search" value="{{ $search }}" placeholder="Search customer, phone, or session..."
                                       class="w-full px-3 py-1.5 rounded-xl bg-white border border-slate-300 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20">
                            </div>
                            <div class="sm:col-span-3">
                                <select name="intent" class="w-full px-2.5 py-1.5 rounded-xl bg-white border border-slate-300 text-xs text-slate-900 focus:outline-none focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20">
                                    <option value="all">All Intents</option>
                                    <option value="bike_recommendation" {{ $intent === 'bike_recommendation' ? 'selected' : '' }}>Motorcycle Inquiry</option>
                                    <option value="test_ride" {{ $intent === 'test_ride' ? 'selected' : '' }}>Test Ride Request</option>
                                    <option value="service_inquiry" {{ $intent === 'service_inquiry' ? 'selected' : '' }}>Workshop Service</option>
                                    <option value="promotions" {{ $intent === 'promotions' ? 'selected' : '' }}>Promotions</option>
                                    <option value="general" {{ $intent === 'general' ? 'selected' : '' }}>General Info</option>
                                </select>
                            </div>
                            <div class="sm:col-span-3 flex gap-1">
                                <select name="converted" class="w-full px-2 py-1.5 rounded-xl bg-white border border-slate-300 text-xs text-slate-900 focus:outline-none focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20">
                                    <option value="">All Leads</option>
                                    <option value="1" {{ $converted === '1' ? 'selected' : '' }}>Converted Only</option>
                                    <option value="0" {{ $converted === '0' ? 'selected' : '' }}>Not Converted</option>
                                </select>
                                <button type="submit" class="px-3 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-xs font-semibold text-white transition shadow-xs">Go</button>
                            </div>
                        </form>
                    </div>

                    <!-- Conversation Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs text-slate-700">
                            <thead class="bg-slate-50 text-[10px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200">
                                <tr>
                                    <th class="py-3 px-4">Visitor / Contact</th>
                                    <th class="py-3 px-4">Intent</th>
                                    <th class="py-3 px-4">Lead Status</th>
                                    <th class="py-3 px-4">Messages</th>
                                    <th class="py-3 px-4">Date</th>
                                    <th class="py-3 px-4 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-medium">
                                @forelse($conversations as $conv)
                                    <tr class="hover:bg-slate-50/80 transition">
                                        <td class="py-3 px-4">
                                            @if($conv->customer_name)
                                                <span class="font-bold text-slate-900 block">{{ $conv->customer_name }}</span>
                                            @endif
                                            @if($conv->customer_phone)
                                                <span class="font-mono text-cyan-700 block text-[11px]">{{ $conv->customer_phone }}</span>
                                            @else
                                                <span class="font-mono text-slate-400 text-[10px]">{{ Str::limit($conv->session_id, 12) }}</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                                {{ ucfirst(str_replace('_', ' ', $conv->detected_intent ?? 'General')) }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-4">
                                            @if($conv->lead_converted)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                    <span>Converted Lead</span>
                                                </span>
                                            @else
                                                <span class="text-slate-500 text-[11px]">Chatting</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4">
                                            <span class="font-mono font-bold text-slate-700">{{ $conv->messages->count() }} msgs</span>
                                        </td>
                                        <td class="py-3 px-4 text-[11px] text-slate-500">
                                            {{ $conv->created_at->diffForHumans() }}
                                        </td>
                                        <td class="py-3 px-4 text-right">
                                            <div class="flex items-center justify-end gap-1.5">
                                                <a href="{{ route('showrooms.manage.ai.conversations.show', [$showroom, $conv]) }}"
                                                   class="p-1.5 rounded-lg text-slate-500 hover:text-slate-900 hover:bg-slate-100 transition" title="View Full Transcript">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                </a>
                                                <form method="POST" action="{{ route('showrooms.manage.ai.conversations.destroy', [$showroom, $conv]) }}"
                                                      onsubmit="return confirm('Delete this conversation history?');" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition" title="Delete conversation">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="p-8 text-center text-slate-500">
                                            No customer chat conversations recorded yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($conversations->hasPages())
                        <div class="p-4 border-t border-slate-200 bg-white">
                            {{ $conversations->links() }}
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </main>
    @include('showrooms.partials.manage-nav-close')
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const btnAddFaq = document.getElementById('btn-add-faq');
    const faqsContainer = document.getElementById('faqs-list-container');
    const noFaqsText = document.getElementById('no-faqs-text');

    if (btnAddFaq && faqsContainer) {
        btnAddFaq.addEventListener('click', function () {
            if (noFaqsText) noFaqsText.style.display = 'none';
            const index = faqsContainer.querySelectorAll('.faq-row').length;

            const row = document.createElement('div');
            row.className = 'faq-row p-3 rounded-xl bg-slate-50 border border-slate-200 space-y-2';
            row.innerHTML = `
                <div class="flex items-center justify-between gap-2">
                    <input type="text" name="faqs[${index}][question]" placeholder="Customer question (e.g. Do you accept trade-ins?)"
                           class="flex-1 px-2.5 py-1.5 rounded-lg bg-white border border-slate-300 text-xs text-slate-900 focus:outline-none focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20">
                    <button type="button" onclick="this.closest('.faq-row').remove()" class="text-rose-600 hover:text-rose-800 p-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <textarea name="faqs[${index}][answer]" rows="2" placeholder="Showroom answer provided by AI"
                          class="w-full px-2.5 py-1.5 rounded-lg bg-white border border-slate-300 text-xs text-slate-900 focus:outline-none focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20 leading-snug"></textarea>
            `;
            faqsContainer.appendChild(row);
        });
    }
});
</script>
@endpush
@endsection
