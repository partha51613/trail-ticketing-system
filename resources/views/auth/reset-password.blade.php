<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center p-4 sm:p-6 lg:p-8 bg-slate-950 text-slate-100 relative overflow-hidden">
        <!-- Ambient Glow Spotlights -->
        <div class="absolute -top-40 -left-40 w-96 h-96 bg-orange-500/15 rounded-full blur-[120px] pointer-events-none"></div>
        <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-indigo-600/15 rounded-full blur-[140px] pointer-events-none"></div>

        <div class="w-full max-w-md relative z-10">
            <!-- Brand Logo -->
            <div class="flex flex-col items-center mb-8">
                <a href="{{ route('login') }}" class="group flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-orange-600 via-orange-500 to-amber-400 p-[1px] shadow-lg shadow-orange-500/25 group-hover:scale-105 transition-transform">
                        <div class="w-full h-full bg-slate-950/90 rounded-[15px] flex items-center justify-center backdrop-blur-sm">
                            <svg class="w-6 h-6 text-orange-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <polygon points="3 11 22 2 13 21 11 13 3 11" fill="currentColor" fill-opacity="0.15"></polygon>
                            </svg>
                        </div>
                    </div>
                </a>
                <h1 class="text-2xl font-extrabold tracking-tight text-white mt-4">{{ __('Set New Password') }}</h1>
                <p class="text-xs font-mono uppercase tracking-widest text-slate-400 mt-1">Trail Security</p>
            </div>

            <!-- Card -->
            <div class="bg-slate-900/80 backdrop-blur-xl border border-slate-800/90 rounded-2xl p-6 sm:p-8 shadow-2xl shadow-black/50">
                <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
                    @csrf

                    <!-- Password Reset Token -->
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    <!-- Email Address -->
                    <div>
                        <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                            {{ __('Work Email') }}
                        </label>
                        <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username"
                            class="w-full px-4 py-2.5 text-sm !bg-slate-900 !text-white placeholder-slate-500 rounded-xl border border-slate-750 focus:outline-none focus:ring-2 focus:ring-orange-500/30 focus:border-orange-500 transition-all" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2 text-xs text-rose-400" />
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                            {{ __('New Password') }}
                        </label>
                        <input id="password" type="password" name="password" required autocomplete="new-password"
                            placeholder="••••••••"
                            class="w-full px-4 py-2.5 text-sm !bg-slate-900 !text-white placeholder-slate-500 rounded-xl border border-slate-750 focus:outline-none focus:ring-2 focus:ring-orange-500/30 focus:border-orange-500 transition-all" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2 text-xs text-rose-400" />
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                            {{ __('Confirm Password') }}
                        </label>
                        <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                            placeholder="••••••••"
                            class="w-full px-4 py-2.5 text-sm !bg-slate-900 !text-white placeholder-slate-500 rounded-xl border border-slate-750 focus:outline-none focus:ring-2 focus:ring-orange-500/30 focus:border-orange-500 transition-all" />
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-xs text-rose-400" />
                    </div>

                    <!-- Submit Button -->
                    <button type="submit"
                        class="w-full py-2.5 px-4 rounded-xl bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-slate-950 font-bold text-sm tracking-wide shadow-lg shadow-orange-500/20 hover:shadow-orange-500/30 active:scale-[0.99] transition-all flex items-center justify-center gap-2">
                        <span>{{ __('Reset Password') }}</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
