<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('projects.index') }}"
                    class="p-2 rounded-xl text-gray-400 hover:text-gray-700 hover:bg-white border border-transparent hover:border-gray-200 transition-all"
                    title="{{ __('Back to Projects') }}">
                    <x-heroicon-m-arrow-left class="w-5 h-5" />
                </a>
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-2xl font-bold tracking-tight text-gray-900">
                            {{ $project->name }}
                        </h1>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-brand-50 text-brand-700 border border-brand-200/60">
                            {{ __('Workspace') }}
                        </span>
                    </div>
                    <p class="text-xs text-gray-400 mt-0.5 font-medium">
                        {{ __('Created') }} {{ $project->created_at->format('M d, Y') }} &bull; {{ $project->created_at->diffForHumans() }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5">
                @can('update', $project)
                    <a href="{{ route('projects.edit', $project) }}"
                        class="inline-flex items-center gap-x-1.5 px-3.5 py-2 text-sm font-semibold text-gray-700 bg-white border border-gray-200/80 rounded-xl hover:bg-gray-50 hover:border-gray-300 transition-all shadow-xs">
                        <x-heroicon-m-pencil-square class="w-4 h-4 text-gray-500" />
                        <span>{{ __('Edit Project') }}</span>
                    </a>
                @endcan

                @can('create', App\Models\Ticket::class)
                    <a href="{{ route('tickets.create', ['project_id' => $project->id]) }}"
                        class="inline-flex items-center gap-x-1.5 px-4 py-2 text-sm font-semibold text-white bg-brand-500 rounded-xl hover:bg-brand-600 shadow-sm shadow-brand-500/20 transition-all">
                        <x-heroicon-m-plus class="w-4 h-4" />
                        <span>{{ __('New Ticket') }}</span>
                    </a>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-gray-50/60 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Project Context & Description Banner -->
            <div class="bg-white rounded-2xl border border-gray-100 p-6 sm:p-7 shadow-xs">
                <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-6">
                    <div class="space-y-2 flex-1">
                        <span class="text-xs font-semibold uppercase tracking-wider text-gray-400">
                            {{ __('About Workspace') }}
                        </span>
                        <p class="text-sm text-gray-600 leading-relaxed max-w-3xl">
                            {{ $project->description ?: __('No detailed workspace description provided for this project.') }}
                        </p>
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-center gap-4 shrink-0 pt-4 md:pt-0 border-t md:border-t-0 border-gray-100">
                        <div class="space-y-1">
                            <span class="text-xs font-semibold uppercase tracking-wider text-gray-400 block">
                                {{ __('Assigned Managers') }}
                            </span>
                            @if ($project->managers->isEmpty())
                                <span class="text-xs text-gray-400 italic">{{ __('No managers assigned') }}</span>
                            @else
                                <div class="flex items-center gap-2">
                                    <div class="flex items-center -space-x-1.5 overflow-hidden">
                                        @foreach ($project->managers->take(5) as $manager)
                                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-brand-50 text-brand-700 ring-2 ring-white text-xs font-bold border border-brand-100"
                                                title="{{ $manager->name }} ({{ $manager->email }})">
                                                {{ strtoupper(substr($manager->name, 0, 1)) }}
                                            </span>
                                        @endforeach
                                    </div>
                                    <span class="text-xs text-gray-600 font-medium">
                                        {{ $project->managers->pluck('name')->join(', ') }}
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mini KPI Stat Row for This Project -->
            <div class="grid grid-cols-2 sm:grid-cols-5 gap-3.5 sm:gap-4">
                <div class="bg-white rounded-2xl border border-gray-100 p-4 shadow-xs">
                    <span class="text-xs font-medium uppercase tracking-wider text-gray-400">{{ __('Total Tickets') }}</span>
                    <div class="text-2xl font-bold tracking-tight text-gray-900 mt-1">
                        {{ $projectStats['total'] }}
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-gray-100 p-4 shadow-xs">
                    <span class="text-xs font-medium uppercase tracking-wider text-brand-600">{{ __('Assigned') }}</span>
                    <div class="text-2xl font-bold tracking-tight text-brand-600 mt-1">
                        {{ $projectStats['assigned'] }}
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-gray-100 p-4 shadow-xs">
                    <span class="text-xs font-medium uppercase tracking-wider {{ $projectStats['unassigned'] > 0 ? 'text-rose-600' : 'text-gray-400' }}">{{ __('Unassigned') }}</span>
                    <div class="text-2xl font-bold tracking-tight {{ $projectStats['unassigned'] > 0 ? 'text-rose-600' : 'text-gray-900' }} mt-1">
                        {{ $projectStats['unassigned'] }}
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-gray-100 p-4 shadow-xs">
                    <span class="text-xs font-medium uppercase tracking-wider text-red-500">{{ __('Blocked') }}</span>
                    <div class="text-2xl font-bold tracking-tight {{ $projectStats['blocked'] > 0 ? 'text-red-600' : 'text-gray-900' }} mt-1">
                        {{ $projectStats['blocked'] }}
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-gray-100 p-4 shadow-xs col-span-2 sm:col-span-1">
                    <span class="text-xs font-medium uppercase tracking-wider text-emerald-600">{{ __('Closed') }}</span>
                    <div class="text-2xl font-bold tracking-tight text-emerald-600 mt-1">
                        {{ $projectStats['closed'] }}
                    </div>
                </div>
            </div>

            <!-- Server-Side Search, Filter & Sort Table -->
            <div x-data="{
                search: '{{ request('search') }}',
                status: '{{ request('status') }}',
                priority: '{{ request('priority') }}',
                sort: '{{ $sort }}',
                direction: '{{ $direction }}',
                applyFilters() {
                    const params = new URLSearchParams(window.location.search);
                    if (this.search) { params.set('search', this.search); } else { params.delete('search'); }
                    if (this.status) { params.set('status', this.status); } else { params.delete('status'); }
                    if (this.priority) { params.set('priority', this.priority); } else { params.delete('priority'); }
                    params.set('sort', this.sort);
                    params.set('direction', this.direction);
                    params.delete('page');
                    window.location.href = `${window.location.pathname}?${params.toString()}`;
                },
                sortBy(column) {
                    if (this.sort === column) {
                        this.direction = this.direction === 'asc' ? 'desc' : 'asc';
                    } else {
                        this.sort = column;
                        this.direction = 'asc';
                    }
                    this.applyFilters();
                }
            }" class="bg-white rounded-2xl border border-gray-100 shadow-xs overflow-hidden">

                <!-- Table Header Toolbar -->
                <div class="p-5 sm:p-6 border-b border-gray-100 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div class="flex items-center gap-2">
                        <h2 class="text-lg font-bold text-gray-900 tracking-tight">
                            {{ __('Project Tickets') }}
                        </h2>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold bg-gray-100 text-gray-600">
                            {{ $tickets->total() }}
                        </span>
                    </div>

                    <!-- Filter Controls -->
                    <div class="flex flex-wrap items-center gap-3">
                        <!-- Search Bar -->
                        <div class="relative min-w-[220px]">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <x-heroicon-m-magnifying-glass class="w-4 h-4" />
                            </div>
                            <input type="text"
                                x-model="search"
                                @keydown.enter="applyFilters()"
                                placeholder="{{ __('Search tickets...') }}"
                                class="w-full pl-9 pr-3 py-1.5 text-xs rounded-xl bg-gray-50 border border-gray-200 text-gray-900 placeholder-gray-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all">
                        </div>

                        @if (request()->hasAny(['search', 'status', 'priority', 'sort']))
                            <a href="{{ route('projects.show', $project) }}"
                            class="inline-flex items-center gap-1.5 py-1.5 px-3 text-xs rounded-xl bg-red-600 border border-transparent text-white hover:bg-red-700 focus:ring-2 focus:ring-red-500/30 transition-all font-medium">
                                <x-heroicon-m-x-mark class="w-3.5 h-3.5 text-white" />
                                <span>{{ __('Reset') }}</span>
                            </a>
                        @endif

                        <!-- Status Filter -->
                        <select x-model="status" @change="applyFilters()"
                            class="py-1.5 pl-3 pr-8 text-xs rounded-xl bg-gray-50 border border-gray-200 text-gray-700 focus:bg-white focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all">
                            <option value="">{{ __('All Statuses') }}</option>
                            <option value="new">{{ __('New') }}</option>
                            <option value="in_progress">{{ __('In Progress') }}</option>
                            <option value="waiting">{{ __('Waiting') }}</option>
                            <option value="blocked">{{ __('Blocked') }}</option>
                            <option value="closed">{{ __('Closed') }}</option>
                        </select>

                        <!-- Priority Filter -->
                        <select x-model="priority" @change="applyFilters()"
                            class="py-1.5 pl-3 pr-8 text-xs rounded-xl bg-gray-50 border border-gray-200 text-gray-700 focus:bg-white focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all">
                            <option value="">{{ __('All Priorities') }}</option>
                            <option value="urgent">{{ __('Urgent') }}</option>
                            <option value="high">{{ __('High') }}</option>
                            <option value="medium">{{ __('Medium') }}</option>
                            <option value="low">{{ __('Low') }}</option>
                        </select>


                    </div>
                </div>

                <!-- Tickets Table -->
                @if ($tickets->isEmpty())
                    <div class="p-12 text-center">
                        <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-gray-100 text-gray-400 mb-3">
                            <x-heroicon-o-ticket class="w-6 h-6" />
                        </div>
                        <h4 class="text-sm font-semibold text-gray-900">{{ __('No tickets found') }}</h4>
                        <p class="text-xs text-gray-500 mt-1 max-w-sm mx-auto">
                            {{ __('No tickets match your filter criteria or none have been opened under this project.') }}
                        </p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-gray-100 bg-gray-50/50 text-[11px] font-semibold uppercase tracking-wider text-gray-400">
                                    <th scope="col" class="py-3.5 pl-6 pr-3 cursor-pointer hover:text-gray-700 transition-colors" @click="sortBy('id')">
                                        <div class="flex items-center gap-1.5">
                                            <span>{{ __('ID') }}</span>
                                            <template x-if="sort === 'id'">
                                                <x-heroicon-m-chevron-up class="w-3.5 h-3.5 text-brand-600" x-bind:class="{ 'rotate-180': direction === 'desc' }" />
                                            </template>
                                        </div>
                                    </th>

                                    <th scope="col" class="py-3.5 px-3 cursor-pointer hover:text-gray-700 transition-colors" @click="sortBy('title')">
                                        <div class="flex items-center gap-1.5">
                                            <span>{{ __('Title') }}</span>
                                            <template x-if="sort === 'title'">
                                                <x-heroicon-m-chevron-up class="w-3.5 h-3.5 text-brand-600" x-bind:class="{ 'rotate-180': direction === 'desc' }" />
                                            </template>
                                        </div>
                                    </th>

                                    <th scope="col" class="py-3.5 px-3 cursor-pointer hover:text-gray-700 transition-colors" @click="sortBy('status')">
                                        <div class="flex items-center gap-1.5">
                                            <span>{{ __('Status') }}</span>
                                            <template x-if="sort === 'status'">
                                                <x-heroicon-m-chevron-up class="w-3.5 h-3.5 text-brand-600" x-bind:class="{ 'rotate-180': direction === 'desc' }" />
                                            </template>
                                        </div>
                                    </th>

                                    <th scope="col" class="py-3.5 px-3 cursor-pointer hover:text-gray-700 transition-colors" @click="sortBy('priority')">
                                        <div class="flex items-center gap-1.5">
                                            <span>{{ __('Priority') }}</span>
                                            <template x-if="sort === 'priority'">
                                                <x-heroicon-m-chevron-up class="w-3.5 h-3.5 text-brand-600" x-bind:class="{ 'rotate-180': direction === 'desc' }" />
                                            </template>
                                        </div>
                                    </th>

                                    <th scope="col" class="py-3.5 px-3">
                                        {{ __('Assignee') }}
                                    </th>

                                    <th scope="col" class="py-3.5 px-3 cursor-pointer hover:text-gray-700 transition-colors" @click="sortBy('due_date')">
                                        <div class="flex items-center gap-1.5">
                                            <span>{{ __('Due Date') }}</span>
                                            <template x-if="sort === 'due_date'">
                                                <x-heroicon-m-chevron-up class="w-3.5 h-3.5 text-brand-600" x-bind:class="{ 'rotate-180': direction === 'desc' }" />
                                            </template>
                                        </div>
                                    </th>

                                    <th scope="col" class="py-3.5 pl-3 pr-6 text-right">
                                        {{ __('Action') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-sm">
                                @foreach ($tickets as $ticket)
                                    <tr class="hover:bg-gray-50/70 transition-colors group">
                                        <!-- Ticket ID -->
                                        <td class="py-4 pl-6 pr-3 font-semibold text-gray-400 text-xs whitespace-nowrap">
                                            #{{ $ticket->id }}
                                        </td>

                                        <!-- Title & Meta -->
                                        <td class="py-4 px-3 max-w-md">
                                            <a href="{{ route('tickets.show', $ticket) }}"
                                                class="font-semibold text-gray-900 group-hover:text-brand-600 transition-colors line-clamp-1 block">
                                                {{ $ticket->title }}
                                            </a>
                                            <span class="text-xs text-gray-400">
                                                {{ __('by') }} {{ $ticket->creator?->name ?? __('Unknown') }} &bull; {{ $ticket->created_at->diffForHumans() }}
                                            </span>
                                        </td>

                                        <!-- Status Badge -->
                                        <td class="py-4 px-3 whitespace-nowrap">
                                            @php
                                                $statusClasses = match ($ticket->status) {
                                                    'new' => 'bg-blue-50 text-blue-700 border-blue-200/60',
                                                    'in_progress' => 'bg-brand-50 text-brand-700 border-brand-200/60',
                                                    'waiting' => 'bg-amber-50 text-amber-700 border-amber-200/60',
                                                    'blocked' => 'bg-red-50 text-red-700 border-red-200/60',
                                                    'closed' => 'bg-emerald-50 text-emerald-700 border-emerald-200/60',
                                                    default => 'bg-gray-50 text-gray-700 border-gray-200/60',
                                                };
                                                $statusDot = match ($ticket->status) {
                                                    'new' => 'bg-blue-500',
                                                    'in_progress' => 'bg-brand-500',
                                                    'waiting' => 'bg-amber-500',
                                                    'blocked' => 'bg-red-500',
                                                    'closed' => 'bg-emerald-500',
                                                    default => 'bg-gray-400',
                                                };
                                            @endphp
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold border {{ $statusClasses }}">
                                                <span class="w-1.5 h-1.5 rounded-full {{ $statusDot }}"></span>
                                                <span>{{ ucfirst(str_replace('_', ' ', $ticket->status)) }}</span>
                                            </span>
                                        </td>

                                        <!-- Priority Badge -->
                                        <td class="py-4 px-3 whitespace-nowrap">
                                            @php
                                                $priorityClasses = match ($ticket->priority) {
                                                    'urgent' => 'text-red-700 bg-red-50 border-red-200',
                                                    'high' => 'text-rose-700 bg-rose-50 border-rose-200',
                                                    'medium' => 'text-amber-700 bg-amber-50 border-amber-200',
                                                    'low' => 'text-slate-600 bg-slate-50 border-slate-200',
                                                    default => 'text-gray-600 bg-gray-50 border-gray-200',
                                                };
                                            @endphp
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold border {{ $priorityClasses }}">
                                                {{ ucfirst($ticket->priority) }}
                                            </span>
                                        </td>

                                        <!-- Assignee -->
                                        <td class="py-4 px-3 whitespace-nowrap">
                                            @if ($ticket->assignedTo)
                                                <div class="flex items-center gap-2">
                                                    <span class="w-6 h-6 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center text-[10px] font-bold">
                                                        {{ strtoupper(substr($ticket->assignedTo->name, 0, 1)) }}
                                                    </span>
                                                    <span class="text-xs text-gray-700 font-medium">
                                                        {{ $ticket->assignedTo->name }}
                                                    </span>
                                                </div>
                                            @else
                                                <span class="text-xs text-rose-600 font-semibold italic">
                                                    {{ __('Unassigned') }}
                                                </span>
                                            @endif
                                        </td>

                                        <!-- Due Date & Overdue Badge -->
                                        <td class="py-4 px-3 whitespace-nowrap text-xs">
                                            @if ($ticket->due_date)
                                                <div class="flex items-center gap-1.5">
                                                    <span class="text-gray-600 font-medium">
                                                        {{ $ticket->due_date->format('M d, Y') }}
                                                    </span>
                                                    @if ($ticket->is_overdue)
                                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-red-100 text-red-700">
                                                            {{ __('OVERDUE') }}
                                                        </span>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-gray-400">&mdash;</span>
                                            @endif
                                        </td>

                                        <!-- Action Link -->
                                        <td class="py-4 pl-3 pr-6 text-right whitespace-nowrap">
                                            <a href="{{ route('tickets.show', $ticket) }}"
                                                class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-gray-700 bg-gray-50 hover:bg-brand-50 hover:text-brand-600 rounded-lg border border-gray-200/80 hover:border-brand-200 transition-all">
                                                <span>{{ __('View') }}</span>
                                                <x-heroicon-m-arrow-up-right class="w-3.5 h-3.5" />
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="p-5 border-t border-gray-100">
                        {{ $tickets->links() }}
                    </div>
                @endif

            </div>

        </div>
    </div>
</x-app-layout>
