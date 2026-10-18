<x-app-layout title="Audit Trail Logs - Super Admin | MotoShowroom SaaS">
    @include('admin.partials.nav')

    <div class="min-h-screen bg-slate-950 text-slate-100 py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto space-y-8">

            <!-- Header -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-slate-800 pb-6">
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-3xl font-black font-display text-white tracking-tight">System Audit Trail</h1>
                        <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-amber-500/20 text-amber-300 border border-amber-500/30">
                            Immutable Ledger
                        </span>
                    </div>
                    <p class="text-sm text-slate-400 mt-1">Cross-platform tracking of administrative overrides, tenant provisioning, status modifications, and membership changes.</p>
                </div>
            </div>

            <!-- Filters Bar -->
            <div class="glass-panel-dark rounded-2xl p-4 border border-slate-800 shadow-lg">
                <form method="GET" action="{{ route('admin.audit-logs.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                    <div class="lg:col-span-2">
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Search action, IP, or entity type..."
                               class="w-full px-4 py-2 rounded-xl bg-slate-900 border border-slate-700/80 text-white placeholder-slate-500 text-xs focus:ring-2 focus:ring-rose-500">
                    </div>

                    <div>
                        <select name="action" class="w-full px-3 py-2 rounded-xl bg-slate-900 border border-slate-700/80 text-white text-xs focus:ring-2 focus:ring-rose-500">
                            <option value="">All Actions</option>
                            @foreach($actions as $action)
                                <option value="{{ $action }}" {{ request('action') === $action ? 'selected' : '' }}>{{ $action }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <select name="tenant_id" class="w-full px-3 py-2 rounded-xl bg-slate-900 border border-slate-700/80 text-white text-xs focus:ring-2 focus:ring-rose-500">
                            <option value="">All Tenants</option>
                            @foreach($showrooms as $sh)
                                <option value="{{ $sh->id }}" {{ request('tenant_id') == $sh->id ? 'selected' : '' }}>{{ $sh->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="submit" class="w-full py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-xs font-semibold transition">
                            Apply
                        </button>
                        @if(request()->hasAny(['search', 'action', 'tenant_id', 'user_id']))
                            <a href="{{ route('admin.audit-logs.index') }}" class="px-3 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-slate-400 hover:text-white text-xs transition">
                                Clear
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Audit Logs Table -->
            <div class="glass-panel-dark rounded-2xl border border-slate-800 shadow-xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-300">
                        <thead class="bg-slate-900/90 uppercase font-bold text-slate-400 border-b border-slate-800">
                            <tr>
                                <th class="px-6 py-4">Timestamp</th>
                                <th class="px-6 py-4">Action</th>
                                <th class="px-6 py-4">Showroom Tenant</th>
                                <th class="px-6 py-4">Actor</th>
                                <th class="px-6 py-4">Network IP</th>
                                <th class="px-6 py-4">Payload & Diffs</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/80">
                            @forelse($logs as $log)
                                <tr class="hover:bg-slate-900/40 transition">
                                    <td class="px-6 py-4 font-mono text-slate-400 shrink-0">
                                        <div class="text-white">{{ $log->created_at->format('Y-m-d H:i:s') }}</div>
                                        <div class="text-[11px] text-slate-500">{{ $log->created_at->diffForHumans() }}</div>
                                    </td>

                                    <td class="px-6 py-4">
                                        <span class="px-2.5 py-1 rounded text-xs font-bold font-mono
                                            @if(str_contains($log->action, 'created')) bg-emerald-500/20 text-emerald-300 border border-emerald-500/30
                                            @elseif(str_contains($log->action, 'status')) bg-amber-500/20 text-amber-300 border border-amber-500/30
                                            @elseif(str_contains($log->action, 'removed') || str_contains($log->action, 'deleted')) bg-rose-500/20 text-rose-300 border border-rose-500/30
                                            @else bg-sky-500/20 text-sky-300 border border-sky-500/30 @endif">
                                            {{ $log->action }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-4">
                                        @if($log->showroom)
                                            <a href="{{ route('admin.showrooms.show', $log->showroom) }}" class="font-bold text-white hover:text-rose-400 transition flex items-center gap-1.5">
                                                <span class="w-2 h-2 rounded-full" style="background-color: {{ $log->showroom->brand_color_primary ?? '#dc2626' }}"></span>
                                                <span>{{ $log->showroom->name }}</span>
                                            </a>
                                        @else
                                            <span class="text-slate-500 italic">Platform Level</span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4">
                                        @if($log->user)
                                            <a href="{{ route('users.show', $log->user) }}" class="text-slate-200 hover:text-rose-400 font-semibold block">
                                                {{ $log->user->name }}
                                            </a>
                                            <span class="text-[11px] text-slate-500">{{ $log->user->email }}</span>
                                        @else
                                            <span class="text-slate-500 italic">System / CLI</span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 font-mono text-[11px] text-slate-400">
                                        <div>{{ $log->ip_address }}</div>
                                        <div class="text-[10px] text-slate-600 truncate max-w-xs" title="{{ $log->user_agent }}">{{ $log->user_agent }}</div>
                                    </td>

                                    <td class="px-6 py-4">
                                        <details class="cursor-pointer group">
                                            <summary class="text-rose-400 hover:text-rose-300 font-semibold list-none flex items-center gap-1 text-xs">
                                                <span>View Payload</span>
                                                <svg class="w-3.5 h-3.5 group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                            </summary>
                                            <div class="mt-2 p-3 rounded-lg bg-slate-900 border border-slate-800 text-[11px] font-mono space-y-2 max-w-md">
                                                @if($log->old_values)
                                                    <div>
                                                        <span class="text-rose-400 uppercase font-bold text-[10px]">Previous:</span>
                                                        <pre class="text-slate-400 overflow-x-auto whitespace-pre-wrap">{{ json_encode($log->old_values, JSON_PRETTY_PRINT) }}</pre>
                                                    </div>
                                                @endif
                                                @if($log->new_values)
                                                    <div>
                                                        <span class="text-emerald-400 uppercase font-bold text-[10px]">Updated:</span>
                                                        <pre class="text-emerald-300 overflow-x-auto whitespace-pre-wrap">{{ json_encode($log->new_values, JSON_PRETTY_PRINT) }}</pre>
                                                    </div>
                                                @endif
                                                @if(!$log->old_values && !$log->new_values)
                                                    <span class="text-slate-600 italic">No structured diff payload.</span>
                                                @endif
                                            </div>
                                        </details>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                        No audit events found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($logs->hasPages())
                    <div class="px-6 py-4 border-t border-slate-800">
                        {{ $logs->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
