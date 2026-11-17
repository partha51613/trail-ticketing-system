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
