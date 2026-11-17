@props([
    'sortable' => null,
    'align' => 'left',
])

@php
    $alignClass = match ($align) {
        'right' => 'text-right pl-3 pr-6',
        'center' => 'text-center px-3',
        default => 'text-left px-3',
    };
@endphp

<th scope="col"
    {{ $attributes->merge(['class' => "py-3.5 text-[11px] font-semibold uppercase tracking-wider text-gray-400 dark:text-slate-500 {$alignClass}" . ($sortable ? ' cursor-pointer hover:text-gray-700 dark:hover:text-slate-200 transition-colors' : '')]) }}
    @if ($sortable) @click="sortBy('{{ $sortable }}')" @endif>
    @if ($sortable)
        <div class="flex items-center gap-1.5 {{ $align === 'right' ? 'justify-end' : ($align === 'center' ? 'justify-center' : '') }}">
            <span>{{ $slot }}</span>
            <template x-if="sort === '{{ $sortable }}'">
                <x-heroicon-m-chevron-up class="w-3.5 h-3.5 text-brand-600 dark:text-brand-400 shrink-0"
                    x-bind:class="{ 'rotate-180': direction === 'desc' }" />
            </template>
        </div>
    @else
        {{ $slot }}
    @endif
</th>
