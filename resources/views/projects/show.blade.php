<x-app-layout>
    <x-slot name="header">
        @include('projects.partials.header')
    </x-slot>

    <div class="py-8 bg-gray-50/60 dark:bg-slate-950 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Project Context & Description Card -->
            @include('projects.partials.details-card')

            <!-- Mini KPI Stat Row -->
            @include('projects.partials.kpi-row')

            <!-- Server-Side Search, Filter & Sort Table -->
            @include('projects.partials.tickets-table')

        </div>
    </div>
</x-app-layout>
