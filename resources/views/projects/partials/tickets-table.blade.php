<div id="tickets-table" x-data="ticketFilter({
    tab: '{{ $tab }}',
    search: '{{ request('search') }}',
    status: '{{ request('status') }}',
    priority: '{{ request('priority') }}',
    sort: '{{ $sort }}',
    direction: '{{ $direction }}',
    hasSort: true
})" class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-slate-800 shadow-xs overflow-hidden">

    <!-- Table Header Tabs & Toolbar -->
    <div class="p-5 sm:p-6 border-b border-gray-100 dark:border-slate-800 space-y-4">

        <!-- Top Row: Segmented Tabs (Active vs Closed vs Deleted) -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <x-ticket.segmented-tabs
                :active-tab="$tab"
                :active-count="$projectStats['active']"
                :closed-count="$projectStats['closed']"
                :deleted-count="$projectStats['deleted']" />

            <!-- Ticket Count Indicator -->
            <span class="text-xs text-gray-500 dark:text-slate-400 font-medium">
                {{ __('Showing') }} <strong class="text-gray-800 dark:text-slate-200">{{ $tickets->total() }}</strong>
                @if ($tab === 'deleted')
                    {{ __('deleted tickets') }}
                @elseif ($tab === 'closed')
                    {{ __('closed tickets') }}
                @else
                    {{ __('active tickets') }}
                @endif
            </span>
        </div>

        <!-- Bottom Row: Filter Controls -->
        <div class="flex flex-wrap items-center justify-between gap-3 pt-1">
            <!-- Search Bar -->
            <div class="relative flex-1 sm:flex-initial sm:min-w-[260px]">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 dark:text-slate-500">
                    <x-heroicon-m-magnifying-glass class="w-4 h-4" />
                </div>
                <input type="text" x-model="search" @keydown.enter="applyFilters()"
                    placeholder="{{ $tab === 'closed' ? __('Search closed tickets...') : __('Search active tickets...') }}"
                    class="w-full pl-9 pr-3 py-1.5 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-slate-400 focus:bg-white dark:focus:bg-slate-850 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all">
            </div>

            <div class="flex flex-wrap items-center gap-2.5">
                @if (request()->hasAny(['search', 'status', 'priority']))
                    <a href="{{ route('projects.show', [$project, 'tab' => $tab]) }}#tickets-table"
                        class="inline-flex items-center gap-1.5 py-1.5 px-3 text-xs rounded-xl bg-red-600 border border-transparent text-white hover:bg-red-700 focus:ring-2 focus:ring-red-500/30 transition-all font-medium">
                        <x-heroicon-m-x-mark class="w-3.5 h-3.5 text-white" />
                        <span>{{ __('Reset') }}</span>
                    </a>
                @endif

                <!-- Status Filter (Only relevant for Active Tickets tab) -->
                @if ($tab === 'active')
                    <select x-model="status" @change="applyFilters()"
                        class="py-1.5 pl-3 pr-8 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-700 dark:text-slate-200 focus:bg-white dark:focus:bg-slate-800 focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all">
                        <option value="">{{ __('All Statuses') }}</option>
                        <option value="new">{{ __('New') }}</option>
                        <option value="in_progress">{{ __('In Progress') }}</option>
                        <option value="waiting">{{ __('Waiting') }}</option>
                        <option value="blocked">{{ __('Blocked') }}</option>
                    </select>
                @endif

                <!-- Priority Filter -->
                <select x-model="priority" @change="applyFilters()"
                    class="py-1.5 pl-3 pr-8 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-700 dark:text-slate-200 focus:bg-white dark:focus:bg-slate-800 focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all">
                    <option value="">{{ __('All Priorities') }}</option>
                    <option value="urgent">{{ __('Urgent') }}</option>
                    <option value="high">{{ __('High') }}</option>
                    <option value="medium">{{ __('Medium') }}</option>
                    <option value="low">{{ __('Low') }}</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Tickets Table -->
    @if ($tickets->isEmpty())
        <div class="p-12 text-center">
            <div
                class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-gray-100 dark:bg-slate-800 text-gray-400 dark:text-slate-500 mb-3 border border-transparent dark:border-slate-700">
                <x-heroicon-o-ticket class="w-6 h-6" />
            </div>
            <h4 class="text-sm font-semibold text-gray-900 dark:text-white">{{ __('No tickets found') }}</h4>
            <p class="text-xs text-gray-500 dark:text-slate-400 mt-1 max-w-sm mx-auto">
                {{ __('No tickets match your filter criteria or none have been opened under this project.') }}
            </p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-slate-800 bg-gray-50/50 dark:bg-slate-850/60">
                        <x-table.th sortable="id" class="pl-6">{{ __('ID') }}</x-table.th>
                        <x-table.th sortable="title">{{ __('Title') }}</x-table.th>
                        <x-table.th sortable="status">{{ __('Status') }}</x-table.th>
                        <x-table.th sortable="priority">{{ __('Priority') }}</x-table.th>
                        <x-table.th>{{ __('Assignee') }}</x-table.th>
                        <x-table.th sortable="due_date">{{ __('Due Date') }}</x-table.th>
                        <x-table.th align="right">{{ __('Action') }}</x-table.th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-800 text-sm">
                    @foreach ($tickets as $ticket)
                        <tr class="hover:bg-gray-50/70 dark:hover:bg-slate-800/50 transition-colors group">
                            <!-- Ticket Key / Reference Code (Clean Neutral Badge) -->
                            <td class="py-4 pl-6 pr-3 whitespace-nowrap">
                                <a href="{{ route('tickets.show', $ticket) }}"
                                    class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-mono font-semibold bg-gray-100 dark:bg-slate-800 text-gray-700 dark:text-slate-300 border border-gray-200 dark:border-slate-700 hover:bg-gray-200/80 dark:hover:bg-slate-700 hover:text-gray-900 dark:hover:text-white transition-colors shadow-2xs">
                                    {{ $ticket->reference_code }}
                                </a>
                            </td>

                            <!-- Title & Meta -->
                            <td class="py-4 px-3 max-w-md">
                                <a href="{{ route('tickets.show', $ticket) }}"
                                    class="font-semibold text-gray-900 dark:text-white group-hover:text-brand-600 dark:group-hover:text-brand-400 transition-colors line-clamp-1 block">
                                    {{ preg_replace('/\s*#\d+$/', '', $ticket->title) }}
                                </a>
                                <span class="text-xs text-gray-400 dark:text-slate-500">
                                    {{ __('by') }} <span class="text-gray-600 dark:text-slate-300">{{ $ticket->creator?->name ?? __('Unknown') }}</span>
                                    &bull; {{ $ticket->created_at->diffForHumans() }}
                                </span>
                            </td>

                            <!-- Status Badge -->
                            <td class="py-4 px-3 whitespace-nowrap">
                                <x-ticket.status-badge :status="$ticket->status" />
                            </td>

                            <!-- Priority Badge -->
                            <td class="py-4 px-3 whitespace-nowrap">
                                <x-ticket.priority-badge :priority="$ticket->priority" />
                            </td>

                            <!-- Assignee -->
                            <td class="py-4 px-3 whitespace-nowrap">
                                @if ($ticket->assignedTo)
                                    <div class="flex items-center gap-2">
                                        <x-ui.avatar :name="$ticket->assignedTo->name" size="md" />
                                        <span class="text-xs text-gray-700 dark:text-slate-300 font-medium">
                                            {{ $ticket->assignedTo->name }}
                                        </span>
                                    </div>
                                @else
                                    <span class="text-xs text-rose-600 dark:text-rose-400 font-semibold italic">
                                        {{ __('Unassigned') }}
                                    </span>
                                @endif
                            </td>

                            <!-- Due Date & Overdue Badge -->
                            <td class="py-4 px-3 whitespace-nowrap text-xs">
                                @if ($ticket->due_date)
                                    <div class="flex items-center gap-1.5">
                                        <span class="font-medium {{ $ticket->is_overdue ? 'text-rose-600 dark:text-rose-400 font-semibold' : 'text-gray-600 dark:text-slate-400' }}">
                                            {{ $ticket->due_date->format('M d, Y') }}
                                        </span>
                                        @if ($ticket->is_overdue)
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-rose-100 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 border border-rose-200/60 dark:border-rose-800/40 uppercase tracking-tight">
                                                {{ __('OVERDUE') }}
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-gray-400 dark:text-slate-600">&mdash;</span>
                                @endif
                            </td>

                            <!-- Action Link / Restore Button -->
                            <td class="py-4 pl-3 pr-6 text-right whitespace-nowrap">
                                @if ($ticket->trashed())
                                    @can('restore', $ticket)
                                        <form action="{{ route('tickets.restore', $ticket->id) }}" method="POST" class="inline-block"
                                            id="restore-ticket-form-{{ $ticket->id }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="button"
                                                @click="$dispatch('open-confirm-modal', {
                                                    title: '{{ __('Restore Ticket?') }}',
                                                    message: '{{ __('Are you sure you want to restore this ticket to active status?') }}',
                                                    confirmText: '{{ __('Restore') }}',
                                                    isDanger: false,
                                                    form: 'restore-ticket-form-{{ $ticket->id }}'
                                                })"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-950/40 hover:bg-emerald-100 dark:hover:bg-emerald-900/60 border border-emerald-200 dark:border-emerald-800/50 transition-all shadow-xs"
                                                title="{{ __('Restore Ticket') }}">
                                                <x-heroicon-m-arrow-path class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" />
                                                <span>{{ __('Restore') }}</span>
                                            </button>
                                        </form>
                                    @endcan
                                @else
                                    <a href="{{ route('tickets.show', $ticket) }}"
                                        class="inline-flex items-center justify-center p-2 rounded-xl text-gray-500 dark:text-slate-400 bg-white dark:bg-slate-800 hover:text-brand-600 dark:hover:text-brand-400 hover:bg-brand-50 dark:hover:bg-brand-950/40 border border-gray-200/90 dark:border-slate-700 hover:border-brand-300 dark:hover:border-brand-800/50 shadow-xs transition-all"
                                        title="{{ __('View Ticket') }}">
                                        <x-heroicon-m-arrow-up-right class="w-4 h-4" />
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="p-5 border-t border-gray-100 dark:border-slate-800">
            {{ $tickets->links() }}
        </div>
    @endif

</div>
