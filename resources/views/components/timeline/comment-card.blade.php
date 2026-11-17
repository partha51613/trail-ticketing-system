@props(['comment', 'ticket'])

<div x-show="filter === 'all' || filter === 'comment'"
    class="relative flex items-start gap-3 group">

    <!-- Node Marker on Timeline -->
    <div class="absolute -left-6 top-1 w-4 h-4 rounded-full bg-white dark:bg-slate-900 border-2 border-brand-500 ring-2 ring-white dark:ring-slate-900"></div>

    <!-- Comment Card -->
    <div class="flex-1 bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-slate-800 p-4 shadow-2xs hover:shadow-xs transition-shadow">
        <div class="flex items-center justify-between gap-2 mb-2 flex-wrap">
            <div class="flex items-center gap-2">
                <x-ui.avatar :name="$comment->user->name" size="sm" />
                <span class="text-xs font-bold text-gray-900 dark:text-white">
                    {{ $comment->user->name }}
                </span>
                @if ($comment->user_id === $ticket->created_by_id)
                    <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-brand-50 dark:bg-brand-950/60 text-brand-700 dark:text-brand-300 border border-brand-200/60 dark:border-brand-800/50">
                        {{ __('Author') }}
                    </span>
                @endif
            </div>

            <div class="flex items-center gap-2 text-[11px] text-gray-400 dark:text-slate-500">
                <span title="@ist($comment->created_at)" class="cursor-help">
                    {{ $comment->created_at->diffForHumans() }}
                </span>

                <!-- Edit Comment Action -->
                @can('update', $comment)
                    <div class="flex items-center gap-1 ml-1 opacity-0 group-hover:opacity-100 transition-opacity">
                        <button type="button" @click="editing = !editing"
                            class="p-1 text-gray-400 hover:text-gray-600 dark:hover:text-slate-300"
                            title="{{ __('Edit comment') }}">
                            <x-heroicon-m-pencil class="w-3.5 h-3.5" />
                        </button>
                    </div>
                @endcan
            </div>
        </div>

        <!-- Comment Body & Inline Edit -->
        <div x-data="{ editing: false, body: '{{ addslashes($comment->body) }}' }">
            <div x-show="!editing" class="text-xs text-gray-700 dark:text-slate-300 leading-relaxed whitespace-pre-line">
                {{ $comment->body }}
            </div>

            <!-- Comment Attachments -->
            @if ($comment->attachments && $comment->attachments->isNotEmpty())
                <div class="mt-3 pt-3 border-t border-gray-100 dark:border-slate-800 flex flex-wrap gap-2">
                    @foreach ($comment->attachments as $att)
                        @if ($att->isImage())
                            <button type="button"
                                @click="openLightbox('{{ route('attachments.show', $att) }}', '{{ addslashes($att->file_name) }}')"
                                class="rounded-lg overflow-hidden border border-gray-200 dark:border-slate-700 hover:border-brand-400 transition-all">
                                <img src="{{ route('attachments.thumb', $att) }}" class="h-14 w-20 object-cover">
                            </button>
                        @else
                            <a href="{{ route('attachments.show', $att) }}" target="_blank"
                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg border border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-800 text-[11px] font-medium text-gray-700 dark:text-slate-300">
                                <x-heroicon-m-document class="w-3.5 h-3.5 text-brand-500" />
                                <span class="truncate max-w-[120px]">{{ $att->file_name }}</span>
                            </a>
                        @endif
                    @endforeach
                </div>
            @endif

            <!-- Inline Edit Form -->
            <div x-show="editing" x-cloak class="mt-2">
                <form action="{{ route('comments.update', $comment) }}" method="POST" class="space-y-2">
                    @csrf
                    @method('PATCH')
                    <textarea name="body" rows="2" required x-model="body"
                        class="w-full text-xs rounded-xl border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-950 p-2.5 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500"></textarea>
                    <div class="flex items-center gap-2 justify-end">
                        <button type="button" @click="editing = false"
                            class="px-2.5 py-1 text-xs text-gray-500 dark:text-slate-400 hover:text-gray-700 dark:hover:text-slate-200">
                            {{ __('Cancel') }}
                        </button>
                        <button type="submit"
                            class="px-3 py-1 text-xs font-semibold text-white bg-brand-500 hover:bg-brand-600 rounded-lg shadow-xs">
                            {{ __('Save') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
