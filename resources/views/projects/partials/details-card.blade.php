@if($project->isClosed())
    <div class="bg-gray-100 dark:bg-slate-800 rounded-2xl border border-gray-300 dark:border-slate-700 p-5 flex items-start gap-3 text-gray-800 dark:text-slate-200 shadow-xs">
        <div class="p-2 rounded-xl bg-gray-200 dark:bg-slate-700 text-gray-700 dark:text-slate-300 shrink-0">
            <x-heroicon-m-lock-closed class="w-5 h-5" />
        </div>
        <div class="text-xs">
            <h4 class="font-bold text-gray-900 dark:text-white text-sm mb-0.5">{{ __('This project is Closed') }}</h4>
            <p class="text-gray-600 dark:text-slate-300">
                {{ __('All tickets in this project are in read-only mode. New tickets, comments, and status transitions cannot be performed.') }}
                @if($project->closed_at)
                    <span class="block mt-1 font-mono text-[11px] text-gray-500 dark:text-slate-400">
                        Closed {{ $project->closed_at->diffForHumans() }}
                        @if($project->closedBy) by {{ $project->closedBy->name }} @endif
                    </span>
                @endif
            </p>
        </div>
    </div>
@endif

<div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-slate-800 p-6 sm:p-7 shadow-xs">
    <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-6">
        <div class="space-y-2 flex-1">
            <span class="text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-slate-500">
                {{ __('About Workspace') }}
            </span>
            <p class="text-sm text-gray-600 dark:text-slate-300 leading-relaxed max-w-3xl">
                {{ $project->description ?: __('No detailed workspace description provided for this project.') }}
            </p>
        </div>

        <div
            class="flex flex-col sm:flex-row sm:items-center gap-4 shrink-0 pt-4 md:pt-0 border-t md:border-t-0 border-gray-100 dark:border-slate-800">
            <div class="space-y-1">
                <span class="text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-slate-500 block">
                    {{ __('Assigned Managers') }}
                </span>
                @if ($project->managers->isEmpty())
                    <span class="text-xs text-gray-400 dark:text-slate-500 italic">{{ __('No managers assigned') }}</span>
                @else
                    <div class="flex items-center gap-2">
                        <div class="flex items-center -space-x-1.5 overflow-hidden">
                            @foreach ($project->managers->take(5) as $manager)
                                <x-ui.avatar :name="$manager->name" size="lg"
                                    class="bg-brand-50 dark:bg-brand-950/50 text-brand-700 dark:text-brand-300 ring-2 ring-white dark:ring-slate-900 border border-brand-100 dark:border-brand-800/50" />
                            @endforeach
                        </div>
                        <span class="text-xs text-gray-600 dark:text-slate-300 font-medium">
                            {{ $project->managers->pluck('name')->join(', ') }}
                        </span>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
