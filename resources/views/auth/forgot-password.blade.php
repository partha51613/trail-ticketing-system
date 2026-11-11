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
                <h1 class="text-2xl font-extrabold tracking-tight text-white mt-4">{{ __('Reset Password') }}</h1>
                <p class="text-xs font-mono uppercase tracking-widest text-slate-400 mt-1">Trail Account Recovery</p>
            </div>

            <!-- Card -->
            <div class="bg-slate-900/80 backdrop-blur-xl border border-slate-800/90 rounded-2xl p-6 sm:p-8 shadow-2xl shadow-black/50">
                <p class="text-sm text-slate-400 leading-relaxed mb-6">
                    {{ __('Forgot your password? No problem. Enter your registered email address and we will send you a secure link to reset it.') }}
                </p>

                <!-- Session Status -->
                <x-auth-session-status class="mb-4 text-sm text-emerald-400 bg-emerald-950/40 border border-emerald-800/50 rounded-xl p-3" :status="session('status')" />

                <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                    @csrf

                    <!-- Email Address -->
                    <div>
                        <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                            {{ __('Work Email') }}
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                                </svg>
                            </div>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                                placeholder="name@company.com"
                                class="w-full pl-10 pr-4 py-2.5 text-sm !bg-slate-900 !text-white placeholder-slate-500 rounded-xl border border-slate-750 focus:outline-none focus:ring-2 focus:ring-orange-500/30 focus:border-orange-500 transition-all" />
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-2 text-xs text-rose-400" />
                    </div>

                    <!-- Submit Button -->
                    <button type="submit"
                        class="w-full py-2.5 px-4 rounded-xl bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-slate-950 font-bold text-sm tracking-wide shadow-lg shadow-orange-500/20 hover:shadow-orange-500/30 active:scale-[0.99] transition-all flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <span>{{ __('Email Password Reset Link') }}</span>
                    </button>

                    <!-- Back to Login -->
                    <div class="pt-3 text-center border-t border-slate-800/80">
                        <a href="{{ route('login') }}" class="text-xs font-semibold text-slate-400 hover:text-orange-400 transition-colors inline-flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            <span>{{ __('Back to login') }}</span>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
