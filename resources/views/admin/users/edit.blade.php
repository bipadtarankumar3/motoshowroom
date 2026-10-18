<x-app-layout :title="'Edit ' . $user->name . ' - User Details | MotoShowroom SaaS'">
    @if(auth()->check() && auth()->user()->isAdmin())
        @include('admin.partials.nav')
    @endif
    <div class="min-h-screen bg-slate-50 text-slate-900 py-8 px-4 sm:px-6 lg:px-8 xl:px-10">
        <div class="w-full space-y-8">

            <!-- Breadcrumbs -->
            <div class="flex items-center gap-2 text-xs text-slate-500">
                @if (auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-slate-900 transition">Admin</a>
                    <span>/</span>
                    <a href="{{ route('admin.users.index') }}" class="hover:text-slate-900 transition">Users</a>
                @else
                    <a href="{{ route('dashboard') }}" class="hover:text-slate-900 transition">Dashboard</a>
                    <span>/</span>
                    <span class="text-slate-600">My Profile</span>
                @endif
                <span>/</span>
                <a href="{{ auth()->user()->isAdmin() ? route('admin.users.show', $user) : route('users.show', $user) }}" class="hover:text-slate-900 transition">{{ $user->name }}</a>
                <span>/</span>
                <span class="text-rose-600 font-semibold">Edit</span>
            </div>

            <!-- Edit Form Card -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs relative overflow-hidden">
                <div class="border-b border-slate-100 pb-5 mb-6">
                    <h1 class="text-2xl font-black font-display text-slate-900">Edit User Profile</h1>
                    <p class="text-sm text-slate-500 mt-1">Update profile information and details for {{ $user->name }}.</p>
                </div>

                <form method="POST" action="{{ route('users.update', $user) }}" class="space-y-6" id="form-edit-user">
                    @csrf
                    @method('PUT')

                    <!-- Role & Status (Admins Only) -->
                    @if (auth()->user()->isAdmin())
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 rounded-2xl bg-slate-50 border border-slate-200/80">
                            <div>
                                <label for="role" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">User Role (Admin Only)</label>
                                <select name="role" id="role" class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-300 text-slate-900 text-sm focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 focus:outline-none transition">
                                    <option value="user" {{ old('role', $user->role) === 'user' ? 'selected' : '' }}>Regular User</option>
                                    <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Administrator</option>
                                </select>
                                @error('role')
                                    <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="status" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Account Status</label>
                                <select name="status" id="status" class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-300 text-slate-900 text-sm focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 focus:outline-none transition">
                                    <option value="active" {{ old('status', $user->status) === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status', $user->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="suspended" {{ old('status', $user->status) === 'suspended' ? 'selected' : '' }}>Suspended</option>
                                </select>
                                @error('status')
                                    <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    @endif

                    <!-- Name & Email Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <!-- Full Name -->
                        <div>
                            <label for="name" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">Full Name</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                                   class="w-full px-4 py-2.5 rounded-xl bg-white border @error('name') border-rose-500 @else border-slate-300 @enderror text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 text-sm transition">
                            @error('name')
                                <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email Address -->
                        <div>
                            <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">Email Address</label>
                            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                                   class="w-full px-4 py-2.5 rounded-xl bg-white border @error('email') border-rose-500 @else border-slate-300 @enderror text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 text-sm transition">
                            @error('email')
                                <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Phone & City -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label for="phone" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">Phone Number</label>
                            <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}"
                                   class="w-full px-4 py-2.5 rounded-xl bg-white border @error('phone') border-rose-500 @else border-slate-300 @enderror text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 text-sm transition"
                                   placeholder="+91 9876543210">
                            @error('phone')
                                <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="city" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">City</label>
                            <input type="text" name="city" id="city" value="{{ old('city', $user->city) }}"
                                   class="w-full px-4 py-2.5 rounded-xl bg-white border @error('city') border-rose-500 @else border-slate-300 @enderror text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 text-sm transition"
                                   placeholder="Mumbai, New Delhi...">
                            @error('city')
                                <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Address -->
                    <div>
                        <label for="address" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">Address / Showroom Location</label>
                        <input type="text" name="address" id="address" value="{{ old('address', $user->address) }}"
                               class="w-full px-4 py-2.5 rounded-xl bg-white border @error('address') border-rose-500 @else border-slate-300 @enderror text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 text-sm transition"
                               placeholder="123 Motorcycle Highway, Sector 4">
                        @error('address')
                            <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Bio / Showroom Notes -->
                    <div>
                        <label for="bio" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">Bio / Showroom Notes</label>
                        <textarea name="bio" id="bio" rows="3"
                                  class="w-full px-4 py-2.5 rounded-xl bg-white border @error('bio') border-rose-500 @else border-slate-300 @enderror text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 text-sm transition leading-relaxed"
                                  placeholder="Tell us about yourself or your showroom...">{{ old('bio', $user->bio) }}</textarea>
                        @error('bio')
                            <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Change Password (Optional) -->
                    <div class="pt-4 border-t border-slate-100">
                        <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                            New Password <span class="text-slate-500 normal-case">(leave blank to keep current)</span>
                        </label>
                        <input type="password" name="password" id="password"
                               class="w-full px-4 py-2.5 rounded-xl bg-white border @error('password') border-rose-500 @else border-slate-300 @enderror text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 text-sm transition"
                               placeholder="Enter new password if changing">
                        @error('password')
                            <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit & Cancel Buttons -->
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                        <a href="{{ auth()->user()->isAdmin() ? route('admin.users.show', $user) : route('users.show', $user) }}" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition border border-slate-300 shadow-2xs">
                            Cancel
                        </a>
                        <button type="submit" id="btn-save-user-details"
                                class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-rose-600 to-rose-500 hover:from-rose-500 hover:to-rose-400 text-white font-bold text-xs uppercase tracking-wider shadow-md shadow-rose-600/20 transition cursor-pointer">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
    @if(auth()->check() && auth()->user()->isAdmin())
        @include('admin.partials.nav-close')
    @endif
</x-app-layout>
