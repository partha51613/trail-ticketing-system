<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-bold tracking-tight text-gray-900">
                        @if (request()->boolean('watched'))
                            {{ __('Watched Tickets') }}
                        @elseif (request('assigned_to_id') == auth()->id())
                            {{ __('Assigned to Me') }}
                        @elseif (request('created_by_id') == auth()->id())
                            {{ __('Created by Me') }}
                        @else
                            {{ __('All Tickets') }}
                        @endif
                    </h1>
                    <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">
                        {{ $tickets->total() }} {{ __('tickets') }}
                    </span>
                </div>
                <p class="text-xs text-gray-500 mt-1">
                    {{ __('Browse, filter, and track issues across all workspaces.') }}
                </p>
            </div>

            @can('create', App\Models\Ticket::class)
                <a href="{{ route('tickets.create') }}"
                    class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-white bg-brand-500 rounded-xl hover:bg-brand-600 shadow-sm shadow-brand-500/20 transition-all self-start sm:self-auto">
                    <x-heroicon-m-plus class="w-4 h-4" />
                    <span>{{ __('New Ticket') }}</span>
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Filter & Search Toolbar Card -->
            <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-xs" x-data="{
                project: '{{ request('project_id') }}',
                status: '{{ request('status') }}',
                priority: '{{ request('priority') }}',
                assignee: '{{ request('assigned_to_id') }}',
                applyFilters() {
                    const params = new URLSearchParams(window.location.search);
                    if (this.project) { params.set('project_id', this.project); } else { params.delete('project_id'); }
                    if (this.status) { params.set('status', this.status); } else { params.delete('status'); }
                    if (this.priority) { params.set('priority', this.priority); } else { params.delete('priority'); }
                    if (this.assignee) { params.set('assigned_to_id', this.assignee); } else { params.delete('assigned_to_id'); }
                    params.delete('page');
                    window.location.href = `${window.location.pathname}?${params.toString()}`;
                }
            }">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div class="flex flex-wrap items-center gap-2.5">
                        <!-- Project Filter -->
                        <select x-model="project" @change="applyFilters()"
                            class="py-1.5 pl-3 pr-8 text-xs rounded-xl bg-gray-50 border border-gray-200 text-gray-700 focus:bg-white focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all">
                            <option value="">{{ __('All Projects') }}</option>
                            @foreach ($projects as $proj)
                                <option value="{{ $proj->id }}" @selected(request('project_id') == $proj->id)>
                                    {{ $proj->name }} ({{ $proj->code }})
                                </option>
                            @endforeach
                        </select>

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

                        <!-- Assignee Filter -->
                        <select x-model="assignee" @change="applyFilters()"
                            class="py-1.5 pl-3 pr-8 text-xs rounded-xl bg-gray-50 border border-gray-200 text-gray-700 focus:bg-white focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all">
                            <option value="">{{ __('All Assignees') }}</option>
                            <option value="unassigned">{{ __('Unassigned Only') }}</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}" @selected(request('assigned_to_id') == $user->id)>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Reset Filters Button (if any filter active) -->
                    @if (request()->hasAny(['project_id', 'status', 'priority', 'assigned_to_id', 'search', 'watched', 'created_by_id']))
                        <a href="{{ route('tickets.index') }}"
                            class="inline-flex items-center gap-1 text-xs font-semibold text-rose-600 hover:text-rose-700 hover:underline">
                            <x-heroicon-m-x-mark class="w-3.5 h-3.5" />
                            <span>{{ __('Clear Filters') }}</span>
                        </a>
                    @endif
                </div>
            </div>

            <!-- Tickets Table Card -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-xs overflow-hidden">
                @if ($tickets->isEmpty())
                    <div class="p-12 text-center">
                        <div
                            class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-gray-50 text-gray-400 mb-3">
                            <x-heroicon-o-ticket class="w-6 h-6" />
                        </div>
                        <h3 class="text-sm font-bold text-gray-900">{{ __('No tickets found') }}</h3>
                        <p class="text-xs text-gray-500 mt-1 max-w-sm mx-auto">
                            {{ __('Try adjusting your filter criteria or create a new ticket to get started.') }}
                        </p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr
                                    class="border-b border-gray-100 bg-gray-50/50 text-[11px] font-semibold uppercase tracking-wider text-gray-400">
                                    <th scope="col" class="py-3.5 pl-6 pr-3">{{ __('Key') }}</th>
                                    <th scope="col" class="py-3.5 px-3">{{ __('Title') }}</th>
                                    <th scope="col" class="py-3.5 px-3">{{ __('Project') }}</th>
                                    <th scope="col" class="py-3.5 px-3">{{ __('Status') }}</th>
                                    <th scope="col" class="py-3.5 px-3">{{ __('Priority') }}</th>
                                    <th scope="col" class="py-3.5 px-3">{{ __('Assignee') }}</th>
                                    <th scope="col" class="py-3.5 px-3">{{ __('Due Date') }}</th>
                                    <th scope="col" class="py-3.5 pl-3 pr-6 text-right">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-sm">
                                @foreach ($tickets as $ticket)
                                    <tr class="hover:bg-gray-50/70 transition-colors group">
                                        <!-- Reference Code -->
                                        <td class="py-4 pl-6 pr-3 whitespace-nowrap">
                                            <a href="{{ route('tickets.show', $ticket) }}"
                                                class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-mono font-bold bg-amber-50 text-amber-800 border border-amber-200/80 hover:bg-amber-100 transition-colors">
                                                {{ $ticket->reference_code }}
                                            </a>
                                        </td>

                                        <!-- Title & Author -->
                                        <td class="py-4 px-3 max-w-xs sm:max-w-md">
                                            <a href="{{ route('tickets.show', $ticket) }}"
                                                class="font-semibold text-gray-900 group-hover:text-brand-600 transition-colors line-clamp-1 block">
                                                {{ $ticket->title }}
                                            </a>
                                            <span class="text-xs text-gray-400">
                                                {{ __('by') }} {{ $ticket->creator?->name ?? __('Unknown') }}
                                                &bull;
                                                <span title="@ist($ticket->created_at)" class="cursor-help">
                                                    {{ $ticket->created_at->diffForHumans() }}
                                                </span>
                                            </span>
                                        </td>

                                        <!-- Project -->
                                        <td class="py-4 px-3 whitespace-nowrap">
                                            <a href="{{ route('projects.show', $ticket->project) }}"
                                                class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-gray-100 text-gray-700 hover:bg-gray-200/70 transition-colors">
                                                {{ $ticket->project->name }}
                                            </a>
                                        </td>

                                        <!-- Status Badge -->
                                        <td class="py-4 px-3 whitespace-nowrap">
                                            @php
                                                $statusClasses = match ($ticket->status) {
                                                    'new' => 'bg-blue-50 text-blue-700 border-blue-200/60',
                                                    'in_progress' => 'bg-brand-50 text-brand-700 border-brand-200/60',
                                                    'waiting' => 'bg-purple-50 text-purple-700 border-purple-200/60',
                                                    'blocked' => 'bg-rose-50 text-rose-700 border-rose-200/60',
                                                    'closed' => 'bg-emerald-50 text-emerald-700 border-emerald-200/60',
                                                    default => 'bg-gray-50 text-gray-700 border-gray-200/60',
                                                };
                                            @endphp
                                            <span
                                                class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $statusClasses }}">
                                                <span
                                                    class="w-1.5 h-1.5 rounded-full {{ match ($ticket->status) {'new' => 'bg-blue-500','in_progress' => 'bg-brand-500','waiting' => 'bg-purple-500','blocked' => 'bg-rose-500','closed' => 'bg-emerald-500',default => 'bg-gray-500'} }}"></span>
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
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold border {{ $priorityClasses }}">
                                                {{ ucfirst($ticket->priority) }}
                                            </span>
                                        </td>

                                        <!-- Assignee -->
                                        <td class="py-4 px-3 whitespace-nowrap">
                                            @if ($ticket->assignedTo)
                                                <div class="flex items-center gap-2">
                                                    <span
                                                        class="w-6 h-6 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center text-[10px] font-bold ring-1 ring-gray-200 shrink-0">
                                                        {{ strtoupper(substr($ticket->assignedTo->name, 0, 1)) }}
                                                    </span>
                                                    <span
                                                        class="text-xs text-gray-700 font-medium truncate max-w-[120px]">
                                                        {{ $ticket->assignedTo->name }}
                                                    </span>
                                                </div>
                                            @else
                                                <span class="text-xs text-rose-600 font-semibold italic">
                                                    {{ __('Unassigned') }}
                                                </span>
                                            @endif
                                        </td>

                                        <!-- Due Date -->
                                        <td class="py-4 px-3 whitespace-nowrap text-xs">
                                            @if ($ticket->due_date)
                                                <div class="flex items-center gap-1.5">
                                                    <span title="{{ $ticket->due_date->endOfDay()->diffForHumans() }}"
                                                        class="text-gray-600 font-medium cursor-help">
                                                        {{ $ticket->due_date->format('M d, Y') }}
                                                    </span>
                                                    @if ($ticket->is_overdue)
                                                        <span
                                                            class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-red-100 text-red-700">
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

                    <!-- Pagination Sliding Window -->
                    <div class="p-5 border-t border-gray-100">
                        {{ $tickets->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
