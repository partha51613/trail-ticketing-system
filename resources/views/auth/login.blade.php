<x-guest-layout>
    <div class="min-h-screen flex flex-col lg:flex-row bg-slate-950 text-slate-100">
        <!-- ========================================== -->
        <!-- LEFT COLUMN: The "TRAIL" Vector Hero Scene -->
        <!-- ========================================== -->
        <div
            class="relative hidden lg:flex lg:w-[54%] flex-col justify-between p-12 xl:p-16 overflow-hidden bg-[#070b14] border-r border-slate-850">
            <!-- Ambient Glow Spotlights -->
            <div
                class="absolute -top-40 -left-40 w-[30rem] h-[30rem] bg-orange-500/15 rounded-full blur-[120px] pointer-events-none">
            </div>
            <div
                class="absolute top-1/2 -right-32 w-[28rem] h-[28rem] bg-indigo-600/15 rounded-full blur-[140px] pointer-events-none">
            </div>
            <div
                class="absolute -bottom-40 left-1/3 w-[26rem] h-[26rem] bg-emerald-500/10 rounded-full blur-[130px] pointer-events-none">
            </div>

            <!-- Top Header / Logo -->
            <div class="relative z-10 flex items-center gap-3.5">
                <div
                    class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-orange-600 via-orange-500 to-amber-400 p-[1px] shadow-lg shadow-orange-500/25">
                    <div
                        class="w-full h-full bg-slate-950/90 rounded-[15px] flex items-center justify-center backdrop-blur-sm">
                        <!-- Custom TRAIL Geometric Compass/Path Mark -->
                        <svg class="w-6 h-6 text-orange-400" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <polygon points="3 11 22 2 13 21 11 13 3 11" fill="currentColor" fill-opacity="0.15">
                            </polygon>
                        </svg>
                    </div>
                </div>
                <div>
                    <div class="text-xl font-extrabold tracking-wider text-white flex items-center gap-2">
                        <span>TRAIL</span>
                        <span class="inline-block w-1.5 h-1.5 rounded-full bg-orange-500"></span>
                    </div>
                    <div class="text-[11px] font-mono uppercase tracking-widest text-slate-400">Issue & Project Engine
                    </div>
                </div>
            </div>

            <!-- Vector Illustration Centerpiece: "The Living Trail" -->
            <div class="relative z-10 my-auto py-8">
                <div class="relative w-full max-w-lg mx-auto">
                    <!-- High-tech SVG Contour / Trail Canvas -->
                    <svg class="w-full h-80 overflow-visible" viewBox="0 0 500 320" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <!-- Subtle Background Elevation Grids (Topography) -->
                        <path d="M10 260 C 120 290, 280 230, 490 270" stroke="#334155" stroke-width="1"
                            stroke-dasharray="4 6" opacity="0.3" />
                        <path d="M10 200 C 150 160, 310 240, 490 180" stroke="#334155" stroke-width="1"
                            stroke-dasharray="4 6" opacity="0.3" />
                        <path d="M10 120 C 130 150, 320 80, 490 110" stroke="#334155" stroke-width="1"
                            stroke-dasharray="4 6" opacity="0.25" />

                        <!-- Glow Effect for Main Trail Line -->
                        <path d="M 50 250 C 150 250, 130 150, 240 150 C 350 150, 330 60, 440 60" stroke="#f97316"
                            stroke-width="8" stroke-linecap="round" opacity="0.2" filter="blur(8px)" />

                        <!-- Active Trail Line -->
                        <path d="M 50 250 C 150 250, 130 150, 240 150 C 350 150, 330 60, 440 60"
                            stroke="url(#trailGradient)" stroke-width="3" stroke-linecap="round" />

                        <!-- Gradients definition -->
                        <defs>
                            <linearGradient id="trailGradient" x1="0%" y1="100%" x2="100%"
                                y2="0%">
                                <stop offset="0%" stop-color="#ea580c" />
                                <stop offset="45%" stop-color="#f97316" />
                                <stop offset="75%" stop-color="#fbbf24" />
                                <stop offset="100%" stop-color="#10b981" />
                            </linearGradient>
                        </defs>

                        <!-- Node 1: Origin / Backlog (X: 50, Y: 250) -->
                        <g transform="translate(50, 250)">
                            <circle r="12" fill="#0f172a" stroke="#64748b" stroke-width="2" />
                            <circle r="4" fill="#94a3b8" />
                        </g>

                        <!-- Node 2: In Progress / Active (X: 190, Y: 185) -->
                        <g transform="translate(190, 185)">
                            <circle r="16" fill="#f97316" fill-opacity="0.15" />
                            <circle r="12" fill="#0f172a" stroke="#f97316" stroke-width="2.5" />
                            <circle r="4" fill="#f97316" />
                        </g>

                        <!-- Node 3: Review / Milestone (X: 300, Y: 110) -->
                        <g transform="translate(300, 110)">
                            <circle r="12" fill="#0f172a" stroke="#fbbf24" stroke-width="2" />
                            <circle r="4" fill="#fbbf24" />
                        </g>

                        <!-- Node 4: Resolved / Goal (X: 440, Y: 60) -->
                        <g transform="translate(440, 60)">
                            <circle r="20" fill="#10b981" fill-opacity="0.15" class="animate-pulse" />
                            <circle r="14" fill="#0f172a" stroke="#10b981" stroke-width="2.5" />
                            <!-- Checkmark SVG -->
                            <path d="M -4 0 L -1 3 L 4 -3" stroke="#10b981" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </g>
                    </svg>

                    <!-- Interactive Floating Glass Cards along the Trail -->
                    <div
                        class="absolute left-2 bottom-4 p-2.5 rounded-xl bg-slate-900/80 border border-slate-700/60 backdrop-blur-md shadow-xl flex items-center gap-2.5">
                        <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                        <div class="text-[11px] font-mono text-slate-300">TR-102 • Triaged</div>
                    </div>

                    <div
                        class="absolute left-36 top-28 p-2.5 rounded-xl bg-slate-900/90 border border-orange-500/40 backdrop-blur-md shadow-xl shadow-orange-500/10 flex items-center gap-2.5">
                        <span class="relative flex h-2 w-2">
                            <span
                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-orange-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-orange-500"></span>
                        </span>
                        <div>
                            <div class="text-[11px] font-semibold text-white">Active Sprint Flow</div>
                            <div class="text-[10px] text-orange-300/80 font-mono">3 Assignees Working</div>
                        </div>
                    </div>

                    <div
                        class="absolute right-4 top-2 p-2.5 rounded-xl bg-slate-900/85 border border-emerald-500/40 backdrop-blur-md shadow-xl shadow-emerald-500/10 flex items-center gap-2.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                        <div class="text-[11px] font-semibold text-emerald-300 font-mono">Resolved & Verified</div>
                    </div>
                </div>

                <!-- Headline Copy -->
                <div class="mt-8">
                    <h1 class="text-3xl xl:text-4xl font-extrabold text-white tracking-tight leading-tight">
                        Navigate issues from report to resolution with <span
                            class="text-transparent bg-clip-text bg-gradient-to-r from-orange-400 via-amber-300 to-emerald-300">total
                            velocity.</span>
                    </h1>
                    <p class="mt-3 text-sm text-slate-400 max-w-md leading-relaxed">
                        Precision ticket dispatching, state-machine transitions, and team workload orchestration built
                        for engineering speed.
                    </p>
                </div>
            </div>

            <!-- Left Footer Metrics -->
            <div
                class="relative z-10 flex items-center justify-between text-xs text-slate-500 border-t border-slate-800/80 pt-6">
                <div class="font-mono text-[11px] text-slate-500">
                    TRAIL v1.1
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- RIGHT COLUMN: Sleek Dark Login Interface   -->
        <!-- ========================================== -->
        <div class="w-full lg:w-[46%] flex flex-col justify-between p-6 sm:p-12 lg:p-14 xl:p-18 bg-slate-950">
            <!-- Mobile Brand Bar -->
            <div class="flex lg:hidden items-center justify-between mb-8 pb-4 border-b border-slate-800">
                <div class="flex items-center gap-3">
                    <div
                        class="w-9 h-9 rounded-xl bg-gradient-to-tr from-orange-600 to-amber-500 flex items-center justify-center text-white font-bold shadow-md shadow-orange-500/20">
                        <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.5">
                            <polygon points="3 11 22 2 13 21 11 13 3 11" fill="currentColor" fill-opacity="0.2">
                            </polygon>
                        </svg>
                    </div>
                    <span class="text-lg font-extrabold tracking-wider text-white">TRAIL</span>
                </div>
                <span
                    class="text-[10px] font-mono uppercase tracking-wider px-2.5 py-1 rounded-full bg-slate-900 border border-slate-800 text-slate-400">
                    Sign In
                </span>
            </div>

            <!-- Login Form Container -->
            <div class="my-auto max-w-md w-full mx-auto">
                <div class="mb-8">
                    <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white">Welcome back</h2>
                    <p class="text-sm text-slate-400 mt-2">
                        Enter your credentials to access your workspace.
                    </p>
                </div>

                <!-- Session Status Notice -->
                <x-auth-session-status class="mb-6" :status="session('status')" />

                <!-- Login Form -->
                <form method="POST" action="{{ route('login') }}" class="space-y-5" x-data="{ showPassword: false }">
                    @csrf

                    <!-- Email Field -->
                    <div>
                        <label for="email"
                            class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">
                            {{ __('Email address') }}
                        </label>
                        <div class="relative rounded-xl shadow-xs">
                            <div
                                class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                                </svg>
                            </div>
                            <input id="email"
                                class="block w-full pl-10 pr-4 py-2.5 text-sm bg-slate-900/90 text-white rounded-xl border border-slate-800 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 placeholder-slate-500 transition-all outline-none"
                                type="email" name="email" value="{{ old('email') }}"
                                placeholder="developer@company.com" required autofocus autocomplete="username" />
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
                    </div>

                    <!-- Password Field with Show/Hide Toggle -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label for="password"
                                class="block text-xs font-semibold uppercase tracking-wider text-slate-300">
                                {{ __('Password') }}
                            </label>
                            @if (Route::has('password.request'))
                                <a class="text-xs font-medium text-orange-400 hover:text-orange-300 transition-colors"
                                    href="{{ route('password.request') }}">
                                    {{ __('Forgot password?') }}
                                </a>
                            @endif
                        </div>
                        <div class="relative rounded-xl shadow-xs">
                            <div
                                class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <input id="password"
                                class="block w-full pl-10 pr-10 py-2.5 text-sm bg-slate-900/90 text-white rounded-xl border border-slate-800 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 placeholder-slate-500 transition-all outline-none"
                                :type="showPassword ? 'text' : 'password'" name="password" placeholder="••••••••••••"
                                required autocomplete="current-password" />

                            <!-- Toggle Button -->
                            <button type="button" @click="showPassword = !showPassword"
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-500 hover:text-slate-300 focus:outline-none transition-colors"
                                tabindex="-1">
                                <svg x-show="!showPassword" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg x-show="showPassword" x-cloak class="h-4 w-4" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                </svg>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
                    </div>

                    <!-- Remember Me Checkbox -->
                    <div class="flex items-center justify-between pt-1">
                        <label for="remember_me" class="inline-flex items-center cursor-pointer group">
                            <input id="remember_me" type="checkbox"
                                class="rounded border-slate-700 bg-slate-900 text-orange-500 shadow-xs focus:ring-orange-500/30 w-4 h-4 transition cursor-pointer"
                                name="remember">
                            <span
                                class="ms-2.5 text-xs text-slate-400 group-hover:text-slate-200 transition-colors select-none">{{ __('Remember me on this device') }}</span>
                        </label>
                    </div>

                    <!-- Submit Button with Sliding Arrow Vector -->
                    <div class="pt-2">
                        <button type="submit"
                            class="group w-full relative flex justify-center items-center py-2.5 px-4 rounded-xl shadow-lg shadow-orange-500/20 text-sm font-semibold text-white bg-gradient-to-r from-orange-500 via-orange-600 to-amber-600 hover:from-orange-600 hover:to-amber-700 active:scale-[0.99] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-slate-950 focus:ring-orange-500 transition-all duration-150">
                            <span>{{ __('Sign In to Workspace') }}</span>
                            <svg class="w-4 h-4 ml-2 transition-transform duration-200 group-hover:translate-x-1"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Right Column Footer -->
            <div
                class="flex items-center justify-between text-[11px] text-slate-500 border-t border-slate-900 pt-6 mt-8">
                <span>&copy; {{ date('Y') }} TRAIL Engine.</span>

            </div>
        </div>
    </div>
</x-guest-layout>
