<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div class="flex items-start sm:items-center gap-3 min-w-0">
        <a href="{{ $backUrl ?? route('projects.show', $ticket->project_id) }}"
            class="p-2 rounded-xl text-gray-500 dark:text-slate-400 bg-white dark:bg-slate-900 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-slate-800 border border-gray-200/90 dark:border-slate-800 hover:border-gray-300 dark:hover:border-slate-700 shadow-xs transition-all shrink-0 mt-0.5 sm:mt-0"
            title="{{ __('Go Back') }}">
            <x-heroicon-m-arrow-left class="w-5 h-5" />
        </a>
        <div class="min-w-0">
            <div class="flex items-center gap-2.5 flex-wrap">
                <span
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ $ticket->trashed() ? 'bg-gray-100 dark:bg-slate-800 text-gray-500 dark:text-slate-400 border border-gray-300 dark:border-slate-700' : 'bg-brand-50 dark:bg-brand-950/50 text-brand-700 dark:text-brand-300 border border-brand-200/80 dark:border-brand-800/50' }} font-mono shadow-2xs">
                    {{ $ticket->reference_code }}
                </span>
                @if($ticket->trashed())
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-50 dark:bg-rose-950/50 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800/50">
                        <x-heroicon-m-trash class="w-3.5 h-3.5 text-rose-600 dark:text-rose-400" />
                        <span>{{ __('DELETED TICKET') }}</span>
                    </span>
                @endif
                <h1 class="text-xl sm:text-2xl font-bold tracking-tight {{ $ticket->trashed() ? 'text-gray-400 dark:text-slate-500 line-through' : 'text-gray-900 dark:text-white' }} truncate">
                    {{ $ticket->title }}
                </h1>
            </div>
            <div class="flex items-center gap-2 mt-0.5 text-xs text-gray-500 dark:text-slate-400 flex-wrap">
                <span>{{ __('Opened') }} {{ $ticket->created_at->diffForHumans() }}</span>
                <span>&bull;</span>
                <span>{{ __('by') }} <strong
                        class="text-gray-700 dark:text-slate-200 font-semibold">{{ $ticket->creator?->name ?? __('Unknown') }}</strong></span>
                <span>&bull;</span>
                <a href="{{ route('projects.show', $ticket->project) }}"
                    class="font-semibold text-brand-600 dark:text-brand-400 hover:text-brand-700 dark:hover:text-brand-300 hover:underline">
                    {{ $ticket->project->name }}
                </a>
            </div>
        </div>
    </div>

    <!-- Quick Action Buttons -->
    <div class="flex items-center gap-2.5 shrink-0">
        @if($ticket->trashed())
            @can('restore', $ticket)
                <form action="{{ route('tickets.restore', $ticket->id) }}" method="POST" id="restore-ticket-header-form">
                    @csrf
                    @method('PATCH')
                    <button type="button"
                        @click="$dispatch('open-confirm-modal', {
                            title: '{{ __('Restore Ticket?') }}',
                            message: '{{ __('Are you sure you want to restore this ticket to active status?') }}',
                            confirmText: '{{ __('Restore') }}',
                            isDanger: false,
                            form: 'restore-ticket-header-form'
                        })"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-950/40 hover:bg-emerald-100 dark:hover:bg-emerald-900/60 rounded-xl border border-emerald-200 dark:border-emerald-800/50 transition-all shadow-xs">
                        <x-heroicon-m-arrow-path class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                        <span>{{ __('Restore Ticket') }}</span>
                    </button>
                </form>
            @endcan
        @else
            <!-- Watch / Unwatch Button -->
            <form action="{{ route('tickets.watch', $ticket) }}" method="POST">
                @csrf
                <button type="submit"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-xl border transition-all shadow-xs {{ $isWatching ? 'bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 border-amber-200 dark:border-amber-800/50 hover:bg-amber-100 dark:hover:bg-amber-900/60' : 'bg-white dark:bg-slate-900 text-gray-700 dark:text-slate-300 border-gray-200/90 dark:border-slate-800 hover:bg-gray-50 dark:hover:bg-slate-800' }}">
                    @if ($isWatching)
                        <x-heroicon-s-bell-alert class="w-4 h-4 text-amber-600 dark:text-amber-400" />
                        <span>{{ __('Watching') }}</span>
                    @else
                        <x-heroicon-o-bell class="w-4 h-4 text-gray-500 dark:text-slate-400" />
                        <span>{{ __('Watch') }}</span>
                    @endif
                </button>
            </form>

            <!-- Edit Ticket -->
            @can('update', $ticket)
                <a href="{{ route('tickets.edit', $ticket) }}"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-gray-700 dark:text-slate-300 bg-white dark:bg-slate-900 border border-gray-200/90 dark:border-slate-800 rounded-xl hover:bg-gray-50 dark:hover:bg-slate-800 hover:border-gray-300 dark:hover:border-slate-700 shadow-xs transition-all">
                    <x-heroicon-m-pencil-square class="w-4 h-4 text-gray-500 dark:text-slate-400" />
                    <span>{{ __('Edit') }}</span>
                </a>
            @endcan

            <!-- Delete Ticket (Creator, PM, Admin) -->
            @can('delete', $ticket)
                <form action="{{ route('tickets.destroy', $ticket) }}" method="POST" id="delete-ticket-header-form">
                    @csrf
                    @method('DELETE')
                    @if(session()->has('ticket_index_url'))
                        <input type="hidden" name="redirect_to" value="{{ session('ticket_index_url') }}">
                    @endif
                    <button type="button"
                        @click="$dispatch('open-confirm-modal', {
                            title: '{{ __('Delete Ticket?') }}',
                            message: '{{ __('Are you sure you want to delete this ticket? It will be soft-deleted and preserved for audit.') }}',
                            confirmText: '{{ __('Delete') }}',
                            isDanger: true,
                            form: 'delete-ticket-header-form'
                        })"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-rose-700 dark:text-rose-300 bg-rose-50 dark:bg-rose-950/40 border border-rose-200/80 dark:border-rose-800/50 rounded-xl hover:bg-rose-100 dark:hover:bg-rose-900/60 hover:border-rose-300 dark:hover:border-rose-700 shadow-xs transition-all"
                        title="{{ __('Delete Ticket') }}">
                        <x-heroicon-m-trash class="w-4 h-4 text-rose-600 dark:text-rose-400" />
                        <span>{{ __('Delete') }}</span>
                    </button>
                </form>
            @endcan
        @endif
    </div>
</div>
