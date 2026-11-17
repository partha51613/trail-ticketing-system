@props([
    'priority' => 'medium',
    'size' => 'md',
    'showIcon' => false,
])

@php
    $normalizedPriority = strtolower(trim((string) $priority));

    $colorClasses = match ($normalizedPriority) {
        'urgent' => 'text-red-700 dark:text-red-300 bg-red-50 dark:bg-red-950/40 border-red-200 dark:border-red-800/50',
        'high' => 'text-rose-700 dark:text-rose-300 bg-rose-50 dark:bg-rose-950/40 border-rose-200 dark:border-rose-800/50',
        'medium' => 'text-amber-700 dark:text-amber-300 bg-amber-50 dark:bg-amber-950/40 border-amber-200 dark:border-amber-800/50',
        'low' => 'text-slate-600 dark:text-slate-300 bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700',
        default => 'text-gray-600 dark:text-slate-300 bg-gray-50 dark:bg-slate-800 border-gray-200 dark:border-slate-700',
    };

    $sizeClasses = match ($size) {
        'sm' => 'px-2 py-0.5 text-[11px] rounded-md gap-1',
        'pill' => 'px-2.5 py-0.5 text-[11px] rounded-full gap-1',
        'lg' => 'px-3 py-1 text-xs rounded-lg gap-1.5',
        default => 'px-2 py-0.5 text-xs rounded-md gap-1',
    };

    $label = ucfirst($normalizedPriority);
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center font-semibold border capitalize {$colorClasses} {$sizeClasses}"]) }}>
    @if ($showIcon)
        @if ($normalizedPriority === 'urgent')
            <x-heroicon-m-exclamation-triangle class="w-3.5 h-3.5 text-red-600 dark:text-red-400 shrink-0" />
        @elseif ($normalizedPriority === 'high')
            <x-heroicon-m-chevron-double-up class="w-3.5 h-3.5 text-rose-600 dark:text-rose-400 shrink-0" />
        @elseif ($normalizedPriority === 'medium')
            <x-heroicon-m-chevron-up class="w-3.5 h-3.5 text-amber-600 dark:text-amber-400 shrink-0" />
        @elseif ($normalizedPriority === 'low')
            <x-heroicon-m-chevron-down class="w-3.5 h-3.5 text-slate-500 dark:text-slate-400 shrink-0" />
        @endif
    @endif
    <span>{{ $slot->isEmpty() ? $label : $slot }}</span>
</span>
