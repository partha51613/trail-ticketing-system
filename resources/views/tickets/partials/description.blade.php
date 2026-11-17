@if($ticket->trashed())
    <!-- Deleted Status Banner -->
    <div class="bg-gray-100 dark:bg-slate-800 rounded-2xl border border-gray-300/80 dark:border-slate-700 p-5 flex items-start gap-3 text-gray-700 dark:text-slate-300">
        <div class="p-2 rounded-xl bg-gray-200/80 dark:bg-slate-700 text-gray-600 dark:text-slate-400 shrink-0">
            <x-heroicon-m-archive-box-x-mark class="w-5 h-5" />
        </div>
        <div class="text-xs">
            <h4 class="font-bold text-gray-900 dark:text-white text-sm mb-0.5">{{ __('This ticket has been deleted') }}</h4>
            <p class="text-gray-500 dark:text-slate-400">
                {{ __('It is archived for audit purposes. Editing, comments, priority adjustments, and status transitions are disabled.') }}
                @if($ticket->deleted_at)
                    <span class="block mt-1 font-mono text-[11px] text-gray-400 dark:text-slate-500">
                        Deleted {{ $ticket->deleted_at->diffForHumans() }}
                        @if($ticket->deletedBy) by {{ $ticket->deletedBy->name }} @endif
                    </span>
                @endif
            </p>
        </div>
    </div>
@endif

<!-- Description Card -->
<div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-slate-800 p-6 sm:p-7 shadow-xs {{ $ticket->trashed() ? 'opacity-80 bg-gray-50/50 dark:bg-slate-900/50' : '' }}">
    <h2 class="text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-slate-500 mb-3">
        {{ __('Description') }}
    </h2>
    <div class="text-sm text-gray-700 dark:text-slate-300 leading-relaxed whitespace-pre-line">
        {{ $ticket->description ?: __('No detailed description provided.') }}
    </div>

    <!-- Ticket Attachments -->
    @if ($ticket->attachments && $ticket->attachments->isNotEmpty())
        <div class="mt-4 pt-4 border-t border-gray-100 dark:border-slate-800">
            <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400 dark:text-slate-500 block mb-2.5">
                {{ __('Attachments') }} ({{ $ticket->attachments->count() }})
            </span>
            <div class="flex flex-wrap gap-2.5">
                @foreach ($ticket->attachments as $attachment)
                    @if ($attachment->isImage())
                        <button type="button"
                            @click="openLightbox('{{ route('attachments.show', $attachment) }}', '{{ addslashes($attachment->file_name) }}')"
                            class="group relative block rounded-xl overflow-hidden border border-gray-200 dark:border-slate-700 hover:border-brand-400 dark:hover:border-brand-500 bg-gray-50 dark:bg-slate-800 transition-all text-left focus:outline-none focus:ring-2 focus:ring-brand-500/30 shadow-2xs">
                            <img src="{{ route('attachments.thumb', $attachment) }}"
                                alt="{{ $attachment->file_name }}"
                                class="h-20 w-28 object-cover group-hover:scale-105 transition-transform duration-200">
                            <span
                                class="absolute bottom-0 inset-x-0 bg-gray-900/60 backdrop-blur-xs text-white text-[10px] truncate px-1.5 py-0.5 font-medium">
                                {{ $attachment->file_name }}
                            </span>
                        </button>
                    @else
                        <a href="{{ route('attachments.show', $attachment) }}"
                            target="_blank"
                            class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl border border-gray-200/90 dark:border-slate-700 bg-white dark:bg-slate-800 hover:bg-gray-50 dark:hover:bg-slate-700 text-xs font-medium text-gray-700 dark:text-slate-300 transition-all shadow-2xs">
                            <x-heroicon-m-document
                                class="w-4 h-4 text-brand-500 shrink-0" />
                            <span class="truncate max-w-[150px]">{{ $attachment->file_name }}</span>
                            <span class="text-[10px] text-gray-400 dark:text-slate-500">({{ $attachment->formatted_size }})</span>
                            <x-heroicon-m-arrow-down-tray class="w-3.5 h-3.5 text-gray-400 dark:text-slate-500 ml-0.5" />
                        </a>
                    @endif
                @endforeach
            </div>
        </div>
    @endif
</div>
