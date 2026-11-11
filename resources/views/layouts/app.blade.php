<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>


    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
    <script>
        // Prevent layout shift/flash on reload
        if (localStorage.getItem('sidebarOpen') === 'false') {
            document.documentElement.classList.add('sidebar-collapsed');
        }
    </script>
    <script>
        // 1. Dark Mode check (runs before body renders to avoid white flicker)
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia(
                '(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }

        // 2. Prevent sidebar layout shift/flash on reload
        if (localStorage.getItem('sidebarOpen') === 'false') {
            document.documentElement.classList.add('sidebar-collapsed');
        }
    </script>
</head>

<body class="font-sans antialiased bg-gray-50/70 text-gray-900 dark:bg-slate-950 dark:text-slate-100"
    x-data="{
        sidebarOpen: localStorage.getItem('sidebarOpen') !== 'false',
        toggleSidebar() {
            this.sidebarOpen = !this.sidebarOpen;
            localStorage.setItem('sidebarOpen', this.sidebarOpen);
            if (this.sidebarOpen) {
                document.documentElement.classList.remove('sidebar-collapsed');
            } else {
                document.documentElement.classList.add('sidebar-collapsed');
            }
        }
    }">

    <div class="min-h-screen flex">

        <!-- Persistent Push-Sidebar -->
        <div class="shrink-0 h-screen sticky top-0 z-40 bg-white dark:bg-slate-900 border-r border-gray-200/80 dark:border-slate-800 shadow-xs flex flex-col w-64 html-collapsed:w-16 transition-[width] duration-200"
            :class="sidebarOpen ? '!w-64' : '!w-16'">
            @include('layouts.sidebar')
        </div>

        <!-- Main Content Area -->
        <div class="flex flex-col flex-1 min-w-0">

            <!-- Top Header with Search & User Status -->
            <header
                class="h-16 bg-white/90 dark:bg-slate-900/90 backdrop-blur-md border-b border-gray-100 dark:border-slate-800 sticky top-0 z-30">
                <div class="max-w-7xl mx-auto h-full px-4 sm:px-6 lg:px-8 flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <!-- Global Search -->
                        <form action="{{ route('tickets.index') }}" method="GET" class="relative">
                            <div class="relative">
                                <div
                                    class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 dark:text-slate-500">
                                    <x-heroicon-m-magnifying-glass class="w-4 h-4" />
                                </div>
                                <input type="text" name="search" value="{{ request('search') }}"
                                    placeholder="{{ __('Search all tickets (e.g. WEB-001, auth)...') }}"
                                    class="w-64 sm:w-80 pl-9 pr-4 py-1.5 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200/90 dark:border-slate-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-slate-400 focus:bg-white dark:focus:bg-slate-850 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all">
                            </div>
                        </form>
                    </div>

                    <!-- Right Header Actions -->
                    <div class="flex items-center gap-3">
                        <!-- Dark Mode Toggle Button -->
                        <button type="button"
                            @click="
                                document.documentElement.classList.toggle('dark');
                                localStorage.setItem('theme', document.documentElement.classList.contains('dark') ? 'dark' : 'light');
                            "
                            class="p-2 rounded-xl text-gray-400 hover:text-gray-700 hover:bg-gray-100 dark:text-slate-400 dark:hover:text-amber-400 dark:hover:bg-slate-800 transition-colors"
                            title="{{ __('Toggle Dark Mode') }}">
                            <!-- Sun Icon (Shown in Dark Mode) -->
                            <x-heroicon-o-sun class="w-5 h-5 hidden dark:block text-amber-400" />
                            <!-- Moon Icon (Shown in Light Mode) -->
                            <x-heroicon-o-moon class="w-5 h-5 block dark:hidden text-gray-500" />
                        </button>

                        <!-- User Role Badge -->
                        <span
                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-gray-100 text-gray-600 dark:bg-slate-800 dark:text-slate-300">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span>{{ ucfirst(Auth::user()->role) }}</span>
                        </span>
                    </div>
                </div>
            </header>

            <!-- Page Heading (Properly aligned with max-w-7xl) -->
            @isset($header)
                <div class="bg-white dark:bg-slate-900 border-b border-gray-100 dark:border-slate-800">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                        {{ $header }}
                    </div>
                </div>
            @endisset

            <!-- Flash Messages -->
            <div class="max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 pt-6">
                @if (session('success'))
                    <div
                        class="mb-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 p-4 border border-emerald-200/80 dark:border-emerald-800/60 flex items-center justify-between text-xs font-semibold text-emerald-800 dark:text-emerald-300 shadow-xs">
                        <div class="flex items-center gap-2.5">
                            <x-heroicon-s-check-circle class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0" />
                            <span>{{ session('success') }}</span>
                        </div>
                    </div>
                @endif

                @if (session('error'))
                    <div
                        class="mb-4 rounded-xl bg-rose-50 dark:bg-rose-950/40 p-4 border border-rose-200/80 dark:border-rose-800/60 flex items-center justify-between text-xs font-semibold text-rose-800 dark:text-rose-300 shadow-xs">
                        <div class="flex items-center gap-2.5">
                            <x-heroicon-s-x-circle class="w-5 h-5 text-rose-600 shrink-0" />
                            <span>{{ session('error') }}</span>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Page Main Content -->
            <main class="flex-1 pb-12">
                {{ $slot }}
            </main>
        </div>

    </div>

    <!-- Global Application Confirmation Modal -->
    <x-confirm-modal />
</body>



</html>
