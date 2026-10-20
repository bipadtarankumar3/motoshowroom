<x-app-layout title="Subscription Plans & Special Feature Access - Super Admin | MotoShowroom SaaS">
    @include('admin.partials.nav')

    @php
        $showroomsPayload = $showrooms->map(function ($s) {
            $sub = $s->subscription;
            $plan = $sub?->plan;
            $overrides = $sub?->feature_overrides ?? $s->settings['feature_overrides'] ?? [];

            return [
                'id' => $s->id,
                'name' => $s->name,
                'slug' => $s->slug,
                'city' => $s->city ?? 'Unknown',
                'owner_name' => $s->ownerMembership?->user?->name ?? 'N/A',
                'owner_email' => $s->ownerMembership?->user?->email ?? $s->email,
                'plan_name' => $plan?->name ?? 'No Plan Assigned',
                'plan_id' => $plan?->id,
                'plan_features' => [
                    'has_careers_module' => (bool) ($plan?->has_careers_module ?? false),
                    'has_scratch_cards' => (bool) ($plan?->has_scratch_cards ?? false),
                    'has_360_viewer' => (bool) ($plan?->has_360_viewer ?? false),
                    'has_ai_assistant' => (bool) ($plan?->has_ai_assistant ?? false),
                    'has_custom_domain' => (bool) ($plan?->has_custom_domain ?? false),
                    'has_advanced_analytics' => (bool) ($plan?->has_advanced_analytics ?? false),
                ],
                'plan_limits' => [
                    'max_staff_accounts' => (int) ($plan?->max_staff_accounts ?? 5),
                    'max_bike_listings' => (int) ($plan?->max_bike_listings ?? 25),
                    'max_monthly_bookings' => (int) ($plan?->max_monthly_bookings ?? 100),
                    'media_storage_mb' => (int) ($plan?->media_storage_mb ?? 1024),
                ],
                'overrides' => $overrides,
                'has_any_override' => ! empty($overrides),
                'action_url' => route('admin.showrooms.feature-overrides', $s),
            ];
        });

        $showroomsWithOverrides = $showroomsPayload->filter(fn ($s) => $s['has_any_override']);
    @endphp

    <div class="bg-slate-50 min-h-screen py-8 px-4 sm:px-6 lg:px-8 xl:px-10"
         x-data="{
             showroomsData: {{ json_encode($showroomsPayload) }},
             overrideModalOpen: false,
             selectedShowroomId: null,
             selectedShowroom: null,
             showroomSearch: '',
             showroomDropdownOpen: false,
             activeOverrides: {
                 has_careers_module: 'inherit',
                 has_scratch_cards: 'inherit',
                 has_360_viewer: 'inherit',
                 has_ai_assistant: 'inherit',
                 has_custom_domain: 'inherit',
                 has_advanced_analytics: 'inherit'
             },
             customStaff: '',
             customBikes: '',
             customBookings: '',
             customStorage: '',
             actionUrl: '',
             grantNotes: '',

             get filteredShowrooms() {
                 if (!this.showroomSearch || this.showroomSearch.trim() === '') {
                     return this.showroomsData;
                 }
                 const q = this.showroomSearch.toLowerCase().trim();
                 return this.showroomsData.filter(item => {
                     return (item.name && item.name.toLowerCase().includes(q)) ||
                            (item.city && item.city.toLowerCase().includes(q)) ||
                            (item.owner_name && item.owner_name.toLowerCase().includes(q)) ||
                            (item.owner_email && item.owner_email.toLowerCase().includes(q)) ||
                            (item.plan_name && item.plan_name.toLowerCase().includes(q)) ||
                            (item.slug && item.slug.toLowerCase().includes(q));
                 });
             },

             openOverrideModal(showroomId = null) {
                 if (!showroomId && this.showroomsData.length > 0) {
                     showroomId = this.showroomsData[0].id;
                 }
                 this.selectShowroom(showroomId);
                 this.overrideModalOpen = true;
             },

             selectShowroom(id) {
                 this.selectedShowroomId = id;
                 const found = this.showroomsData.find(s => s.id == id);
                 if (found) {
                     this.selectedShowroom = found;
                     this.showroomSearch = found.name;
                     this.showroomDropdownOpen = false;
                     this.actionUrl = found.action_url;
                     const ov = found.overrides || {};
                     this.activeOverrides = {
                         has_careers_module: ov.has_careers_module === true ? 'grant' : (ov.has_careers_module === false ? 'deny' : 'inherit'),
                         has_scratch_cards: ov.has_scratch_cards === true ? 'grant' : (ov.has_scratch_cards === false ? 'deny' : 'inherit'),
                         has_360_viewer: ov.has_360_viewer === true ? 'grant' : (ov.has_360_viewer === false ? 'deny' : 'inherit'),
                         has_ai_assistant: ov.has_ai_assistant === true ? 'grant' : (ov.has_ai_assistant === false ? 'deny' : 'inherit'),
                         has_custom_domain: ov.has_custom_domain === true ? 'grant' : (ov.has_custom_domain === false ? 'deny' : 'inherit'),
                         has_advanced_analytics: ov.has_advanced_analytics === true ? 'grant' : (ov.has_advanced_analytics === false ? 'deny' : 'inherit'),
                     };
                     this.customStaff = ov.max_staff_accounts ?? '';
                     this.customBikes = ov.max_bike_listings ?? '';
                     this.customBookings = ov.max_monthly_bookings ?? '';
                     this.customStorage = ov.media_storage_mb ?? '';
                     this.grantNotes = '';
                 }
             }
         }">
        <div class="w-full space-y-8">

            <!-- Header -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-slate-200 pb-6">
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-3xl font-black font-display text-slate-900 tracking-tight">Subscription Plans</h1>
                        <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-rose-50 text-rose-700 border border-rose-200">
                            Commercial Tiers
                        </span>
                    </div>
                    <p class="text-sm text-slate-500 mt-1">Configure pricing, quotas, and feature flags. Grant extra special access to any user regardless of plan tier.</p>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <!-- Special Feature Access Grant Button -->
                    <button type="button"
                            @click="openOverrideModal()"
                            class="px-5 py-2.5 rounded-xl bg-white hover:bg-slate-50 border border-amber-300 text-amber-900 font-bold text-sm shadow-xs transition flex items-center gap-2 cursor-pointer"
                            id="btn-grant-extra-feature">
                        <svg class="w-4 h-4 text-amber-500" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        <span>Grant Extra Feature Access</span>
                    </button>

                    <a href="{{ route('admin.plans.create') }}" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-rose-600 to-rose-500 hover:from-rose-500 hover:to-rose-400 text-white font-bold text-sm shadow-xs transition flex items-center gap-2" id="btn-create-plan">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>Create New Plan</span>
                    </a>
                </div>
            </div>

            <!-- Plans Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @forelse($plans as $plan)
                    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs flex flex-col justify-between relative overflow-hidden group hover:border-slate-300 transition">
                        @if($plan->is_active)
                            <div class="absolute top-0 right-0 bg-emerald-600 text-white text-[10px] font-black uppercase tracking-wider px-3 py-1 rounded-bl-xl shadow-xs">
                                Active Tier
                            </div>
                        @else
                            <div class="absolute top-0 right-0 bg-slate-200 text-slate-600 text-[10px] font-black uppercase tracking-wider px-3 py-1 rounded-bl-xl shadow-xs">
                                Inactive
                            </div>
                        @endif

                        <div class="space-y-4">
                            <div>
                                <h3 class="text-xl font-black font-display text-slate-900">{{ $plan->name }}</h3>
                                <p class="text-xs text-slate-500 mt-1 min-h-[32px]">{{ $plan->description ?? 'All-in-one motorcycle showroom management tier.' }}</p>
                            </div>

                            <!-- Pricing -->
                            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 flex items-baseline justify-between">
                                <div>
                                    <span class="text-2xl font-black font-display text-slate-900">{{ $plan->formatted_monthly_price }}</span>
                                    <span class="text-xs text-slate-500">/mo</span>
                                </div>
                                <div class="text-right">
                                    <span class="text-sm font-bold text-slate-700">{{ $plan->formatted_annual_price }}</span>
                                    <span class="text-[11px] text-slate-500 block">/yr billed annually</span>
                                </div>
                            </div>

                            <!-- Limits Breakdown -->
                            <div class="space-y-2 text-xs">
                                <div class="flex justify-between py-1 border-b border-slate-100 text-slate-600">
                                    <span>Trial Period:</span>
                                    <span class="font-bold text-slate-900">{{ $plan->trial_days }} Days</span>
                                </div>
                                <div class="flex justify-between py-1 border-b border-slate-100 text-slate-600">
                                    <span>Staff Team Accounts:</span>
                                    <span class="font-bold text-slate-900">{{ $plan->max_staff_accounts }} Staff</span>
                                </div>
                                <div class="flex justify-between py-1 border-b border-slate-100 text-slate-600">
                                    <span>Active Bike Listings:</span>
                                    <span class="font-bold text-slate-900">{{ $plan->max_bike_listings }} Bikes</span>
                                </div>
                                <div class="flex justify-between py-1 border-b border-slate-100 text-slate-600">
                                    <span>Monthly Bookings:</span>
                                    <span class="font-bold text-slate-900">{{ $plan->max_monthly_bookings }} Appointments</span>
                                </div>
                                <div class="flex justify-between py-1 border-b border-slate-100 text-slate-600">
                                    <span>Cloud Media Storage:</span>
                                    <span class="font-bold text-slate-900">{{ $plan->media_storage_mb >= 1024 ? ($plan->media_storage_mb / 1024).' GB' : $plan->media_storage_mb.' MB' }}</span>
                                </div>
                            </div>

                            <!-- Feature Badges -->
                            <div class="space-y-1.5 pt-2">
                                <span class="text-[10px] uppercase font-bold text-slate-500 tracking-wider block">Entitled Features:</span>
                                <div class="flex flex-wrap gap-1.5">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold {{ $plan->has_careers_module ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-400 line-through border border-slate-200' }}">
                                        Careers Portal
                                    </span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold {{ $plan->has_scratch_cards ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-400 line-through border border-slate-200' }}">
                                        Scratch Cards
                                    </span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold {{ $plan->has_360_viewer ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-400 line-through border border-slate-200' }}">
                                        360° Viewer
                                    </span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold {{ $plan->has_ai_assistant ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-400 line-through border border-slate-200' }}">
                                        AI Assistant
                                    </span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold {{ $plan->has_custom_domain ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-400 line-through border border-slate-200' }}">
                                        Custom Domain
                                    </span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold {{ $plan->has_advanced_analytics ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-400 line-through border border-slate-200' }}">
                                        Advanced Analytics
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Card Footer -->
                        <div class="pt-6 border-t border-slate-100 mt-6 flex items-center justify-between gap-2">
                            <span class="text-xs text-slate-500">
                                <strong class="text-slate-900">{{ $plan->subscriptions_count }}</strong> Showrooms
                            </span>

                            <div class="flex items-center gap-2">
                                <form method="POST" action="{{ route('admin.plans.toggle', $plan) }}">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 rounded-lg border border-slate-300 bg-white hover:bg-slate-50 text-xs font-semibold text-slate-700 shadow-xs transition">
                                        {{ $plan->is_active ? 'Deactivate' : 'Activate' }}
                                    </button>
                                </form>

                                <a href="{{ route('admin.plans.edit', $plan) }}" class="px-3 py-1.5 rounded-lg bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-xs transition">
                                    Edit
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 p-12 text-center text-slate-500 bg-white rounded-2xl border border-slate-200 shadow-xs">
                        No subscription plans configured. Click "Create New Plan" to set up pricing tiers.
                    </div>
                @endforelse
            </div>

            <!-- Showrooms with Special Feature Grants & Plan Overrides -->
            <div class="bg-white rounded-3xl border border-slate-200 shadow-xs p-6 sm:p-8 space-y-6">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-slate-100 pb-5">
                    <div>
                        <div class="flex items-center gap-2.5">
                            <span class="w-3 h-3 rounded-full bg-amber-400 animate-pulse"></span>
                            <h2 class="text-xl font-black font-display text-slate-900 tracking-tight">Showroom Special Feature Access & VIP Grants</h2>
                        </div>
                        <p class="text-xs sm:text-sm text-slate-500 mt-1">
                            Grant extra access of any feature (AI Assistant, 360 Viewer, Custom Domains, etc.) to specific showrooms regardless of their current subscription tier.
                        </p>
                    </div>

                    <button type="button"
                            @click="openOverrideModal()"
                            class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs shadow-xs transition flex items-center gap-1.5 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>Grant Extra Access to a Showroom</span>
                    </button>
                </div>

                @if($showroomsWithOverrides->isNotEmpty())
                    <div class="overflow-x-auto rounded-2xl border border-slate-200">
                        <table class="w-full text-left text-xs text-slate-700">
                            <thead class="bg-slate-50 uppercase font-bold text-slate-500 text-[11px] border-b border-slate-200">
                                <tr>
                                    <th class="px-5 py-3.5">Showroom & Owner</th>
                                    <th class="px-5 py-3.5">Subscribed Plan</th>
                                    <th class="px-5 py-3.5">Special Granted Features</th>
                                    <th class="px-5 py-3.5">Custom Quotas</th>
                                    <th class="px-5 py-3.5 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-medium">
                                @foreach($showroomsWithOverrides as $so)
                                    <tr class="hover:bg-slate-50/80 transition">
                                        <td class="px-5 py-4">
                                            <div class="font-bold text-slate-900 text-sm">{{ $so['name'] }}</div>
                                            <div class="text-[11px] text-slate-500 mt-0.5">Owner: {{ $so['owner_name'] }} ({{ $so['owner_email'] }})</div>
                                            <div class="text-[10px] text-slate-400 font-mono mt-0.5">{{ $so['city'] }} • {{ $so['slug'] }}</div>
                                        </td>
                                        <td class="px-5 py-4">
                                            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-800 border border-slate-200">
                                                {{ $so['plan_name'] }}
                                            </span>
                                        </td>
                                        <td class="px-5 py-4">
                                            <div class="flex flex-wrap gap-1.5 max-w-md">
                                                @php
                                                    $labels = [
                                                        'has_ai_assistant' => 'AI Assistant',
                                                        'has_360_viewer' => '360° Viewer',
                                                        'has_custom_domain' => 'Custom Domain',
                                                        'has_scratch_cards' => 'Scratch Cards',
                                                        'has_careers_module' => 'Careers Portal',
                                                        'has_advanced_analytics' => 'Advanced Analytics',
                                                    ];
                                                @endphp
                                                @foreach($labels as $k => $label)
                                                    @if(isset($so['overrides'][$k]))
                                                        @if($so['overrides'][$k] === true)
                                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-300 inline-flex items-center gap-1">
                                                                <span>⭐</span> {{ $label }} (VIP Grant)
                                                            </span>
                                                        @elseif($so['overrides'][$k] === false)
                                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200 line-through">
                                                                {{ $label }} (Disabled)
                                                            </span>
                                                        @endif
                                                    @endif
                                                @endforeach
                                            </div>
                                        </td>
                                        <td class="px-5 py-4">
                                            <div class="space-y-0.5 text-[11px]">
                                                @if(isset($so['overrides']['max_bike_listings']))
                                                    <div>Bikes: <strong class="text-slate-900 font-mono">{{ $so['overrides']['max_bike_listings'] }}</strong></div>
                                                @endif
                                                @if(isset($so['overrides']['max_staff_accounts']))
                                                    <div>Staff: <strong class="text-slate-900 font-mono">{{ $so['overrides']['max_staff_accounts'] }}</strong></div>
                                                @endif
                                                @if(isset($so['overrides']['max_monthly_bookings']))
                                                    <div>Bookings: <strong class="text-slate-900 font-mono">{{ $so['overrides']['max_monthly_bookings'] }}</strong></div>
                                                @endif
                                                @if(isset($so['overrides']['media_storage_mb']))
                                                    <div>Storage: <strong class="text-slate-900 font-mono">{{ $so['overrides']['media_storage_mb'] }} MB</strong></div>
                                                @endif
                                                @if(!isset($so['overrides']['max_bike_listings']) && !isset($so['overrides']['max_staff_accounts']) && !isset($so['overrides']['max_monthly_bookings']) && !isset($so['overrides']['media_storage_mb']))
                                                    <span class="text-slate-400 italic">Default Plan Limits</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-5 py-4 text-right">
                                            <button type="button"
                                                    @click="openOverrideModal({{ $so['id'] }})"
                                                    class="px-3 py-1.5 rounded-lg bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 font-bold text-xs shadow-xs transition cursor-pointer">
                                                Edit Overrides
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-8 text-center rounded-2xl bg-slate-50 border border-slate-200/80 space-y-3">
                        <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center mx-auto text-xl font-black">
                            ⭐
                        </div>
                        <h3 class="text-sm font-bold text-slate-800">No Custom Feature Overrides Active</h3>
                        <p class="text-xs text-slate-500 max-w-md mx-auto">
                            All showrooms are currently following their subscribed plan tiers. You can unlock any feature (AI Assistant, 360 Viewer, Custom Domains, etc.) for any specific dealership by clicking below.
                        </p>
                        <button type="button"
                                @click="openOverrideModal()"
                                class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-xs transition cursor-pointer inline-flex items-center gap-1.5">
                            <span>Grant Feature Access to a Showroom</span>
                        </button>
                    </div>
                @endif
            </div>

        </div>

        <!-- Extra Feature Access & Quota Overrides Modal -->
        <div x-show="overrideModalOpen"
             x-cloak
             class="fixed inset-0 z-50 overflow-y-auto"
             aria-labelledby="override-modal-title"
             role="dialog"
             aria-modal="true">

            <!-- Backdrop -->
            <div x-show="overrideModalOpen"
                 x-transition:enter="ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"
                 @click="overrideModalOpen = false"></div>

            <div class="flex min-h-screen items-center justify-center p-3 sm:p-5">
                <div x-show="overrideModalOpen"
                     x-transition:enter="ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="ease-in duration-150"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="relative w-full max-w-5xl xl:max-w-6xl transform overflow-hidden rounded-3xl bg-white border border-slate-200 p-5 sm:p-7 text-left shadow-2xl transition-all">

                    <form :action="actionUrl" method="POST" class="space-y-4 sm:space-y-5">
                        @csrf

                        <!-- Modal Header -->
                        <div class="flex items-start justify-between border-b border-slate-100 pb-3.5">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-black uppercase tracking-wider bg-amber-100 text-amber-800 border border-amber-300">
                                        ⭐ Super Admin Grant
                                    </span>
                                </div>
                                <h3 class="text-xl sm:text-2xl font-black font-display text-slate-900 tracking-tight mt-1" id="override-modal-title">
                                    Showroom Feature Access & Plan Overrides
                                </h3>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    Grant extra access of any feature to a showroom, even if their subscription plan does not include it.
                                </p>
                            </div>

                            <button type="button"
                                    @click="overrideModalOpen = false"
                                    class="p-2 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition cursor-pointer">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        <!-- Top 2-Column Controls: Target Showroom & Audit Notes -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-3.5">
                            <!-- Target Showroom Selector with Instant Search -->
                            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 space-y-2 relative"
                                 @click.away="showroomDropdownOpen = false">
                                <div class="flex items-center justify-between">
                                    <label class="block text-[11px] uppercase font-bold text-slate-700 tracking-wider">
                                        Select Showroom Dealership
                                    </label>
                                    <span class="text-[10px] text-slate-400 font-medium">Type to search</span>
                                </div>

                                <!-- Search Input & Dropdown Toggle -->
                                <div class="relative">
                                    <div class="relative flex items-center">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                        </div>
                                        <input type="text"
                                               x-model="showroomSearch"
                                               @focus="showroomDropdownOpen = true"
                                               @input="showroomDropdownOpen = true"
                                               placeholder="Search by showroom name, city, owner, email, or plan..."
                                               class="w-full pl-9 pr-14 py-2 rounded-xl bg-white border border-slate-300 text-slate-900 text-xs sm:text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                                        <div class="absolute inset-y-0 right-0 pr-2 flex items-center gap-1">
                                            <button type="button"
                                                    x-show="showroomSearch"
                                                    @click="showroomSearch = ''; showroomDropdownOpen = true"
                                                    class="text-slate-400 hover:text-slate-600 p-1 rounded cursor-pointer"
                                                    title="Clear search">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </button>
                                            <button type="button"
                                                    @click="showroomDropdownOpen = !showroomDropdownOpen"
                                                    class="text-slate-400 hover:text-slate-600 p-1 rounded cursor-pointer">
                                                <svg class="w-4 h-4 transition-transform duration-200" :class="showroomDropdownOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Floating Search Results Dropdown -->
                                    <div x-show="showroomDropdownOpen"
                                         x-transition:enter="transition ease-out duration-100"
                                         x-transition:enter-start="opacity-0 scale-95"
                                         x-transition:enter-end="opacity-100 scale-100"
                                         x-transition:leave="transition ease-in duration-75"
                                         x-transition:leave-start="opacity-100 scale-100"
                                         x-transition:leave-end="opacity-0 scale-95"
                                         class="absolute left-0 right-0 mt-1 max-h-56 overflow-y-auto rounded-2xl bg-white border border-slate-200 shadow-xl z-50 divide-y divide-slate-100">
                                        <template x-for="item in filteredShowrooms" :key="item.id">
                                            <div @click="selectShowroom(item.id)"
                                                 class="px-3.5 py-2.5 hover:bg-slate-50 cursor-pointer transition flex items-center justify-between gap-3 text-xs"
                                                 :class="selectedShowroomId == item.id ? 'bg-rose-50/60' : ''">
                                                <div class="min-w-0">
                                                    <div class="font-bold text-slate-900 truncate" x-text="item.name"></div>
                                                    <div class="text-[11px] text-slate-500 truncate" x-text="item.owner_name + ' (' + item.owner_email + ') • ' + item.city"></div>
                                                </div>
                                                <div class="text-right shrink-0">
                                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200"
                                                          x-text="item.plan_name"></span>
                                                </div>
                                            </div>
                                        </template>
                                        <div x-show="filteredShowrooms.length === 0" class="p-4 text-center text-xs text-slate-400">
                                            No showrooms matching search query.
                                        </div>
                                    </div>
                                </div>

                                <template x-if="selectedShowroom">
                                    <div class="flex flex-wrap items-center gap-2 text-[11px] text-slate-600 pt-1 border-t border-slate-200/60">
                                        <span>Owner: <strong class="text-slate-900" x-text="selectedShowroom.owner_name + ' (' + selectedShowroom.owner_email + ')'"></strong></span>
                                        <span>•</span>
                                        <span>Plan: <span class="px-2 py-0.5 rounded font-bold bg-white border border-slate-300 text-slate-800" x-text="selectedShowroom.plan_name"></span></span>
                                    </div>
                                </template>
                            </div>

                            <!-- Audit Notes -->
                            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                                <label class="block text-[11px] uppercase font-bold text-slate-700 tracking-wider">
                                    Audit Reason / Notes (Optional)
                                </label>
                                <input type="text" name="grant_notes" x-model="grantNotes" placeholder="e.g. Complimentary AI Assistant grant for enterprise partner"
                                       class="w-full px-3.5 py-2 rounded-xl bg-white border border-slate-300 text-slate-900 text-xs sm:text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                                <p class="text-[10px] text-slate-400">
                                    All modifications are recorded with timestamps and admin user credentials in the platform audit trail.
                                </p>
                            </div>
                        </div>

                        <!-- 6 Entitlement Feature Toggles Grid (3 Columns, No Scrollbar) -->
                        <div class="space-y-2.5">
                            <div class="flex items-center justify-between">
                                <h4 class="text-xs uppercase font-bold text-slate-700 tracking-wider">
                                    Feature Entitlements & Overrides
                                </h4>
                                <span class="text-[11px] text-slate-500">
                                    "⭐ Grant Access" unlocks the feature immediately regardless of tier.
                                </span>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">

                                <!-- 1. AI Assistant -->
                                <div class="p-3 rounded-xl border border-slate-200 bg-white hover:border-slate-300 transition space-y-2 flex flex-col justify-between">
                                    <div class="space-y-1">
                                        <div class="flex items-start justify-between gap-1.5">
                                            <div class="font-bold text-slate-900 text-xs flex items-center gap-1.5">
                                                <span>🤖</span> AI Virtual Assistant
                                            </div>
                                            <template x-if="selectedShowroom">
                                                <span class="px-1.5 py-0.5 rounded text-[9px] font-bold shrink-0"
                                                      :class="selectedShowroom.plan_features.has_ai_assistant ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-500 border border-slate-200'"
                                                      x-text="selectedShowroom.plan_features.has_ai_assistant ? 'Plan: Included' : 'Plan: Excluded'"></span>
                                            </template>
                                        </div>
                                        <p class="text-[10px] text-slate-500 leading-tight">Autonomous concierge chat, automated lead capture.</p>
                                    </div>

                                    <div class="grid grid-cols-3 gap-1 p-1 bg-slate-100 rounded-lg text-[10px] font-bold">
                                        <label class="cursor-pointer">
                                            <input type="radio" name="has_ai_assistant" value="inherit" x-model="activeOverrides.has_ai_assistant" class="sr-only">
                                            <div class="py-1 px-1 rounded-md text-center transition"
                                                 :class="activeOverrides.has_ai_assistant === 'inherit' ? 'bg-white shadow-xs text-slate-900' : 'text-slate-600 hover:text-slate-900'">
                                                Follow Plan
                                            </div>
                                        </label>
                                        <label class="cursor-pointer">
                                            <input type="radio" name="has_ai_assistant" value="grant" x-model="activeOverrides.has_ai_assistant" class="sr-only">
                                            <div class="py-1 px-1 rounded-md text-center transition"
                                                 :class="activeOverrides.has_ai_assistant === 'grant' ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'">
                                                ⭐ Grant
                                            </div>
                                        </label>
                                        <label class="cursor-pointer">
                                            <input type="radio" name="has_ai_assistant" value="deny" x-model="activeOverrides.has_ai_assistant" class="sr-only">
                                            <div class="py-1 px-1 rounded-md text-center transition"
                                                 :class="activeOverrides.has_ai_assistant === 'deny' ? 'bg-rose-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'">
                                                🚫 Disable
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                <!-- 2. 360° Viewer -->
                                <div class="p-3 rounded-xl border border-slate-200 bg-white hover:border-slate-300 transition space-y-2 flex flex-col justify-between">
                                    <div class="space-y-1">
                                        <div class="flex items-start justify-between gap-1.5">
                                            <div class="font-bold text-slate-900 text-xs flex items-center gap-1.5">
                                                <span>🔄</span> 360° Spin Viewer
                                            </div>
                                            <template x-if="selectedShowroom">
                                                <span class="px-1.5 py-0.5 rounded text-[9px] font-bold shrink-0"
                                                      :class="selectedShowroom.plan_features.has_360_viewer ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-500 border border-slate-200'"
                                                      x-text="selectedShowroom.plan_features.has_360_viewer ? 'Plan: Included' : 'Plan: Excluded'"></span>
                                            </template>
                                        </div>
                                        <p class="text-[10px] text-slate-500 leading-tight">Interactive 360-degree frame turntable inspection.</p>
                                    </div>

                                    <div class="grid grid-cols-3 gap-1 p-1 bg-slate-100 rounded-lg text-[10px] font-bold">
                                        <label class="cursor-pointer">
                                            <input type="radio" name="has_360_viewer" value="inherit" x-model="activeOverrides.has_360_viewer" class="sr-only">
                                            <div class="py-1 px-1 rounded-md text-center transition"
                                                 :class="activeOverrides.has_360_viewer === 'inherit' ? 'bg-white shadow-xs text-slate-900' : 'text-slate-600 hover:text-slate-900'">
                                                Follow Plan
                                            </div>
                                        </label>
                                        <label class="cursor-pointer">
                                            <input type="radio" name="has_360_viewer" value="grant" x-model="activeOverrides.has_360_viewer" class="sr-only">
                                            <div class="py-1 px-1 rounded-md text-center transition"
                                                 :class="activeOverrides.has_360_viewer === 'grant' ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'">
                                                ⭐ Grant
                                            </div>
                                        </label>
                                        <label class="cursor-pointer">
                                            <input type="radio" name="has_360_viewer" value="deny" x-model="activeOverrides.has_360_viewer" class="sr-only">
                                            <div class="py-1 px-1 rounded-md text-center transition"
                                                 :class="activeOverrides.has_360_viewer === 'deny' ? 'bg-rose-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'">
                                                🚫 Disable
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                <!-- 3. Custom Domain -->
                                <div class="p-3 rounded-xl border border-slate-200 bg-white hover:border-slate-300 transition space-y-2 flex flex-col justify-between">
                                    <div class="space-y-1">
                                        <div class="flex items-start justify-between gap-1.5">
                                            <div class="font-bold text-slate-900 text-xs flex items-center gap-1.5">
                                                <span>🌐</span> Custom Domain Support
                                            </div>
                                            <template x-if="selectedShowroom">
                                                <span class="px-1.5 py-0.5 rounded text-[9px] font-bold shrink-0"
                                                      :class="selectedShowroom.plan_features.has_custom_domain ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-500 border border-slate-200'"
                                                      x-text="selectedShowroom.plan_features.has_custom_domain ? 'Plan: Included' : 'Plan: Excluded'"></span>
                                            </template>
                                        </div>
                                        <p class="text-[10px] text-slate-500 leading-tight">Link custom dealership domain (apexmotohub.com).</p>
                                    </div>

                                    <div class="grid grid-cols-3 gap-1 p-1 bg-slate-100 rounded-lg text-[10px] font-bold">
                                        <label class="cursor-pointer">
                                            <input type="radio" name="has_custom_domain" value="inherit" x-model="activeOverrides.has_custom_domain" class="sr-only">
                                            <div class="py-1 px-1 rounded-md text-center transition"
                                                 :class="activeOverrides.has_custom_domain === 'inherit' ? 'bg-white shadow-xs text-slate-900' : 'text-slate-600 hover:text-slate-900'">
                                                Follow Plan
                                            </div>
                                        </label>
                                        <label class="cursor-pointer">
                                            <input type="radio" name="has_custom_domain" value="grant" x-model="activeOverrides.has_custom_domain" class="sr-only">
                                            <div class="py-1 px-1 rounded-md text-center transition"
                                                 :class="activeOverrides.has_custom_domain === 'grant' ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'">
                                                ⭐ Grant
                                            </div>
                                        </label>
                                        <label class="cursor-pointer">
                                            <input type="radio" name="has_custom_domain" value="deny" x-model="activeOverrides.has_custom_domain" class="sr-only">
                                            <div class="py-1 px-1 rounded-md text-center transition"
                                                 :class="activeOverrides.has_custom_domain === 'deny' ? 'bg-rose-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'">
                                                🚫 Disable
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                <!-- 4. Scratch Cards -->
                                <div class="p-3 rounded-xl border border-slate-200 bg-white hover:border-slate-300 transition space-y-2 flex flex-col justify-between">
                                    <div class="space-y-1">
                                        <div class="flex items-start justify-between gap-1.5">
                                            <div class="font-bold text-slate-900 text-xs flex items-center gap-1.5">
                                                <span>🎁</span> Scratch Cards & Rewards
                                            </div>
                                            <template x-if="selectedShowroom">
                                                <span class="px-1.5 py-0.5 rounded text-[9px] font-bold shrink-0"
                                                      :class="selectedShowroom.plan_features.has_scratch_cards ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-500 border border-slate-200'"
                                                      x-text="selectedShowroom.plan_features.has_scratch_cards ? 'Plan: Included' : 'Plan: Excluded'"></span>
                                            </template>
                                        </div>
                                        <p class="text-[10px] text-slate-500 leading-tight">Digital scratch cards, reward coupons, and offers.</p>
                                    </div>

                                    <div class="grid grid-cols-3 gap-1 p-1 bg-slate-100 rounded-lg text-[10px] font-bold">
                                        <label class="cursor-pointer">
                                            <input type="radio" name="has_scratch_cards" value="inherit" x-model="activeOverrides.has_scratch_cards" class="sr-only">
                                            <div class="py-1 px-1 rounded-md text-center transition"
                                                 :class="activeOverrides.has_scratch_cards === 'inherit' ? 'bg-white shadow-xs text-slate-900' : 'text-slate-600 hover:text-slate-900'">
                                                Follow Plan
                                            </div>
                                        </label>
                                        <label class="cursor-pointer">
                                            <input type="radio" name="has_scratch_cards" value="grant" x-model="activeOverrides.has_scratch_cards" class="sr-only">
                                            <div class="py-1 px-1 rounded-md text-center transition"
                                                 :class="activeOverrides.has_scratch_cards === 'grant' ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'">
                                                ⭐ Grant
                                            </div>
                                        </label>
                                        <label class="cursor-pointer">
                                            <input type="radio" name="has_scratch_cards" value="deny" x-model="activeOverrides.has_scratch_cards" class="sr-only">
                                            <div class="py-1 px-1 rounded-md text-center transition"
                                                 :class="activeOverrides.has_scratch_cards === 'deny' ? 'bg-rose-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'">
                                                🚫 Disable
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                <!-- 5. Careers Module -->
                                <div class="p-3 rounded-xl border border-slate-200 bg-white hover:border-slate-300 transition space-y-2 flex flex-col justify-between">
                                    <div class="space-y-1">
                                        <div class="flex items-start justify-between gap-1.5">
                                            <div class="font-bold text-slate-900 text-xs flex items-center gap-1.5">
                                                <span>💼</span> Careers & Hiring Portal
                                            </div>
                                            <template x-if="selectedShowroom">
                                                <span class="px-1.5 py-0.5 rounded text-[9px] font-bold shrink-0"
                                                      :class="selectedShowroom.plan_features.has_careers_module ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-500 border border-slate-200'"
                                                      x-text="selectedShowroom.plan_features.has_careers_module ? 'Plan: Included' : 'Plan: Excluded'"></span>
                                            </template>
                                        </div>
                                        <p class="text-[10px] text-slate-500 leading-tight">Job board, candidate applications, hiring pipeline.</p>
                                    </div>

                                    <div class="grid grid-cols-3 gap-1 p-1 bg-slate-100 rounded-lg text-[10px] font-bold">
                                        <label class="cursor-pointer">
                                            <input type="radio" name="has_careers_module" value="inherit" x-model="activeOverrides.has_careers_module" class="sr-only">
                                            <div class="py-1 px-1 rounded-md text-center transition"
                                                 :class="activeOverrides.has_careers_module === 'inherit' ? 'bg-white shadow-xs text-slate-900' : 'text-slate-600 hover:text-slate-900'">
                                                Follow Plan
                                            </div>
                                        </label>
                                        <label class="cursor-pointer">
                                            <input type="radio" name="has_careers_module" value="grant" x-model="activeOverrides.has_careers_module" class="sr-only">
                                            <div class="py-1 px-1 rounded-md text-center transition"
                                                 :class="activeOverrides.has_careers_module === 'grant' ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'">
                                                ⭐ Grant
                                            </div>
                                        </label>
                                        <label class="cursor-pointer">
                                            <input type="radio" name="has_careers_module" value="deny" x-model="activeOverrides.has_careers_module" class="sr-only">
                                            <div class="py-1 px-1 rounded-md text-center transition"
                                                 :class="activeOverrides.has_careers_module === 'deny' ? 'bg-rose-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'">
                                                🚫 Disable
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                <!-- 6. Advanced Analytics -->
                                <div class="p-3 rounded-xl border border-slate-200 bg-white hover:border-slate-300 transition space-y-2 flex flex-col justify-between">
                                    <div class="space-y-1">
                                        <div class="flex items-start justify-between gap-1.5">
                                            <div class="font-bold text-slate-900 text-xs flex items-center gap-1.5">
                                                <span>📊</span> Advanced Analytics
                                            </div>
                                            <template x-if="selectedShowroom">
                                                <span class="px-1.5 py-0.5 rounded text-[9px] font-bold shrink-0"
                                                      :class="selectedShowroom.plan_features.has_advanced_analytics ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-500 border border-slate-200'"
                                                      x-text="selectedShowroom.plan_features.has_advanced_analytics ? 'Plan: Included' : 'Plan: Excluded'"></span>
                                            </template>
                                        </div>
                                        <p class="text-[10px] text-slate-500 leading-tight">Revenue telemetry, turnover metrics, performance.</p>
                                    </div>

                                    <div class="grid grid-cols-3 gap-1 p-1 bg-slate-100 rounded-lg text-[10px] font-bold">
                                        <label class="cursor-pointer">
                                            <input type="radio" name="has_advanced_analytics" value="inherit" x-model="activeOverrides.has_advanced_analytics" class="sr-only">
                                            <div class="py-1 px-1 rounded-md text-center transition"
                                                 :class="activeOverrides.has_advanced_analytics === 'inherit' ? 'bg-white shadow-xs text-slate-900' : 'text-slate-600 hover:text-slate-900'">
                                                Follow Plan
                                            </div>
                                        </label>
                                        <label class="cursor-pointer">
                                            <input type="radio" name="has_advanced_analytics" value="grant" x-model="activeOverrides.has_advanced_analytics" class="sr-only">
                                            <div class="py-1 px-1 rounded-md text-center transition"
                                                 :class="activeOverrides.has_advanced_analytics === 'grant' ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'">
                                                ⭐ Grant
                                            </div>
                                        </label>
                                        <label class="cursor-pointer">
                                            <input type="radio" name="has_advanced_analytics" value="deny" x-model="activeOverrides.has_advanced_analytics" class="sr-only">
                                            <div class="py-1 px-1 rounded-md text-center transition"
                                                 :class="activeOverrides.has_advanced_analytics === 'deny' ? 'bg-rose-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'">
                                                🚫 Disable
                                            </div>
                                        </label>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <!-- Optional Quota Limits Overrides (Compact 1-row layout) -->
                        <div class="p-3 sm:p-3.5 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                            <div class="flex items-center justify-between">
                                <h4 class="text-[11px] uppercase font-bold text-slate-700 tracking-wider">
                                    Custom Quantitative Limit Overrides (Optional)
                                </h4>
                                <span class="text-[10px] text-slate-400">Leave blank to use plan limit</span>
                            </div>

                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 text-xs">
                                <div>
                                    <label class="block text-[10px] font-semibold text-slate-600 mb-0.5">Max Active Bikes</label>
                                    <input type="number" name="max_bike_listings" x-model="customBikes" min="1" placeholder="Plan default"
                                           class="w-full px-2.5 py-1.5 rounded-lg bg-white border border-slate-300 text-slate-900 text-xs focus:ring-1 focus:ring-rose-500">
                                </div>

                                <div>
                                    <label class="block text-[10px] font-semibold text-slate-600 mb-0.5">Staff Accounts</label>
                                    <input type="number" name="max_staff_accounts" x-model="customStaff" min="1" placeholder="Plan default"
                                           class="w-full px-2.5 py-1.5 rounded-lg bg-white border border-slate-300 text-slate-900 text-xs focus:ring-1 focus:ring-rose-500">
                                </div>

                                <div>
                                    <label class="block text-[10px] font-semibold text-slate-600 mb-0.5">Monthly Bookings</label>
                                    <input type="number" name="max_monthly_bookings" x-model="customBookings" min="1" placeholder="Plan default"
                                           class="w-full px-2.5 py-1.5 rounded-lg bg-white border border-slate-300 text-slate-900 text-xs focus:ring-1 focus:ring-rose-500">
                                </div>

                                <div>
                                    <label class="block text-[10px] font-semibold text-slate-600 mb-0.5">Media Storage (MB)</label>
                                    <input type="number" name="media_storage_mb" x-model="customStorage" min="100" placeholder="Plan default"
                                           class="w-full px-2.5 py-1.5 rounded-lg bg-white border border-slate-300 text-slate-900 text-xs focus:ring-1 focus:ring-rose-500">
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center justify-end gap-3 pt-2 border-t border-slate-100">
                            <button type="button"
                                    @click="overrideModalOpen = false"
                                    class="px-5 py-2 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs shadow-xs transition cursor-pointer">
                                Cancel
                            </button>

                            <button type="submit"
                                    class="px-6 py-2 rounded-xl bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-500 hover:to-emerald-400 text-white font-bold text-xs shadow-xs transition flex items-center gap-1.5 cursor-pointer"
                                    id="btn-save-feature-overrides">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Save Special Feature Access</span>
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>

    </div>
    @include('admin.partials.nav-close')
</x-app-layout>
