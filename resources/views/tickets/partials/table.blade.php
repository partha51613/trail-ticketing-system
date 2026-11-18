<div id="tickets-table" class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-slate-800 shadow-xs overflow-hidden scroll-mt-6">
    @if ($tickets->isEmpty())
        <div class="p-12 text-center">
            <div
                class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-gray-50 dark:bg-slate-800 text-gray-400 dark:text-slate-500 mb-3 border border-gray-100 dark:border-slate-700">
                <x-heroicon-o-ticket class="w-6 h-6" />
            </div>
            <h3 class="text-sm font-bold text-gray-900 dark:text-white">{{ __('No tickets found') }}</h3>
            <p class="text-xs text-gray-500 dark:text-slate-400 mt-1 max-w-sm mx-auto">
                {{ __('Try adjusting your filter criteria or create a new ticket to get started.') }}
            </p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-slate-800 bg-gray-50/50 dark:bg-slate-850/60">
                        <x-table.th class="pl-6">{{ __('Key') }}</x-table.th>
                        <x-table.th>{{ __('Title') }}</x-table.th>
                        <x-table.th>{{ __('Project') }}</x-table.th>
                        <x-table.th>{{ __('Status') }}</x-table.th>
                        <x-table.th>{{ __('Priority') }}</x-table.th>
                        <x-table.th>{{ __('Assignee') }}</x-table.th>
                        <x-table.th>{{ __('Due Date') }}</x-table.th>
                        <x-table.th align="right">{{ __('Action') }}</x-table.th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-800 text-sm">
                    @foreach ($tickets as $ticket)
                        <tr class="hover:bg-gray-50/70 dark:hover:bg-slate-800/50 transition-colors group">
                            <!-- Reference Code -->
                            <td class="py-4 pl-6 pr-3 whitespace-nowrap">
                                <a href="{{ route('tickets.show', $ticket) }}"
                                    class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-mono font-bold bg-amber-50 dark:bg-amber-950/40 text-amber-800 dark:text-amber-300 border border-amber-200/80 dark:border-amber-800/50 hover:bg-amber-100 dark:hover:bg-amber-900/60 transition-colors">
                                    {{ $ticket->reference_code }}
                                </a>
                            </td>

                            <!-- Title & Author -->
                            <td class="py-4 px-3 max-w-xs sm:max-w-md">
                                <a href="{{ route('tickets.show', $ticket) }}"
                                    class="font-semibold text-gray-900 dark:text-white group-hover:text-brand-600 dark:group-hover:text-brand-400 transition-colors line-clamp-1 block">
                                    {{ $ticket->title }}
                                </a>
                                <span class="text-xs text-gray-400 dark:text-slate-500">
                                    {{ __('by') }} <span class="text-gray-600 dark:text-slate-300">{{ $ticket->creator?->name ?? __('Unknown') }}</span>
                                    &bull;
                                    <span title="@ist($ticket->created_at)" class="cursor-help">
                                        {{ $ticket->created_at->diffForHumans() }}
                                    </span>
                                </span>
                            </td>

                            <!-- Project -->
                            <td class="py-4 px-3 whitespace-nowrap">
                                <a href="{{ route('projects.show', $ticket->project) }}"
                                    class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-gray-100 dark:bg-slate-800 text-gray-700 dark:text-slate-300 hover:bg-gray-200/70 dark:hover:bg-slate-700 border border-gray-200/60 dark:border-slate-700 transition-colors">
                                    {{ $ticket->project->name }}
                                </a>
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
                                        <span
                                            class="text-xs text-gray-700 dark:text-slate-300 font-medium truncate max-w-[120px]">
                                            {{ $ticket->assignedTo->name }}
                                        </span>
                                    </div>
                                @else
                                    <span class="text-xs text-rose-600 dark:text-rose-400 font-semibold italic">
                                        {{ __('Unassigned') }}
                                    </span>
                                @endif
                            </td>

                            <!-- Due Date -->
                            <td class="py-4 px-3 whitespace-nowrap text-xs">
                                @if ($ticket->due_date)
                                    <div class="flex items-center gap-1.5">
                                        <span title="{{ $ticket->due_date->endOfDay()->diffForHumans() }}"
                                            class="text-gray-600 dark:text-slate-400 font-medium cursor-help">
                                            {{ $ticket->due_date->format('M d, Y') }}
                                        </span>
                                        @if ($ticket->is_overdue)
                                            <span
                                                class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-red-100 dark:bg-red-950/60 text-red-700 dark:text-red-300 border border-red-200/50 dark:border-red-800/40">
                                                {{ __('OVERDUE') }}
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-gray-400 dark:text-slate-600">&mdash;</span>
                                @endif
                            </td>

                            <!-- Action Link / Restore Action -->
                            <td class="py-4 pl-3 pr-6 text-right whitespace-nowrap">
                                @if ($ticket->trashed())
                                    @can('restore', $ticket)
                                        <form action="{{ route('tickets.restore', $ticket->id) }}" method="POST" class="inline-block"
                                            id="restore-ticket-index-{{ $ticket->id }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="button"
                                                @click="$dispatch('open-confirm-modal', {
                                                    title: '{{ __('Restore Ticket?') }}',
                                                    message: '{{ __('Are you sure you want to restore this ticket to active status?') }}',
                                                    confirmText: '{{ __('Restore') }}',
                                                    isDanger: false,
                                                    form: 'restore-ticket-index-{{ $ticket->id }}'
                                                })"
                                                class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-950/40 hover:bg-emerald-100 dark:hover:bg-emerald-900/60 rounded-lg border border-emerald-200 dark:border-emerald-800/50 transition-all shadow-xs">
                                                <x-heroicon-m-arrow-path class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" />
                                                <span>{{ __('Restore') }}</span>
                                            </button>
                                        </form>
                                    @endcan
                                @else
                                    <a href="{{ route('tickets.show', $ticket) }}"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-gray-700 dark:text-slate-300 bg-gray-50 dark:bg-slate-800 hover:bg-brand-50 dark:hover:bg-brand-950/40 hover:text-brand-600 dark:hover:text-brand-400 rounded-lg border border-gray-200/80 dark:border-slate-700 hover:border-brand-200 dark:hover:border-brand-800/50 transition-all">
                                        <span>{{ __('View') }}</span>
                                        <x-heroicon-m-arrow-up-right class="w-3.5 h-3.5" />
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination Sliding Window -->
        <div class="p-5 border-t border-gray-100 dark:border-slate-800">
            {{ $tickets->links() }}
        </div>
    @endif
</div>
