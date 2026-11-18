<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                        @if (request()->routeIs('tickets.assigned-to-me') || request('assigned_to_id') == auth()->id())
                            {{ __('Assigned to Me') }}
                        @elseif (request()->routeIs('tickets.watched') || request()->boolean('watched'))
                            {{ __('Watched Tickets') }}
                        @elseif (request()->routeIs('tickets.created-by-me') || request('created_by_id') == auth()->id())
                            {{ __('Created by Me') }}
                        @else
                            {{ __('All Tickets') }}
                        @endif
                    </h1>
                    <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 dark:bg-slate-800 text-gray-700 dark:text-slate-300 border border-gray-200/60 dark:border-slate-700">
                        {{ $tickets->total() }} {{ __('tickets') }}
                    </span>
                </div>
                <p class="text-xs text-gray-500 dark:text-slate-400 mt-1">
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
            <!-- Filter & Search Toolbar -->
            @include('tickets.partials.filter-toolbar')

            <!-- Tickets Table Card -->
            @include('tickets.partials.table')
        </div>
    </div>
</x-app-layout>
