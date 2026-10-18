<nav class="border-b border-slate-800 bg-slate-950/90 backdrop-blur sticky top-0 z-40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

            <!-- Brand & Showroom Tenant Identity -->
            <div class="flex items-center gap-6">
                <a href="{{ route('showrooms.manage', $showroom) }}" class="flex items-center gap-2.5 group">
                    <span class="w-8 h-8 rounded-xl flex items-center justify-center font-black text-white text-sm shadow group-hover:scale-105 transition-transform"
                          style="background-color: {{ $showroom->brand_color_primary ?? '#dc2626' }}">
                        {{ strtoupper(substr($showroom->name, 0, 1)) }}
                    </span>
                    <div>
                        <span class="font-display font-black text-base tracking-tight text-white block leading-none">{{ $showroom->name }}</span>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Staff Back-Office</span>
                    </div>
                </a>

                <!-- Desktop Navigation Links -->
                <div class="hidden lg:flex items-center space-x-1">
                    <a href="{{ route('showrooms.manage', $showroom) }}"
                       class="px-3 py-1.5 rounded-xl text-xs font-semibold transition {{ request()->routeIs('showrooms.manage') ? 'bg-rose-500/15 text-rose-400 border border-rose-500/30' : 'text-slate-300 hover:text-white hover:bg-slate-900' }}">
                        Dashboard
                    </a>

                    <a href="{{ route('showrooms.manage.bikes.index', $showroom) }}"
                       class="px-3 py-1.5 rounded-xl text-xs font-semibold transition {{ request()->routeIs('showrooms.manage.bikes*') ? 'bg-rose-500/15 text-rose-400 border border-rose-500/30' : 'text-slate-300 hover:text-white hover:bg-slate-900' }}">
                        Motorcycles
                    </a>

                    <a href="{{ route('showrooms.manage.enquiries.index', $showroom) }}"
                       class="px-3 py-1.5 rounded-xl text-xs font-semibold transition {{ request()->routeIs('showrooms.manage.enquiries*') ? 'bg-rose-500/15 text-rose-400 border border-rose-500/30' : 'text-slate-300 hover:text-white hover:bg-slate-900' }}">
                        Enquiries & Leads
                    </a>

                    <a href="{{ route('showrooms.manage.services.index', $showroom) }}"
                       class="px-3 py-1.5 rounded-xl text-xs font-semibold transition {{ request()->routeIs('showrooms.manage.services*') ? 'bg-rose-500/15 text-rose-400 border border-rose-500/30' : 'text-slate-300 hover:text-white hover:bg-slate-900' }}">
                        Workshop & Service
                    </a>

                    <a href="{{ route('showrooms.manage.careers.index', $showroom) }}"
                       class="px-3 py-1.5 rounded-xl text-xs font-semibold transition {{ request()->routeIs('showrooms.manage.careers*', 'showrooms.manage.applicants*') ? 'bg-rose-500/15 text-rose-400 border border-rose-500/30' : 'text-slate-300 hover:text-white hover:bg-slate-900' }}">
                        Careers & ATS
                    </a>

                    <a href="{{ route('showrooms.manage.campaigns.index', $showroom) }}"
                       class="px-3 py-1.5 rounded-xl text-xs font-semibold transition {{ request()->routeIs('showrooms.manage.campaigns*') ? 'bg-rose-500/15 text-rose-400 border border-rose-500/30' : 'text-slate-300 hover:text-white hover:bg-slate-900' }}">
                        Scratch Cards
                    </a>

                    <a href="{{ route('showrooms.manage.ai.index', $showroom) }}"
                       class="px-3 py-1.5 rounded-xl text-xs font-semibold transition {{ request()->routeIs('showrooms.manage.ai*') ? 'bg-rose-500/15 text-rose-400 border border-rose-500/30' : 'text-slate-300 hover:text-white hover:bg-slate-900' }}">
                        AI Concierge
                    </a>

                    <a href="{{ route('showrooms.manage.website', $showroom) }}"
                       class="px-3 py-1.5 rounded-xl text-xs font-semibold transition {{ request()->routeIs('showrooms.manage.website*') ? 'bg-rose-500/15 text-rose-400 border border-rose-500/30' : 'text-slate-300 hover:text-white hover:bg-slate-900' }}">
                        Website Builder
                    </a>

                    <a href="{{ route('showrooms.billing.index', $showroom) }}"
                       class="px-3 py-1.5 rounded-xl text-xs font-semibold transition {{ request()->routeIs('showrooms.billing*') ? 'bg-rose-500/15 text-rose-400 border border-rose-500/30' : 'text-slate-300 hover:text-white hover:bg-slate-900' }}">
                        Subscription & Quotas
                    </a>

                    <a href="{{ route('showrooms.manage.payments.index', $showroom) }}"
                       class="px-3 py-1.5 rounded-xl text-xs font-semibold transition {{ request()->routeIs('showrooms.manage.payments*') ? 'bg-rose-500/15 text-rose-400 border border-rose-500/30' : 'text-slate-300 hover:text-white hover:bg-slate-900' }}">
                        Payments
                    </a>

                    <a href="{{ route('showrooms.manage.email-previews.index', $showroom) }}"
                       class="px-3 py-1.5 rounded-xl text-xs font-semibold transition {{ request()->routeIs('showrooms.manage.email-previews*') ? 'bg-rose-500/15 text-rose-400 border border-rose-500/30' : 'text-slate-300 hover:text-white hover:bg-slate-900' }}">
                        Emails
                    </a>

                    <a href="{{ route('showrooms.manage.settings', $showroom) }}"
                       class="px-3 py-1.5 rounded-xl text-xs font-semibold transition {{ request()->routeIs('showrooms.manage.settings*') ? 'bg-rose-500/15 text-rose-400 border border-rose-500/30' : 'text-slate-300 hover:text-white hover:bg-slate-900' }}">
                        Settings
                    </a>
                </div>
            </div>

            <!-- Right Utilities -->
            <div class="flex items-center gap-3">
                <a href="{{ route('showrooms.show', $showroom) }}" target="_blank"
                   class="flex items-center gap-1.5 text-xs text-slate-300 hover:text-white px-3 py-1.5 rounded-xl border border-slate-800 bg-slate-900 hover:bg-slate-800 transition"
                   title="Open Public Showroom Website">
                    <svg class="w-3.5 h-3.5 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    <span class="hidden sm:inline">Storefront</span>
                </a>

                <a href="{{ route('profile') }}" class="flex items-center gap-2 px-3 py-1.5 rounded-xl border border-slate-800 bg-slate-900 hover:bg-slate-800 text-xs font-medium text-slate-200 transition">
                    <div class="w-6 h-6 rounded-full bg-rose-500/20 text-rose-400 flex items-center justify-center font-bold text-xs">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <span class="hidden sm:inline">{{ auth()->user()->name }}</span>
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="p-2 rounded-xl text-slate-400 hover:text-rose-400 hover:bg-slate-900 transition" title="Sign Out">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </button>
                </form>
            </div>

        </div>

        <!-- Mobile Horizontal Tab Bar -->
        <div class="lg:hidden flex items-center space-x-1 overflow-x-auto py-2 border-t border-slate-800/80 text-xs">
            <a href="{{ route('showrooms.manage', $showroom) }}" class="px-3 py-1.5 rounded-lg shrink-0 {{ request()->routeIs('showrooms.manage') ? 'bg-rose-500/20 text-rose-300' : 'text-slate-400' }}">Dashboard</a>
            <a href="{{ route('showrooms.manage.careers.index', $showroom) }}" class="px-3 py-1.5 rounded-lg shrink-0 {{ request()->routeIs('showrooms.manage.careers*', 'showrooms.manage.applicants*') ? 'bg-rose-500/20 text-rose-300' : 'text-slate-400' }}">Careers</a>
            <a href="{{ route('showrooms.manage.campaigns.index', $showroom) }}" class="px-3 py-1.5 rounded-lg shrink-0 {{ request()->routeIs('showrooms.manage.campaigns*') ? 'bg-rose-500/20 text-rose-300' : 'text-slate-400' }}">Scratch Cards</a>
            <a href="{{ route('showrooms.manage.ai.index', $showroom) }}" class="px-3 py-1.5 rounded-lg shrink-0 {{ request()->routeIs('showrooms.manage.ai*') ? 'bg-rose-500/20 text-rose-300' : 'text-slate-400' }}">AI Concierge</a>
            <a href="{{ route('showrooms.manage.website', $showroom) }}" class="px-3 py-1.5 rounded-lg shrink-0 {{ request()->routeIs('showrooms.manage.website*') ? 'bg-rose-500/20 text-rose-300' : 'text-slate-400' }}">Website Builder</a>
            <a href="{{ route('showrooms.billing.index', $showroom) }}" class="px-3 py-1.5 rounded-lg shrink-0 {{ request()->routeIs('showrooms.billing*') ? 'bg-rose-500/20 text-rose-300' : 'text-slate-400' }}">Subscription</a>
            <a href="{{ route('showrooms.manage.settings', $showroom) }}" class="px-3 py-1.5 rounded-lg shrink-0 {{ request()->routeIs('showrooms.manage.settings*') ? 'bg-rose-500/20 text-rose-300' : 'text-slate-400' }}">Settings</a>
        </div>
    </div>
</nav>
