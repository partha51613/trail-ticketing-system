@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex items-center justify-between">
        <div class="flex justify-between flex-1 sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="relative inline-flex items-center px-4 py-2 text-xs font-semibold text-gray-400 dark:text-slate-500 bg-white dark:bg-slate-900 border border-gray-200/80 dark:border-slate-800 cursor-not-allowed rounded-xl shadow-xs">
                    {!! __('pagination.previous') !!}
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 text-xs font-semibold text-gray-700 dark:text-slate-200 bg-white dark:bg-slate-800 border border-gray-200/80 dark:border-slate-700 rounded-xl hover:bg-gray-50 dark:hover:bg-slate-750 hover:text-brand-600 dark:hover:text-brand-400 hover:border-brand-200 dark:hover:border-brand-700 shadow-xs transition-all">
                    {!! __('pagination.previous') !!}
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="relative inline-flex items-center px-4 py-2 ml-3 text-xs font-semibold text-gray-700 dark:text-slate-200 bg-white dark:bg-slate-800 border border-gray-200/80 dark:border-slate-700 rounded-xl hover:bg-gray-50 dark:hover:bg-slate-750 hover:text-brand-600 dark:hover:text-brand-400 hover:border-brand-200 dark:hover:border-brand-700 shadow-xs transition-all">
                    {!! __('pagination.next') !!}
                </a>
            @else
                <span class="relative inline-flex items-center px-4 py-2 ml-3 text-xs font-semibold text-gray-400 dark:text-slate-500 bg-white dark:bg-slate-900 border border-gray-200/80 dark:border-slate-800 cursor-not-allowed rounded-xl shadow-xs">
                    {!! __('pagination.next') !!}
                </span>
            @endif
        </div>

        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
                <p class="text-xs text-gray-500 dark:text-slate-400 font-medium">
                    {!! __('Showing') !!}
                    <span class="font-bold text-gray-900 dark:text-white">{{ $paginator->firstItem() }}</span>
                    {!! __('to') !!}
                    <span class="font-bold text-gray-900 dark:text-white">{{ $paginator->lastItem() }}</span>
                    {!! __('of') !!}
                    <span class="font-bold text-gray-900 dark:text-white">{{ $paginator->total() }}</span>
                    {!! __('results') !!}
                </p>
            </div>

            <div>
                <span class="relative z-0 inline-flex items-center gap-1">
                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}">
                            <span class="inline-flex items-center justify-center w-8 h-8 text-gray-300 dark:text-slate-600 bg-white dark:bg-slate-900 border border-gray-200/80 dark:border-slate-800 cursor-not-allowed rounded-xl shadow-xs" aria-hidden="true">
                                <x-heroicon-m-chevron-left class="w-4 h-4" />
                            </span>
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center justify-center w-8 h-8 text-gray-500 dark:text-slate-400 bg-white dark:bg-slate-800 border border-gray-200/80 dark:border-slate-700 rounded-xl hover:text-brand-600 dark:hover:text-brand-400 hover:border-brand-300 dark:hover:border-brand-700 hover:bg-brand-50 dark:hover:bg-brand-950/40 shadow-xs transition-all" aria-label="{{ __('pagination.previous') }}">
                            <x-heroicon-m-chevron-left class="w-4 h-4" />
                        </a>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <span aria-disabled="true">
                                <span class="inline-flex items-center justify-center w-8 h-8 text-xs font-semibold text-gray-400 dark:text-slate-500">{{ $element }}</span>
                            </span>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span aria-current="page">
                                        <span class="inline-flex items-center justify-center w-8 h-8 text-xs font-bold text-white bg-brand-500 rounded-xl shadow-xs shadow-brand-500/20 border border-brand-500 cursor-default">{{ $page }}</span>
                                    </span>
                                @else
                                    <a href="{{ $url }}" class="inline-flex items-center justify-center w-8 h-8 text-xs font-semibold text-gray-600 dark:text-slate-300 bg-white dark:bg-slate-800 border border-gray-200/80 dark:border-slate-700 rounded-xl hover:text-brand-600 dark:hover:text-brand-400 hover:border-brand-300 dark:hover:border-brand-700 hover:bg-brand-50 dark:hover:bg-brand-950/40 shadow-xs transition-all" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center justify-center w-8 h-8 text-gray-500 dark:text-slate-400 bg-white dark:bg-slate-800 border border-gray-200/80 dark:border-slate-700 rounded-xl hover:text-brand-600 dark:hover:text-brand-400 hover:border-brand-300 dark:hover:border-brand-700 hover:bg-brand-50 dark:hover:bg-brand-950/40 shadow-xs transition-all" aria-label="{{ __('pagination.next') }}">
                            <x-heroicon-m-chevron-right class="w-4 h-4" />
                        </a>
                    @else
                        <span aria-disabled="true" aria-label="{{ __('pagination.next') }}">
                            <span class="inline-flex items-center justify-center w-8 h-8 text-gray-300 dark:text-slate-600 bg-white dark:bg-slate-900 border border-gray-200/80 dark:border-slate-800 cursor-not-allowed rounded-xl shadow-xs" aria-hidden="true">
                                <x-heroicon-m-chevron-right class="w-4 h-4" />
                            </span>
                        </span>
                    @endif
                </span>
            </div>
        </div>
    </nav>
@endif
