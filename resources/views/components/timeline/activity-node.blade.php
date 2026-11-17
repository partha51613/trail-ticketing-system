@props(['log'])

<div x-show="filter === 'all' || filter === 'activity'"
    class="relative flex items-start gap-3">

    <!-- Node Marker on Timeline -->
    <div class="absolute -left-6 top-1 w-4 h-4 rounded-full bg-white dark:bg-slate-900 border-2 border-gray-400 dark:border-slate-600 ring-2 ring-white dark:ring-slate-900"></div>

    <!-- Audit Log Content -->
    <div class="min-w-0 flex-1 py-0.5">
        <p class="text-xs text-gray-800 dark:text-slate-300">
            <strong class="font-semibold text-gray-900 dark:text-white">{{ $log->user?->name ?? __('System') }}</strong>
            @if ($log->field_changed === 'attachment_added')
                {{ __('added attachment') }} <span class="font-semibold text-gray-900 dark:text-white">{{ $log->new_value }}</span>
            @elseif ($log->field_changed === 'attachment_removed')
                {{ __('removed attachment') }} <span class="font-semibold text-gray-900 dark:text-white line-through">{{ $log->old_value }}</span>
            @else
                {{ __('changed') }} <span class="font-medium text-gray-700 dark:text-slate-300 underline decoration-gray-300 dark:decoration-slate-600">{{ str_replace('_', ' ', $log->formatted_field) }}</span>
            @endif
        </p>

        <!-- Old vs New Value Badges -->
        @if ($log->field_changed !== 'attachment_added' && $log->field_changed !== 'attachment_removed')
            <div class="flex items-center gap-1.5 mt-1.5 text-xs font-medium flex-wrap">
                <span class="inline-flex items-center px-2 py-0.5 rounded-lg bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-300 border border-rose-200/60 dark:border-rose-800/50 line-through text-[11px]">
                    {{ $log->formatValue($log->old_value) ?: __('empty') }}
                </span>
                <x-heroicon-m-arrow-right class="w-3.5 h-3.5 text-gray-400 dark:text-slate-500 shrink-0" />
                <span class="inline-flex items-center px-2 py-0.5 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200/60 dark:border-emerald-800/50 font-semibold text-[11px]">
                    {{ $log->formatValue($log->new_value) }}
                </span>
            </div>
        @endif

        <!-- Timestamp & Relative Time Tooltip -->
        <div class="text-[11px] text-gray-400 dark:text-slate-500 mt-1.5 flex items-center gap-1.5 flex-wrap">
            <div class="relative group inline-flex items-center cursor-default">
                <span class="group-hover:text-brand-600 dark:group-hover:text-brand-400 transition-colors font-medium">
                    @ist($log->created_at)
                </span>
                <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1 hidden group-hover:flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-gray-900 dark:bg-slate-800 dark:ring-1 dark:ring-slate-700 text-white shadow-md whitespace-nowrap z-30 pointer-events-none">
                    <span>{{ $log->created_at->diffForHumans() }}</span>
                    <div class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-gray-900 dark:border-t-slate-800"></div>
                </div>
            </div>

            @if ($log->created_at->diffInHours() < 24)
                <span>&bull;</span>
                <span class="text-gray-400 dark:text-slate-500 font-normal">
                    {{ $log->created_at->diffForHumans() }}
                </span>
            @endif
        </div>
    </div>
</div>
