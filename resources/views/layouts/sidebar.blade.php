<aside class="flex flex-col h-full bg-white border-r border-gray-200/80 w-64 select-none">
    <!-- Brand / Workspace Header -->
    <!-- Brand / Workspace Header with Collapse Button -->
    <div class="h-16 flex items-center justify-between px-5 border-b border-gray-100 shrink-0">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 group min-w-0">
            <div
                class="w-8 h-8 rounded-xl bg-brand-500 text-white flex items-center justify-center font-bold text-sm shadow-sm shadow-brand-500/30 group-hover:bg-brand-600 transition-all shrink-0">
                T
            </div>
            <div class="flex flex-col min-w-0">
                <span
                    class="text-sm font-bold tracking-tight text-gray-900 group-hover:text-brand-600 transition-colors truncate">
                    {{ config('app.name', 'Ticket Tracker') }}
                </span>
                <span class="text-[10px] font-medium text-gray-400 -mt-0.5">
                    Workspace
                </span>
            </div>
        </a>

        <!-- Collapse Sidebar Button (Visible when sidebar is open) -->
        <button type="button" @click="toggleSidebar()"
            class="p-1.5 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition-colors"
            title="{{ __('Collapse Sidebar') }}">
            <x-heroicon-m-chevron-double-left class="w-4 h-4" />
        </button>
    </div>

    <!-- Quick Action: + New Ticket -->
    <div class="px-4 pt-4 pb-2 shrink-0">
        <a href="{{ route('tickets.create') }}"
            class="flex items-center justify-center gap-2 w-full py-2.5 px-3 text-xs font-semibold text-white bg-brand-500 hover:bg-brand-600 rounded-xl shadow-sm shadow-brand-500/25 transition-all">
            <x-heroicon-m-plus class="w-4 h-4" />
            <span>{{ __('New Ticket') }}</span>
        </a>
    </div>

    <!-- Navigation Groups (Scrollable if needed) -->
    <nav class="flex-1 px-3 py-3 space-y-6 overflow-y-auto">
        <!-- Group 1: Workspace Overview -->
        <div>
            <span class="px-3 text-[10px] font-bold uppercase tracking-wider text-gray-400 block mb-1.5">
                {{ __('Overview') }}
            </span>
            <div class="space-y-1">
                <!-- Dashboard -->
                <a href="{{ route('dashboard') }}"
                    class="flex items-center gap-2.5 px-3 py-2 text-xs font-semibold rounded-xl transition-colors {{ request()->routeIs('dashboard') ? 'bg-brand-50 text-brand-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <x-heroicon-o-squares-2x2
                        class="w-4 h-4 {{ request()->routeIs('dashboard') ? 'text-brand-600' : 'text-gray-400' }}" />
                    <span>{{ __('Dashboard') }}</span>
                </a>

                <!-- Projects -->
                <a href="{{ route('projects.index') }}"
                    class="flex items-center gap-2.5 px-3 py-2 text-xs font-semibold rounded-xl transition-colors {{ request()->routeIs('projects.*') ? 'bg-brand-50 text-brand-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <x-heroicon-o-folder
                        class="w-4 h-4 {{ request()->routeIs('projects.*') ? 'text-brand-600' : 'text-gray-400' }}" />
                    <span>{{ __('Projects') }}</span>
                </a>

                <!-- All Tickets -->
                <a href="{{ route('tickets.index') }}"
                    class="flex items-center gap-2.5 px-3 py-2 text-xs font-semibold rounded-xl transition-colors {{ request()->routeIs('tickets.index') && !request()->hasAny(['assigned_to_id', 'watched', 'created_by_id']) ? 'bg-brand-50 text-brand-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <x-heroicon-o-ticket
                        class="w-4 h-4 {{ request()->routeIs('tickets.index') && !request()->hasAny(['assigned_to_id', 'watched', 'created_by_id']) ? 'text-brand-600' : 'text-gray-400' }}" />
                    <span>{{ __('All Tickets') }}</span>
                </a>
            </div>
        </div>

        <!-- Group 2: My Work -->
        <div>
            <span class="px-3 text-[10px] font-bold uppercase tracking-wider text-gray-400 block mb-1.5">
                {{ __('My Work') }}
            </span>
            <div class="space-y-1">
                <!-- Assigned to Me -->
                <a href="{{ route('tickets.index', ['assigned_to_id' => auth()->id()]) }}"
                    class="flex items-center justify-between px-3 py-2 text-xs font-semibold rounded-xl transition-colors {{ request('assigned_to_id') == auth()->id() ? 'bg-brand-50 text-brand-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <div class="flex items-center gap-2.5">
                        <x-heroicon-o-user
                            class="w-4 h-4 {{ request('assigned_to_id') == auth()->id() ? 'text-brand-600' : 'text-gray-400' }}" />
                        <span>{{ __('Assigned to Me') }}</span>
                    </div>
                </a>

                <!-- Watched Tickets -->
                <a href="{{ route('tickets.index', ['watched' => 1]) }}"
                    class="flex items-center justify-between px-3 py-2 text-xs font-semibold rounded-xl transition-colors {{ request()->boolean('watched') ? 'bg-brand-50 text-brand-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <div class="flex items-center gap-2.5">
                        <x-heroicon-o-bell
                            class="w-4 h-4 {{ request()->boolean('watched') ? 'text-brand-600' : 'text-gray-400' }}" />
                        <span>{{ __('Watched Tickets') }}</span>
                    </div>
                </a>

                <!-- Created by Me -->
                <a href="{{ route('tickets.index', ['created_by_id' => auth()->id()]) }}"
                    class="flex items-center justify-between px-3 py-2 text-xs font-semibold rounded-xl transition-colors {{ request('created_by_id') == auth()->id() ? 'bg-brand-50 text-brand-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <div class="flex items-center gap-2.5">
                        <x-heroicon-o-pencil-square
                            class="w-4 h-4 {{ request('created_by_id') == auth()->id() ? 'text-brand-600' : 'text-gray-400' }}" />
                        <span>{{ __('Created by Me') }}</span>
                    </div>
                </a>
            </div>
        </div>
    </nav>

    <!-- User Profile & Footer -->
    <div class="p-3 border-t border-gray-100 shrink-0 bg-gray-50/50">
        <div
            class="flex items-center justify-between gap-2 p-2 rounded-xl hover:bg-white transition-colors border border-transparent hover:border-gray-200/80">
            <div class="flex items-center gap-2.5 min-w-0">
                <span
                    class="w-8 h-8 rounded-full bg-brand-100 text-brand-700 font-bold text-xs flex items-center justify-center shrink-0 ring-2 ring-white">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </span>
                <div class="min-w-0">
                    <p class="text-xs font-bold text-gray-900 truncate">
                        {{ Auth::user()->name }}
                    </p>
                    <p class="text-[10px] font-medium text-gray-400 uppercase tracking-wider">
                        {{ Auth::user()->role }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-1 shrink-0">
                <!-- Profile Link -->
                <a href="{{ route('profile.edit') }}"
                    class="p-1.5 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition-colors"
                    title="{{ __('Profile') }}">
                    <x-heroicon-o-cog-6-tooth class="w-4 h-4" />
                </a>

                <!-- Log Out Form -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="p-1.5 rounded-lg text-gray-400 hover:text-rose-600 hover:bg-rose-50 transition-colors"
                        title="{{ __('Log Out') }}">
                        <x-heroicon-o-arrow-left-on-rectangle class="w-4 h-4" />
                    </button>
                </form>
            </div>
        </div>
    </div>
</aside>
