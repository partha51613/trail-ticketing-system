@props([
    'title',
    'value',
    'subtitle' => null,
    'href' => null,
    'color' => 'indigo', // indigo, red, emerald, brand, amber
])

@php
    $colorMap = [
        'indigo' => [
            'hover_border' => 'hover:border-indigo-300 dark:hover:border-indigo-500/50',
            'hover_text' => 'group-hover:text-indigo-600 dark:group-hover:text-indigo-400',
            'bg_icon' => 'bg-indigo-50/80 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 group-hover:bg-indigo-600 group-hover:text-white',
            'val_text' => 'text-gray-900 dark:text-white',
        ],
        'red' => [
            'hover_border' => 'hover:border-red-300 dark:hover:border-red-500/50',
            'hover_text' => 'group-hover:text-red-600 dark:group-hover:text-red-400',
            'bg_icon' => 'bg-red-50 dark:bg-red-950/40 text-red-600 dark:text-red-400 group-hover:bg-red-600 group-hover:text-white',
            'val_text' => 'text-red-600 dark:text-red-400',
        ],
        'emerald' => [
            'hover_border' => 'hover:border-emerald-300 dark:hover:border-emerald-500/50',
            'hover_text' => 'group-hover:text-emerald-600 dark:group-hover:text-emerald-400',
            'bg_icon' => 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 group-hover:bg-emerald-600 group-hover:text-white',
            'val_text' => 'text-gray-900 dark:text-white',
        ],
        'brand' => [
            'hover_border' => 'hover:border-brand-300 dark:hover:border-brand-500/50',
            'hover_text' => 'group-hover:text-brand-600 dark:group-hover:text-brand-400',
            'bg_icon' => 'bg-brand-50 dark:bg-brand-950/40 text-brand-600 dark:text-brand-400 group-hover:bg-brand-600 group-hover:text-white',
            'val_text' => 'text-gray-900 dark:text-white',
        ],
        'amber' => [
            'hover_border' => 'hover:border-amber-300 dark:hover:border-amber-500/50',
            'hover_text' => 'group-hover:text-amber-600 dark:group-hover:text-amber-400',
            'bg_icon' => 'bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 group-hover:bg-amber-600 group-hover:text-white',
            'val_text' => 'text-gray-900 dark:text-white',
        ],
    ];

    $palette = $colorMap[$color] ?? $colorMap['indigo'];
    $tag = $href ? 'a' : 'div';
@endphp

<{{ $tag }}
    @if ($href) href="{{ $href }}" @endif
    {{ $attributes->merge(['class' => "bg-white dark:bg-slate-900 rounded-2xl border border-gray-200/80 dark:border-slate-800 p-6 shadow-xs transition-all duration-200 group " . ($href ? "hover:shadow-md hover:-translate-y-0.5 {$palette['hover_border']}" : "hover:shadow-md")]) }}>
    <div class="flex items-center justify-between">
        <div>
            <p class="text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">{{ $title }}</p>
            <h3 class="text-3xl font-extrabold mt-2 transition {{ $palette['val_text'] }} {{ $href ? $palette['hover_text'] : '' }}">
                {{ $value }}
            </h3>
            @if ($subtitle)
                <p class="text-xs text-gray-400 dark:text-slate-500 mt-1">{{ $subtitle }}</p>
            @endif
        </div>
        @if (isset($icon))
            <div class="p-3.5 rounded-2xl transition-all duration-200 shadow-2xs {{ $palette['bg_icon'] }}">
                {{ $icon }}
            </div>
        @endif
    </div>
</{{ $tag }}>
