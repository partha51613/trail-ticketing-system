<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-gray-900">
                    {{ __('Projects') }}
                </h1>
                <p class="text-sm text-gray-500 mt-0.5">
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

    <div class="py-8 bg-gray-50/60 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- KPI Metric Cards Overview -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
                <!-- Total Projects -->
                <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-xs flex items-center justify-between">
                    <div>
                        <span class="text-xs font-medium uppercase tracking-wider text-gray-400">
                            {{ __('Total Projects') }}
                        </span>
                        <div class="text-3xl font-bold tracking-tight text-gray-900 mt-1">
                            {{ $stats['total_projects'] }}
                        </div>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-brand-50 text-brand-500 flex items-center justify-center shrink-0">
                        <x-heroicon-o-folder class="w-6 h-6" />
                    </div>
                </div>

                <!-- Total Tickets -->
                <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-xs flex items-center justify-between">
                    <div>
                        <span class="text-xs font-medium uppercase tracking-wider text-gray-400">
                            {{ __('Total Tickets') }}
                        </span>
                        <div class="text-3xl font-bold tracking-tight text-gray-900 mt-1">
                            {{ $stats['total_tickets'] }}
                        </div>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center shrink-0">
                        <x-heroicon-o-ticket class="w-6 h-6" />
                    </div>
                </div>

                <!-- Assigned Tickets -->
                <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-xs flex items-center justify-between">
                    <div>
                        <span class="text-xs font-medium uppercase tracking-wider text-gray-400">
                            {{ __('Assigned') }}
                        </span>
                        <div class="text-3xl font-bold tracking-tight text-brand-600 mt-1">
                            {{ $stats['assigned_tickets'] }}
                        </div>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center shrink-0">
                        <x-heroicon-o-user-group class="w-6 h-6" />
                    </div>
                </div>

                <!-- Unassigned Tickets -->
                <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-xs flex items-center justify-between">
                    <div>
                        <span class="text-xs font-medium uppercase tracking-wider text-gray-400">
                            {{ __('Unassigned') }}
                        </span>
                        <div class="text-3xl font-bold tracking-tight {{ $stats['unassigned_tickets'] > 0 ? 'text-rose-600' : 'text-gray-900' }} mt-1">
                            {{ $stats['unassigned_tickets'] }}
                        </div>
                    </div>
                    <div class="w-11 h-11 rounded-xl {{ $stats['unassigned_tickets'] > 0 ? 'bg-rose-50 text-rose-600' : 'bg-gray-100 text-gray-500' }} flex items-center justify-center shrink-0">
                        <x-heroicon-o-exclamation-circle class="w-6 h-6" />
                    </div>
                </div>
            </div>

            <!-- Project Listing (Spacious Rows) -->
            @if ($projects->isEmpty())
                <div class="bg-white rounded-2xl border border-dashed border-gray-300 p-12 text-center shadow-xs">
                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-brand-50 text-brand-500 mb-4">
                        <x-heroicon-o-folder class="w-6 h-6" />
                    </div>
                    <h3 class="text-base font-semibold text-gray-900">{{ __('No projects found') }}</h3>
                    <p class="mt-1 text-sm text-gray-500 max-w-sm mx-auto">
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
                        <div class="group bg-white rounded-2xl border border-gray-100 shadow-xs hover:border-brand-200 hover:shadow-md transition-all duration-200 p-6 sm:p-7">
                            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">

                                <!-- Left: Project Details & Managers -->
                                <div class="flex items-start gap-4 min-w-0 flex-1">
                                    <div class="w-12 h-12 rounded-2xl bg-brand-50 border border-brand-100 text-brand-600 flex items-center justify-center font-bold text-lg shrink-0 shadow-xs">
                                        {{ strtoupper(substr($project->name, 0, 1)) }}
                                    </div>

                                    <div class="min-w-0 flex-1 space-y-1.5">
                                        <div class="flex items-center gap-3 flex-wrap">
                                            <a href="{{ route('projects.show', $project) }}"
                                                class="text-lg font-semibold text-gray-900 hover:text-brand-600 transition-colors">
                                                {{ $project->name }}
                                            </a>
                                            <span class="text-xs text-gray-400 font-medium">
                                                {{ __('Added') }} {{ $project->created_at->diffForHumans() }}
                                            </span>
                                        </div>

                                        <p class="text-sm text-gray-500 line-clamp-2 leading-relaxed max-w-2xl">
                                            {{ $project->description ?: __('No project description provided.') }}
                                        </p>

                                        <!-- Assigned Managers Stack -->
                                        <div class="flex items-center gap-2 pt-2">
                                            <span class="text-xs font-medium text-gray-400 uppercase tracking-wider">
                                                {{ __('Managers:') }}
                                            </span>
                                            @if ($project->managers->isEmpty())
                                                <span class="text-xs text-gray-400 italic">{{ __('None assigned') }}</span>
                                            @else
                                                <div class="flex items-center -space-x-1.5 overflow-hidden">
                                                    @foreach ($project->managers->take(4) as $manager)
                                                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-slate-100 text-slate-700 ring-2 ring-white text-xs font-semibold"
                                                            title="{{ $manager->name }}">
                                                            {{ strtoupper(substr($manager->name, 0, 1)) }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                                <span class="text-xs text-gray-600 font-medium ml-1">
                                                    {{ $project->managers->pluck('name')->join(', ') }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Center/Right: Big Numbers Ticket Distribution -->
                                <div class="flex items-center justify-between sm:justify-start gap-8 lg:gap-12 shrink-0 py-3 lg:py-0 border-y lg:border-y-0 border-gray-100">
                                    <!-- Total Tickets -->
                                    <div class="text-center sm:text-left">
                                        <div class="text-xs uppercase tracking-wider font-semibold text-gray-400">
                                            {{ __('Total') }}
                                        </div>
                                        <div class="text-2xl sm:text-3xl font-extrabold text-gray-900 mt-0.5">
                                            {{ $project->total_tickets_count }}
                                        </div>
                                    </div>

                                    <!-- Assigned Tickets -->
                                    <div class="text-center sm:text-left">
                                        <div class="text-xs uppercase tracking-wider font-semibold text-brand-600">
                                            {{ __('Assigned') }}
                                        </div>
                                        <div class="text-2xl sm:text-3xl font-extrabold text-brand-600 mt-0.5">
                                            {{ $project->assigned_tickets_count }}
                                        </div>
                                    </div>

                                    <!-- Unassigned Tickets -->
                                    <div class="text-center sm:text-left">
                                        <div class="text-xs uppercase tracking-wider font-semibold {{ $project->unassigned_tickets_count > 0 ? 'text-rose-600' : 'text-gray-400' }}">
                                            {{ __('Unassigned') }}
                                        </div>
                                        <div class="text-2xl sm:text-3xl font-extrabold {{ $project->unassigned_tickets_count > 0 ? 'text-rose-600' : 'text-gray-400' }} mt-0.5">
                                            {{ $project->unassigned_tickets_count }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Far Right: Quick Actions -->
                                <div class="flex items-center justify-end gap-2 shrink-0">
                                    @can('update', $project)
                                        <a href="{{ route('projects.edit', $project) }}"
                                            class="inline-flex items-center justify-center p-2.5 rounded-xl text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition-colors"
                                            title="{{ __('Edit Project') }}">
                                            <x-heroicon-m-pencil-square class="w-5 h-5" />
                                        </a>
                                    @endcan

                                    <a href="{{ route('projects.show', $project) }}"
                                        class="inline-flex items-center justify-center gap-x-1.5 px-4 py-2.5 rounded-xl text-sm font-semibold text-gray-700 bg-gray-50 hover:bg-brand-50 hover:text-brand-600 border border-gray-200/80 hover:border-brand-200 transition-all">
                                        <span>{{ __('View') }}</span>
                                        <x-heroicon-m-arrow-up-right class="w-4 h-4" />
                                    </a>
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
