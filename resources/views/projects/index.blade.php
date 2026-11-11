<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                    {{ __('Projects') }}
                </h1>
                <p class="text-sm text-gray-500 dark:text-slate-400 mt-0.5">
                    {{ __('Manage client workspaces, oversee assigned managers, and track ticket distributions.') }}
                </p>
            </div>
            @can('create', App\Models\Project::class)
                <a href="{{ route('projects.create') }}"
                    class="inline-flex items-center justify-center gap-x-2 px-4 py-2.5 text-sm font-semibold text-white bg-brand-500 rounded-xl hover:bg-brand-600 shadow-sm shadow-brand-500/20 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-500 transition-all">
                    <x-heroicon-m-plus class="w-4 h-4" />
                    <span>{{ __('New Project') }}</span>
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-8 bg-gray-50/60 dark:bg-slate-950 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- KPI Metric Cards Overview -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
                <!-- Total Projects -->
                <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-slate-800 p-5 shadow-xs flex items-center justify-between">
                    <div>
                        <span class="text-xs font-medium uppercase tracking-wider text-gray-400 dark:text-slate-400">
                            {{ __('Total Projects') }}
                        </span>
                        <div class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white mt-1">
                            {{ $stats['total_projects'] }}
                        </div>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-brand-50 dark:bg-brand-950/40 text-brand-500 dark:text-brand-400 border border-transparent dark:border-brand-800/30 flex items-center justify-center shrink-0">
                        <x-heroicon-o-folder class="w-6 h-6" />
                    </div>
                </div>

                <!-- Total Tickets -->
                <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-slate-800 p-5 shadow-xs flex items-center justify-between">
                    <div>
                        <span class="text-xs font-medium uppercase tracking-wider text-gray-400 dark:text-slate-400">
                            {{ __('Total Tickets') }}
                        </span>
                        <div class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white mt-1">
                            {{ $stats['total_tickets'] }}
                        </div>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-transparent dark:border-slate-700 flex items-center justify-center shrink-0">
                        <x-heroicon-o-ticket class="w-6 h-6" />
                    </div>
                </div>

                <!-- Assigned Tickets -->
                <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-slate-800 p-5 shadow-xs flex items-center justify-between">
                    <div>
                        <span class="text-xs font-medium uppercase tracking-wider text-gray-400 dark:text-slate-400">
                            {{ __('Assigned') }}
                        </span>
                        <div class="text-3xl font-bold tracking-tight text-brand-600 dark:text-brand-400 mt-1">
                            {{ $stats['assigned_tickets'] }}
                        </div>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-brand-50 dark:bg-brand-950/40 text-brand-600 dark:text-brand-400 border border-transparent dark:border-brand-800/30 flex items-center justify-center shrink-0">
                        <x-heroicon-o-user-group class="w-6 h-6" />
                    </div>
                </div>

                <!-- Unassigned Tickets -->
                <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-slate-800 p-5 shadow-xs flex items-center justify-between">
                    <div>
                        <span class="text-xs font-medium uppercase tracking-wider text-gray-400 dark:text-slate-400">
                            {{ __('Unassigned') }}
                        </span>
                        <div class="text-3xl font-bold tracking-tight {{ $stats['unassigned_tickets'] > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-gray-900 dark:text-white' }} mt-1">
                            {{ $stats['unassigned_tickets'] }}
                        </div>
                    </div>
                    <div class="w-11 h-11 rounded-xl {{ $stats['unassigned_tickets'] > 0 ? 'bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 border border-transparent dark:border-rose-800/30' : 'bg-gray-100 dark:bg-slate-800 text-gray-500 dark:text-slate-400 border border-transparent dark:border-slate-700' }} flex items-center justify-center shrink-0">
                        <x-heroicon-o-exclamation-circle class="w-6 h-6" />
                    </div>
                </div>
            </div>

            <!-- Navigation Tabs (Active vs Closed vs Deleted Projects) -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-1">
                <div class="inline-flex items-center p-1 bg-gray-100 dark:bg-slate-800/80 rounded-xl text-xs font-semibold">
                    <!-- Active Projects Tab -->
                    <a href="{{ route('projects.index') }}"
                        class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-lg transition-all {{ $tab === 'active' ? 'bg-white dark:bg-slate-700/80 text-gray-900 dark:text-white shadow-xs font-bold' : 'text-gray-500 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white' }}">
                        <x-heroicon-m-folder class="w-4 h-4 {{ $tab === 'active' ? 'text-brand-600 dark:text-brand-400' : 'text-gray-400 dark:text-slate-500' }}" />
                        <span>{{ __('Active Projects') }}</span>
                        <span class="px-1.5 py-0.5 rounded-full text-[10px] font-bold {{ $tab === 'active' ? 'bg-brand-50 dark:bg-brand-950/60 text-brand-700 dark:text-brand-300' : 'bg-gray-200/70 dark:bg-slate-700 text-gray-600 dark:text-slate-300' }}">
                            {{ $activeCount }}
                        </span>
                    </a>

                    <!-- Closed Projects Tab -->
                    <a href="{{ route('projects.index', ['tab' => 'closed']) }}"
                        class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-lg transition-all {{ $tab === 'closed' ? 'bg-white dark:bg-slate-700/80 text-gray-900 dark:text-white shadow-xs font-bold' : 'text-gray-500 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white' }}">
                        <x-heroicon-m-lock-closed class="w-4 h-4 {{ $tab === 'closed' ? 'text-gray-700 dark:text-slate-200' : 'text-gray-400 dark:text-slate-500' }}" />
                        <span>{{ __('Closed') }}</span>
                        <span class="px-1.5 py-0.5 rounded-full text-[10px] font-bold {{ $tab === 'closed' ? 'bg-gray-200 dark:bg-slate-600 text-gray-800 dark:text-white' : 'bg-gray-200/70 dark:bg-slate-700 text-gray-600 dark:text-slate-300' }}">
                            {{ $closedCount }}
                        </span>
                    </a>

                    <!-- Deleted Projects Tab -->
                    <a href="{{ route('projects.index', ['tab' => 'deleted']) }}"
                        class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-lg transition-all {{ $tab === 'deleted' ? 'bg-white dark:bg-slate-700/80 text-gray-900 dark:text-white shadow-xs font-bold' : 'text-gray-500 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white' }}">
                        <x-heroicon-m-trash class="w-4 h-4 {{ $tab === 'deleted' ? 'text-rose-600 dark:text-rose-400' : 'text-gray-400 dark:text-slate-500' }}" />
                        <span>{{ __('Deleted') }}</span>
                        <span class="px-1.5 py-0.5 rounded-full text-[10px] font-bold {{ $tab === 'deleted' ? 'bg-rose-50 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300' : 'bg-gray-200/70 dark:bg-slate-700 text-gray-600 dark:text-slate-300' }}">
                            {{ $deletedCount }}
                        </span>
                    </a>
                </div>

                <span class="text-xs text-gray-500 dark:text-slate-400 font-medium">
                    {{ __('Showing') }} <strong class="text-gray-800 dark:text-slate-200">{{ $projects->total() }}</strong>
                    @if($tab === 'deleted')
                        {{ __('deleted workspaces') }}
                    @elseif($tab === 'closed')
                        {{ __('closed workspaces') }}
                    @else
                        {{ __('active workspaces') }}
                    @endif
                </span>
            </div>

            <!-- Project Listing (Spacious Rows) -->
            @if ($projects->isEmpty())
                <div class="bg-white dark:bg-slate-900 rounded-2xl border border-dashed border-gray-300 dark:border-slate-700 p-12 text-center shadow-xs">
                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-brand-50 dark:bg-brand-950/40 text-brand-500 dark:text-brand-400 mb-4 border border-transparent dark:border-brand-800/30">
                        <x-heroicon-o-folder class="w-6 h-6" />
                    </div>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">{{ __('No projects found') }}</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-slate-400 max-w-sm mx-auto">
                        {{ __('Get started by creating your first workspace and assigning managers.') }}
                    </p>
                    @can('create', App\Models\Project::class)
                        <div class="mt-6">
                            <a href="{{ route('projects.create') }}"
                                class="inline-flex items-center gap-x-2 px-4 py-2.5 text-sm font-semibold text-white bg-brand-500 rounded-xl hover:bg-brand-600 shadow-sm transition-all">
                                <x-heroicon-m-plus class="w-4 h-4" />
                                <span>{{ __('Create Project') }}</span>
                            </a>
                        </div>
                    @endcan
                </div>
            @else
                <div class="space-y-4">
                    @foreach ($projects as $project)
                        <div class="group bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-slate-800 shadow-xs hover:border-brand-200 dark:hover:border-brand-700/60 hover:shadow-md transition-all duration-200 p-6 sm:p-7">
                            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">

                                <!-- Left: Project Details & Managers -->
                                <div class="flex items-start gap-4 min-w-0 flex-1">
                                    <div class="w-12 h-12 rounded-2xl bg-brand-50 dark:bg-brand-950/40 border border-brand-100 dark:border-brand-800/50 text-brand-600 dark:text-brand-400 flex items-center justify-center font-bold text-lg shrink-0 shadow-xs">
                                        {{ strtoupper(substr($project->name, 0, 1)) }}
                                    </div>

                                    <div class="min-w-0 flex-1 space-y-1.5">
                                        <div class="flex items-center gap-3 flex-wrap">
                                            <a href="{{ route('projects.show', $project) }}"
                                                class="text-lg font-semibold text-gray-900 dark:text-white hover:text-brand-600 dark:hover:text-brand-400 transition-colors">
                                                {{ $project->name }}
                                            </a>
                                            <span class="text-xs text-gray-400 dark:text-slate-500 font-medium">
                                                {{ __('Added') }} {{ $project->created_at->diffForHumans() }}
                                            </span>
                                        </div>

                                        <p class="text-sm text-gray-500 dark:text-slate-400 line-clamp-2 leading-relaxed max-w-2xl">
                                            {{ $project->description ?: __('No project description provided.') }}
                                        </p>

                                        <!-- Assigned Managers Stack -->
                                        <div class="flex items-center gap-2 pt-2">
                                            <span class="text-xs font-medium text-gray-400 dark:text-slate-500 uppercase tracking-wider">
                                                {{ __('Managers:') }}
                                            </span>
                                            @if ($project->managers->isEmpty())
                                                <span class="text-xs text-gray-400 dark:text-slate-500 italic">{{ __('None assigned') }}</span>
                                            @else
                                                <div class="flex items-center -space-x-1.5 overflow-hidden">
                                                    @foreach ($project->managers->take(4) as $manager)
                                                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 ring-2 ring-white dark:ring-slate-900 text-xs font-semibold"
                                                            title="{{ $manager->name }}">
                                                            {{ strtoupper(substr($manager->name, 0, 1)) }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                                <span class="text-xs text-gray-600 dark:text-slate-300 font-medium ml-1">
                                                    {{ $project->managers->pluck('name')->join(', ') }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Center/Right: Big Numbers Segmented Container & Mini Progress Bar -->
                                <div class="bg-gray-50/70 dark:bg-slate-850/70 border border-gray-100 dark:border-slate-800 rounded-xl px-5 py-2.5 sm:px-6 shrink-0 flex flex-col justify-center gap-2">
                                    <div class="flex items-center justify-between sm:justify-start gap-5 sm:gap-7 divide-x divide-gray-200/70 dark:divide-slate-800">
                                        <!-- Total Tickets -->
                                        <div class="text-center sm:text-left pr-1 sm:pr-0">
                                            <div class="text-[11px] uppercase tracking-wider font-semibold text-gray-400 dark:text-slate-400">
                                                {{ __('Total') }}
                                            </div>
                                            <div class="text-center text-lg sm:text-xl font-bold text-gray-900 dark:text-white mt-0.5 leading-none">
                                                {{ $project->total_tickets_count }}
                                            </div>
                                        </div>

                                        <!-- Assigned Tickets -->
                                        <div class="text-center sm:text-left pl-5 sm:pl-7">
                                            <div class="text-[11px] uppercase tracking-wider font-semibold text-brand-600 dark:text-brand-400">
                                                {{ __('Assigned') }}
                                            </div>
                                            <div class="text-center text-lg sm:text-xl font-bold text-brand-600 dark:text-brand-400 mt-0.5 leading-none">
                                                {{ $project->assigned_tickets_count }}
                                            </div>
                                        </div>

                                        <!-- Unassigned Tickets -->
                                        <div class="text-center sm:text-left pl-5 sm:pl-7">
                                            <div class="text-[11px] uppercase tracking-wider font-semibold {{ $project->unassigned_tickets_count > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-gray-400 dark:text-slate-500' }}">
                                                {{ __('Unassigned') }}
                                            </div>
                                            <div class="text-center text-lg sm:text-xl font-bold {{ $project->unassigned_tickets_count > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-gray-400 dark:text-slate-500' }} mt-0.5 leading-none">
                                                {{ $project->unassigned_tickets_count }}
                                            </div>
                                        </div>
                                    </div>

                                    {{-- <!-- Mini Dual-Tone Ratio Bar -->
                                    @php
                                        $total = $project->total_tickets_count;
                                        $assignedPct = $total > 0 ? round(($project->assigned_tickets_count / $total) * 100) : 0;
                                        $unassignedPct = $total > 0 ? 100 - $assignedPct : 0;
                                    @endphp
                                    <div class="w-full bg-gray-200/70 h-1.5 rounded-full overflow-hidden flex">
                                        @if ($total > 0)
                                            <div class="bg-brand-500 h-full rounded-l-full transition-all duration-300" style="width: {{ $assignedPct }}%" title="{{ $assignedPct }}% Assigned"></div>
                                            <div class="bg-rose-500 h-full rounded-r-full transition-all duration-300" style="width: {{ $unassignedPct }}%" title="{{ $unassignedPct }}% Unassigned"></div>
                                        @else
                                            <div class="bg-gray-300 h-full w-full rounded-full"></div>
                                        @endif
                                    </div> --}}
                                </div>

                                <!-- Far Right: Quick Actions -->
                                <div class="flex items-center justify-end gap-2.5 shrink-0">
                                    @if ($project->trashed())
                                        @can('restore', $project)
                                            <form action="{{ route('projects.restore', $project->id) }}" method="POST" id="restore-project-{{ $project->id }}">
                                                @csrf
                                                @method('PATCH')
                                                <button type="button"
                                                    @click="$dispatch('open-confirm-modal', {
                                                        title: '{{ __('Restore Project?') }}',
                                                        message: '{{ __('Are you sure you want to restore this workspace?') }}',
                                                        confirmText: '{{ __('Restore') }}',
                                                        isDanger: false,
                                                        form: 'restore-project-{{ $project->id }}'
                                                    })"
                                                    class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-950/40 hover:bg-emerald-100 dark:hover:bg-emerald-900/60 border border-emerald-200 dark:border-emerald-800/50 rounded-xl transition-all shadow-xs"
                                                    title="{{ __('Restore Project') }}">
                                                    <x-heroicon-m-arrow-path class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                                                    <span>{{ __('Restore') }}</span>
                                                </button>
                                            </form>
                                        @endcan
                                    @else
                                        @can('toggleClose', $project)
                                            <form action="{{ route('projects.toggle-close', $project) }}" method="POST" id="toggle-close-project-{{ $project->id }}">
                                                @csrf
                                                @method('PATCH')
                                                <button type="button"
                                                    @click="$dispatch('open-confirm-modal', {
                                                        title: '{{ $project->isClosed() ? __('Reopen Project?') : __('Close Project?') }}',
                                                        message: '{{ $project->isClosed() ? __('Reopening will allow tickets to be created and updated again.') : __('Closing this project will make it and all its tickets read-only for all users.') }}',
                                                        confirmText: '{{ $project->isClosed() ? __('Reopen') : __('Close Project') }}',
                                                        isDanger: {{ $project->isClosed() ? 'false' : 'true' }},
                                                        form: 'toggle-close-project-{{ $project->id }}'
                                                    })"
                                                    class="inline-flex items-center justify-center p-2.5 rounded-xl {{ $project->isClosed() ? 'text-amber-700 dark:text-amber-300 bg-amber-50 dark:bg-amber-950/40 hover:bg-amber-100 dark:hover:bg-amber-900/60 border border-amber-200 dark:border-amber-800/50' : 'text-gray-500 dark:text-slate-400 bg-white dark:bg-slate-800 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-slate-700 border border-gray-200/90 dark:border-slate-700 hover:border-gray-300 dark:hover:border-slate-600' }} shadow-xs transition-all"
                                                    title="{{ $project->isClosed() ? __('Reopen Project') : __('Close Project') }}">
                                                    @if($project->isClosed())
                                                        <x-heroicon-m-lock-open class="w-5 h-5" />
                                                    @else
                                                        <x-heroicon-m-lock-closed class="w-5 h-5" />
                                                    @endif
                                                </button>
                                            </form>
                                        @endcan

                                        @can('update', $project)
                                            <a href="{{ route('projects.edit', $project) }}"
                                                class="inline-flex items-center justify-center p-2.5 rounded-xl text-gray-500 dark:text-slate-400 bg-white dark:bg-slate-800 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-slate-700 border border-gray-200/90 dark:border-slate-700 hover:border-gray-300 dark:hover:border-slate-600 shadow-xs transition-all"
                                                title="{{ __('Edit Project') }}">
                                                <x-heroicon-m-pencil-square class="w-5 h-5" />
                                            </a>
                                        @endcan

                                        <a href="{{ route('projects.show', $project) }}"
                                            class="inline-flex items-center justify-center p-2.5 rounded-xl text-gray-600 dark:text-slate-400 bg-white dark:bg-slate-800 hover:text-brand-600 dark:hover:text-brand-400 hover:bg-brand-50 dark:hover:bg-brand-950/40 border border-gray-200/90 dark:border-slate-700 hover:border-brand-300 dark:hover:border-brand-800/50 shadow-xs transition-all"
                                            title="{{ __('View Project') }}">
                                            <x-heroicon-m-arrow-up-right class="w-5 h-5" />
                                        </a>
                                    @endif
                                </div>

                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $projects->links() }}
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
