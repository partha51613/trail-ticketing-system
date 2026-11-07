<aside class="flex flex-col h-full bg-white select-none transition-all duration-200"
    :class="sidebarOpen ? 'w-64' : 'w-16'">

    <!-- Brand / Workspace Header with Expand/Collapse Action at TOP -->
    <div class="h-16 flex items-center border-b border-gray-100 shrink-0"
        :class="sidebarOpen ? 'justify-between px-4' : 'flex-col justify-center gap-1 py-2'">

        <!-- When Open: Full Logo + Name + Collapse Button -->
        <a x-show="sidebarOpen" x-cloak href="{{ route('dashboard') }}" class="flex items-center gap-2.5 group min-w-0">
            <div
                class="w-8 h-8 rounded-xl bg-brand-500 text-white flex items-center justify-center font-bold text-sm shadow-sm shadow-brand-500/30 group-hover:bg-brand-600 transition-all shrink-0">
                T
            </div>
            <div class="flex flex-col min-w-0 transition-opacity duration-150">
                <span
                    class="text-sm font-bold tracking-tight text-gray-900 group-hover:text-brand-600 transition-colors truncate">
                    {{ config('app.name', 'Ticket Tracker') }}
                </span>
                <span class="text-[10px] font-medium text-gray-400 -mt-0.5">
                    Workspace
                </span>
            </div>
        </a>

        <!-- Collapse Toggle Button (When Open) -->
        <button x-show="sidebarOpen" x-cloak type="button" @click="toggleSidebar()"
            class="p-1.5 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition-colors"
            title="{{ __('Collapse to Mini Rail') }}">
            <x-heroicon-m-chevron-double-left class="w-4 h-4" />
        </button>

        <!-- When Collapsed: Prominent Expand Button right at the TOP -->
        <button x-show="!sidebarOpen" type="button" @click="toggleSidebar()"
            class="group w-10 h-10 rounded-xl text-brand-600 bg-brand-50 hover:bg-brand-50 border border-gray-200/80 hover:border-brand-200 hover:text-brand-600 flex items-center justify-center transition-all shadow-xs"
            title="{{ __('Click to Expand Sidebar') }}">
            <x-heroicon-m-chevron-double-right class="w-4 h-4 transition-transform group-hover:translate-x-0.5" />
        </button>
    </div>

    <!-- Navigation Rail / Menu Items -->
    <nav class="flex-1 py-4 space-y-6 overflow-y-auto px-2" :class="sidebarOpen ? 'px-3' : 'px-2'">

        <!-- Group 1: Workspace Overview -->
        <div>
            <span x-show="sidebarOpen" x-cloak
                class="px-3 text-[10px] font-bold uppercase tracking-wider text-gray-400 block mb-1.5">
                {{ __('Overview') }}
            </span>
            <div class="space-y-1 pt-3">
                <!-- Dashboard -->
                <a href="{{ route('dashboard') }}"
                    class="flex items-center gap-3 py-2 text-xs font-semibold rounded-xl transition-colors {{ request()->routeIs('dashboard') ? 'bg-brand-50 text-brand-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}"
                    :class="sidebarOpen ? 'px-3' : 'justify-center px-0'" title="{{ __('Dashboard') }}">
                    <x-heroicon-o-squares-2x2
                        class="w-5 h-5 shrink-0 {{ request()->routeIs('dashboard') ? 'text-brand-600' : 'text-gray-400' }}" />
                    <span x-show="sidebarOpen" x-cloak class="truncate">{{ __('Dashboard') }}</span>
                </a>

                <!-- Projects -->
                <a href="{{ route('projects.index') }}"
                    class="flex items-center gap-3 py-2 text-xs font-semibold rounded-xl transition-colors {{ request()->routeIs('projects.*') ? 'bg-brand-50 text-brand-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}"
                    :class="sidebarOpen ? 'px-3' : 'justify-center px-0'" title="{{ __('Projects') }}">
                    <x-heroicon-o-folder
                        class="w-5 h-5 shrink-0 {{ request()->routeIs('projects.*') ? 'text-brand-600' : 'text-gray-400' }}" />
                    <span x-show="sidebarOpen" x-cloak class="truncate">{{ __('Projects') }}</span>
                </a>

                <!-- All Tickets -->
                <a href="{{ route('tickets.index') }}"
                    class="flex items-center gap-3 py-2 text-xs font-semibold rounded-xl transition-colors {{ request()->routeIs('tickets.index') && !request()->hasAny(['assigned_to_id', 'watched', 'created_by_id']) ? 'bg-brand-50 text-brand-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}"
                    :class="sidebarOpen ? 'px-3' : 'justify-center px-0'" title="{{ __('All Tickets') }}">
                    <x-heroicon-o-ticket
                        class="w-5 h-5 shrink-0 {{ request()->routeIs('tickets.index') && !request()->hasAny(['assigned_to_id', 'watched', 'created_by_id']) ? 'text-brand-600' : 'text-gray-400' }}" />
                    <span x-show="sidebarOpen" x-cloak class="truncate">{{ __('All Tickets') }}</span>
                </a>

                <!-- Workload Statistics -->
                <a href="{{ route('workload.index') }}"
                    class="flex items-center gap-3 py-2 text-xs font-semibold rounded-xl transition-colors {{ request()->routeIs('workload.*') ? 'bg-brand-50 text-brand-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}"
                    :class="sidebarOpen ? 'px-3' : 'justify-center px-0'" title="{{ __('Workload Statistics') }}">
                    <x-heroicon-o-chart-bar
                        class="w-5 h-5 shrink-0 {{ request()->routeIs('workload.*') ? 'text-brand-600' : 'text-gray-400' }}" />
                    <span x-show="sidebarOpen" x-cloak class="truncate">{{ __('Workload Statistics') }}</span>
                </a>
            </div>
        </div>

        <!-- Group 2: My Work -->
        <div>
            <span x-show="sidebarOpen" x-cloak
                class="px-3 text-[10px] font-bold uppercase tracking-wider text-gray-400 block mb-1.5">
                {{ __('My Work') }}
            </span>
            <div class="space-y-1 pt-3">
                <!-- Assigned to Me -->
                <a href="{{ route('tickets.assigned-to-me') }}"
                    class="flex items-center gap-3 py-2 text-xs font-semibold rounded-xl transition-colors {{ request()->routeIs('tickets.assigned-to-me') || request('assigned_to_id') == auth()->id() ? 'bg-brand-50 text-brand-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}"
                    :class="sidebarOpen ? 'px-3' : 'justify-center px-0'" title="{{ __('Assigned to Me') }}">
                    <x-heroicon-o-user
                        class="w-5 h-5 shrink-0 {{ request()->routeIs('tickets.assigned-to-me') || request('assigned_to_id') == auth()->id() ? 'text-brand-600' : 'text-gray-400' }}" />
                    <span x-show="sidebarOpen" x-cloak class="truncate">{{ __('Assigned to Me') }}</span>
                </a>

                <!-- Watched Tickets -->
                <a href="{{ route('tickets.watched') }}"
                    class="flex items-center gap-3 py-2 text-xs font-semibold rounded-xl transition-colors {{ request()->routeIs('tickets.watched') || request()->boolean('watched') ? 'bg-brand-50 text-brand-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}"
                    :class="sidebarOpen ? 'px-3' : 'justify-center px-0'" title="{{ __('Watched Tickets') }}">
                    <x-heroicon-o-bell
                        class="w-5 h-5 shrink-0 {{ request()->routeIs('tickets.watched') || request()->boolean('watched') ? 'text-brand-600' : 'text-gray-400' }}" />
                    <span x-show="sidebarOpen" x-cloak class="truncate">{{ __('Watched Tickets') }}</span>
                </a>

                <!-- Created by Me -->
                <a href="{{ route('tickets.created-by-me') }}"
                    class="flex items-center gap-3 py-2 text-xs font-semibold rounded-xl transition-colors {{ request()->routeIs('tickets.created-by-me') || request('created_by_id') == auth()->id() ? 'bg-brand-50 text-brand-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}"
                    :class="sidebarOpen ? 'px-3' : 'justify-center px-0'" title="{{ __('Created by Me') }}">
                    <x-heroicon-o-pencil-square
                        class="w-5 h-5 shrink-0 {{ request()->routeIs('tickets.created-by-me') || request('created_by_id') == auth()->id() ? 'text-brand-600' : 'text-gray-400' }}" />
                    <span x-show="sidebarOpen" x-cloak class="truncate">{{ __('Created by Me') }}</span>
                </a>
            </div>
        </div>

    </nav>

    <!-- User Profile & Footer -->
    <div class="border-t border-gray-100 shrink-0 bg-gray-50/50"
        :class="sidebarOpen ? 'p-3' : 'p-2 flex justify-center'">

        <!-- Expanded Profile -->
        <div x-show="sidebarOpen" x-cloak
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
                <a href="{{ route('profile.edit') }}"
                    class="flex items-center justify-center p-1.5 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition-colors"
                    title="{{ __('Profile') }}">
                    <x-heroicon-o-cog-6-tooth class="w-4 h-4" />
                </a>

                <form method="POST" action="{{ route('logout') }}" class="flex items-center">
                    @csrf
                    <button type="submit"
                        class="flex items-center justify-center p-1.5 rounded-lg text-gray-400 hover:text-rose-600 hover:bg-rose-50 transition-colors"
                        title="{{ __('Log Out') }}">
                        <x-heroicon-o-arrow-left-on-rectangle class="w-4 h-4" />
                    </button>
                </form>
            </div>
        </div>

        <!-- Collapsed Mini Avatar with Dropdown / Logout -->
        <div x-show="!sidebarOpen" class="relative group">
            <a href="{{ route('profile.edit') }}"
                class="w-9 h-9 rounded-full bg-brand-100 text-brand-700 font-bold text-xs flex items-center justify-center ring-2 ring-white hover:ring-brand-300 transition-all shadow-xs"
                title="{{ Auth::user()->name }} ({{ ucfirst(Auth::user()->role) }})">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </a>
        </div>
    </div>
</aside>
