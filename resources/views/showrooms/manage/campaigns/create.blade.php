@extends('layouts.app')

@section('title', 'Create Promotional Campaign - ' . $showroom->name)

@section('content')
<div class="min-h-screen bg-slate-50 text-slate-900 flex flex-col">
    @include('showrooms.partials.manage-nav')

    <main class="flex-1 w-full px-4 sm:px-6 lg:px-8 xl:px-10 py-8 space-y-6">

        <!-- Breadcrumbs -->
        <div class="flex items-center gap-2 text-xs font-medium text-slate-500 mb-2">
            <a href="{{ route('showrooms.manage', $showroom) }}" class="hover:text-slate-900 transition">Dashboard</a>
            <span>/</span>
            <a href="{{ route('showrooms.manage.campaigns.index', $showroom) }}" class="hover:text-slate-900 transition">Campaigns</a>
            <span>/</span>
            <span class="text-slate-700 font-semibold">New Campaign</span>
        </div>

        <div class="mb-8">
            <h1 class="text-2xl sm:text-3xl font-black font-display text-slate-900 tracking-tight">Create Promotional Campaign</h1>
            <p class="text-sm text-slate-500 mt-1">Configure digital scratch cards, set probability weights, and allocate prize quotas.</p>
        </div>

        <div class="rounded-3xl bg-white border border-slate-200 p-6 sm:p-8 shadow-xs">
            <form method="POST" action="{{ route('showrooms.manage.campaigns.store', $showroom) }}" class="space-y-8">
                @csrf

                <!-- Campaign Core Info -->
                <div class="space-y-4">
                    <h3 class="text-base font-bold text-slate-900 flex items-center gap-2 border-b border-slate-200 pb-2">
                        <span class="w-2 h-2 rounded-full bg-purple-600"></span>
                        1. Campaign Overview
                    </h3>

                    <div>
                        <label for="name" class="block text-xs font-semibold text-slate-700 mb-1">
                            Campaign Name <span class="text-purple-600">*</span>
                        </label>
                        <input type="text"
                               id="name"
                               name="name"
                               value="{{ old('name') }}"
                               required
                               placeholder="e.g. Diwali Superbike Festival Scratch & Win"
                               class="w-full bg-white border border-slate-300 rounded-xl px-4 py-2.5 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500">
                        @error('name')
                            <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="description" class="block text-xs font-semibold text-slate-700 mb-1">Marketing Tagline / Description</label>
                        <textarea id="description"
                                  name="description"
                                  rows="2"
                                  placeholder="Describe the occasion, eligible motorcycle models, or showroom benefits..."
                                  class="w-full bg-white border border-slate-300 rounded-xl p-3 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500">{{ old('description') }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                        <div>
                            <label for="start_date" class="block text-xs font-semibold text-slate-700 mb-1">Start Date <span class="text-purple-600">*</span></label>
                            <input type="date"
                                   id="start_date"
                                   name="start_date"
                                   value="{{ old('start_date', date('Y-m-d')) }}"
                                   required
                                   class="w-full bg-white border border-slate-300 rounded-xl px-4 py-2.5 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500">
                            @error('start_date')
                                <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="end_date" class="block text-xs font-semibold text-slate-700 mb-1">End Date <span class="text-purple-600">*</span></label>
                            <input type="date"
                                   id="end_date"
                                   name="end_date"
                                   value="{{ old('end_date', date('Y-m-d', strtotime('+30 days'))) }}"
                                   required
                                   class="w-full bg-white border border-slate-300 rounded-xl px-4 py-2.5 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500">
                            @error('end_date')
                                <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="status" class="block text-xs font-semibold text-slate-700 mb-1">Initial Status <span class="text-purple-600">*</span></label>
                            <select id="status" name="status" class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2.5 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500">
                                <option value="active" @selected(old('status', 'active') === 'active')>Active & Live</option>
                                <option value="draft" @selected(old('status') === 'draft')>Draft (Hidden)</option>
                                <option value="paused" @selected(old('status') === 'paused')>Paused</option>
                            </select>
                        </div>

                        <div>
                            <label for="applicable_to" class="block text-xs font-semibold text-slate-700 mb-1">Target Audience <span class="text-purple-600">*</span></label>
                            <select id="applicable_to" name="applicable_to" class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2.5 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500">
                                <option value="all" @selected(old('applicable_to', 'all') === 'all')>All Bookings & Visitors</option>
                                <option value="service" @selected(old('applicable_to') === 'service')>Service & Repair Bookings</option>
                                <option value="bike" @selected(old('applicable_to') === 'bike')>Bike Purchase & Test Rides</option>
                            </select>
                        </div>

                        <div>
                            <label for="max_participations_per_customer" class="block text-xs font-semibold text-slate-700 mb-1">Max Cards / Rider <span class="text-purple-600">*</span></label>
                            <input type="number"
                                   id="max_participations_per_customer"
                                   name="max_participations_per_customer"
                                   value="{{ old('max_participations_per_customer', 1) }}"
                                   min="1"
                                   max="20"
                                   required
                                   class="w-full bg-white border border-slate-300 rounded-xl px-4 py-2.5 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500">
                        </div>
                    </div>
                </div>

                <!-- Prize Pool Configuration -->
                <div class="space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-slate-200 pb-3 gap-2">
                        <div>
                            <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span>
                                2. Prize Tiers & Probabilities
                            </h3>
                            <p class="text-xs text-slate-500 mt-0.5">Total available quantity is enforced server-side with zero overselling.</p>
                        </div>
                        <button type="button"
                                id="btn-add-prize"
                                class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold shadow-xs transition self-start sm:self-auto cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span>Add Prize Tier</span>
                        </button>
                    </div>

                    @error('prizes')
                        <p class="text-[11px] text-rose-600">{{ $message }}</p>
                    @enderror

                    @php
                        $initialPrizes = old('prizes', [
                            [
                                'title' => 'Free Premium Riding Gloves',
                                'prize_type' => 'free_accessory',
                                'total_quantity' => 10,
                                'probability_weight' => 15,
                            ],
                            [
                                'title' => 'Flat ₹500 Off Workshop General Service',
                                'prize_type' => 'service_discount',
                                'total_quantity' => 50,
                                'probability_weight' => 35,
                            ],
                            [
                                'title' => 'Better Luck Next Time',
                                'prize_type' => 'better_luck',
                                'total_quantity' => 999,
                                'probability_weight' => 50,
                            ],
                        ]);
                    @endphp

                    <div id="prize-rows-container" class="space-y-3">
                        @foreach($initialPrizes as $index => $prize)
                            <div class="prize-row p-4 rounded-2xl bg-slate-50 border border-slate-200 grid grid-cols-1 sm:grid-cols-12 gap-3 items-center transition hover:border-slate-300">
                                <div class="sm:col-span-4">
                                    <label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">Prize Title <span class="text-rose-500">*</span></label>
                                    <input type="text"
                                           name="prizes[{{ $index }}][title]"
                                           value="{{ $prize['title'] ?? '' }}"
                                           required
                                           placeholder="e.g. Free Riding Helmet"
                                           class="w-full bg-white border border-slate-300 rounded-xl px-3 py-1.5 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500">
                                </div>
                                <div class="sm:col-span-3">
                                    <label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">Type <span class="text-rose-500">*</span></label>
                                    <select name="prizes[{{ $index }}][prize_type]" class="w-full bg-white border border-slate-300 rounded-xl px-2 py-1.5 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500">
                                        @foreach($prizeTypes as $pt)
                                            <option value="{{ $pt->value }}" @selected(($prize['prize_type'] ?? '') === $pt->value)>{{ $pt->label() }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">Quantity <span class="text-rose-500">*</span></label>
                                    <input type="number"
                                           name="prizes[{{ $index }}][total_quantity]"
                                           value="{{ $prize['total_quantity'] ?? 10 }}"
                                           min="0"
                                           required
                                           class="w-full bg-white border border-slate-300 rounded-xl px-3 py-1.5 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500">
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">Weight <span class="text-rose-500">*</span></label>
                                    <input type="number"
                                           name="prizes[{{ $index }}][probability_weight]"
                                           value="{{ $prize['probability_weight'] ?? 10 }}"
                                           min="1"
                                           max="100"
                                           required
                                           class="w-full bg-white border border-slate-300 rounded-xl px-3 py-1.5 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500">
                                </div>
                                <div class="sm:col-span-1 text-center pt-2 sm:pt-4">
                                    <button type="button"
                                            class="btn-remove-prize inline-flex items-center justify-center w-8 h-8 rounded-xl text-slate-400 hover:text-rose-600 hover:bg-rose-50 border border-transparent hover:border-rose-200 transition cursor-pointer"
                                            title="Remove this prize tier">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Add More Button at Bottom -->
                    <button type="button"
                            id="btn-add-prize-bottom"
                            class="w-full py-3 border-2 border-dashed border-slate-300 hover:border-purple-500 rounded-2xl text-slate-600 hover:text-purple-700 bg-white hover:bg-purple-50/50 flex items-center justify-center gap-2 text-xs font-bold transition cursor-pointer shadow-2xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>+ Add More Prize Tier</span>
                    </button>
                </div>

                <!-- Terms and Conditions -->
                <div class="space-y-4">
                    <h3 class="text-base font-bold text-slate-900 flex items-center gap-2 border-b border-slate-200 pb-2">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                        3. Terms, Restrictions & Rules
                    </h3>

                    <div>
                        <label for="terms_and_conditions" class="block text-xs font-semibold text-slate-700 mb-1">Terms & Conditions</label>
                        <textarea id="terms_and_conditions"
                                  name="terms_and_conditions"
                                  rows="3"
                                  placeholder="e.g. Valid only for redemption in physical showroom workshop. Cannot be exchanged for cash. One coupon per invoice."
                                  class="w-full bg-white border border-slate-300 rounded-xl p-3 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500">{{ old('terms_and_conditions') }}</textarea>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-6 border-t border-slate-200 flex items-center justify-end gap-3">
                    <a href="{{ route('showrooms.manage.campaigns.index', $showroom) }}"
                       class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 text-xs font-bold transition">
                        Cancel
                    </a>
                    <button type="submit"
                            id="btn-save-campaign"
                            class="btn-brand-primary px-6 py-2.5 rounded-xl font-bold text-xs uppercase tracking-wider shadow-md transition cursor-pointer">
                        Publish Campaign
                    </button>
                </div>
            </form>
        </div>

    </main>

    <!-- Hidden Row Template for Dynamic Addition -->
    <template id="prize-row-template">
        <div class="prize-row p-4 rounded-2xl bg-slate-50 border border-slate-200 grid grid-cols-1 sm:grid-cols-12 gap-3 items-center transition hover:border-slate-300">
            <div class="sm:col-span-4">
                <label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">Prize Title <span class="text-rose-500">*</span></label>
                <input type="text"
                       name="prizes[__INDEX__][title]"
                       value=""
                       required
                       placeholder="e.g. Free Riding Helmet"
                       class="w-full bg-white border border-slate-300 rounded-xl px-3 py-1.5 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500">
            </div>
            <div class="sm:col-span-3">
                <label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">Type <span class="text-rose-500">*</span></label>
                <select name="prizes[__INDEX__][prize_type]" class="w-full bg-white border border-slate-300 rounded-xl px-2 py-1.5 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500">
                    @foreach($prizeTypes as $pt)
                        <option value="{{ $pt->value }}">{{ $pt->label() }}</option>
                    @endforeach
                </select>
            </div>
            <div class="sm:col-span-2">
                <label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">Quantity <span class="text-rose-500">*</span></label>
                <input type="number"
                       name="prizes[__INDEX__][total_quantity]"
                       value="10"
                       min="0"
                       required
                       class="w-full bg-white border border-slate-300 rounded-xl px-3 py-1.5 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500">
            </div>
            <div class="sm:col-span-2">
                <label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">Weight <span class="text-rose-500">*</span></label>
                <input type="number"
                       name="prizes[__INDEX__][probability_weight]"
                       value="10"
                       min="1"
                       max="100"
                       required
                       class="w-full bg-white border border-slate-300 rounded-xl px-3 py-1.5 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500">
            </div>
            <div class="sm:col-span-1 text-center pt-2 sm:pt-4">
                <button type="button"
                        class="btn-remove-prize inline-flex items-center justify-center w-8 h-8 rounded-xl text-slate-400 hover:text-rose-600 hover:bg-rose-50 border border-transparent hover:border-rose-200 transition cursor-pointer"
                        title="Remove this prize tier">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            </div>
        </div>
    </template>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('prize-rows-container');
    const template = document.getElementById('prize-row-template');
    let nextIndex = {{ count($initialPrizes) }};

    function addPrizeRow() {
        if (!template || !container) return;

        const html = template.innerHTML.replace(/__INDEX__/g, nextIndex++);
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = html.trim();
        const newRow = tempDiv.firstElementChild;

        newRow.style.opacity = '0';
        newRow.style.transform = 'translateY(-4px)';
        container.appendChild(newRow);

        requestAnimationFrame(() => {
            newRow.style.transition = 'all 0.2s ease-out';
            newRow.style.opacity = '1';
            newRow.style.transform = 'translateY(0)';
        });

        // Auto focus new prize title input
        const titleInput = newRow.querySelector('input[name*="[title]"]');
        if (titleInput) {
            titleInput.focus();
        }
    }

    document.getElementById('btn-add-prize')?.addEventListener('click', addPrizeRow);
    document.getElementById('btn-add-prize-bottom')?.addEventListener('click', addPrizeRow);

    container?.addEventListener('click', function (e) {
        const removeBtn = e.target.closest('.btn-remove-prize');
        if (!removeBtn) return;

        const rows = container.querySelectorAll('.prize-row');
        if (rows.length <= 1) {
            alert('A campaign must have at least one prize tier.');
            return;
        }

        const row = removeBtn.closest('.prize-row');
        if (row) {
            row.style.transition = 'all 0.15s ease-in';
            row.style.opacity = '0';
            row.style.transform = 'scale(0.98)';
            setTimeout(() => {
                row.remove();
            }, 150);
        }
    });
});
</script>
@endpush
