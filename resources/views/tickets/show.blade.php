<x-app-layout>
    <x-slot name="header">
        @include('tickets.partials.header')
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
                    @include('tickets.partials.description')
                    @include('tickets.partials.timeline')
                </div>

                <!-- Right Column (Sidebar Meta & Actions - 1/3) -->
                @include('tickets.partials.sidebar')

            </div>
        </div>

        @include('tickets.partials.lightbox')
    </div>
</x-app-layout>
