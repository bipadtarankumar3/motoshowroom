@extends('layouts.app')

@section('title', 'Conversation Transcript #' . $conversation->id . ' - ' . $showroom->name)

@section('content')
<div class="min-h-screen bg-slate-950 text-slate-100 flex flex-col font-sans selection:bg-rose-500 selection:text-white">
    @include('showrooms.partials.manage-nav')

    <main class="flex-1 max-w-4xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Top Header & Breadcrumbs -->
        <div class="flex items-center justify-between gap-4 mb-6">
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-400 mb-1">
                    <a href="{{ route('showrooms.manage', $showroom) }}" class="hover:text-white transition">Dashboard</a>
                    <span>/</span>
                    <a href="{{ route('showrooms.manage.ai.index', $showroom) }}" class="hover:text-white transition">AI Concierge</a>
                    <span>/</span>
                    <span class="text-rose-400">Transcript #{{ $conversation->id }}</span>
                </div>
                <h1 class="text-xl sm:text-2xl font-black font-display text-white tracking-tight">Conversation Transcript</h1>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('showrooms.manage.ai.index', $showroom) }}"
                   class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 border border-slate-800 text-xs font-semibold text-slate-300 hover:text-white transition">
                    &larr; Back to Conversations
                </a>
            </div>
        </div>

        <!-- Visitor Profile & Lead Conversion Card -->
        <div class="glass-panel-dark rounded-3xl border border-slate-800 p-6 shadow-xl mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-rose-500/15 border border-rose-500/30 flex items-center justify-center text-rose-400 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-white">{{ $conversation->customer_name ?? 'Anonymous Visitor' }}</h2>
                        <div class="flex flex-wrap items-center gap-3 text-xs text-slate-400 mt-0.5">
                            @if($conversation->customer_phone)
                                <span class="font-mono text-cyan-400 font-bold">{{ $conversation->customer_phone }}</span>
                            @endif
                            @if($conversation->customer_email)
                                <span>{{ $conversation->customer_email }}</span>
                            @endif
                            <span class="font-mono text-slate-500">Session: {{ $conversation->session_id }}</span>
                        </div>
                    </div>
                </div>

                <div>
                    @if($conversation->lead_converted && $conversation->enquiry)
                        <a href="{{ route('showrooms.manage.enquiries.show', [$showroom, $conversation->enquiry]) }}"
                           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-500/15 border border-emerald-500/30 text-emerald-400 font-bold text-xs hover:bg-emerald-500/25 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Enquiry #{{ $conversation->enquiry->enquiry_number }}</span>
                        </a>
                    @else
                        <span class="px-3 py-1.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-400 text-xs font-semibold">
                            Intent: {{ ucfirst(str_replace('_', ' ', $conversation->detected_intent ?? 'General')) }}
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Chat Timeline -->
        <div class="glass-panel-dark rounded-3xl border border-slate-800 p-6 shadow-xl space-y-5">
            <h3 class="text-sm font-bold uppercase tracking-wider text-slate-400 pb-3 border-b border-slate-800 flex items-center justify-between">
                <span>Message Timeline ({{ $conversation->messages->count() }} messages)</span>
                <span class="text-xs text-slate-500">{{ $conversation->created_at->format('M d, Y h:i A') }}</span>
            </h3>

            <div class="space-y-4 pt-2">
                @forelse($conversation->messages as $msg)
                    @if($msg->sender === 'user')
                        <div class="flex items-start justify-end gap-3">
                            <div class="max-w-[80%] rounded-2xl rounded-tr-sm p-4 bg-gradient-to-r from-rose-600 to-rose-500 text-white shadow-md">
                                <div class="text-[10px] text-rose-200 uppercase font-bold tracking-wider mb-1 flex items-center justify-between gap-4">
                                    <span>Visitor</span>
                                    <span>{{ $msg->created_at->format('h:i:s A') }}</span>
                                </div>
                                <p class="text-xs leading-relaxed whitespace-pre-wrap">{{ $msg->message }}</p>
                            </div>
                        </div>
                    @else
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-xl bg-rose-600/20 border border-rose-500/30 flex items-center justify-center shrink-0 text-rose-400 mt-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            </div>
                            <div class="max-w-[80%] rounded-2xl rounded-tl-sm p-4 bg-slate-900 border border-slate-800 text-slate-200 shadow-md">
                                <div class="text-[10px] text-cyan-400 uppercase font-bold tracking-wider mb-1 flex items-center justify-between gap-4">
                                    <span>AI Concierge</span>
                                    <span class="text-slate-500 font-mono">{{ $msg->created_at->format('h:i:s A') }}</span>
                                </div>
                                <p class="text-xs leading-relaxed whitespace-pre-wrap">{{ $msg->message }}</p>

                                @if($msg->structured_payload)
                                    <div class="mt-3 pt-3 border-t border-slate-800">
                                        <span class="text-[10px] text-slate-400 uppercase font-bold block mb-1">Payload: {{ $msg->structured_payload['type'] ?? 'Structured Data' }}</span>
                                        <pre class="p-2.5 rounded-xl bg-slate-950 border border-slate-800 text-[11px] font-mono text-slate-300 overflow-x-auto">{{ json_encode($msg->structured_payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                @empty
                    <p class="text-xs text-slate-500 text-center py-4">No messages recorded in this conversation.</p>
                @endforelse
            </div>
        </div>

    </main>
</div>
@endsection
