<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-start sm:items-center gap-3 min-w-0">
                <a href="{{ $backUrl ?? route('projects.show', $ticket->project_id) }}"
                    class="p-2 rounded-xl text-gray-500 dark:text-slate-400 bg-white dark:bg-slate-900 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-slate-800 border border-gray-200/90 dark:border-slate-800 hover:border-gray-300 dark:hover:border-slate-700 shadow-xs transition-all shrink-0 mt-0.5 sm:mt-0"
                    title="{{ __('Go Back') }}">
                    <x-heroicon-m-arrow-left class="w-5 h-5" />
                </a>
                <div class="min-w-0">
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ $ticket->trashed() ? 'bg-gray-100 dark:bg-slate-800 text-gray-500 dark:text-slate-400 border border-gray-300 dark:border-slate-700' : 'bg-brand-50 dark:bg-brand-950/50 text-brand-700 dark:text-brand-300 border border-brand-200/80 dark:border-brand-800/50' }} font-mono shadow-2xs">
                            {{ $ticket->reference_code }}
                        </span>
                        @if($ticket->trashed())
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-50 dark:bg-rose-950/50 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800/50">
                                <x-heroicon-m-trash class="w-3.5 h-3.5 text-rose-600 dark:text-rose-400" />
                                <span>{{ __('DELETED TICKET') }}</span>
                            </span>
                        @endif
                        <h1 class="text-xl sm:text-2xl font-bold tracking-tight {{ $ticket->trashed() ? 'text-gray-400 dark:text-slate-500 line-through' : 'text-gray-900 dark:text-white' }} truncate">
                            {{ $ticket->title }}
                        </h1>
                    </div>
                    <div class="flex items-center gap-2 mt-0.5 text-xs text-gray-500 dark:text-slate-400 flex-wrap">
                        <span>{{ __('Opened') }} {{ $ticket->created_at->diffForHumans() }}</span>
                        <span>&bull;</span>
                        <span>{{ __('by') }} <strong
                                class="text-gray-700 dark:text-slate-200 font-semibold">{{ $ticket->creator?->name ?? __('Unknown') }}</strong></span>
                        <span>&bull;</span>
                        <a href="{{ route('projects.show', $ticket->project) }}"
                            class="font-semibold text-brand-600 dark:text-brand-400 hover:text-brand-700 dark:hover:text-brand-300 hover:underline">
                            {{ $ticket->project->name }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- Quick Action Buttons -->
            <div class="flex items-center gap-2.5 shrink-0">
                @if($ticket->trashed())
                    @can('restore', $ticket)
                        <form action="{{ route('tickets.restore', $ticket->id) }}" method="POST" id="restore-ticket-header-form">
                            @csrf
                            @method('PATCH')
                            <button type="button"
                                @click="$dispatch('open-confirm-modal', {
                                    title: '{{ __('Restore Ticket?') }}',
                                    message: '{{ __('Are you sure you want to restore this ticket to active status?') }}',
                                    confirmText: '{{ __('Restore') }}',
                                    isDanger: false,
                                    form: 'restore-ticket-header-form'
                                })"
                                class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-950/40 hover:bg-emerald-100 dark:hover:bg-emerald-900/60 rounded-xl border border-emerald-200 dark:border-emerald-800/50 transition-all shadow-xs">
                                <x-heroicon-m-arrow-path class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                                <span>{{ __('Restore Ticket') }}</span>
                            </button>
                        </form>
                    @endcan
                @else
                    <!-- Watch / Unwatch Button -->
                    <form action="{{ route('tickets.watch', $ticket) }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-xl border transition-all shadow-xs {{ $isWatching ? 'bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 border-amber-200 dark:border-amber-800/50 hover:bg-amber-100 dark:hover:bg-amber-900/60' : 'bg-white dark:bg-slate-900 text-gray-700 dark:text-slate-300 border-gray-200/90 dark:border-slate-800 hover:bg-gray-50 dark:hover:bg-slate-800' }}">
                            @if ($isWatching)
                                <x-heroicon-s-bell-alert class="w-4 h-4 text-amber-600 dark:text-amber-400" />
                                <span>{{ __('Watching') }}</span>
                            @else
                                <x-heroicon-o-bell class="w-4 h-4 text-gray-500 dark:text-slate-400" />
                                <span>{{ __('Watch') }}</span>
                            @endif
                        </button>
                    </form>

                    <!-- Edit Ticket -->
                    @can('update', $ticket)
                        <a href="{{ route('tickets.edit', $ticket) }}"
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-gray-700 dark:text-slate-300 bg-white dark:bg-slate-900 border border-gray-200/90 dark:border-slate-800 rounded-xl hover:bg-gray-50 dark:hover:bg-slate-800 hover:border-gray-300 dark:hover:border-slate-700 shadow-xs transition-all">
                            <x-heroicon-m-pencil-square class="w-4 h-4 text-gray-500 dark:text-slate-400" />
                            <span>{{ __('Edit') }}</span>
                        </a>
                    @endcan

                    <!-- Delete Ticket (Creator, PM, Admin) -->
                    @can('delete', $ticket)
                        <form action="{{ route('tickets.destroy', $ticket) }}" method="POST" id="delete-ticket-header-form">
                            @csrf
                            @method('DELETE')
                            @if(session()->has('ticket_index_url'))
                                <input type="hidden" name="redirect_to" value="{{ session('ticket_index_url') }}">
                            @endif
                            <button type="button"
                                @click="$dispatch('open-confirm-modal', {
                                    title: '{{ __('Delete Ticket?') }}',
                                    message: '{{ __('Are you sure you want to delete this ticket? It will be soft-deleted and preserved for audit.') }}',
                                    confirmText: '{{ __('Delete') }}',
                                    isDanger: true,
                                    form: 'delete-ticket-header-form'
                                })"
                                class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-rose-700 dark:text-rose-300 bg-rose-50 dark:bg-rose-950/40 border border-rose-200/80 dark:border-rose-800/50 rounded-xl hover:bg-rose-100 dark:hover:bg-rose-900/60 hover:border-rose-300 dark:hover:border-rose-700 shadow-xs transition-all"
                                title="{{ __('Delete Ticket') }}">
                                <x-heroicon-m-trash class="w-4 h-4 text-rose-600 dark:text-rose-400" />
                                <span>{{ __('Delete') }}</span>
                            </button>
                        </form>
                    @endcan
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-gray-50/60 dark:bg-slate-950 min-h-screen"
        x-data="{
            lightboxOpen: false,
            lightboxSrc: '',
            lightboxTitle: '',
            openLightbox(src, title) {
                this.lightboxSrc = src;
                this.lightboxTitle = title;
                this.lightboxOpen = true;
            },
            closeLightbox() {
                this.lightboxOpen = false;
                this.lightboxSrc = '';
                this.lightboxTitle = '';
            }
        }"
        @keydown.escape.window="closeLightbox()">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 sm:gap-8 items-start">

                <!-- Left Column (Main Ticket Body & Unified Timeline - 2/3) -->
                <div class="lg:col-span-2 space-y-6">

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

                    <!-- Unified Timeline with Filter Pills -->
                    <div x-data="{ filter: 'all' }"
                        class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-slate-800 shadow-xs overflow-hidden">

                        <!-- Timeline Header & Filter Pills -->
                        <div
                            class="border-b border-gray-100 dark:border-slate-800 px-6 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div class="flex items-center gap-2">
                                <h3 class="text-sm font-bold text-gray-900 dark:text-white tracking-tight">
                                    {{ __('Timeline') }}
                                </h3>
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100 dark:bg-slate-800 text-gray-600 dark:text-slate-300 border border-gray-200/60 dark:border-slate-700">
                                    {{ $timeline->count() }}
                                </span>
                            </div>

                            <!-- Filter Pills (All / Comments / History) -->
                            <div class="inline-flex items-center p-1 bg-gray-100/80 dark:bg-slate-800 rounded-xl text-xs font-semibold">
                                <button type="button" @click="filter = 'all'"
                                    :class="filter === 'all' ? 'bg-white dark:bg-slate-700 text-gray-900 dark:text-white shadow-xs' :
                                        'text-gray-500 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white'"
                                    class="px-3 py-1 rounded-lg transition-all flex items-center gap-1.5">
                                    <span>{{ __('All') }}</span>
                                    <span class="text-[10px] opacity-75">({{ $timeline->count() }})</span>
                                </button>

                                <button type="button" @click="filter = 'comment'"
                                    :class="filter === 'comment' ? 'bg-white dark:bg-slate-700 text-brand-600 dark:text-brand-400 shadow-xs' :
                                        'text-gray-500 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white'"
                                    class="px-3 py-1 rounded-lg transition-all flex items-center gap-1.5">
                                    <x-heroicon-m-chat-bubble-left-ellipsis class="w-3.5 h-3.5" />
                                    <span>{{ __('Comments') }}</span>
                                    <span class="text-[10px] opacity-75">({{ $ticket->comments->count() }})</span>
                                </button>

                                <button type="button" @click="filter = 'activity'"
                                    :class="filter === 'activity' ? 'bg-white dark:bg-slate-700 text-gray-900 dark:text-white shadow-xs' :
                                        'text-gray-500 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white'"
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
                        <div class="p-6 border-b border-gray-100 dark:border-slate-800 bg-gray-50/30 dark:bg-slate-850/30"
                            x-data="{
                                selectedFiles: [],
                                maxFiles: 10,
                                maxFileSize: 10 * 1024 * 1024, // 10MB in bytes
                                errorMessage: '',
                                sizeErrorModal: false,
                                oversizedFileName: '',
                                oversizedFileSize: '',
                                handleFileSelect(event) {
                                    const newFiles = Array.from(event.target.files);
                                    this.errorMessage = '';

                                    for (let file of newFiles) {
                                        // Check file size limit (10MB)
                                        if (file.size > this.maxFileSize) {
                                            this.oversizedFileName = file.name;
                                            this.oversizedFileSize = this.formatBytes(file.size);
                                            this.sizeErrorModal = true;
                                            continue; // Skip oversized file
                                        }

                                        if (this.selectedFiles.length >= this.maxFiles) {
                                            this.errorMessage = 'You can attach up to ' + this.maxFiles + ' files.';
                                            break;
                                        }
                                        // Generate preview URL for images
                                        const isImage = file.type.startsWith('image/');
                                        const previewUrl = isImage ? URL.createObjectURL(file) : null;

                                        this.selectedFiles.push({
                                            file: file,
                                            name: file.name,
                                            size: this.formatBytes(file.size),
                                            isImage: isImage,
                                            previewUrl: previewUrl,
                                        });
                                    }

                                    // Clear picker so same file can be re-selected if needed
                                    event.target.value = '';
                                    this.syncFilesToInput();
                                },
                                removeFile(index) {
                                    const removed = this.selectedFiles.splice(index, 1)[0];
                                    if (removed && removed.previewUrl) {
                                        URL.revokeObjectURL(removed.previewUrl);
                                    }
                                    this.errorMessage = '';
                                    this.syncFilesToInput();
                                },
                                syncFilesToInput() {
                                    const dt = new DataTransfer();
                                    this.selectedFiles.forEach(item => dt.items.add(item.file));
                                    this.$refs.actualFileInput.files = dt.files;
                                },
                                formatBytes(bytes) {
                                    if (bytes === 0) return '0 B';
                                    const k = 1024;
                                    const sizes = ['B', 'KB', 'MB', 'GB'];
                                    const i = Math.floor(Math.log(bytes) / Math.log(k));
                                    return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
                                }
                            }">
                            <form action="{{ route('comments.store', $ticket) }}" method="POST"
                                enctype="multipart/form-data" class="space-y-3">
                                @csrf

                                <!-- Actual File Input submitted with Form -->
                                <input type="file" name="attachments[]" x-ref="actualFileInput" multiple class="hidden">

                                <div class="relative">
                                    <textarea name="body" rows="3" required placeholder="{{ __('Write a comment or status update...') }}"
                                        class="w-full text-sm rounded-xl border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-950 p-3.5 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 placeholder-gray-400 dark:placeholder-slate-500 transition-all"></textarea>
                                </div>

                                <!-- Queued File Previews (Thumbnails & File Badges with Remove 'X') -->
                                <div x-show="selectedFiles.length > 0" x-cloak class="pt-1">
                                    <div class="flex flex-wrap gap-2.5">
                                        <template x-for="(item, index) in selectedFiles" :key="index">
                                            <div class="group relative rounded-xl border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-1.5 shadow-2xs hover:border-gray-300 dark:hover:border-slate-600 transition-all flex items-center gap-2">
                                                <!-- Image Thumbnail -->
                                                <template x-if="item.isImage">
                                                    <img :src="item.previewUrl" class="w-12 h-12 rounded-lg object-cover bg-gray-50 dark:bg-slate-900 border border-gray-100 dark:border-slate-700 shrink-0">
                                                </template>

                                                <!-- Document Icon for Non-Images -->
                                                <template x-if="!item.isImage">
                                                    <div class="w-12 h-12 rounded-lg bg-gray-50 dark:bg-slate-900 border border-gray-100 dark:border-slate-700 flex flex-col items-center justify-center shrink-0 text-brand-600 dark:text-brand-400">
                                                        <x-heroicon-m-document class="w-5 h-5" />
                                                        <span class="text-[9px] font-semibold uppercase tracking-wider text-gray-500 dark:text-slate-400" x-text="item.name.split('.').pop() || 'FILE'"></span>
                                                    </div>
                                                </template>

                                                <!-- Name & Size -->
                                                <div class="pr-6 min-w-0 max-w-[130px]">
                                                    <p class="text-xs font-medium text-gray-800 dark:text-slate-200 truncate" :title="item.name" x-text="item.name"></p>
                                                    <p class="text-[10px] text-gray-400 dark:text-slate-500" x-text="item.size"></p>
                                                </div>

                                                <!-- Remove 'X' Button -->
                                                <button type="button" @click="removeFile(index)"
                                                    title="{{ __('Remove file') }}"
                                                    class="absolute top-1 right-1 p-1 rounded-full bg-gray-100 dark:bg-slate-700 hover:bg-rose-100 dark:hover:bg-rose-950/60 text-gray-500 dark:text-slate-400 hover:text-rose-600 dark:hover:text-rose-300 transition-colors">
                                                    <x-heroicon-m-x-mark class="w-3.5 h-3.5" />
                                                </button>
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                <!-- Validation / Max Limit Notice -->
                                <div x-show="errorMessage" x-cloak class="text-xs font-medium text-rose-600 dark:text-rose-400">
                                    <span x-text="errorMessage"></span>
                                </div>

                                <!-- Attachment Action Row -->
                                <div class="flex items-center justify-between gap-3 pt-1">
                                    <div class="flex items-center gap-2">
                                        <label
                                            class="cursor-pointer inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-medium text-gray-700 dark:text-slate-300 hover:bg-gray-50 dark:hover:bg-slate-750 hover:border-gray-300 dark:hover:border-slate-600 transition-all shadow-2xs">
                                            <x-heroicon-m-paper-clip class="w-4 h-4 text-gray-500 dark:text-slate-400" />
                                            <span>{{ __('Attach Files') }}</span>
                                            <!-- Transient input that triggers the collector -->
                                            <input type="file" multiple class="hidden"
                                                @change="handleFileSelect($event)"
                                                accept="image/png,image/jpeg,image/webp,image/gif,application/pdf,.doc,.docx,.txt,.csv,.log,.zip">
                                        </label>

                                        <!-- Dynamic count badge -->
                                        <span x-show="selectedFiles.length > 0" x-cloak
                                            class="text-xs font-semibold px-2 py-0.5 rounded-full bg-brand-50 dark:bg-brand-950/60 text-brand-700 dark:text-brand-300 border border-brand-200 dark:border-brand-800/50">
                                            <span x-text="selectedFiles.length"></span>/10 {{ __('files') }}
                                        </span>
                                        <span class="text-[11px] text-gray-400 dark:text-slate-500 hidden sm:inline">
                                            {{ __('Up to 10 files (max 10MB each)') }}
                                        </span>
                                    </div>

                                    <button type="submit"
                                        class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold text-white bg-brand-500 rounded-xl hover:bg-brand-600 shadow-sm shadow-brand-500/20 transition-all">
                                        <x-heroicon-m-chat-bubble-left-right class="w-4 h-4" />
                                        <span>{{ __('Post Comment') }}</span>
                                    </button>
                                </div>
                            </form>

                            <!-- Oversized File Modal Dialog -->
                            <div x-show="sizeErrorModal"
                                x-cloak
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0"
                                x-transition:enter-end="opacity-100"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="opacity-100"
                                x-transition:leave-end="opacity-0"
                                class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-950/60 backdrop-blur-xs"
                                @click.self="sizeErrorModal = false"
                                @keydown.escape.window="sizeErrorModal = false"
                                role="alertdialog"
                                aria-modal="true">

                                <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-sm w-full p-6 shadow-2xl border border-gray-100 dark:border-slate-800 text-center space-y-4"
                                    @click.stop>
                                    <div class="w-12 h-12 rounded-full bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 flex items-center justify-center mx-auto border border-rose-100 dark:border-rose-800/40">
                                        <x-heroicon-o-exclamation-triangle class="w-6 h-6" />
                                    </div>

                                    <div>
                                        <h4 class="text-base font-bold text-gray-900 dark:text-white">{{ __('File Too Large') }}</h4>
                                        <p class="text-xs text-gray-500 dark:text-slate-400 mt-1">
                                            {{ __('The maximum allowed file size is') }} <strong class="text-gray-800 dark:text-slate-200">10 MB</strong>.
                                        </p>
                                        <div class="mt-3 p-2.5 rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200/80 dark:border-slate-700 text-left">
                                            <p class="text-xs font-semibold text-gray-800 dark:text-slate-200 truncate" x-text="oversizedFileName"></p>
                                            <p class="text-[11px] text-rose-600 dark:text-rose-400 font-medium mt-0.5">
                                                {{ __('Size:') }} <span x-text="oversizedFileSize"></span> ({{ __('exceeds limit') }})
                                            </p>
                                        </div>
                                    </div>

                                    <button type="button"
                                        @click="sizeErrorModal = false"
                                        class="w-full py-2.5 px-4 text-xs font-semibold rounded-xl text-white bg-gray-900 dark:bg-brand-500 hover:bg-gray-800 dark:hover:bg-brand-600 transition-colors shadow-xs">
                                        {{ __('Got it') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Unified Chronological Stream -->
                        <div class="p-6">
                            @if ($timeline->isEmpty())
                                <div class="py-10 text-center">
                                    <div
                                        class="w-10 h-10 rounded-xl bg-gray-50 dark:bg-slate-800 text-gray-400 dark:text-slate-500 flex items-center justify-center mx-auto mb-2 border border-gray-100 dark:border-slate-700">
                                        <x-heroicon-o-chat-bubble-left-ellipsis class="w-5 h-5" />
                                    </div>
                                    <p class="text-xs text-gray-500 dark:text-slate-400 font-medium">
                                        {{ __('No timeline activity or comments yet.') }}
                                    </p>
                                </div>
                            @else
                                <div
                                    class="relative pl-6 space-y-6 before:absolute before:left-2 before:top-2 before:bottom-2 before:w-0.5 before:bg-gray-200/80 dark:before:bg-slate-800">
                                    @foreach ($timeline as $item)
                                        @if ($item['type'] === 'comment')
                                            <!-- Comment Node -->
                                            @php $comment = $item['data']; @endphp
                                            <div x-show="filter === 'all' || filter === 'comment'"
                                                x-data="{ editing: false, body: '{{ addslashes($comment->body) }}' }" class="relative flex items-start gap-3">

                                                <!-- Node Marker on Timeline -->
                                                <div
                                                    class="absolute -left-6 top-3 w-4 h-4 rounded-full bg-white dark:bg-slate-900 border-2 border-brand-500 ring-2 ring-white dark:ring-slate-900">
                                                </div>

                                                <!-- Comment Card Content -->
                                                <div
                                                    class="min-w-0 flex-1 bg-gray-50/70 dark:bg-slate-850/60 rounded-2xl border border-gray-100 dark:border-slate-800 p-4 space-y-2">
                                                    <div class="flex items-center justify-between gap-3">
                                                        <div class="flex items-center gap-2 min-w-0 flex-wrap">
                                                            <!-- User Avatar -->
                                                            <span
                                                                class="w-6 h-6 rounded-full bg-brand-100 dark:bg-brand-950/60 text-brand-700 dark:text-brand-300 flex items-center justify-center text-[10px] font-bold ring-1 ring-white dark:ring-slate-800 shrink-0">
                                                                {{ strtoupper(substr($comment->user?->name ?? 'U', 0, 1)) }}
                                                            </span>

                                                            <!-- Commenter Name -->
                                                            <span class="text-xs font-semibold text-gray-900 dark:text-white truncate">
                                                                {{ $comment->user?->name ?? __('Unknown') }}
                                                            </span>

                                                            <!-- Dot Separator -->
                                                            <span class="text-gray-300 dark:text-slate-600 text-xs shrink-0">&bull;</span>

                                                            <!-- Exact IST Timestamp (Always visible inline) with Tooltip on Hover -->
                                                            <div
                                                                class="relative group inline-flex items-center cursor-default">
                                                                <span
                                                                    class="text-xs text-gray-500 dark:text-slate-400 group-hover:text-brand-600 dark:group-hover:text-brand-400 font-medium whitespace-nowrap transition-colors">
                                                                    @ist($comment->created_at)
                                                                </span>
                                                                <!-- Floating Tooltip showing Relative Time on Hover -->
                                                                <div
                                                                    class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1.5 hidden group-hover:flex items-center px-2 py-0.5 rounded-lg text-[10px] font-semibold bg-gray-900 dark:bg-slate-800 dark:ring-1 dark:ring-slate-700 text-white shadow-md whitespace-nowrap z-30 pointer-events-none">
                                                                    <span>{{ $comment->created_at->diffForHumans() }}</span>
                                                                    <div
                                                                        class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-gray-900 dark:border-t-slate-800">
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <!-- For comments less than 24 hours old, also display relative time inline -->
                                                            @if ($comment->created_at->diffInHours() < 24)
                                                                <span
                                                                    class="text-gray-300 dark:text-slate-600 text-xs shrink-0">&bull;</span>
                                                                <span
                                                                    class="text-xs text-gray-400 dark:text-slate-500 font-normal whitespace-nowrap">
                                                                    {{ $comment->created_at->diffForHumans() }}
                                                                </span>
                                                            @endif

                                                            <!-- Edited Pill -->
                                                            @if ($comment->edited_at)
                                                                <span
                                                                    title="{{ __('Edited') }} {{ $comment->edited_at->diffForHumans() }} (@ist($comment->edited_at))"
                                                                    class="text-[10px] font-medium text-gray-400 dark:text-slate-500 bg-gray-100 dark:bg-slate-800 px-1.5 py-0.5 rounded shrink-0 cursor-default">
                                                                    {{ __('edited') }}
                                                                </span>
                                                            @endif
                                                        </div>

                                                        <!-- 5-Minute Window Actions (Policy Guarded) -->
                                                        <div class="flex items-center gap-1.5 shrink-0">
                                                            @can('update', $comment)
                                                                <button type="button" @click="editing = !editing"
                                                                    class="p-1 rounded-md text-gray-400 dark:text-slate-500 hover:text-gray-700 dark:hover:text-slate-200 hover:bg-gray-200/60 dark:hover:bg-slate-700 transition-colors"
                                                                    title="{{ __('Edit Comment (5m window)') }}">
                                                                    <x-heroicon-m-pencil class="w-3.5 h-3.5" />
                                                                </button>
                                                            @endcan

                                                            @can('delete', $comment)
                                                                <form action="{{ route('comments.destroy', $comment) }}"
                                                                    method="POST" id="delete-comment-form-{{ $comment->id }}">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="button"
                                                                        @click="$dispatch('open-confirm-modal', {
                                                                            title: '{{ __('Delete Comment?') }}',
                                                                            message: '{{ __('Are you sure you want to delete this comment? This action cannot be undone.') }}',
                                                                            confirmText: '{{ __('Delete') }}',
                                                                            isDanger: true,
                                                                            form: 'delete-comment-form-{{ $comment->id }}'
                                                                        })"
                                                                        class="p-1 rounded-md text-gray-400 dark:text-slate-500 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition-colors"
                                                                        title="{{ __('Delete Comment') }}">
                                                                        <x-heroicon-m-trash class="w-3.5 h-3.5" />
                                                                    </button>
                                                                </form>
                                                            @endcan
                                                        </div>
                                                    </div>

                                                    <!-- Comment Body / Inline Edit -->
                                                    <div x-show="!editing"
                                                        class="text-sm text-gray-700 dark:text-slate-300 whitespace-pre-line pl-8">
                                                        {{ $comment->body }}
                                                    </div>

                                                    <!-- Comment Attachments -->
                                                    @if ($comment->attachments && $comment->attachments->isNotEmpty())
                                                        <div class="mt-3 pl-8 flex flex-wrap gap-2.5">
                                                            @foreach ($comment->attachments as $attachment)
                                                                @if ($attachment->isImage())
                                                                    <button type="button"
                                                                        @click="openLightbox('{{ route('attachments.show', $attachment) }}', '{{ addslashes($attachment->file_name) }}')"
                                                                        class="group relative block rounded-xl overflow-hidden border border-gray-200 dark:border-slate-700 hover:border-brand-400 dark:hover:border-brand-500 bg-gray-50 dark:bg-slate-800 transition-all text-left focus:outline-none focus:ring-2 focus:ring-brand-500/30">
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
                                                                        class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl border border-gray-200/90 dark:border-slate-700 bg-white dark:bg-slate-800 hover:bg-gray-50 dark:hover:bg-slate-750 text-xs font-medium text-gray-700 dark:text-slate-300 transition-all shadow-2xs">
                                                                        <x-heroicon-m-document
                                                                            class="w-4 h-4 text-brand-500 shrink-0" />
                                                                        <span
                                                                            class="truncate max-w-[160px]">{{ $attachment->file_name }}</span>
                                                                        <span
                                                                            class="text-[10px] text-gray-400 dark:text-slate-500">({{ $attachment->formatted_size }})</span>
                                                                    </a>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    @endif

                                                    <div x-show="editing" x-cloak class="pl-8 pt-1">
                                                        <form action="{{ route('comments.update', $comment) }}"
                                                            method="POST" class="space-y-2">
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
                                        @elseif ($item['type'] === 'activity')
                                            <!-- Audit Log Node -->
                                            @php $log = $item['data']; @endphp
                                            <div x-show="filter === 'all' || filter === 'activity'"
                                                class="relative flex items-start gap-3">

                                                <!-- Node Marker on Timeline -->
                                                <div
                                                    class="absolute -left-6 top-1 w-4 h-4 rounded-full bg-white dark:bg-slate-900 border-2 border-gray-400 dark:border-slate-600 ring-2 ring-white dark:ring-slate-900">
                                                </div>

                                                <!-- Audit Log Content -->
                                                <div class="min-w-0 flex-1 py-0.5">
                                                    <p class="text-xs text-gray-800 dark:text-slate-300">
                                                        <strong
                                                            class="font-semibold text-gray-900 dark:text-white">{{ $log->user?->name ?? __('System') }}</strong>
                                                        @if ($log->field_changed === 'attachment_added')
                                                            {{ __('added attachment') }} <span class="font-semibold text-gray-900 dark:text-white">{{ $log->new_value }}</span>
                                                        @elseif ($log->field_changed === 'attachment_removed')
                                                            {{ __('removed attachment') }} <span class="font-semibold text-gray-900 dark:text-white line-through">{{ $log->old_value }}</span>
                                                        @else
                                                            {{ __('changed') }} <span
                                                                class="font-medium text-gray-700 dark:text-slate-300 underline decoration-gray-300 dark:decoration-slate-600">{{ str_replace('_', ' ', $log->formatted_field) }}</span>
                                                        @endif
                                                    </p>

                                                    <!-- Old vs New Value Badges (Polished) -->
                                                    @if ($log->field_changed !== 'attachment_added' && $log->field_changed !== 'attachment_removed')
                                                        <div
                                                            class="flex items-center gap-1.5 mt-1.5 text-xs font-medium flex-wrap">
                                                            <span
                                                                class="inline-flex items-center px-2 py-0.5 rounded-lg bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-300 border border-rose-200/60 dark:border-rose-800/50 line-through text-[11px]">
                                                                {{ $log->formatValue($log->old_value) ?: __('empty') }}
                                                            </span>
                                                            <x-heroicon-m-arrow-right
                                                                class="w-3.5 h-3.5 text-gray-400 dark:text-slate-500 shrink-0" />
                                                            <span
                                                                class="inline-flex items-center px-2 py-0.5 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200/60 dark:border-emerald-800/50 font-semibold text-[11px]">
                                                                {{ $log->formatValue($log->new_value) }}
                                                            </span>
                                                        </div>
                                                    @endif

                                                    <!-- Timestamp & Conditional Relative Time Suffix -->
                                                    <div
                                                        class="text-[11px] text-gray-400 dark:text-slate-500 mt-1.5 flex items-center gap-1.5 flex-wrap">
                                                        <div
                                                            class="relative group inline-flex items-center cursor-default">
                                                            <span
                                                                class="group-hover:text-brand-600 dark:group-hover:text-brand-400 transition-colors font-medium">
                                                                @ist($log->created_at)
                                                            </span>
                                                            <!-- Floating Relative Time Tooltip -->
                                                            <div
                                                                class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1 hidden group-hover:flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-gray-900 dark:bg-slate-800 dark:ring-1 dark:ring-slate-700 text-white shadow-md whitespace-nowrap z-30 pointer-events-none">
                                                                <span>{{ $log->created_at->diffForHumans() }}</span>
                                                                <div
                                                                    class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-gray-900 dark:border-t-slate-800">
                                                                </div>
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
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                        </div>

                    </div>

                </div>

                <!-- Right Column (Sidebar Meta & Actions - 1/3) -->
                <div class="space-y-6">

                    <!-- Status Transition Card -->
                    <div x-data="{ open: false, selectedStatus: '{{ $ticket->status }}' }"
                        class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-slate-800 p-6 shadow-xs space-y-4">
                        <span class="text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-slate-500 block">
                            {{ __('Status') }}
                        </span>

                        <!-- Current Status Badge & Change Button Side-by-Side -->
                        <div class="flex items-center justify-between gap-3 flex-wrap">
                            @php
                                $statusClasses = match ($ticket->status) {
                                    'new' => 'bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300 border-blue-200/60 dark:border-blue-800/50',
                                    'in_progress' => 'bg-brand-50 dark:bg-brand-950/40 text-brand-700 dark:text-brand-300 border-brand-200/60 dark:border-brand-800/50',
                                    'waiting' => 'bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 border-purple-200/60 dark:border-purple-800/50',
                                    'blocked' => 'bg-red-50 dark:bg-red-950/40 text-red-700 dark:text-red-300 border-red-200/60 dark:border-red-800/50',
                                    'closed' => 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border-emerald-200/60 dark:border-emerald-800/50',
                                    default => 'bg-gray-50 dark:bg-slate-800 text-gray-700 dark:text-slate-300 border-gray-200/60 dark:border-slate-700',
                                };
                                $statusDot = match ($ticket->status) {
                                    'new' => 'bg-blue-500',
                                    'in_progress' => 'bg-brand-500',
                                    'waiting' => 'bg-purple-500',
                                    'blocked' => 'bg-red-500',
                                    'closed' => 'bg-emerald-500',
                                    default => 'bg-gray-400',
                                };
                            @endphp
                            <span
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold border {{ $statusClasses }}">
                                <span class="w-2 h-2 rounded-full {{ $statusDot }}"></span>
                                <span>{{ ucfirst(str_replace('_', ' ', $ticket->status)) }}</span>
                            </span>

                            <button type="button" @click="open = !open"
                                class="inline-flex items-center gap-1.5 text-xs font-semibold text-brand-600 dark:text-brand-400 hover:text-brand-700 dark:hover:text-brand-300 bg-brand-50 dark:bg-brand-950/40 hover:bg-brand-100/80 dark:hover:bg-brand-900/60 px-3 py-1.5 rounded-xl border border-brand-200/60 dark:border-brand-800/50 transition-colors shadow-xs">
                                <x-heroicon-m-arrows-right-left class="w-3.5 h-3.5" />
                                <span x-text="open ? '{{ __('Cancel') }}' : '{{ __('Change Status') }}'"></span>
                            </button>
                        </div>

                        <!-- Transition Form (Reveals upon clicking 'Change Status') -->
                        <div x-show="open" x-cloak class="pt-3 border-t border-gray-100 dark:border-slate-800">
                            <form action="{{ route('tickets.transition', $ticket) }}" method="POST"
                                class="space-y-3">
                                @csrf
                                @method('PATCH')

                                <div>
                                    <label for="status" class="block text-xs font-medium text-gray-600 dark:text-slate-300 mb-1">
                                        {{ __('Select New Status:') }}
                                    </label>
                                    <select name="status" id="status" x-model="selectedStatus"
                                        class="w-full text-xs rounded-xl border border-gray-200 dark:border-slate-700 bg-gray-50/70 dark:bg-slate-800 p-2.5 text-gray-900 dark:text-white focus:bg-white dark:focus:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all font-medium">
                                        <option value="new">{{ __('New') }}</option>
                                        <option value="in_progress">{{ __('In Progress') }}</option>
                                        <option value="waiting">{{ __('Waiting') }}</option>
                                        <option value="blocked">{{ __('Blocked') }}</option>
                                        <option value="closed">{{ __('Closed') }}</option>
                                    </select>
                                </div>

                                <!-- Atomic Blocked Reason Comment Requirement -->
                                <div x-show="selectedStatus === 'blocked'" x-cloak
                                    class="space-y-1.5 p-3 rounded-xl bg-red-50/70 dark:bg-red-950/30 border border-red-200/80 dark:border-red-800/50">
                                    <label class="block text-[11px] font-bold text-red-800 dark:text-red-300">
                                        {{ __('Mandatory Blocked Reason:') }}
                                    </label>
                                    <textarea name="comment" rows="2" x-bind:required="selectedStatus === 'blocked'"
                                        placeholder="{{ __('Explain what is blocking this ticket...') }}"
                                        class="w-full text-xs rounded-lg border border-red-200 dark:border-red-800/60 bg-white dark:bg-slate-950 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-slate-500 p-2 focus:ring-2 focus:ring-red-400 focus:outline-none"></textarea>
                                </div>

                                <div class="flex items-center gap-2">
                                    <button type="button" @click="open = false"
                                        class="flex-1 py-2 text-xs font-medium text-gray-600 dark:text-slate-300 hover:text-gray-900 dark:hover:text-white bg-gray-50 dark:bg-slate-800 hover:bg-gray-100 dark:hover:bg-slate-700 rounded-xl transition-colors">
                                        {{ __('Cancel') }}
                                    </button>
                                    <button type="submit"
                                        class="flex-1 inline-flex items-center justify-center gap-1.5 px-4 py-2 text-xs font-semibold text-white bg-brand-500 rounded-xl hover:bg-brand-600 shadow-sm shadow-brand-500/20 transition-all">
                                        <span>{{ __('Apply') }}</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Quick Reassign Card -->
                    <div x-data="{ reassignOpen: false }"
                        class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-slate-800 p-6 shadow-xs space-y-4">
                        <span class="text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-slate-500 block">
                            {{ __('Assignee') }}
                        </span>

                        <!-- Current Assignee Info & Reassign Button Side-by-Side -->
                        <div class="flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <span
                                    class="w-9 h-9 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 flex items-center justify-center text-sm font-bold ring-2 ring-white dark:ring-slate-900 shrink-0">
                                    {{ strtoupper(substr($ticket->assignedTo?->name ?? '?', 0, 1)) }}
                                </span>
                                <div class="min-w-0 flex-1">
                                    <div class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                                        {{ $ticket->assignedTo?->name ?? __('Unassigned') }}
                                    </div>
                                    <div class="text-xs text-gray-400 dark:text-slate-500 truncate">
                                        {{ $ticket->assignedTo?->email ?? __('Not assigned to anyone yet') }}
                                    </div>
                                </div>
                            </div>

                            @can('reassign', $ticket)
                                <button type="button" @click="reassignOpen = !reassignOpen"
                                    class="inline-flex items-center gap-1.5 text-xs font-semibold text-brand-600 dark:text-brand-400 hover:text-brand-700 dark:hover:text-brand-300 bg-brand-50 dark:bg-brand-950/40 hover:bg-brand-100/80 dark:hover:bg-brand-900/60 px-3 py-1.5 rounded-xl border border-brand-200/60 dark:border-brand-800/50 transition-colors shadow-xs shrink-0">
                                    <x-heroicon-m-user-plus class="w-3.5 h-3.5" />
                                    <span x-text="reassignOpen ? '{{ __('Cancel') }}' : '{{ __('Reassign') }}'"></span>
                                </button>
                            @endcan
                        </div>

                        <!-- Inline Reassignment Form (Reveals upon clicking 'Reassign') -->
                        @can('reassign', $ticket)
                            <div x-show="reassignOpen" x-cloak class="pt-3 border-t border-gray-100 dark:border-slate-800">
                                <form action="{{ route('tickets.reassign', $ticket) }}" method="POST"
                                    class="space-y-3">
                                    @csrf
                                    @method('PATCH')
                                    <div>
                                        <label for="assigned_to_id" class="block text-xs font-medium text-gray-600 dark:text-slate-300 mb-1">
                                            {{ __('Select New Assignee:') }}
                                        </label>
                                        <select name="assigned_to_id" id="assigned_to_id"
                                            class="w-full text-xs rounded-xl border border-gray-200 dark:border-slate-700 bg-gray-50/70 dark:bg-slate-800 p-2.5 text-gray-900 dark:text-white focus:bg-white dark:focus:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all font-medium">
                                            <option value="">{{ __('Unassigned') }}</option>
                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}" @selected($ticket->assigned_to_id === $user->id)>
                                                    {{ $user->name }} ({{ ucfirst($user->role) }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button type="button" @click="reassignOpen = false"
                                            class="flex-1 py-2 text-xs font-medium text-gray-600 dark:text-slate-300 hover:text-gray-900 dark:hover:text-white bg-gray-50 dark:bg-slate-800 hover:bg-gray-100 dark:hover:bg-slate-700 rounded-xl transition-colors">
                                            {{ __('Cancel') }}
                                        </button>
                                        <button type="submit"
                                            class="flex-1 inline-flex items-center justify-center px-4 py-2 text-xs font-semibold text-white bg-brand-500 rounded-xl hover:bg-brand-600 shadow-sm shadow-brand-500/20 transition-all">
                                            {{ __('Save Assignee') }}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        @endcan
                    </div>

                    <!-- Meta & Attributes Card -->
                    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-slate-800 p-6 shadow-xs space-y-4 text-xs">
                        <span class="text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-slate-500 block">
                            {{ __('Ticket Details') }}
                        </span>

                        <!-- Ticket Reference & Number -->
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500 dark:text-slate-400 font-medium">{{ __('Ticket Number') }}</span>
                            <span class="font-bold text-gray-900 dark:text-white font-mono">{{ $ticket->reference_code }}</span>
                        </div>

                        <!-- Priority with Inline Change Action -->
                        <div x-data="{ priorityOpen: false, selectedPriority: '{{ $ticket->priority }}' }" class="border-t border-gray-100 dark:border-slate-800 pt-3 space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-500 dark:text-slate-400 font-medium">{{ __('Priority') }}</span>
                                <div class="flex items-center gap-2">
                                    @php
                                        $priorityClasses = match ($ticket->priority) {
                                            'urgent' => 'text-red-700 dark:text-red-300 bg-red-50 dark:bg-red-950/40 border-red-200 dark:border-red-800/50',
                                            'high' => 'text-rose-700 dark:text-rose-300 bg-rose-50 dark:bg-rose-950/40 border-rose-200 dark:border-rose-800/50',
                                            'medium' => 'text-amber-700 dark:text-amber-300 bg-amber-50 dark:bg-amber-950/40 border-amber-200 dark:border-amber-800/50',
                                            'low' => 'text-slate-600 dark:text-slate-300 bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700',
                                            default => 'text-gray-600 dark:text-slate-300 bg-gray-50 dark:bg-slate-800 border-gray-200 dark:border-slate-700',
                                        };
                                    @endphp
                                    <span
                                        class="px-2.5 py-0.5 rounded-md font-semibold border {{ $priorityClasses }}">
                                        {{ ucfirst($ticket->priority) }}
                                    </span>

                                    @can('update', $ticket)
                                        <button type="button" @click="priorityOpen = !priorityOpen"
                                            class="inline-flex items-center p-1 rounded-md text-gray-400 dark:text-slate-500 hover:text-brand-600 dark:hover:text-brand-400 hover:bg-brand-50 dark:hover:bg-brand-950/40 transition-colors"
                                            title="{{ __('Change Priority') }}">
                                            <x-heroicon-m-pencil-square class="w-3.5 h-3.5" />
                                        </button>
                                    @endcan
                                </div>
                            </div>

                            <!-- Inline Priority Change Form -->
                            @can('update', $ticket)
                                <div x-show="priorityOpen" x-cloak class="pt-2">
                                    <form action="{{ route('tickets.priority', $ticket) }}" method="POST"
                                        class="space-y-2">
                                        @csrf
                                        @method('PATCH')
                                        <div class="flex items-center gap-2">
                                            <select name="priority" x-model="selectedPriority"
                                                class="flex-1 text-xs rounded-xl border border-gray-200 dark:border-slate-700 bg-gray-50/70 dark:bg-slate-800 p-2 text-gray-900 dark:text-white focus:bg-white dark:focus:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 font-medium">
                                                <option value="low">{{ __('Low') }}</option>
                                                <option value="medium">{{ __('Medium') }}</option>
                                                <option value="high">{{ __('High') }}</option>
                                                <option value="urgent">{{ __('Urgent') }}</option>
                                            </select>
                                            <button type="button" @click="priorityOpen = false"
                                                class="px-2.5 py-1.5 text-xs text-gray-500 dark:text-slate-400 hover:text-gray-700 dark:hover:text-slate-200">
                                                {{ __('Cancel') }}
                                            </button>
                                            <button type="submit"
                                                class="px-3 py-1.5 text-xs font-semibold text-white bg-brand-500 hover:bg-brand-600 rounded-xl shadow-xs transition-all">
                                                {{ __('Save') }}
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            @endcan
                        </div>

                        <!-- Due Date (with 11:59 PM deadline) -->
                        <div class="flex items-center justify-between border-t border-gray-100 dark:border-slate-800 pt-3">
                            <span class="text-gray-500 dark:text-slate-400 font-medium">{{ __('Due Date') }}</span>
                            @if ($ticket->due_date)
                                <div class="flex items-center gap-1.5 flex-wrap justify-end">
                                    <span class="font-semibold text-gray-800 dark:text-slate-200">
                                        {{ $ticket->due_date->format('M d, Y') }}, 11:59 PM
                                    </span>
                                    @if ($ticket->is_overdue)
                                        <span
                                            class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-red-100 dark:bg-red-950/60 text-red-700 dark:text-red-300 border border-red-200/50 dark:border-red-800/40">
                                            {{ __('OVERDUE') }}
                                        </span>
                                    @endif
                                </div>
                            @else
                                <span class="text-gray-400 dark:text-slate-600 italic">{{ __('None set') }}</span>
                            @endif
                        </div>

                        <!-- Created On (matching 'M d, Y, h:i A' format) -->
                        <div class="flex items-center justify-between border-t border-gray-100 dark:border-slate-800 pt-3">
                            <span class="text-gray-500 dark:text-slate-400 font-medium">{{ __('Created On') }}</span>
                            <span title="{{ $ticket->created_at->diffForHumans() }}"
                                class="font-semibold text-gray-800 dark:text-slate-200 cursor-help">
                                {{ $ticket->created_at->setTimezone('Asia/Kolkata')->format('M d, Y, h:i A') }}
                            </span>
                        </div>

                        <!-- Workspace -->
                        <div class="flex items-center justify-between border-t border-gray-100 dark:border-slate-800 pt-3">
                            <span class="text-gray-500 dark:text-slate-400 font-medium">{{ __('Project') }}</span>
                            <a href="{{ route('projects.show', $ticket->project) }}"
                                class="font-semibold text-brand-600 dark:text-brand-400 hover:text-brand-700 dark:hover:text-brand-300 hover:underline truncate max-w-[150px]">
                                {{ $ticket->project->name }}
                            </a>
                        </div>

                        <!-- Watchers -->
                        <div class="border-t border-gray-100 dark:border-slate-800 pt-3 space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-500 dark:text-slate-400 font-medium">{{ __('Watchers') }}</span>
                                <span class="font-semibold text-gray-700 dark:text-slate-300">{{ $ticket->watchers->count() }}</span>
                            </div>
                            @if ($ticket->watchers->isNotEmpty())
                                <div class="flex items-center -space-x-1.5 overflow-hidden pt-1">
                                    @foreach ($ticket->watchers->take(6) as $watcher)
                                        <span
                                            class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 ring-2 ring-white dark:ring-slate-900 text-[10px] font-bold"
                                            title="{{ $watcher->name }}">
                                            {{ strtoupper(substr($watcher->name, 0, 1)) }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                </div>

            </div>
        </div>

        <!-- Image Lightbox Modal -->
        <div x-show="lightboxOpen"
            x-cloak
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 md:p-10 bg-gray-950/85 backdrop-blur-md cursor-pointer select-none"
            @click="closeLightbox()"
            role="dialog"
            aria-modal="true">

            <!-- Floating Modern Close Hint (Top Center) -->
            <div class="fixed top-5 inset-x-0 flex justify-center pointer-events-none z-10">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-medium bg-white/10 text-gray-300 border border-white/15 backdrop-blur-md shadow-lg">
                    <x-heroicon-m-x-mark class="w-3.5 h-3.5 text-gray-400" />
                    <span>{{ __('Click anywhere outside the card to close') }}</span>
                    <kbd class="ml-1 px-1.5 py-0.5 text-[9px] font-semibold bg-white/10 rounded text-gray-200 border border-white/20">Esc</kbd>
                </span>
            </div>

            <!-- Modal Content Card (The bounded area: clicking inside will NOT close) -->
            <div class="relative max-w-4xl w-auto max-h-[88vh] flex flex-col bg-gray-900/95 rounded-2xl border border-white/20 shadow-2xl ring-1 ring-black/40 overflow-hidden cursor-default"
                @click.stop>

                <!-- Top Header Bar inside the card boundary -->
                <div class="flex items-center justify-between gap-4 px-4 py-3 bg-gray-900 border-b border-white/10 text-white shrink-0">
                    <div class="flex items-center gap-2 min-w-0">
                        <x-heroicon-m-photo class="w-4 h-4 text-brand-400 shrink-0" />
                        <span class="text-xs sm:text-sm font-semibold truncate text-gray-200" x-text="lightboxTitle"></span>
                    </div>

                    <div class="flex items-center gap-2 shrink-0">
                        <!-- Open in New Tab -->
                        <a :href="lightboxSrc" target="_blank"
                            title="{{ __('Open original in new tab') }}"
                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-medium text-gray-300 hover:text-white bg-white/5 hover:bg-white/10 transition-colors border border-white/10">
                            <x-heroicon-m-arrow-top-right-on-square class="w-3.5 h-3.5" />
                            <span class="hidden sm:inline">{{ __('Open') }}</span>
                        </a>

                        <!-- Close Button -->
                        <button type="button"
                            @click="closeLightbox()"
                            title="{{ __('Close preview') }}"
                            class="p-1.5 rounded-lg text-gray-300 hover:text-white hover:bg-rose-500/80 transition-colors border border-white/10">
                            <x-heroicon-m-x-mark class="w-4 h-4" />
                        </button>
                    </div>
                </div>

                <!-- Inner Image Display Area -->
                <div class="p-3 bg-black/70 flex items-center justify-center overflow-auto max-h-[78vh]">
                    <img :src="lightboxSrc"
                        :alt="lightboxTitle"
                        class="max-h-[74vh] max-w-full w-auto object-contain rounded-lg shadow-md select-none">
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
