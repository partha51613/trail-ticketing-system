<!-- Mini KPI Stat Row for This Project -->
<div class="grid grid-cols-2 sm:grid-cols-5 gap-3.5 sm:gap-4">
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-slate-800 p-4 shadow-xs flex items-center justify-between">
        <div>
            <span class="text-[11px] font-semibold uppercase tracking-wider text-gray-400 dark:text-slate-400 block">{{ __('Total Tickets') }}</span>
            <div class="text-2xl font-extrabold tracking-tight text-gray-900 dark:text-white mt-1">
                {{ $projectStats['total'] }}
            </div>
        </div>
        <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-transparent dark:border-slate-700 flex items-center justify-center shrink-0">
            <x-heroicon-o-ticket class="w-5 h-5" />
        </div>
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-slate-800 p-4 shadow-xs flex items-center justify-between">
        <div>
            <span class="text-[11px] font-semibold uppercase tracking-wider text-brand-600 dark:text-brand-400 block">{{ __('Assigned') }}</span>
            <div class="text-2xl font-extrabold tracking-tight text-brand-600 dark:text-brand-400 mt-1">
                {{ $projectStats['assigned'] }}
            </div>
        </div>
        <div class="w-10 h-10 rounded-xl bg-brand-50 dark:bg-brand-950/40 text-brand-600 dark:text-brand-400 border border-transparent dark:border-brand-800/30 flex items-center justify-center shrink-0">
            <x-heroicon-o-user-group class="w-5 h-5" />
        </div>
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-slate-800 p-4 shadow-xs flex items-center justify-between">
        <div>
            <span class="text-[11px] font-semibold uppercase tracking-wider {{ $projectStats['unassigned'] > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-gray-400 dark:text-slate-400' }} block">{{ __('Unassigned') }}</span>
            <div class="text-2xl font-extrabold tracking-tight {{ $projectStats['unassigned'] > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-gray-900 dark:text-white' }} mt-1">
                {{ $projectStats['unassigned'] }}
            </div>
        </div>
        <div class="w-10 h-10 rounded-xl {{ $projectStats['unassigned'] > 0 ? 'bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 border border-transparent dark:border-rose-800/30' : 'bg-gray-100 dark:bg-slate-800 text-gray-400 dark:text-slate-500 border border-transparent dark:border-slate-700' }} flex items-center justify-center shrink-0">
            <x-heroicon-o-exclamation-circle class="w-5 h-5" />
        </div>
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-slate-800 p-4 shadow-xs flex items-center justify-between">
        <div>
            <span class="text-[11px] font-semibold uppercase tracking-wider text-red-500 dark:text-red-400 block">{{ __('Blocked') }}</span>
            <div class="text-2xl font-extrabold tracking-tight {{ $projectStats['blocked'] > 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }} mt-1">
                {{ $projectStats['blocked'] }}
            </div>
        </div>
        <div class="w-10 h-10 rounded-xl {{ $projectStats['blocked'] > 0 ? 'bg-red-50 dark:bg-red-950/40 text-red-600 dark:text-red-400 border border-transparent dark:border-red-800/30' : 'bg-gray-100 dark:bg-slate-800 text-gray-400 dark:text-slate-500 border border-transparent dark:border-slate-700' }} flex items-center justify-center shrink-0">
            <x-heroicon-o-no-symbol class="w-5 h-5" />
        </div>
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-slate-800 p-4 shadow-xs col-span-2 sm:col-span-1 flex items-center justify-between">
        <div>
            <span class="text-[11px] font-semibold uppercase tracking-wider text-emerald-600 dark:text-emerald-400 block">{{ __('Closed') }}</span>
            <div class="text-2xl font-extrabold tracking-tight text-emerald-600 dark:text-emerald-400 mt-1">
                {{ $projectStats['closed'] }}
            </div>
        </div>
        <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 border border-transparent dark:border-emerald-800/30 flex items-center justify-center shrink-0">
            <x-heroicon-o-check-badge class="w-5 h-5" />
        </div>
    </div>
</div>
