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

<body class="font-sans antialiased bg-gray-50/70 text-gray-900" x-data="{
    sidebarOpen: localStorage.getItem('sidebarOpen') !== 'false',
    toggleSidebar() {
        this.sidebarOpen = !this.sidebarOpen;
        localStorage.setItem('sidebarOpen', this.sidebarOpen);
    }
}">

    <div class="min-h-screen flex">

        <!-- Persistent Push-Sidebar (Expands to 64 (256px) or collapses to slim 16 (64px) icon rail) -->
        <div class="shrink-0 h-screen sticky top-0 z-40 bg-white border-r border-gray-200/80 shadow-xs flex flex-col transition-all duration-200"
            :class="sidebarOpen ? 'w-64' : 'w-16'">
            @include('layouts.sidebar')
        </div>

        <!-- Main Content Area -->
        <div class="flex flex-col flex-1 min-w-0 transition-all duration-200">

            <!-- Top Header with Search & User Status -->
            <header class="h-16 bg-white/90 backdrop-blur-md border-b border-gray-100 sticky top-0 z-30">
                <div class="max-w-7xl mx-auto h-full px-4 sm:px-6 lg:px-8 flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
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
                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-gray-100 text-gray-600">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span>{{ ucfirst(Auth::user()->role) }}</span>
                        </span>
                    </div>
                </div>
            </header>

            <!-- Page Heading (Properly aligned with max-w-7xl) -->
            @isset($header)
                <div class="bg-white border-b border-gray-100">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                        {{ $header }}
                    </div>
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
