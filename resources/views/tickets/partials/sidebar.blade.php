<div class="space-y-6">

    <!-- Status Transition Card -->
    <div x-data="{ open: false, selectedStatus: '{{ $ticket->status }}' }"
        class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-slate-800 p-6 shadow-xs space-y-4">
        <span class="text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-slate-500 block">
            {{ __('Status') }}
        </span>

        <!-- Current Status Badge & Change Button Side-by-Side -->
        <div class="flex items-center justify-between gap-3 flex-wrap">
            <x-ticket.status-badge :status="$ticket->status" size="lg" />

            <button type="button" @click="open = !open"
                class="inline-flex items-center gap-1.5 text-xs font-semibold text-brand-600 dark:text-brand-400 hover:text-brand-700 dark:hover:text-brand-300 bg-brand-50 dark:bg-brand-950/40 hover:bg-brand-100/80 dark:hover:bg-brand-900/60 px-3 py-1.5 rounded-xl border border-brand-200/60 dark:border-brand-800/50 transition-colors shadow-xs">
                <x-heroicon-m-arrows-right-left class="w-3.5 h-3.5" />
                <span x-text="open ? '{{ __('Cancel') }}' : '{{ __('Change Status') }}'"></span>
            </button>
        </div>

        <!-- Transition Form (Reveals upon clicking 'Change Status') -->
        <div x-show="open" x-cloak class="pt-3 border-t border-gray-100 dark:border-slate-800">
            <form action="{{ route('tickets.transition', $ticket) }}" method="POST"
                class="space-y-3">
                @csrf
                @method('PATCH')

                <div>
                    <label for="status" class="block text-xs font-medium text-gray-600 dark:text-slate-300 mb-1">
                        {{ __('Select New Status:') }}
                    </label>
                    <select name="status" id="status" x-model="selectedStatus"
                        class="w-full text-xs rounded-xl border border-gray-200 dark:border-slate-700 bg-gray-50/70 dark:bg-slate-800 p-2.5 text-gray-900 dark:text-white focus:bg-white dark:focus:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all font-medium">
                        <option value="new">{{ __('New') }}</option>
                        <option value="in_progress">{{ __('In Progress') }}</option>
                        <option value="waiting">{{ __('Waiting') }}</option>
                        <option value="blocked">{{ __('Blocked') }}</option>
                        <option value="closed">{{ __('Closed') }}</option>
                    </select>
                </div>

                <!-- Atomic Blocked Reason Comment Requirement -->
                <div x-show="selectedStatus === 'blocked'" x-cloak
                    class="space-y-1.5 p-3 rounded-xl bg-red-50/70 dark:bg-red-950/30 border border-red-200/80 dark:border-red-800/50">
                    <label class="block text-[11px] font-bold text-red-800 dark:text-red-300">
                        {{ __('Mandatory Blocked Reason:') }}
                    </label>
                    <textarea name="comment" rows="2" x-bind:required="selectedStatus === 'blocked'"
                        placeholder="{{ __('Explain what is blocking this ticket...') }}"
                        class="w-full text-xs rounded-lg border border-red-200 dark:border-red-800/60 bg-white dark:bg-slate-950 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-slate-500 p-2 focus:ring-2 focus:ring-red-400 focus:outline-none"></textarea>
                </div>

                <div class="flex items-center gap-2">
                    <button type="button" @click="open = false"
                        class="flex-1 py-2 text-xs font-medium text-gray-600 dark:text-slate-300 hover:text-gray-900 dark:hover:text-white bg-gray-50 dark:bg-slate-800 hover:bg-gray-100 dark:hover:bg-slate-700 rounded-xl transition-colors">
                        {{ __('Cancel') }}
                    </button>
                    <button type="submit"
                        class="flex-1 inline-flex items-center justify-center gap-1.5 px-4 py-2 text-xs font-semibold text-white bg-brand-500 rounded-xl hover:bg-brand-600 shadow-sm shadow-brand-500/20 transition-all">
                        <span>{{ __('Apply') }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Quick Reassign Card -->
    <div x-data="{ reassignOpen: false }"
        class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-slate-800 p-6 shadow-xs space-y-4">
        <span class="text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-slate-500 block">
            {{ __('Assignee') }}
        </span>

        <!-- Current Assignee Info & Reassign Button Side-by-Side -->
        <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-3 min-w-0">
                <x-ui.avatar :name="$ticket->assignedTo?->name" size="lg" />
                <div class="min-w-0 flex-1">
                    <div class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                        {{ $ticket->assignedTo?->name ?? __('Unassigned') }}
                    </div>
                    <div class="text-xs text-gray-400 dark:text-slate-500 truncate">
                        {{ $ticket->assignedTo?->email ?? __('Not assigned to anyone yet') }}
                    </div>
                </div>
            </div>

            @can('reassign', $ticket)
                <button type="button" @click="reassignOpen = !reassignOpen"
                    class="inline-flex items-center gap-1.5 text-xs font-semibold text-brand-600 dark:text-brand-400 hover:text-brand-700 dark:hover:text-brand-300 bg-brand-50 dark:bg-brand-950/40 hover:bg-brand-100/80 dark:hover:bg-brand-900/60 px-3 py-1.5 rounded-xl border border-brand-200/60 dark:border-brand-800/50 transition-colors shadow-xs shrink-0">
                    <x-heroicon-m-user-plus class="w-3.5 h-3.5" />
                    <span x-text="reassignOpen ? '{{ __('Cancel') }}' : '{{ __('Reassign') }}'"></span>
                </button>
            @endcan
        </div>

        <!-- Inline Reassignment Form (Reveals upon clicking 'Reassign') -->
        @can('reassign', $ticket)
            <div x-show="reassignOpen" x-cloak class="pt-3 border-t border-gray-100 dark:border-slate-800">
                <form action="{{ route('tickets.reassign', $ticket) }}" method="POST"
                    class="space-y-3">
                    @csrf
                    @method('PATCH')
                    <div>
                        <label for="assigned_to_id" class="block text-xs font-medium text-gray-600 dark:text-slate-300 mb-1">
                            {{ __('Select New Assignee:') }}
                        </label>
                        <select name="assigned_to_id" id="assigned_to_id"
                            class="w-full text-xs rounded-xl border border-gray-200 dark:border-slate-700 bg-gray-50/70 dark:bg-slate-800 p-2.5 text-gray-900 dark:text-white focus:bg-white dark:focus:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all font-medium">
                            <option value="">{{ __('Unassigned') }}</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}" @selected($ticket->assigned_to_id === $user->id)>
                                    {{ $user->name }} ({{ ucfirst($user->role) }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" @click="reassignOpen = false"
                            class="flex-1 py-2 text-xs font-medium text-gray-600 dark:text-slate-300 hover:text-gray-900 dark:hover:text-white bg-gray-50 dark:bg-slate-800 hover:bg-gray-100 dark:hover:bg-slate-700 rounded-xl transition-colors">
                            {{ __('Cancel') }}
                        </button>
                        <button type="submit"
                            class="flex-1 inline-flex items-center justify-center px-4 py-2 text-xs font-semibold text-white bg-brand-500 rounded-xl hover:bg-brand-600 shadow-sm shadow-brand-500/20 transition-all">
                            {{ __('Save Assignee') }}
                        </button>
                    </div>
                </form>
            </div>
        @endcan
    </div>

    <!-- Meta & Attributes Card -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-slate-800 p-6 shadow-xs space-y-4 text-xs">
        <span class="text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-slate-500 block">
            {{ __('Ticket Details') }}
        </span>

        <!-- Ticket Reference & Number -->
        <div class="flex items-center justify-between">
            <span class="text-gray-500 dark:text-slate-400 font-medium">{{ __('Ticket Number') }}</span>
            <span class="font-bold text-gray-900 dark:text-white font-mono">{{ $ticket->reference_code }}</span>
        </div>

        <!-- Priority with Inline Change Action -->
        <div x-data="{ priorityOpen: false, selectedPriority: '{{ $ticket->priority }}' }" class="border-t border-gray-100 dark:border-slate-800 pt-3 space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-gray-500 dark:text-slate-400 font-medium">{{ __('Priority') }}</span>
                <div class="flex items-center gap-2">
                    <x-ticket.priority-badge :priority="$ticket->priority" />

                    @can('update', $ticket)
                        <button type="button" @click="priorityOpen = !priorityOpen"
                            class="inline-flex items-center p-1 rounded-md text-gray-400 dark:text-slate-500 hover:text-brand-600 dark:hover:text-brand-400 hover:bg-brand-50 dark:hover:bg-brand-950/40 transition-colors"
                            title="{{ __('Change Priority') }}">
                            <x-heroicon-m-pencil-square class="w-3.5 h-3.5" />
                        </button>
                    @endcan
                </div>
            </div>

            <!-- Inline Priority Change Form -->
            @can('update', $ticket)
                <div x-show="priorityOpen" x-cloak class="pt-2">
                    <form action="{{ route('tickets.priority', $ticket) }}" method="POST"
                        class="space-y-2">
                        @csrf
                        @method('PATCH')
                        <div class="flex items-center gap-2">
                            <select name="priority" x-model="selectedPriority"
                                class="flex-1 text-xs rounded-xl border border-gray-200 dark:border-slate-700 bg-gray-50/70 dark:bg-slate-800 p-2 text-gray-900 dark:text-white focus:bg-white dark:focus:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 font-medium">
                                <option value="low">{{ __('Low') }}</option>
                                <option value="medium">{{ __('Medium') }}</option>
                                <option value="high">{{ __('High') }}</option>
                                <option value="urgent">{{ __('Urgent') }}</option>
                            </select>
                            <button type="button" @click="priorityOpen = false"
                                class="px-2.5 py-1.5 text-xs text-gray-500 dark:text-slate-400 hover:text-gray-700 dark:hover:text-slate-200">
                                {{ __('Cancel') }}
                            </button>
                            <button type="submit"
                                class="px-3 py-1.5 text-xs font-semibold text-white bg-brand-500 hover:bg-brand-600 rounded-xl shadow-xs transition-all">
                                {{ __('Save') }}
                            </button>
                        </div>
                    </form>
                </div>
            @endcan
        </div>

        <!-- Due Date (with 11:59 PM deadline) -->
        <div class="flex items-center justify-between border-t border-gray-100 dark:border-slate-800 pt-3">
            <span class="text-gray-500 dark:text-slate-400 font-medium">{{ __('Due Date') }}</span>
            @if ($ticket->due_date)
                <div class="flex items-center gap-1.5 flex-wrap justify-end">
                    <span class="font-semibold text-gray-800 dark:text-slate-200">
                        {{ $ticket->due_date->format('M d, Y') }}, 11:59 PM
                    </span>
                    @if ($ticket->is_overdue)
                        <span
                            class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-red-100 dark:bg-red-950/60 text-red-700 dark:text-red-300 border border-red-200/50 dark:border-red-800/40">
                            {{ __('OVERDUE') }}
                        </span>
                    @endif
                </div>
            @else
                <span class="text-gray-400 dark:text-slate-600 italic">{{ __('None set') }}</span>
            @endif
        </div>

        <!-- Created On (matching 'M d, Y, h:i A' format) -->
        <div class="flex items-center justify-between border-t border-gray-100 dark:border-slate-800 pt-3">
            <span class="text-gray-500 dark:text-slate-400 font-medium">{{ __('Created On') }}</span>
            <span title="{{ $ticket->created_at->diffForHumans() }}"
                class="font-semibold text-gray-800 dark:text-slate-200 cursor-help">
                {{ $ticket->created_at->setTimezone('Asia/Kolkata')->format('M d, Y, h:i A') }}
            </span>
        </div>

        <!-- Workspace -->
        <div class="flex items-center justify-between border-t border-gray-100 dark:border-slate-800 pt-3">
            <span class="text-gray-500 dark:text-slate-400 font-medium">{{ __('Project') }}</span>
            <a href="{{ route('projects.show', $ticket->project) }}"
                class="font-semibold text-brand-600 dark:text-brand-400 hover:text-brand-700 dark:hover:text-brand-300 hover:underline truncate max-w-[150px]">
                {{ $ticket->project->name }}
            </a>
        </div>

        <!-- Watchers -->
        <div class="border-t border-gray-100 dark:border-slate-800 pt-3 space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-gray-500 dark:text-slate-400 font-medium">{{ __('Watchers') }}</span>
                <span class="font-semibold text-gray-700 dark:text-slate-300">{{ $ticket->watchers->count() }}</span>
            </div>
            @if ($ticket->watchers->isNotEmpty())
                <div class="flex items-center -space-x-1.5 overflow-hidden pt-1">
                    @foreach ($ticket->watchers->take(6) as $watcher)
                        <x-ui.avatar :name="$watcher->name" size="md" class="ring-2 ring-white dark:ring-slate-900" />
                    @endforeach
                </div>
            @endif
        </div>
    </div>

</div>
