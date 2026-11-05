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
</head>

<body class="font-sans antialiased bg-gray-50/70 text-gray-900" x-data="{ sidebarOpen: false }">
    <div class="min-h-screen flex">

        <!-- 1. Mobile Sidebar Backdrop & Drawer (Off-canvas) -->
        <div x-show="sidebarOpen" x-cloak class="relative z-50 md:hidden" role="dialog" aria-modal="true">
            <!-- Dim backdrop -->
            <div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-linear duration-200"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity ease-linear duration-200" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0" @click="sidebarOpen = false"
                class="fixed inset-0 bg-gray-900/60 backdrop-blur-xs"></div>

            <!-- Slide-over panel -->
            <div class="fixed inset-0 flex z-50">
                <div x-show="sidebarOpen" x-transition:enter="transition ease-in-out duration-200 transform"
                    x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
                    x-transition:leave="transition ease-in-out duration-200 transform"
                    x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full"
                    class="relative flex-1 flex flex-col max-w-xs w-full bg-white shadow-2xl">

                    <!-- Close button on mobile -->
                    <div class="absolute top-3 right-3">
                        <button type="button" @click="sidebarOpen = false"
                            class="p-2 rounded-xl text-gray-400 hover:text-gray-600 hover:bg-gray-100">
                            <x-heroicon-m-x-mark class="w-5 h-5" />
                        </button>
                    </div>

                    @include('layouts.sidebar')
                </div>
            </div>
        </div>

        <!-- 2. Desktop Persistent Sidebar (Fixed left) -->
        <div class="hidden md:flex md:w-64 md:flex-col md:fixed md:inset-y-0 z-40">
            @include('layouts.sidebar')
        </div>

        <!-- 3. Main Content Area (Offset by sidebar width on desktop) -->
        <div class="md:pl-64 flex flex-col flex-1 min-w-0">

            <!-- Slim Top Header (Mobile trigger & Search) -->
            <header
                class="h-16 bg-white/80 backdrop-blur-md border-b border-gray-100 sticky top-0 z-30 px-4 sm:px-6 flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <!-- Mobile Hamburger -->
                    <button type="button" @click="sidebarOpen = true"
                        class="md:hidden p-2 rounded-xl text-gray-500 hover:text-gray-900 hover:bg-gray-100">
                        <x-heroicon-m-bars-3 class="w-5 h-5" />
                    </button>

                    <!-- Global Search -->
                    <form action="{{ route('tickets.index') }}" method="GET" class="relative">
                        <div class="relative">
                            <div
                                class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <x-heroicon-m-magnifying-glass class="w-4 h-4" />
                            </div>
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="{{ __('Search all tickets (e.g. WEB-001, auth)...') }}"
                                class="w-64 sm:w-80 pl-9 pr-4 py-1.5 text-xs rounded-xl bg-gray-50 border border-gray-200/90 text-gray-900 placeholder-gray-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all">
                        </div>
                    </form>
                </div>

                <!-- Right Header Actions -->
                <div class="flex items-center gap-2">
                    <span
                        class="hidden sm:inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-gray-100 text-gray-600">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>{{ ucfirst(Auth::user()->role) }}</span>
                    </span>
                </div>
            </header>

            <!-- Page Heading (Optional, provided by individual blade views) -->
            @isset($header)
                <div class="bg-white border-b border-gray-100 px-4 sm:px-6 lg:px-8 py-5">
                    {{ $header }}
                </div>
            @endisset

            <!-- Flash Messages -->
            <div class="max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 pt-6">
                @if (session('success'))
                    <div
                        class="mb-4 rounded-xl bg-emerald-50 p-4 border border-emerald-200/80 flex items-center justify-between text-xs font-semibold text-emerald-800 shadow-xs">
                        <div class="flex items-center gap-2.5">
                            <x-heroicon-s-check-circle class="w-5 h-5 text-emerald-600 shrink-0" />
                            <span>{{ session('success') }}</span>
                        </div>
                    </div>
                @endif

                @if (session('error'))
                    <div
                        class="mb-4 rounded-xl bg-rose-50 p-4 border border-rose-200/80 flex items-center justify-between text-xs font-semibold text-rose-800 shadow-xs">
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
</body>

</html>
