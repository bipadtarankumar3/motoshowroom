@php
    $aiAvailable = $showroom->canAccessFeature('has_ai_assistant');
    $aiSetting = $showroom->getOrCreateAiSetting();
@endphp

@if($aiAvailable && $aiSetting->is_enabled)
<div id="ai-assistant-root" class="fixed bottom-6 right-6 z-50 font-sans" style="font-family: inherit;">

    <!-- Floating Chat Launcher Button -->
    <button type="button" id="ai-launcher-btn"
            class="group relative flex items-center gap-3 px-4 py-3.5 rounded-2xl bg-gradient-to-r from-rose-600 to-rose-500 hover:from-rose-500 hover:to-rose-400 text-white shadow-2xl shadow-rose-600/40 hover:scale-105 transition-all duration-300 focus:outline-none">
        <!-- Bot Avatar with Pulse Ring -->
        <div class="relative w-7 h-7 rounded-xl bg-white/20 flex items-center justify-center shrink-0">
            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            <span class="absolute -top-1 -right-1 w-2.5 h-2.5 rounded-full bg-emerald-400 ring-2 ring-slate-950 animate-pulse"></span>
        </div>
        <div class="text-left hidden sm:block">
            <span class="text-xs font-black tracking-tight block leading-tight">{{ $aiSetting->bot_name }}</span>
            <span class="text-[10px] text-white/80 font-medium block">Ask me anything &bull; Online</span>
        </div>
    </button>

    <!-- Chat Dialog Box Container -->
    <div id="ai-chat-dialog"
         class="hidden fixed bottom-6 right-4 sm:right-6 w-[calc(100vw-2rem)] sm:w-[420px] max-h-[640px] h-[85vh] rounded-3xl bg-slate-950/95 border border-slate-800 shadow-2xl flex flex-col overflow-hidden backdrop-blur-xl z-50 transition-all duration-300 transform scale-95 opacity-0">

        <!-- Chat Header -->
        <div class="px-5 py-4 border-b border-slate-800/80 bg-slate-900/80 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-rose-600 to-rose-400 flex items-center justify-center shadow-lg shadow-rose-600/30">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-black text-white leading-tight block">{{ $aiSetting->bot_name }}</span>
                        <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">Live AI</span>
                    </div>
                    <span class="text-[11px] text-slate-400">{{ $showroom->name }} Concierge</span>
                </div>
            </div>

            <div class="flex items-center gap-1.5">
                <button type="button" id="ai-reset-btn" class="p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition" title="Start New Conversation">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                </button>
                <button type="button" id="ai-close-btn" class="p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition" title="Minimize Chat">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        <!-- Chat Messages Container -->
        <div id="ai-messages-container" class="flex-1 p-4 overflow-y-auto space-y-4 text-xs scroll-smooth">
            <!-- Welcome message -->
            <div class="flex items-start gap-2.5">
                <div class="w-7 h-7 rounded-lg bg-rose-600/20 border border-rose-500/30 flex items-center justify-center shrink-0 text-rose-400 mt-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <div class="max-w-[85%] rounded-2xl rounded-tl-sm p-3.5 bg-slate-900 border border-slate-800 text-slate-200 leading-relaxed shadow-sm">
                    {{ $aiSetting->welcome_message_or_default }}
                </div>
            </div>

            <!-- Quick Suggestions Prompt Chips -->
            @if(!empty($aiSetting->suggested_prompts))
                <div id="ai-prompt-chips" class="pt-2 flex flex-wrap gap-1.5">
                    @foreach($aiSetting->suggested_prompts as $prompt)
                        <button type="button" class="ai-chip px-3 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-800 border border-slate-800 text-slate-300 hover:text-white text-[11px] font-medium transition text-left">
                            {{ $prompt }}
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Typing Indicator -->
        <div id="ai-typing-indicator" class="hidden px-5 py-2 flex items-center gap-2 text-[11px] text-slate-400 bg-slate-950/60 border-t border-slate-900">
            <span class="w-2 h-2 rounded-full bg-cyan-400 animate-ping"></span>
            <span>{{ $aiSetting->bot_name }} is thinking...</span>
        </div>

        <!-- Input Bar -->
        <form id="ai-chat-form" class="p-3 bg-slate-900/90 border-t border-slate-800 flex items-center gap-2 shrink-0">
            <input type="text" id="ai-chat-input"
                   placeholder="Ask about bikes, specs, test rides, or timings..."
                   autocomplete="off"
                   class="flex-1 px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-rose-500 transition">

            <button type="submit" id="ai-send-btn"
                    class="p-2.5 rounded-xl bg-gradient-to-r from-rose-600 to-rose-500 hover:from-rose-500 hover:to-rose-400 text-white shadow-lg shadow-rose-600/30 transition shrink-0 focus:outline-none" title="Send Message">
                <svg class="w-4 h-4 transform rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
            </button>
        </form>
    </div>

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const launcherBtn = document.getElementById('ai-launcher-btn');
    const dialog = document.getElementById('ai-chat-dialog');
    const closeBtn = document.getElementById('ai-close-btn');
    const resetBtn = document.getElementById('ai-reset-btn');
    const chatForm = document.getElementById('ai-chat-form');
    const chatInput = document.getElementById('ai-chat-input');
    const messagesContainer = document.getElementById('ai-messages-container');
    const typingIndicator = document.getElementById('ai-typing-indicator');

    if (!launcherBtn || !dialog) return;

    // Persistent session ID per showroom
    const storageKey = 'motoshowroom_ai_session_{{ $showroom->id }}';
    let sessionId = localStorage.getItem(storageKey);
    if (!sessionId) {
        sessionId = 'sess_' + Math.random().toString(36).substring(2, 15) + Date.now().toString(36);
        localStorage.setItem(storageKey, sessionId);
    }

    function openChat() {
        dialog.classList.remove('hidden');
        setTimeout(() => {
            dialog.classList.remove('scale-95', 'opacity-0');
            dialog.classList.add('scale-100', 'opacity-100');
            chatInput.focus();
        }, 10);
    }

    function closeChat() {
        dialog.classList.remove('scale-100', 'opacity-100');
        dialog.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            dialog.classList.add('hidden');
        }, 200);
    }

    launcherBtn.addEventListener('click', function () {
        if (dialog.classList.contains('hidden')) openChat();
        else closeChat();
    });

    closeBtn.addEventListener('click', closeChat);

    resetBtn.addEventListener('click', function () {
        sessionId = 'sess_' + Math.random().toString(36).substring(2, 15) + Date.now().toString(36);
        localStorage.setItem(storageKey, sessionId);
        // Reset messages to welcome
        messagesContainer.innerHTML = `
            <div class="flex items-start gap-2.5">
                <div class="w-7 h-7 rounded-lg bg-rose-600/20 border border-rose-500/30 flex items-center justify-center shrink-0 text-rose-400 mt-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <div class="max-w-[85%] rounded-2xl rounded-tl-sm p-3.5 bg-slate-900 border border-slate-800 text-slate-200 leading-relaxed shadow-sm">
                    {{ addslashes($aiSetting->welcome_message_or_default) }}
                </div>
            </div>
        `;
    });

    // Quick Prompt Chips
    document.addEventListener('click', function (e) {
        if (e.target && e.target.classList.contains('ai-chip')) {
            const promptText = e.target.textContent.trim();
            chatInput.value = promptText;
            chatForm.dispatchEvent(new Event('submit'));
        }
    });

    function appendUserMessage(text) {
        const msgDiv = document.createElement('div');
        msgDiv.className = 'flex items-start justify-end gap-2.5';
        msgDiv.innerHTML = `
            <div class="max-w-[85%] rounded-2xl rounded-tr-sm p-3.5 bg-gradient-to-r from-rose-600 to-rose-500 text-white leading-relaxed shadow-md">
                ${escapeHtml(text)}
            </div>
        `;
        messagesContainer.appendChild(msgDiv);
        scrollToBottom();
    }

    function appendBotMessage(text, structuredPayload) {
        const msgDiv = document.createElement('div');
        msgDiv.className = 'flex items-start gap-2.5';

        let extraHtml = '';
        if (structuredPayload && structuredPayload.type === 'bike_recommendations' && structuredPayload.bikes) {
            extraHtml = '<div class="mt-3 space-y-2">';
            structuredPayload.bikes.forEach(b => {
                extraHtml += `
                    <div class="p-2.5 rounded-xl bg-slate-950 border border-slate-800 flex items-center gap-3">
                        <img src="${b.image_url}" alt="${escapeHtml(b.name)}" class="w-16 h-12 rounded-lg object-cover bg-slate-900 shrink-0">
                        <div class="flex-1 min-w-0">
                            <span class="text-[10px] text-slate-400 uppercase font-bold tracking-wider">${escapeHtml(b.brand)} &bull; ${escapeHtml(b.category)}</span>
                            <h5 class="text-xs font-bold text-white truncate">${escapeHtml(b.name)}</h5>
                            <span class="font-mono text-cyan-300 font-bold text-xs">${b.price}</span>
                        </div>
                        <div class="shrink-0 flex flex-col gap-1">
                            <a href="${b.url}" class="px-2 py-1 rounded-lg bg-slate-900 hover:bg-slate-800 border border-slate-800 text-[10px] font-semibold text-white text-center transition">View</a>
                            <a href="${b.test_ride_url}" class="px-2 py-1 rounded-lg bg-rose-600 hover:bg-rose-500 text-[10px] font-bold text-white text-center transition">Test Ride</a>
                        </div>
                    </div>
                `;
            });
            extraHtml += '</div>';
        } else if (structuredPayload && structuredPayload.type === 'test_ride_prompt' && structuredPayload.booking_url) {
            extraHtml = `
                <div class="mt-3">
                    <a href="${structuredPayload.booking_url}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs transition shadow-md shadow-rose-600/30">
                        <span>Schedule Your Test Ride</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>
            `;
        } else if (structuredPayload && structuredPayload.type === 'service_options') {
            extraHtml = `
                <div class="mt-3 flex items-center gap-2">
                    <a href="${structuredPayload.book_url}" class="px-3 py-1.5 rounded-xl bg-slate-950 border border-slate-800 text-rose-400 hover:text-white hover:bg-slate-800 font-semibold text-[11px] transition">Book Service</a>
                    <a href="${structuredPayload.track_url}" class="px-3 py-1.5 rounded-xl bg-slate-950 border border-slate-800 text-slate-300 hover:text-white hover:bg-slate-800 font-semibold text-[11px] transition">Track Repair</a>
                </div>
            `;
        } else if (structuredPayload && structuredPayload.type === 'promotional_campaigns' && structuredPayload.campaigns) {
            extraHtml = '<div class="mt-2.5 flex flex-wrap gap-1.5">';
            structuredPayload.campaigns.forEach(c => {
                extraHtml += `
                    <a href="${c.url}" class="px-3 py-1.5 rounded-xl bg-purple-500/15 border border-purple-500/30 text-purple-300 hover:text-white font-bold text-[11px] transition flex items-center gap-1">
                        <span>🎁 Play ${escapeHtml(c.name)}</span>
                    </a>
                `;
            });
            extraHtml += '</div>';
        }

        msgDiv.innerHTML = `
            <div class="w-7 h-7 rounded-lg bg-rose-600/20 border border-rose-500/30 flex items-center justify-center shrink-0 text-rose-400 mt-0.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </div>
            <div class="max-w-[85%] rounded-2xl rounded-tl-sm p-3.5 bg-slate-900 border border-slate-800 text-slate-200 leading-relaxed shadow-sm">
                ${escapeHtml(text)}
                ${extraHtml}
            </div>
        `;
        messagesContainer.appendChild(msgDiv);
        scrollToBottom();
    }

    function scrollToBottom() {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    function escapeHtml(string) {
        return String(string).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    // Form Submission
    chatForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const text = chatInput.value.trim();
        if (!text) return;

        appendUserMessage(text);
        chatInput.value = '';
        typingIndicator.classList.remove('hidden');

        fetch('{{ route('showrooms.ai.chat', $showroom) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                message: text,
                session_id: sessionId
            })
        })
        .then(res => res.json())
        .then(data => {
            typingIndicator.classList.add('hidden');
            if (data.success) {
                appendBotMessage(data.reply, data.structured_payload);
            } else {
                appendBotMessage(data.message || 'Something went wrong. Please try again.');
            }
        })
        .catch(err => {
            typingIndicator.classList.add('hidden');
            appendBotMessage('I encountered a connection hiccup. Please try asking again.');
        });
    });
});
</script>
@endpush
@endif
