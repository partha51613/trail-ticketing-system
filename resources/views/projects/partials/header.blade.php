<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div class="flex items-center gap-3">
        <a href="{{ route('projects.index') }}"
            class="p-2 rounded-xl text-gray-400 dark:text-slate-400 hover:text-gray-700 dark:hover:text-white hover:bg-white dark:hover:bg-slate-800 border border-transparent hover:border-gray-200 dark:hover:border-slate-700 transition-all"
            title="{{ __('Back to Projects') }}">
            <x-heroicon-m-arrow-left class="w-5 h-5" />
        </a>
        <div>
            <div class="flex items-center gap-3 flex-wrap">
                <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                    {{ $project->name }}
                </h1>
                @if($project->isClosed())
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-200 dark:bg-slate-800 text-gray-800 dark:text-slate-200 border border-gray-300 dark:border-slate-700">
                        <x-heroicon-m-lock-closed class="w-3.5 h-3.5 text-gray-700 dark:text-slate-300" />
                        <span>{{ __('Closed Workspace (Read-Only)') }}</span>
                    </span>
                @else
                    <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-brand-50 dark:bg-brand-950/50 text-brand-700 dark:text-brand-300 border border-brand-200/60 dark:border-brand-800/50">
                        {{ __('Workspace') }}
                    </span>
                @endif
            </div>
            <p class="text-xs text-gray-400 dark:text-slate-400 mt-0.5 font-medium">
                {{ __('Created') }} {{ $project->created_at->format('M d, Y') }} &bull;
                {{ $project->created_at->diffForHumans() }}
            </p>
        </div>
    </div>

    <div class="flex items-center gap-2.5 flex-wrap">
        @can('toggleClose', $project)
            <form action="{{ route('projects.toggle-close', $project) }}" method="POST" id="toggle-close-project-header">
                @csrf
                @method('PATCH')
                <button type="button"
                    @click="$dispatch('open-confirm-modal', {
                        title: '{{ $project->isClosed() ? __('Reopen Project?') : __('Close Project?') }}',
                        message: '{{ $project->isClosed() ? __('Reopening will allow tickets to be created and updated again.') : __('Closing this project will make it and all its tickets read-only for all users.') }}',
                        confirmText: '{{ $project->isClosed() ? __('Reopen Project') : __('Close Project') }}',
                        isDanger: {{ $project->isClosed() ? 'false' : 'true' }},
                        form: 'toggle-close-project-header'
                    })"
                    class="inline-flex items-center gap-x-1.5 px-3.5 py-2 text-sm font-semibold {{ $project->isClosed() ? 'text-amber-700 dark:text-amber-300 bg-amber-50 dark:bg-amber-950/40 border-amber-200 dark:border-amber-800/50 hover:bg-amber-100 dark:hover:bg-amber-900/60' : 'text-gray-700 dark:text-slate-300 bg-white dark:bg-slate-900 border-gray-200/80 dark:border-slate-700 hover:bg-gray-50 dark:hover:bg-slate-800 hover:border-gray-300 dark:hover:border-slate-600' }} border rounded-xl transition-all shadow-xs">
                    @if($project->isClosed())
                        <x-heroicon-m-lock-open class="w-4 h-4 text-amber-600 dark:text-amber-400" />
                        <span>{{ __('Reopen Project') }}</span>
                    @else
                        <x-heroicon-m-lock-closed class="w-4 h-4 text-gray-500 dark:text-slate-400" />
                        <span>{{ __('Close Project') }}</span>
                    @endif
                </button>
            </form>
        @endcan

        @can('update', $project)
            <a href="{{ route('projects.edit', $project) }}"
                class="inline-flex items-center gap-x-1.5 px-3.5 py-2 text-sm font-semibold text-gray-700 dark:text-slate-300 bg-white dark:bg-slate-900 border border-gray-200/80 dark:border-slate-700 rounded-xl hover:bg-gray-50 dark:hover:bg-slate-800 hover:border-gray-300 dark:hover:border-slate-600 transition-all shadow-xs">
                <x-heroicon-m-pencil-square class="w-4 h-4 text-gray-500 dark:text-slate-400" />
                <span>{{ __('Edit Project') }}</span>
            </a>
        @endcan

        @if(! $project->isClosed())
            @can('create', App\Models\Ticket::class)
                <a href="{{ route('tickets.create', ['project_id' => $project->id]) }}"
                    class="inline-flex items-center gap-x-1.5 px-4 py-2 text-sm font-semibold text-white bg-brand-500 rounded-xl hover:bg-brand-600 shadow-sm shadow-brand-500/20 transition-all">
                    <x-heroicon-m-plus class="w-4 h-4" />
                    <span>{{ __('New Ticket') }}</span>
                </a>
            @endcan
        @endif
    </div>
</div>
