<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 leading-tight">
                    {{ __('Workload Statistics') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    {{ __('Real-time workload distribution by team member to monitor bandwidth and prevent bottlenecks.') }}
                </p>
            </div>
            <div class="flex items-center gap-2">
                <span
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold bg-brand-50 text-brand-700 border border-brand-200/60 shadow-2xs">
                    <span class="w-2 h-2 rounded-full bg-brand-500 animate-pulse"></span>
                    {{ __('Live Workload Tracking') }}
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-10">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">


            <div class="bg-white rounded-2xl border border-gray-200/80 p-6 shadow-xs" x-data="velocityChart({{ Js::from($velocityData) }})">

                <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Weekly Throughput & Velocity</h3>
                        <p class="text-xs text-gray-400 mt-0.5">Tickets created vs. closed over the last 8 weeks</p>
                    </div>

                    <!-- Legend or summary badge -->
                    <div class="flex items-center gap-4 text-xs font-semibold">
                        <span class="flex items-center gap-1.5 text-gray-600">
                            <span class="w-3 h-3 rounded-full bg-amber-500"></span> Created
                        </span>
                        <span class="flex items-center gap-1.5 text-gray-600">
                            <span class="w-3 h-3 rounded-full bg-emerald-500"></span> Closed
                        </span>
                    </div>
                </div>

                <!-- Chart Canvas -->
                <div class="mt-4 h-64 sm:h-72">
                    <canvas x-ref="canvas"></canvas>
                </div>
            </div>
            <!-- 1. Interactive Metric Cards with Matching Elevation & Hover Aesthetics -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 pt-4">
                <!-- Total Active Workload -->
                <a href="{{ route('tickets.index') }}"
                    class="bg-white rounded-2xl border border-gray-200/80 p-6 shadow-xs hover:shadow-md hover:-translate-y-0.5 hover:border-brand-300 transition-all duration-200 group">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                {{ __('Active Workload') }}</p>
                            <h3
                                class="text-3xl font-extrabold text-gray-900 mt-2 group-hover:text-brand-600 transition">
                                {{ $totalActiveTickets }}</h3>
                            <p class="text-xs text-gray-400 mt-1">{{ __('Across active projects') }}</p>
                        </div>
                        <div
                            class="p-3.5 bg-brand-50/80 text-brand-600 rounded-2xl group-hover:bg-brand-600 group-hover:text-white transition-all duration-200 shadow-2xs">
                            <x-heroicon-o-ticket class="w-6 h-6" />
                        </div>
                    </div>
                </a>

                <!-- Assigned Tickets -->
                <div
                    class="bg-white rounded-2xl border border-gray-200/80 p-6 shadow-xs hover:shadow-md transition-all duration-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                {{ __('Assigned Tickets') }}</p>
                            <h3 class="text-3xl font-extrabold text-indigo-600 mt-2">{{ $totalAssignedTickets }}</h3>
                            <p class="text-xs text-gray-400 mt-1">{{ __('Distributed across team') }}</p>
                        </div>
                        <div class="p-3.5 bg-indigo-50 text-indigo-600 rounded-2xl shadow-2xs">
                            <x-heroicon-o-user-group class="w-6 h-6" />
                        </div>
                    </div>
                </div>

                <!-- Unassigned Tickets -->
                <a href="{{ route('tickets.index', ['assigned_to_id' => 'unassigned']) }}"
                    class="bg-white rounded-2xl border border-gray-200/80 p-6 shadow-xs hover:shadow-md hover:-translate-y-0.5 hover:border-amber-300 transition-all duration-200 group">
                    <div class="flex items-center justify-between">
                        <div>
                            <p
                                class="text-xs font-semibold text-gray-500 uppercase tracking-wider group-hover:text-amber-600 transition">
                                {{ __('Unassigned Tickets') }}</p>
                            <h3 class="text-3xl font-extrabold text-amber-600 mt-2">{{ $totalUnassignedTickets }}</h3>
                            <p class="text-xs text-gray-400 mt-1">{{ __('Requires assignment') }}</p>
                        </div>
                        <div
                            class="p-3.5 bg-amber-50 text-amber-600 rounded-2xl group-hover:bg-amber-600 group-hover:text-white transition-all duration-200 shadow-2xs">
                            <x-heroicon-o-question-mark-circle class="w-6 h-6" />
                        </div>
                    </div>
                </a>

                <!-- Heaviest Load -->
                <div
                    class="bg-white rounded-2xl border border-gray-200/80 p-6 shadow-xs hover:shadow-md transition-all duration-200">
                    <div class="flex items-center justify-between">
                        <div class="min-w-0 pr-2">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                {{ __('Heaviest Load') }}</p>
                            <h3 class="text-2xl font-extrabold text-purple-600 mt-2 truncate">
                                {{ $heaviestUser ? $heaviestUser->name : 'N/A' }}
                            </h3>
                            <p class="text-xs text-gray-400 mt-1 font-medium">
                                {{ $heaviestUser ? $heaviestUser->active_tickets_count . ' active tickets' : '0 tickets' }}
                            </p>
                        </div>
                        <div class="p-3.5 bg-purple-50 text-purple-600 rounded-2xl shadow-2xs shrink-0">
                            <x-heroicon-o-fire class="w-6 h-6" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Generous Breathing Space Between KPI Cards and Main Section -->
            <div class="mt-12"></div>

            <!-- 2. Main Workload Leaderboard Table -->
            <div id="user-workload-table"
                class="bg-white rounded-2xl shadow-xs border border-gray-200/80 overflow-hidden hover:shadow-md transition-all duration-200">
                <!-- Header with Precise Height (h-16) & Matched Dashboard Alignment -->
                <div class="h-16 px-6 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                    <div class="flex items-center space-x-2.5">
                        <span class="h-2.5 w-2.5 rounded-full bg-brand-500 ring-4 ring-brand-100"></span>
                        <h3 class="text-base font-bold text-gray-900">{{ __('User Workload Leaderboard') }}</h3>

                        <!-- Info Tooltip -->
                        <div class="relative group inline-flex items-center cursor-help">
                            <span
                                class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-gray-100 text-gray-400 group-hover:bg-brand-50 group-hover:text-brand-600 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </span>
                            <div
                                class="absolute left-0 sm:left-1/2 sm:-translate-x-1/2 top-full mt-2.5 hidden group-hover:block w-80 bg-white border border-gray-200/90 rounded-2xl shadow-xl z-50 pointer-events-none transition-all duration-200">
                                <div
                                    class="px-4 py-3 bg-gradient-to-r from-gray-50 to-brand-50/30 border-b border-gray-100 rounded-t-2xl flex items-center justify-between">
                                    <span
                                        class="text-xs font-bold text-gray-900">{{ __('Workload Calculation') }}</span>
                                    <span
                                        class="text-[10px] font-semibold text-brand-700 bg-brand-100/70 px-2 py-0.5 rounded-full">{{ __('Heaviest → Lightest') }}</span>
                                </div>
                                <div class="p-4 space-y-2 text-xs text-gray-600">
                                    <p>{{ __('Workload is strictly measured as the number of active assigned tickets (New, In Progress, Waiting, Blocked) in non-closed projects.') }}
                                    </p>
                                    <p class="text-[11px] text-gray-400 pt-1 border-t border-gray-100">
                                        {{ __('Closed tickets and tickets in closed projects are excluded from active load.') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <span class="text-xs font-medium text-gray-400">
                            {{ $allUsersCount }} {{ __('Team Members') }}
                        </span>
                    </div>
                </div>

                <!-- Leaderboard Streamlined Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr
                                class="border-b border-gray-100 text-[11px] font-bold uppercase tracking-wider text-gray-400 bg-gray-50/40">
                                <th scope="col" class="py-3.5 pl-6 pr-3 w-20 text-center">{{ __('Rank') }}</th>
                                <th scope="col" class="py-3.5 px-3 min-w-[220px]">{{ __('Team Member') }}</th>
                                <th scope="col" class="py-3.5 px-3 text-center whitespace-nowrap">
                                    {{ __('Priority Breakdown') }}</th>
                                <th scope="col" class="py-3.5 pl-3 pr-6 text-right w-44 whitespace-nowrap">
                                    {{ __('Active Workload') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            @forelse($users as $u)
                                <tr class="hover:bg-gray-50/70 transition-colors group">
                                    <!-- Rank Column -->
                                    <td class="py-4 pl-6 pr-3 text-center align-middle whitespace-nowrap">
                                        @if ($u->workload_rank === 1 && $u->active_tickets_count > 0)
                                            <span
                                                class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-xs font-extrabold bg-amber-100 text-amber-800 border border-amber-300 shadow-2xs">
                                                #1
                                            </span>
                                        @elseif($u->workload_rank === 2)
                                            <span
                                                class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-xs font-bold bg-gray-100 text-gray-700 border border-gray-200">
                                                #2
                                            </span>
                                        @elseif($u->workload_rank === 3)
                                            <span
                                                class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-xs font-bold bg-orange-50 text-orange-800 border border-orange-200">
                                                #3
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-xs font-medium text-gray-400">
                                                #{{ $u->workload_rank }}
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Team Member Info -->
                                    <td class="py-4 px-3 align-middle whitespace-nowrap">
                                        <a href="{{ route('tickets.index', ['assigned_to_id' => $u->id]) }}"
                                            class="text-sm font-bold text-gray-900 group-hover:text-brand-600 transition">
                                            {{ $u->name }}
                                        </a>
                                    </td>

                                    <!-- Priority & Health Breakdown -->
                                    <td class="py-4 px-3 align-middle text-center whitespace-nowrap">
                                        <div class="inline-flex items-center justify-center gap-1.5">
                                            @if ($u->urgent_tickets_count > 0)
                                                <span
                                                    class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-50 text-red-700 border border-red-200"
                                                    title="{{ __('Urgent Tickets') }}">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                                    {{ $u->urgent_tickets_count }} {{ __('urgent') }}
                                                </span>
                                            @endif

                                            @if ($u->high_tickets_count > 0)
                                                <span
                                                    class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200"
                                                    title="{{ __('High Priority Tickets') }}">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                    {{ $u->high_tickets_count }} {{ __('high') }}
                                                </span>
                                            @endif

                                            @if ($u->overdue_tickets_count > 0)
                                                <span
                                                    class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-purple-50 text-purple-700 border border-purple-200"
                                                    title="{{ __('Overdue Tickets') }}">
                                                    <x-heroicon-m-clock class="w-3 h-3 text-purple-600" />
                                                    {{ $u->overdue_tickets_count }} {{ __('overdue') }}
                                                </span>
                                            @endif

                                            @if ($u->urgent_tickets_count === 0 && $u->high_tickets_count === 0 && $u->overdue_tickets_count === 0)
                                                <span class="text-xs text-gray-400 italic">{{ __('Normal') }}</span>
                                            @endif
                                        </div>
                                    </td>

                                    <!-- Active Workload Column -->
                                    <td class="py-4 pl-3 pr-6 align-middle text-right whitespace-nowrap">
                                        <a href="{{ route('tickets.index', ['assigned_to_id' => $u->id]) }}"
                                            class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl text-xs font-extrabold {{ $u->active_tickets_count > 0 ? 'bg-gray-900 text-white hover:bg-brand-600' : 'bg-gray-100 text-gray-400 hover:bg-gray-200' }} transition shadow-2xs group/btn">
                                            <span>{{ $u->active_tickets_count }} {{ __('tickets') }}</span>
                                            <svg class="w-3.5 h-3.5 opacity-60 group-hover/btn:translate-x-0.5 transition-transform"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5l7 7-7 7" />
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-16 text-center text-sm text-gray-500">
                                        {{ __('No team members found.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination for Employee Table -->
                @if ($users->hasPages())
                    <div class="p-5 border-t border-gray-100 bg-white">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>


<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('velocityChart', (data) => ({
            init() {
                const labels = data.map(item => item.label);
                const created = data.map(item => item.created);
                const closed = data.map(item => item.closed);

                new Chart(this.$refs.canvas, {
                    type: 'line', // or 'line'
                    data: {
                        labels: labels,
                        datasets: [{
                                label: 'Tickets Created',
                                data: created,
                                backgroundColor: 'rgba(245, 158, 11, 0.85)', // Amber
                                borderRadius: 6,
                            },
                            {
                                label: 'Tickets Closed',
                                data: closed,
                                backgroundColor: 'rgba(16, 185, 129, 0.85)', // Emerald
                                borderRadius: 6,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            } // We already built a custom header legend
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            }
        }));
    });
</script>
