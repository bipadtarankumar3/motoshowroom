@extends('layouts.app')

@section('title', 'Edit Campaign - ' . $campaign->name)

@section('content')
<div class="min-h-screen bg-slate-50 text-slate-900 flex flex-col">
    @include('showrooms.partials.manage-nav')

    <main class="flex-1 max-w-4xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Breadcrumbs -->
        <div class="flex items-center gap-2 text-xs font-medium text-slate-500 mb-2">
            <a href="{{ route('showrooms.manage', $showroom) }}" class="hover:text-slate-900 transition">Dashboard</a>
            <span>/</span>
            <a href="{{ route('showrooms.manage.campaigns.index', $showroom) }}" class="hover:text-slate-900 transition">Campaigns</a>
            <span>/</span>
            <span class="text-slate-900 font-semibold truncate max-w-sm">Edit: {{ $campaign->name }}</span>
        </div>

        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-2xl sm:text-3xl font-black font-display text-slate-900 tracking-tight">Edit Campaign</h1>
                <p class="text-sm text-slate-500 mt-1">Update campaign details, date schedule, or status.</p>
            </div>
            <a href="{{ route('showrooms.promotions.show', [$showroom, $campaign]) }}" target="_blank" class="px-4 py-2 rounded-xl bg-white hover:bg-slate-50 border border-slate-200 text-xs font-bold text-slate-700 hover:text-slate-900 shadow-xs transition flex items-center gap-1.5">
                <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                <span>View Public Page</span>
            </a>
        </div>

        <div class="rounded-3xl bg-white border border-slate-200 p-6 sm:p-8 shadow-xs">
            <form method="POST" action="{{ route('showrooms.manage.campaigns.update', [$showroom, $campaign]) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label for="name" class="block text-xs font-semibold text-slate-700 mb-1">Campaign Name <span class="text-purple-600">*</span></label>
                    <input type="text"
                           id="name"
                           name="name"
                           value="{{ old('name', $campaign->name) }}"
                           required
                           class="w-full bg-white border border-slate-300 rounded-xl px-4 py-2.5 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-500/20">
                    @error('name')
                        <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description" class="block text-xs font-semibold text-slate-700 mb-1">Description</label>
                    <textarea id="description"
                              name="description"
                              rows="3"
                              class="w-full bg-white border border-slate-300 rounded-xl p-3 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-500/20">{{ old('description', $campaign->description) }}</textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                    <div>
                        <label for="start_date" class="block text-xs font-semibold text-slate-700 mb-1">Start Date <span class="text-purple-600">*</span></label>
                        <input type="date"
                               id="start_date"
                               name="start_date"
                               value="{{ old('start_date', $campaign->start_date->format('Y-m-d')) }}"
                               required
                               class="w-full bg-white border border-slate-300 rounded-xl px-4 py-2.5 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-500/20">
                    </div>

                    <div>
                        <label for="end_date" class="block text-xs font-semibold text-slate-700 mb-1">End Date <span class="text-purple-600">*</span></label>
                        <input type="date"
                               id="end_date"
                               name="end_date"
                               value="{{ old('end_date', $campaign->end_date->format('Y-m-d')) }}"
                               required
                               class="w-full bg-white border border-slate-300 rounded-xl px-4 py-2.5 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-500/20">
                    </div>

                    <div>
                        <label for="status" class="block text-xs font-semibold text-slate-700 mb-1">Campaign Status <span class="text-purple-600">*</span></label>
                        <select id="status" name="status" class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2.5 text-xs text-slate-900 focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-500/20">
                            @foreach($statuses as $st)
                                <option value="{{ $st->value }}" @selected(old('status', $campaign->status->value) === $st->value)>{{ $st->label() }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="max_participations_per_customer" class="block text-xs font-semibold text-slate-700 mb-1">Max Cards / Rider <span class="text-purple-600">*</span></label>
                        <input type="number"
                               id="max_participations_per_customer"
                               name="max_participations_per_customer"
                               value="{{ old('max_participations_per_customer', $campaign->max_participations_per_customer) }}"
                               min="1"
                               max="20"
                               required
                               class="w-full bg-white border border-slate-300 rounded-xl px-4 py-2.5 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-500/20">
                    </div>
                </div>

                <div>
                    <label for="terms_and_conditions" class="block text-xs font-semibold text-slate-700 mb-1">Terms & Conditions</label>
                    <textarea id="terms_and_conditions"
                              name="terms_and_conditions"
                              rows="3"
                              class="w-full bg-white border border-slate-300 rounded-xl p-3 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-500/20">{{ old('terms_and_conditions', $campaign->terms_and_conditions) }}</textarea>
                </div>

                <!-- Existing Prize Pool Summary -->
                <div>
                    <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider mb-2">Configured Prize Tiers ({{ $campaign->prizes->count() }})</h4>
                    <div class="space-y-2">
                        @foreach($campaign->prizes as $prize)
                            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between text-xs">
                                <div>
                                    <span class="font-bold text-slate-900">{{ $prize->title }}</span>
                                    <span class="text-[10px] text-slate-500 block">Type: {{ $prize->prize_type->label() }}</span>
                                </div>
                                <div class="text-right">
                                    <span class="text-emerald-700 font-bold">{{ $prize->remaining_quantity }} / {{ $prize->total_quantity }} left</span>
                                    <span class="text-[10px] text-slate-500 block">Weight: {{ $prize->probability_weight }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-6 border-t border-slate-200 flex items-center justify-end gap-3">
                    <a href="{{ route('showrooms.manage.campaigns.index', $showroom) }}"
                       class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 text-xs font-bold transition">
                        Cancel
                    </a>
                    <button type="submit"
                            id="btn-update-campaign"
                            class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-bold text-xs uppercase tracking-wider shadow-sm transition">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>

    </main>
</div>
@endsection
