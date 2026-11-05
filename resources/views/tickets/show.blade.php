<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3 min-w-0">
                <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('projects.show', $ticket->project_id) }}"
                    class="p-2 rounded-xl text-gray-500 bg-white hover:text-gray-900 hover:bg-gray-50 border border-gray-200/90 hover:border-gray-300 shadow-xs transition-all shrink-0"
                    title="{{ __('Go Back') }}">
                    <x-heroicon-m-arrow-left class="w-5 h-5" />
                </a>
                <div class="min-w-0">
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <span
                            class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-mono font-bold bg-amber-300/30 text-amber-800 border border-amber-200/80 shadow-2xs">
                            {{ $ticket->reference_code }}
                        </span>
                        <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-gray-900 truncate">
                            {{ $ticket->title }}
                        </h1>
                    </div>
                    <div class="flex items-center gap-2 mt-0.5 text-xs text-gray-500 flex-wrap">
                        <span>{{ __('Opened') }} {{ $ticket->created_at->diffForHumans() }}</span>
                        <span>&bull;</span>
                        <span>{{ __('by') }} <strong
                                class="text-gray-700 font-semibold">{{ $ticket->creator?->name ?? __('Unknown') }}</strong></span>
                        <span>&bull;</span>
                        <a href="{{ route('projects.show', $ticket->project) }}"
                            class="font-semibold text-brand-600 hover:text-brand-700 hover:underline">
                            {{ $ticket->project->name }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- Quick Action Buttons -->
            <div class="flex items-center gap-2.5 shrink-0">
                <!-- Watch / Unwatch Button -->
                <form action="{{ route('tickets.watch', $ticket) }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-xl border transition-all shadow-xs {{ $isWatching ? 'bg-amber-50 text-amber-700 border-amber-200 hover:bg-amber-100' : 'bg-white text-gray-700 border-gray-200/90 hover:bg-gray-50' }}">
                        @if ($isWatching)
                            <x-heroicon-s-bell-alert class="w-4 h-4 text-amber-600" />
                            <span>{{ __('Watching') }}</span>
                        @else
                            <x-heroicon-o-bell class="w-4 h-4 text-gray-500" />
                            <span>{{ __('Watch') }}</span>
                        @endif
                    </button>
                </form>

                <!-- Edit Ticket -->
                @can('update', $ticket)
                    <a href="{{ route('tickets.edit', $ticket) }}"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-gray-700 bg-white border border-gray-200/90 rounded-xl hover:bg-gray-50 hover:border-gray-300 shadow-xs transition-all">
                        <x-heroicon-m-pencil-square class="w-4 h-4 text-gray-500" />
                        <span>{{ __('Edit') }}</span>
                    </a>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-gray-50/60 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 sm:gap-8 items-start">

                <!-- Left Column (Main Ticket Body & Tabs - 2/3) -->
                <div class="lg:col-span-2 space-y-6">

                    <!-- Description Card -->
                    <div class="bg-white rounded-2xl border border-gray-100 p-6 sm:p-7 shadow-xs">
                        <h2 class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-3">
                            {{ __('Description') }}
                        </h2>
                        <div class="text-sm text-gray-700 leading-relaxed whitespace-pre-line">
                            {{ $ticket->description ?: __('No detailed description provided.') }}
                        </div>
                    </div>

                    <!-- Tabs: Comments vs Activity Log -->
                    <div x-data="{ tab: 'comments' }"
                        class="bg-white rounded-2xl border border-gray-100 shadow-xs overflow-hidden">
                        <!-- Tab Bar -->
                        <div class="border-b border-gray-100 px-6 pt-4 flex items-center justify-between gap-4">
                            <div class="flex items-center gap-6">
                                <button type="button" @click="tab = 'comments'"
                                    :class="tab === 'comments' ? 'border-brand-500 text-brand-600 font-bold' :
                                        'border-transparent text-gray-500 hover:text-gray-700 font-medium'"
                                    class="pb-3.5 border-b-2 text-sm transition-all flex items-center gap-2">
                                    <span>{{ __('Comments') }}</span>
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold"
                                        :class="tab === 'comments' ? 'bg-brand-50 text-brand-700' : 'bg-gray-100 text-gray-600'">
                                        {{ $ticket->comments->count() }}
                                    </span>
                                </button>

                                <button type="button" @click="tab = 'activity'"
                                    :class="tab === 'activity' ? 'border-brand-500 text-brand-600 font-bold' :
                                        'border-transparent text-gray-500 hover:text-gray-700 font-medium'"
                                    class="pb-3.5 border-b-2 text-sm transition-all flex items-center gap-2">
                                    <span>{{ __('Activity History') }}</span>
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold"
                                        :class="tab === 'activity' ? 'bg-brand-50 text-brand-700' : 'bg-gray-100 text-gray-600'">
                                        {{ $ticket->activityLogs->count() }}
                                    </span>
                                </button>
                            </div>
                        </div>

                        <!-- Tab 1: Comments Thread -->
                        <div x-show="tab === 'comments'" class="p-6 space-y-6">

                            <!-- New Comment Box -->
                            <form action="{{ route('comments.store', $ticket) }}" method="POST" class="space-y-3">
                                @csrf
                                <div class="relative">
                                    <textarea name="body" rows="3" required placeholder="{{ __('Add a comment or update on this ticket...') }}"
                                        class="w-full text-sm rounded-xl border border-gray-200 bg-gray-50/50 p-3.5 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 placeholder-gray-400 transition-all"></textarea>
                                </div>
                                <div class="flex justify-end">
                                    <button type="submit"
                                        class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold text-white bg-brand-500 rounded-xl hover:bg-brand-600 shadow-sm shadow-brand-500/20 transition-all">
                                        <x-heroicon-m-chat-bubble-left-right class="w-4 h-4" />
                                        <span>{{ __('Post Comment') }}</span>
                                    </button>
                                </div>
                            </form>

                            <!-- Comments List -->
                            @if ($ticket->comments->isEmpty())
                                <div class="py-8 text-center border-t border-gray-100">
                                    <div
                                        class="w-10 h-10 rounded-xl bg-gray-50 text-gray-400 flex items-center justify-center mx-auto mb-2">
                                        <x-heroicon-o-chat-bubble-left-ellipsis class="w-5 h-5" />
                                    </div>
                                    <p class="text-xs text-gray-500 font-medium">
                                        {{ __('No comments yet. Start the discussion above.') }}</p>
                                </div>
                            @else
                                <div class="space-y-4 pt-2 border-t border-gray-100">
                                    @foreach ($ticket->comments as $comment)
                                        <div x-data="{ editing: false, body: '{{ addslashes($comment->body) }}' }"
                                            class="bg-gray-50/60 rounded-xl border border-gray-100 p-4 space-y-2">
                                            <div class="flex items-center justify-between gap-3">
                                                <div class="flex items-center gap-2 min-w-0">
                                                    <!-- User Avatar -->
                                                    <span
                                                        class="w-6 h-6 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center text-[10px] font-bold ring-1 ring-gray-200 shrink-0">
                                                        {{ strtoupper(substr($comment->user?->name ?? 'U', 0, 1)) }}
                                                    </span>

                                                    <!-- Commenter Name -->
                                                    <span class="text-xs font-semibold text-gray-900 truncate">
                                                        {{ $comment->user?->name ?? __('Unknown') }}
                                                    </span>

                                                    <!-- Dot Separator -->
                                                    <span class="text-gray-300 text-xs shrink-0">&bull;</span>

                                                    <!-- Exact IST Timestamp (Hover reveals relative time) -->
                                                    <span title="{{ $comment->created_at->diffForHumans() }}"
                                                        class="text-xs text-gray-500 hover:text-amber-500  font-bold cursor-help transition-colors whitespace-nowrap">
                                                        @ist($comment->created_at)
                                                    </span>

                                                    <!-- Edited Pill -->
                                                    @if ($comment->edited_at)
                                                        <span
                                                            title="{{ __('Edited') }} {{ $comment->edited_at->diffForHumans() }} (@ist($comment->edited_at))"
                                                            class="text-[10px] font-medium text-gray-400 bg-gray-100 px-1.5 py-0.5 rounded cursor-help shrink-0">
                                                            {{ __('edited') }}
                                                        </span>
                                                    @endif
                                                </div>

                                                <!-- 5-Minute Window Actions (Policy Guarded) -->
                                                <div class="flex items-center gap-1.5">
                                                    @can('update', $comment)
                                                        <button type="button" @click="editing = !editing"
                                                            class="p-1 rounded-md text-gray-400 hover:text-gray-700 hover:bg-gray-200/60 transition-colors"
                                                            title="{{ __('Edit Comment (5m window)') }}">
                                                            <x-heroicon-m-pencil class="w-3.5 h-3.5" />
                                                        </button>
                                                    @endcan

                                                    @can('delete', $comment)
                                                        <form action="{{ route('comments.destroy', $comment) }}"
                                                            method="POST"
                                                            onsubmit="return confirm('{{ __('Are you sure you want to delete this comment?') }}');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                class="p-1 rounded-md text-gray-400 hover:text-rose-600 hover:bg-rose-50 transition-colors"
                                                                title="{{ __('Delete Comment') }}">
                                                                <x-heroicon-m-trash class="w-3.5 h-3.5" />
                                                            </button>
                                                        </form>
                                                    @endcan
                                                </div>
                                            </div>

                                            <!-- Comment Body / Inline Edit -->
                                            <div x-show="!editing"
                                                class="text-sm text-gray-700 whitespace-pre-line pl-9">
                                                {{ $comment->body }}
                                            </div>

                                            <div x-show="editing" x-cloak class="pl-9 pt-1">
                                                <form action="{{ route('comments.update', $comment) }}" method="POST"
                                                    class="space-y-2">
                                                    @csrf
                                                    @method('PATCH')
                                                    <textarea name="body" rows="2" required x-model="body"
                                                        class="w-full text-xs rounded-xl border border-gray-200 bg-white p-2.5 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500"></textarea>
                                                    <div class="flex items-center gap-2 justify-end">
                                                        <button type="button" @click="editing = false"
                                                            class="px-2.5 py-1 text-xs text-gray-500 hover:text-gray-700">
                                                            {{ __('Cancel') }}
                                                        </button>
                                                        <button type="submit"
                                                            class="px-3 py-1 text-xs font-semibold text-white bg-brand-500 hover:bg-brand-600 rounded-lg shadow-xs">
                                                            {{ __('Save') }}
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                        </div>

                        <!-- Tab 2: Activity Audit Timeline -->
                        <div x-show="tab === 'activity'" x-cloak class="p-6">
                            @if ($ticket->activityLogs->isEmpty())
                                <div class="py-8 text-center">
                                    <div
                                        class="w-10 h-10 rounded-xl bg-gray-50 text-gray-400 flex items-center justify-center mx-auto mb-2">
                                        <x-heroicon-o-clock class="w-5 h-5" />
                                    </div>
                                    <p class="text-xs text-gray-500 font-medium">{{ __('No activity recorded yet.') }}
                                    </p>
                                </div>
                            @else
                                <div
                                    class="relative pl-6 space-y-6 before:absolute before:left-2 before:top-2 before:bottom-2 before:w-0.5 before:bg-gray-200/80">
                                    @foreach ($ticket->activityLogs as $log)
                                        <div class="relative flex items-start gap-3">
                                            <div
                                                class="absolute -left-6 top-1 w-4 h-4 rounded-full bg-white border-2 border-brand-500 ring-2 ring-white">
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <p class="text-xs text-gray-800">
                                                    <strong
                                                        class="font-semibold text-gray-900">{{ $log->user?->name ?? __('System') }}</strong>
                                                    {{ __('changed') }} <span
                                                        class="font-medium text-gray-700 underline decoration-gray-300">{{ str_replace('_', ' ', $log->formatted_field) }}</span>
                                                </p>
                                                <div class="flex items-center gap-1.5 mt-1 text-xs font-mono">
                                                    <span
                                                        class="px-2 py-0.5 rounded bg-rose-50 text-rose-700 line-through">
                                                        {{ $log->formatValue($log->old_value) ?: __('empty') }}
                                                    </span>
                                                    <x-heroicon-m-arrow-right class="w-3.5 h-3.5 text-gray-400" />
                                                    <span
                                                        class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 font-semibold">
                                                        {{ $log->formatValue($log->new_value) }}
                                                    </span>
                                                </div>
                                                <span class="text-[10px] text-gray-400 mt-1 block">
                                                    {{ $log->created_at->format('M d, Y h:i A') }} &bull;
                                                    {{ $log->created_at->diffForHumans() }}
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                    </div>

                </div>

                <!-- Right Column (Sidebar Meta & Actions - 1/3) -->
                <div class="space-y-6">

                    <!-- Status Transition Card -->
                    <div x-data="{ open: false, selectedStatus: '{{ $ticket->status }}' }"
                        class="bg-white rounded-2xl border border-gray-100 p-6 shadow-xs space-y-4">
                        <span class="text-xs font-semibold uppercase tracking-wider text-gray-400 block">
                            {{ __('Status') }}
                        </span>

                        <!-- Current Status Badge & Change Button Side-by-Side -->
                        <div class="flex items-center justify-between gap-3 flex-wrap">
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
                            <span
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold border {{ $statusClasses }}">
                                <span class="w-2 h-2 rounded-full {{ $statusDot }}"></span>
                                <span>{{ ucfirst(str_replace('_', ' ', $ticket->status)) }}</span>
                            </span>

                            <button type="button" @click="open = !open"
                                class="inline-flex items-center gap-1.5 text-xs font-semibold text-brand-600 hover:text-brand-700 bg-brand-50 hover:bg-brand-100/80 px-3 py-1.5 rounded-xl border border-brand-200/60 transition-colors shadow-xs">
                                <x-heroicon-m-arrows-right-left class="w-3.5 h-3.5" />
                                <span x-text="open ? '{{ __('Cancel') }}' : '{{ __('Change Status') }}'"></span>
                            </button>
                        </div>

                        <!-- Transition Form (Reveals upon clicking 'Change Status') -->
                        <div x-show="open" x-cloak class="pt-3 border-t border-gray-100">
                            <form action="{{ route('tickets.transition', $ticket) }}" method="POST"
                                class="space-y-3">
                                @csrf
                                @method('PATCH')

                                <div>
                                    <label for="status" class="block text-xs font-medium text-gray-600 mb-1">
                                        {{ __('Select New Status:') }}
                                    </label>
                                    <select name="status" id="status" x-model="selectedStatus"
                                        class="w-full text-xs rounded-xl border border-gray-200 bg-gray-50/70 p-2.5 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all font-medium">
                                        <option value="new">{{ __('New') }}</option>
                                        <option value="in_progress">{{ __('In Progress') }}</option>
                                        <option value="waiting">{{ __('Waiting') }}</option>
                                        <option value="blocked">{{ __('Blocked') }}</option>
                                        <option value="closed">{{ __('Closed') }}</option>
                                    </select>
                                </div>

                                <!-- Atomic Blocked Reason Comment Requirement -->
                                <div x-show="selectedStatus === 'blocked'" x-cloak
                                    class="space-y-1.5 p-3 rounded-xl bg-red-50/70 border border-red-200/80">
                                    <label class="block text-[11px] font-bold text-red-800">
                                        {{ __('Mandatory Blocked Reason:') }}
                                    </label>
                                    <textarea name="comment" rows="2" x-bind:required="selectedStatus === 'blocked'"
                                        placeholder="{{ __('Explain what is blocking this ticket...') }}"
                                        class="w-full text-xs rounded-lg border border-red-200 bg-white p-2 focus:ring-2 focus:ring-red-400 focus:outline-none"></textarea>
                                </div>

                                <div class="flex items-center gap-2">
                                    <button type="button" @click="open = false"
                                        class="flex-1 py-2 text-xs font-medium text-gray-600 hover:text-gray-900 bg-gray-50 hover:bg-gray-100 rounded-xl transition-colors">
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
                        class="bg-white rounded-2xl border border-gray-100 p-6 shadow-xs space-y-4">
                        <span class="text-xs font-semibold uppercase tracking-wider text-gray-400 block">
                            {{ __('Assignee') }}
                        </span>

                        <!-- Current Assignee Info & Reassign Button Side-by-Side -->
                        <div class="flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <span
                                    class="w-9 h-9 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center text-sm font-bold ring-2 ring-white shrink-0">
                                    {{ strtoupper(substr($ticket->assignedTo?->name ?? '?', 0, 1)) }}
                                </span>
                                <div class="min-w-0 flex-1">
                                    <div class="text-sm font-semibold text-gray-900 truncate">
                                        {{ $ticket->assignedTo?->name ?? __('Unassigned') }}
                                    </div>
                                    <div class="text-xs text-gray-400 truncate">
                                        {{ $ticket->assignedTo?->email ?? __('Not assigned to anyone yet') }}
                                    </div>
                                </div>
                            </div>

                            @can('reassign', $ticket)
                                <button type="button" @click="reassignOpen = !reassignOpen"
                                    class="inline-flex items-center gap-1.5 text-xs font-semibold text-brand-600 hover:text-brand-700 bg-brand-50 hover:bg-brand-100/80 px-3 py-1.5 rounded-xl border border-brand-200/60 transition-colors shadow-xs shrink-0">
                                    <x-heroicon-m-user-plus class="w-3.5 h-3.5" />
                                    <span x-text="reassignOpen ? '{{ __('Cancel') }}' : '{{ __('Reassign') }}'"></span>
                                </button>
                            @endcan
                        </div>

                        <!-- Inline Reassignment Form (Reveals upon clicking 'Reassign') -->
                        @can('reassign', $ticket)
                            <div x-show="reassignOpen" x-cloak class="pt-3 border-t border-gray-100">
                                <form action="{{ route('tickets.reassign', $ticket) }}" method="POST"
                                    class="space-y-3">
                                    @csrf
                                    @method('PATCH')
                                    <div>
                                        <label for="assigned_to_id" class="block text-xs font-medium text-gray-600 mb-1">
                                            {{ __('Select New Assignee:') }}
                                        </label>
                                        <select name="assigned_to_id" id="assigned_to_id"
                                            class="w-full text-xs rounded-xl border border-gray-200 bg-gray-50/70 p-2.5 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all font-medium">
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
                                            class="flex-1 py-2 text-xs font-medium text-gray-600 hover:text-gray-900 bg-gray-50 hover:bg-gray-100 rounded-xl transition-colors">
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
                    <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-xs space-y-4 text-xs">
                        <span class="text-xs font-semibold uppercase tracking-wider text-gray-400 block">
                            {{ __('Ticket Details') }}
                        </span>
                        {{-- TicketID --}}
                        <div class="flex items-center justify-between border-t border-gray-100 pt-3">
                            <span class="text-gray-500 font-medium">{{ __('Ticket No: ') }}</span>
                            {{ $ticket->reference_code }}
                        </div>

                        <!-- Priority -->
                        <div class=" flex items-center justify-between border-t  border-gray-100 pt-3 ">
                            <span class="text-gray-500 font-medium">{{ __('Priority') }}</span>
                            @php
                                $priorityClasses = match ($ticket->priority) {
                                    'urgent' => 'text-red-700 bg-red-50 border-red-200',
                                    'high' => 'text-rose-700 bg-rose-50 border-rose-200',
                                    'medium' => 'text-amber-700 bg-amber-50 border-amber-200',
                                    'low' => 'text-slate-600 bg-slate-50 border-slate-200',
                                    default => 'text-gray-600 bg-gray-50 border-gray-200',
                                };
                            @endphp
                            <span class="px-2.5 py-0.5 rounded-md font-semibold border {{ $priorityClasses }}">
                                {{ ucfirst($ticket->priority) }}
                            </span>
                        </div>

                        <!-- Created Date (Full Timestamp with Hover Relative Time) -->
                        <div class="flex items-center justify-between border-t border-gray-100 pt-3">
                            <span class="text-gray-500 font-medium">{{ __('Created') }}</span>
                            <span title="{{ $ticket->created_at->diffForHumans() }}"
                                class="font-semibold text-gray-800 cursor-help hover:text-brand-600 transition-colors">
                                @ist($ticket->created_at)
                            </span>
                        </div>


                        <!-- Due Date (with Hover Relative Time: e.g. "due in 3 days" or "3 days ago") -->
                        <div class="flex items-center justify-between border-t border-gray-100 pt-3">
                            <span class="text-gray-500 font-medium">{{ __('Due Date') }}</span>
                            @if ($ticket->due_date)
                                <div class="flex items-center gap-1.5">
                                    <span title="{{ $ticket->due_date->endOfDay()->diffForHumans() }}"
                                        class="font-semibold text-gray-800 cursor-help hover:text-brand-600 transition-colors">
                                        {{ $ticket->due_date->format('d M Y') }} <span
                                            class="text-gray-400 font-normal text-[11px]">(11:59 PM IST)</span>
                                    </span>
                                    @if ($ticket->is_overdue)
                                        <span
                                            class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-red-100 text-red-700">
                                            {{ __('OVERDUE') }}
                                        </span>
                                    @endif
                                </div>
                            @else
                                <span class="text-gray-400 italic">{{ __('None set') }}</span>
                            @endif
                        </div>



                        <!-- Workspace -->
                        <div class="flex items-center justify-between border-t border-gray-100 pt-3">
                            <span class="text-gray-500 font-medium">{{ __('Project') }}</span>
                            <a href="{{ route('projects.show', $ticket->project) }}"
                                class="font-semibold text-brand-600 hover:text-brand-700 hover:underline truncate max-w-[150px]">
                                {{ $ticket->project->name }}
                            </a>
                        </div>

                        <!-- Watchers -->
                        <div class="border-t border-gray-100 pt-3 space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-500 font-medium">{{ __('Watchers') }}</span>
                                <span class="font-semibold text-gray-700">{{ $ticket->watchers->count() }}</span>
                            </div>
                            @if ($ticket->watchers->isNotEmpty())
                                <div class="flex items-center -space-x-1.5 overflow-hidden pt-1">
                                    @foreach ($ticket->watchers->take(6) as $watcher)
                                        <span
                                            class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-slate-100 text-slate-700 ring-2 ring-white text-[10px] font-bold"
                                            title="{{ $watcher->name }}">
                                            {{ strtoupper(substr($watcher->name, 0, 1)) }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</x-app-layout>
