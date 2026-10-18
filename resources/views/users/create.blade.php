<x-app-layout title="Admin: Create New User | MotoShowroom SaaS">
    @include('admin.partials.nav')
    <div class="min-h-screen bg-slate-950 text-slate-100 py-10 px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto space-y-8">

            <!-- Breadcrumbs / Top Navigation -->
            <div class="flex items-center justify-between">
                <a href="{{ route('admin.users.index') }}" class="text-sm font-medium text-slate-400 hover:text-white transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span>Back to User Management</span>
                </a>
                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-rose-500/20 text-rose-300 border border-rose-500/30">
                    Admin Privilege
                </span>
            </div>

            <!-- Create Form Card -->
            <div class="glass-panel-dark rounded-3xl p-8 border border-slate-800 shadow-2xl relative overflow-hidden backdrop-blur-xl">
                <div class="absolute -top-24 -right-24 w-52 h-52 bg-rose-600/15 blur-[80px] rounded-full pointer-events-none"></div>

                <div class="border-b border-slate-800 pb-5 mb-6">
                    <h1 class="text-2xl font-black font-display text-white">Create New User</h1>
                    <p class="text-sm text-slate-400 mt-1">Provision a new administrator or standard showroom user account.</p>
                </div>

                <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-6" id="form-admin-create-user">
                    @csrf

                    <!-- Role & Status Selection -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 rounded-2xl bg-slate-900/60 border border-slate-800">
                        <div>
                            <label for="role" class="block text-xs font-bold uppercase tracking-wider text-amber-400 mb-1.5">User Role</label>
                            <select name="role" id="role" required class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm focus:ring-2 focus:ring-rose-500 focus:outline-none">
                                <option value="user" {{ old('role') === 'user' ? 'selected' : '' }}>Standard User (Showroom / Customer)</option>
                                <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Platform Administrator (Admin)</option>
                            </select>
                            @error('role')
                                <p class="mt-1 text-xs text-rose-400 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="status" class="block text-xs font-bold uppercase tracking-wider text-amber-400 mb-1.5">Initial Status</label>
                            <select name="status" id="status" required class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm focus:ring-2 focus:ring-rose-500 focus:outline-none">
                                <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="suspended" {{ old('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-xs text-rose-400 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Full Name -->
                    <div>
                        <label for="name" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">Full Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                               class="w-full px-4 py-3 rounded-xl bg-slate-900/80 border @error('name') border-rose-500 @else border-slate-700/80 @enderror text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-rose-500 text-sm transition"
                               placeholder="e.g. Alex Rider">
                        @error('name')
                            <p class="mt-1 text-xs text-rose-400 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email Address -->
                    <div>
                        <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">Email Address</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required
                               class="w-full px-4 py-3 rounded-xl bg-slate-900/80 border @error('email') border-rose-500 @else border-slate-700/80 @enderror text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-rose-500 text-sm transition"
                               placeholder="alex@example.com">
                        @error('email')
                            <p class="mt-1 text-xs text-rose-400 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Initial Password -->
                    <div>
                        <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">Initial Password</label>
                        <input type="password" name="password" id="password" required
                               class="w-full px-4 py-3 rounded-xl bg-slate-900/80 border @error('password') border-rose-500 @else border-slate-700/80 @enderror text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-rose-500 text-sm transition"
                               placeholder="Minimum 8 characters">
                        @error('password')
                            <p class="mt-1 text-xs text-rose-400 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phone & City -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="phone" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">Phone Number</label>
                            <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                                   class="w-full px-4 py-3 rounded-xl bg-slate-900/80 border @error('phone') border-rose-500 @else border-slate-700/80 @enderror text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-rose-500 text-sm transition"
                                   placeholder="+91 9876543210">
                            @error('phone')
                                <p class="mt-1 text-xs text-rose-400 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="city" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">City</label>
                            <input type="text" name="city" id="city" value="{{ old('city') }}"
                                   class="w-full px-4 py-3 rounded-xl bg-slate-900/80 border @error('city') border-rose-500 @else border-slate-700/80 @enderror text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-rose-500 text-sm transition"
                                   placeholder="Mumbai, Bangalore...">
                            @error('city')
                                <p class="mt-1 text-xs text-rose-400 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Address -->
                    <div>
                        <label for="address" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">Address / Showroom Location</label>
                        <input type="text" name="address" id="address" value="{{ old('address') }}"
                               class="w-full px-4 py-3 rounded-xl bg-slate-900/80 border @error('address') border-rose-500 @else border-slate-700/80 @enderror text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-rose-500 text-sm transition"
                               placeholder="100 Speedway Blvd, Suite 2">
                        @error('address')
                            <p class="mt-1 text-xs text-rose-400 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Bio / Showroom Notes -->
                    <div>
                        <label for="bio" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">Biography & Notes</label>
                        <textarea name="bio" id="bio" rows="3"
                                  class="w-full px-4 py-3 rounded-xl bg-slate-900/80 border @error('bio') border-rose-500 @else border-slate-700/80 @enderror text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-rose-500 text-sm transition"
                                  placeholder="Initial showroom notes, role scope, or background...">{{ old('bio') }}</textarea>
                        @error('bio')
                            <p class="mt-1 text-xs text-rose-400 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit & Cancel Buttons -->
                    <div class="flex items-center justify-end gap-4 pt-4 border-t border-slate-800">
                        <a href="{{ route('admin.users.index') }}" class="px-5 py-3 rounded-xl bg-slate-900 hover:bg-slate-800 text-slate-300 text-sm font-medium transition border border-slate-800">
                            Cancel
                        </a>
                        <button type="submit" id="btn-admin-submit-user"
                                class="px-6 py-3 rounded-xl bg-gradient-to-r from-rose-600 to-rose-500 hover:from-rose-500 hover:to-rose-400 text-white font-bold text-sm shadow-xl shadow-rose-600/30 transition">
                            Create User & Open Details
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
