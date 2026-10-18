@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-slate-950 text-slate-100 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        <!-- Breadcrumbs -->
        <div class="flex items-center gap-2 text-xs text-slate-400">
            <a href="{{ route('showrooms.manage', $showroom) }}" class="hover:text-white transition">Dashboard</a>
            <span>/</span>
            <span class="text-slate-200">Email Notification Templates</span>
        </div>

        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-black font-display text-white tracking-tight">Transactional Email System</h1>
                <p class="text-sm text-slate-400 mt-1">Branded HTML email notifications for customer enquiries, workshop tracking, payment receipts, and plan alerts.</p>
            </div>

            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-semibold">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    <span>{{ count($templates) }} Templates Active</span>
                </span>
            </div>
        </div>

        <!-- Grid of Email Notification Templates -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($templates as $tpl)
                <div class="glass-panel-dark rounded-3xl p-6 border border-slate-800 shadow-xl flex flex-col justify-between space-y-6 hover:border-slate-700 transition">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider
                                @if($tpl['color'] === 'rose') bg-rose-500/10 text-rose-400 border border-rose-500/20
                                @elseif($tpl['color'] === 'amber') bg-amber-500/10 text-amber-400 border border-amber-500/20
                                @elseif($tpl['color'] === 'emerald') bg-emerald-500/10 text-emerald-400 border border-emerald-500/20
                                @else bg-sky-500/10 text-sky-400 border border-sky-500/20
                                @endif">
                                {{ $tpl['badge'] }}
                            </span>
                            <span class="text-[11px] font-mono text-slate-500">{{ $tpl['id'] }}</span>
                        </div>

                        <h3 class="text-base font-bold text-white font-display">
                            {{ $tpl['title'] }}
                        </h3>

                        <p class="text-xs text-slate-400 leading-relaxed">
                            {{ $tpl['description'] }}
                        </p>
                    </div>

                    <div class="pt-4 border-t border-slate-800/80 flex items-center justify-between">
                        <span class="text-[11px] text-slate-500">Mailable Class</span>
                        <a href="{{ route('showrooms.manage.email-previews.show', ['showroom' => $showroom, 'type' => $tpl['id']]) }}"
                           target="_blank"
                           class="px-3.5 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-800 border border-slate-700 text-xs font-semibold text-rose-400 hover:text-rose-300 transition flex items-center gap-1.5 shadow-sm">
                            <span>Open HTML Preview</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

    </div>
</div>
@endsection
