@props([
    'activeTab' => 'active',
    'activeCount' => 0,
    'closedCount' => 0,
    'deletedCount' => 0,
])

<div class="inline-flex items-center p-1 bg-gray-100 dark:bg-slate-800/80 rounded-xl text-xs font-semibold">
    <!-- Active Tickets Tab -->
    <button type="button" @click="switchTab('active')"
        class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-lg transition-all {{ $activeTab === 'active' ? 'bg-white dark:bg-slate-700/80 text-gray-900 dark:text-white shadow-xs font-bold' : 'text-gray-500 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white' }}">
        <x-heroicon-m-ticket class="w-4 h-4 {{ $activeTab === 'active' ? 'text-brand-600 dark:text-brand-400' : 'text-gray-400 dark:text-slate-500' }}" />
        <span>{{ __('Active Tickets') }}</span>
        <span class="px-1.5 py-0.5 rounded-full text-[10px] font-bold {{ $activeTab === 'active' ? 'bg-brand-50 dark:bg-brand-950/60 text-brand-700 dark:text-brand-300' : 'bg-gray-200/70 dark:bg-slate-700 text-gray-600 dark:text-slate-300' }}">
            {{ $activeCount }}
        </span>
    </button>

    <!-- Closed Tickets Tab -->
    <button type="button" @click="switchTab('closed')"
        class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-lg transition-all {{ $activeTab === 'closed' ? 'bg-white dark:bg-slate-700/80 text-gray-900 dark:text-white shadow-xs font-bold' : 'text-gray-500 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white' }}">
        <x-heroicon-m-check-badge class="w-4 h-4 {{ $activeTab === 'closed' ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-400 dark:text-slate-500' }}" />
        <span>{{ __('Closed Tickets') }}</span>
        <span class="px-1.5 py-0.5 rounded-full text-[10px] font-bold {{ $activeTab === 'closed' ? 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300' : 'bg-gray-200/70 dark:bg-slate-700 text-gray-600 dark:text-slate-300' }}">
            {{ $closedCount }}
        </span>
    </button>

    <!-- Deleted Tickets Tab -->
    <button type="button" @click="switchTab('deleted')"
        class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-lg transition-all {{ $activeTab === 'deleted' ? 'bg-white dark:bg-slate-700/80 text-gray-900 dark:text-white shadow-xs font-bold' : 'text-gray-500 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white' }}">
        <x-heroicon-m-trash class="w-4 h-4 {{ $activeTab === 'deleted' ? 'text-rose-600 dark:text-rose-400' : 'text-gray-400 dark:text-slate-500' }}" />
        <span>{{ __('Deleted') }}</span>
        <span class="px-1.5 py-0.5 rounded-full text-[10px] font-bold {{ $activeTab === 'deleted' ? 'bg-rose-50 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300' : 'bg-gray-200/70 dark:bg-slate-700 text-gray-600 dark:text-slate-300' }}">
            {{ $deletedCount }}
        </span>
    </button>
</div>
