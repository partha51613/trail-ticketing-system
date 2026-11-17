<div x-data="{ filter: 'all' }"
    class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-slate-800 shadow-xs overflow-hidden">

    <!-- Timeline Header & Filter Pills -->
    <div class="border-b border-gray-100 dark:border-slate-800 px-6 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div class="flex items-center gap-2">
            <h3 class="text-sm font-bold text-gray-900 dark:text-white tracking-tight">
                {{ __('Timeline') }}
            </h3>
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100 dark:bg-slate-800 text-gray-600 dark:text-slate-300 border border-gray-200/60 dark:border-slate-700">
                {{ $timeline->count() }}
            </span>
        </div>

        <!-- Filter Pills (All / Comments / History) -->
        <div class="inline-flex items-center p-1 bg-gray-100/80 dark:bg-slate-800 rounded-xl text-xs font-semibold">
            <button type="button" @click="filter = 'all'"
                :class="filter === 'all' ? 'bg-white dark:bg-slate-700 text-gray-900 dark:text-white shadow-xs' : 'text-gray-500 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white'"
                class="px-3 py-1 rounded-lg transition-all flex items-center gap-1.5">
                <span>{{ __('All') }}</span>
                <span class="text-[10px] opacity-75">({{ $timeline->count() }})</span>
            </button>

            <button type="button" @click="filter = 'comment'"
                :class="filter === 'comment' ? 'bg-white dark:bg-slate-700 text-brand-600 dark:text-brand-400 shadow-xs' : 'text-gray-500 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white'"
                class="px-3 py-1 rounded-lg transition-all flex items-center gap-1.5">
                <x-heroicon-m-chat-bubble-left-ellipsis class="w-3.5 h-3.5" />
                <span>{{ __('Comments') }}</span>
                <span class="text-[10px] opacity-75">({{ $ticket->comments->count() }})</span>
            </button>

            <button type="button" @click="filter = 'activity'"
                :class="filter === 'activity' ? 'bg-white dark:bg-slate-700 text-gray-900 dark:text-white shadow-xs' : 'text-gray-500 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white'"
                class="px-3 py-1 rounded-lg transition-all flex items-center gap-1.5">
                <x-heroicon-m-clock class="w-3.5 h-3.5" />
                <span>{{ __('History') }}</span>
                <span class="text-[10px] opacity-75">({{ $ticket->activityLogs->count() }})</span>
            </button>
        </div>
    </div>

    <!-- Post New Comment Input Form (Hidden on Deleted or Closed Projects) -->
    @if($ticket->trashed() || $ticket->project?->isClosed())
        <div class="p-5 border-b border-gray-100 dark:border-slate-800 bg-gray-50 dark:bg-slate-850/50 text-center">
            <p class="text-xs text-gray-500 dark:text-slate-400 font-medium italic">
                {{ $ticket->trashed() ? __('Commenting is disabled on deleted tickets.') : __('Commenting is disabled because this project workspace is closed.') }}
            </p>
        </div>
    @else
        <div class="p-6 border-b border-gray-100 dark:border-slate-800 bg-gray-50/30 dark:bg-slate-850/30" x-data="fileCollector()">
            <form action="{{ route('comments.store', $ticket) }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                @csrf
                <div class="relative">
                    <textarea name="body" rows="3" required placeholder="{{ __('Write a comment or status update...') }}"
                        class="w-full text-sm rounded-xl border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-950 p-3.5 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 placeholder-gray-400 dark:placeholder-slate-500 transition-all"></textarea>
                </div>

                <x-file-upload-field />

                <div class="flex items-center justify-end pt-1">
                    <button type="submit"
                        class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold text-white bg-brand-500 rounded-xl hover:bg-brand-600 shadow-sm shadow-brand-500/20 transition-all">
                        <x-heroicon-m-chat-bubble-left-right class="w-4 h-4" />
                        <span>{{ __('Post Comment') }}</span>
                    </button>
                </div>
            </form>
        </div>
    @endif

    <!-- Unified Chronological Stream -->
    <div class="p-6">
        @if ($timeline->isEmpty())
            <div class="py-10 text-center">
                <div class="inline-flex items-center justify-center w-10 h-10 rounded-2xl bg-gray-50 dark:bg-slate-800 text-gray-400 dark:text-slate-500 mb-2 border border-gray-100 dark:border-slate-700">
                    <x-heroicon-o-chat-bubble-bottom-center-text class="w-5 h-5" />
                </div>
                <h4 class="text-xs font-bold text-gray-800 dark:text-slate-200">{{ __('No activity yet') }}</h4>
                <p class="text-[11px] text-gray-400 dark:text-slate-500 mt-0.5">
                    {{ __('Be the first to leave a comment or track updates.') }}
                </p>
            </div>
        @else
            <div class="relative pl-6 space-y-6 before:absolute before:left-2 before:top-2 before:bottom-2 before:w-0.5 before:bg-gray-200 dark:before:bg-slate-750">
                @foreach ($timeline as $item)
                    @if ($item['type'] === 'comment')
                        <x-timeline.comment-card :comment="$item['data']" :ticket="$ticket" />
                    @elseif ($item['type'] === 'activity')
                        <x-timeline.activity-node :log="$item['data']" />
                    @endif
                @endforeach
            </div>
        @endif
    </div>
</div>
