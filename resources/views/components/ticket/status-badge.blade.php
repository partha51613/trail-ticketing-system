@props([
    'status' => 'new',
    'size' => 'md',
    'showDot' => true,
])

@php
    $normalizedStatus = strtolower(trim((string) $status));

    $colorClasses = match ($normalizedStatus) {
        'new' => 'bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300 border-blue-200/60 dark:border-blue-800/50',
        'in_progress' => 'bg-brand-50 dark:bg-brand-950/40 text-brand-700 dark:text-brand-300 border-brand-200/60 dark:border-brand-800/50',
        'waiting' => 'bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 border-purple-200/60 dark:border-purple-800/50',
        'blocked' => 'bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-300 border-rose-200/60 dark:border-rose-800/50',
        'closed' => 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border-emerald-200/60 dark:border-emerald-800/50',
        default => 'bg-gray-50 dark:bg-slate-800 text-gray-700 dark:text-slate-300 border-gray-200/60 dark:border-slate-700',
    };

    $dotColor = match ($normalizedStatus) {
        'new' => 'bg-blue-500',
        'in_progress' => 'bg-brand-500',
        'waiting' => 'bg-purple-500',
        'blocked' => 'bg-rose-500',
        'closed' => 'bg-emerald-500',
        default => 'bg-gray-400',
    };

    $sizeClasses = match ($size) {
        'sm' => 'px-2 py-0.5 text-[11px] gap-1',
        'lg' => 'px-3 py-1.5 text-xs gap-1.5',
        default => 'px-2.5 py-0.5 text-xs gap-1',
    };

    $label = ucfirst(str_replace('_', ' ', $normalizedStatus));
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center rounded-full font-semibold border {$colorClasses} {$sizeClasses}"]) }}>
    @if ($showDot)
        <span class="w-1.5 h-1.5 rounded-full {{ $dotColor }} shrink-0"></span>
    @endif
    <span>{{ $slot->isEmpty() ? $label : $slot }}</span>
</span>
