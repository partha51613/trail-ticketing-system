<div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-slate-800 p-5 shadow-xs space-y-4"
    x-data="ticketFilter({
        tab: '{{ $tab }}',
        project: '{{ request('project_id') }}',
        status: '{{ request('status') }}',
        priority: '{{ request('priority') }}',
        assignee: '{{ request('assigned_to_id') }}'
    })">
    <!-- Top Row: Segmented Tabs (Active vs Closed vs Deleted) -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-gray-100 dark:border-slate-800 pb-4">
        <x-ticket.segmented-tabs
            :active-tab="$tab"
            :active-count="$activeCount"
            :closed-count="$closedCount"
            :deleted-count="$deletedCount" />

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
        <div class="flex flex-wrap items-center gap-2.5">
            <!-- Project Filter -->
            <select x-model="project" @change="applyFilters()"
                class="py-1.5 pl-3 pr-8 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-700 dark:text-slate-200 focus:bg-white dark:focus:bg-slate-800 focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all">
                <option value="">{{ __('All Projects') }}</option>
                @foreach ($projects as $proj)
                    <option value="{{ $proj->id }}" @selected(request('project_id') == $proj->id)>
                        {{ $proj->name }} ({{ $proj->code }})
                    </option>
                @endforeach
            </select>

            <!-- Status Filter -->
            @if ($tab === 'active')
                <select x-model="status" @change="applyFilters()"
                    class="py-1.5 pl-3 pr-8 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-700 dark:text-slate-200 focus:bg-white dark:focus:bg-slate-800 focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all">
                    <option value="">{{ __('All Statuses') }}</option>
                    <option value="new">{{ __('New') }}</option>
                    <option value="in_progress">{{ __('In Progress') }}</option>
                    <option value="waiting">{{ __('Waiting') }}</option>
                    <option value="blocked">{{ __('Blocked') }}</option>
                </select>
            @elseif ($tab === 'closed')
                <select disabled
                    class="py-1.5 pl-3 pr-8 text-xs rounded-xl bg-gray-100/70 dark:bg-slate-800/50 border border-gray-200 dark:border-slate-700 text-gray-400 dark:text-slate-500 cursor-not-allowed"
                    title="{{ __('Status is fixed to Closed on this tab') }}">
                    <option selected>{{ __('Closed') }}</option>
                </select>
            @else
                <select disabled
                    class="py-1.5 pl-3 pr-8 text-xs rounded-xl bg-gray-100/70 dark:bg-slate-800/50 border border-gray-200 dark:border-slate-700 text-gray-400 dark:text-slate-500 cursor-not-allowed"
                    title="{{ __('Status is fixed to Deleted on this tab') }}">
                    <option selected>{{ __('Deleted') }}</option>
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

            <!-- Assignee Filter -->
            <select x-model="assignee" @change="applyFilters()"
                class="py-1.5 pl-3 pr-8 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-700 dark:text-slate-200 focus:bg-white dark:focus:bg-slate-800 focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all">
                <option value="">{{ __('All Assignees') }}</option>
                <option value="unassigned">{{ __('Unassigned Only') }}</option>
                @foreach ($users as $user)
                    <option value="{{ $user->id }}" @selected(request('assigned_to_id') == $user->id)>
                        {{ $user->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Clear Filters Button (if any non-tab filter active) -->
        @if (request()->hasAny(['project_id', 'status', 'priority', 'assigned_to_id', 'search', 'watched', 'created_by_id']))
            @php
                $resetUrl = request()->routeIs('tickets.assigned-to-me')
                    ? route('tickets.assigned-to-me', array_filter(['tab' => request('tab')]))
                    : (request()->routeIs('tickets.created-by-me')
                        ? route('tickets.created-by-me', array_filter(['tab' => request('tab')]))
                        : (request()->routeIs('tickets.watched')
                            ? route('tickets.watched', array_filter(['tab' => request('tab')]))
                            : route('tickets.index', array_filter(['tab' => request('tab')]))));
            @endphp
            <a href="{{ $resetUrl }}#tickets-table"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-rose-700 dark:text-rose-300 bg-rose-50 dark:bg-rose-950/40 hover:bg-rose-100 dark:hover:bg-rose-900/60 hover:text-rose-800 dark:hover:text-rose-200 border border-rose-200 dark:border-rose-800/50 rounded-xl shadow-2xs transition-all">
                <x-heroicon-m-x-mark class="w-3.5 h-3.5 text-rose-600 dark:text-rose-400" />
                <span>{{ __('Clear Filters') }}</span>
            </a>
        @endif
    </div>
</div>
