@props([
    'name' => null,
    'size' => 'md', // sm (w-5 h-5), md (w-6 h-6), lg (w-8 h-8), xl (w-10 h-10)
    'src' => null,
])

@php
    $initial = strtoupper(substr(trim((string) ($name ?? 'U')), 0, 1));
    if ($initial === '') {
        $initial = 'U';
    }

    $sizeClasses = match ($size) {
        'xs' => 'w-4 h-4 text-[9px]',
        'sm' => 'w-5 h-5 text-[10px]',
        'lg' => 'w-8 h-8 text-xs',
        'xl' => 'w-10 h-10 text-sm',
        default => 'w-6 h-6 text-[10px]',
    };
@endphp

@if ($src)
    <img src="{{ $src }}" alt="{{ $name ?? 'User avatar' }}"
        {{ $attributes->merge(['class' => "rounded-full object-cover border border-gray-200/60 dark:border-slate-700 shrink-0 {$sizeClasses}"]) }}>
@else
    <span
        {{ $attributes->merge(['class' => "rounded-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-gray-200/60 dark:border-slate-700 inline-flex items-center justify-center font-bold shrink-0 {$sizeClasses}"]) }}
        title="{{ $name }}">
        {{ $initial }}
    </span>
@endif
