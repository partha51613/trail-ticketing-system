@props([
    'name' => 'attachments[]',
    'maxFiles' => 10,
    'maxSizeMb' => 10,
    'accept' => 'image/png,image/jpeg,image/webp,image/gif,application/pdf,.doc,.docx,.txt,.csv,.log,.zip',
])

<div>
    <!-- Hidden actual file input submitted with the form -->
    <input type="file" name="{{ $name }}" x-ref="actualFileInput" multiple class="hidden">

    <!-- Queued File Previews (Thumbnails & File Badges with Remove 'X') -->
    <div x-show="selectedFiles.length > 0" x-cloak class="pt-1">
        <div class="flex flex-wrap gap-2.5">
            <template x-for="(item, index) in selectedFiles" :key="index">
                <div
                    class="group relative rounded-xl border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-1.5 shadow-2xs hover:border-gray-300 dark:hover:border-slate-600 transition-all flex items-center gap-2">
                    <!-- Image Thumbnail -->
                    <template x-if="item.isImage">
                        <img :src="item.previewUrl"
                            class="w-12 h-12 rounded-lg object-cover bg-gray-50 dark:bg-slate-900 border border-gray-100 dark:border-slate-700 shrink-0">
                    </template>

                    <!-- Document Icon for Non-Images -->
                    <template x-if="!item.isImage">
                        <div
                            class="w-12 h-12 rounded-lg bg-gray-50 dark:bg-slate-900 border border-gray-100 dark:border-slate-700 flex flex-col items-center justify-center shrink-0 text-brand-600 dark:text-brand-400">
                            <x-heroicon-m-document class="w-5 h-5" />
                            <span class="text-[9px] font-semibold uppercase tracking-wider text-gray-500 dark:text-slate-400"
                                x-text="item.name.split('.').pop() || 'FILE'"></span>
                        </div>
                    </template>

                    <!-- Name & Size -->
                    <div class="pr-6 min-w-0 max-w-[140px]">
                        <p class="text-xs font-medium text-gray-800 dark:text-slate-200 truncate"
                            :title="item.name" x-text="item.name"></p>
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

    <!-- Error Message -->
    <div x-show="errorMessage" x-cloak class="text-xs font-medium text-rose-600 dark:text-rose-400 mt-2">
        <span x-text="errorMessage"></span>
    </div>

    <!-- Attachment Trigger Button Row -->
    <div class="flex items-center gap-2.5 pt-2">
        <label
            class="cursor-pointer inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-medium text-gray-700 dark:text-slate-300 hover:bg-gray-50 dark:hover:bg-slate-750 hover:border-gray-300 dark:hover:border-slate-600 transition-all shadow-2xs">
            <x-heroicon-m-paper-clip class="w-4 h-4 text-gray-500 dark:text-slate-400" />
            <span>{{ __('Attach Files') }}</span>
            <input type="file" multiple class="hidden" @change="handleFileSelect($event)"
                accept="{{ $accept }}">
        </label>

        <span x-show="selectedFiles.length > 0" x-cloak
            class="text-xs font-semibold px-2 py-0.5 rounded-full bg-brand-50 dark:bg-brand-950/50 text-brand-700 dark:text-brand-300 border border-brand-200 dark:border-brand-800/50">
            <span x-text="selectedFiles.length"></span>/{{ $maxFiles }} {{ __('files') }}
        </span>
        <span class="text-[11px] text-gray-400 dark:text-slate-500">
            {{ __('Up to :max files (max :sizeMB each: images, PDF, docs, logs, zip)', ['max' => $maxFiles, 'size' => $maxSizeMb]) }}
        </span>
    </div>

    <!-- Oversized File Modal Dialog -->
    <div x-show="sizeErrorModal" x-cloak x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-950/60 backdrop-blur-xs"
        @click.self="sizeErrorModal = false" @keydown.escape.window="sizeErrorModal = false"
        role="alertdialog" aria-modal="true">

        <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-sm w-full p-6 shadow-2xl border border-gray-100 dark:border-slate-800 text-center space-y-4"
            @click.stop>
            <div
                class="w-12 h-12 rounded-full bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 flex items-center justify-center mx-auto border border-rose-100 dark:border-rose-800/40">
                <x-heroicon-o-exclamation-triangle class="w-6 h-6" />
            </div>

            <div>
                <h4 class="text-base font-bold text-gray-900 dark:text-white">{{ __('File Too Large') }}</h4>
                <p class="text-xs text-gray-500 dark:text-slate-400 mt-1">
                    {{ __('The maximum allowed file size is') }} <strong class="text-gray-800 dark:text-slate-200">{{ $maxSizeMb }} MB</strong>.
                </p>
                <div
                    class="mt-3 p-2.5 rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200/80 dark:border-slate-700 text-left">
                    <p class="text-xs font-semibold text-gray-800 dark:text-slate-200 truncate"
                        x-text="oversizedFileName"></p>
                    <p class="text-[11px] text-rose-600 dark:text-rose-400 font-medium mt-0.5">
                        {{ __('Size:') }} <span x-text="oversizedFileSize"></span> ({{ __('exceeds limit') }})
                    </p>
                </div>
            </div>

            <button type="button" @click="sizeErrorModal = false"
                class="w-full py-2.5 px-4 text-xs font-semibold rounded-xl text-white bg-gray-900 dark:bg-brand-500 hover:bg-gray-800 dark:hover:bg-brand-600 transition-colors shadow-xs">
                {{ __('Got it') }}
            </button>
        </div>
    </div>
</div>
