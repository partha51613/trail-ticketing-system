<!-- Modern Project Confirmation Modal (Custom Theme Replacement for Browser Alerts) -->
<div x-data="{
    open: false,
    title: '',
    message: '',
    confirmText: 'Confirm',
    cancelText: 'Cancel',
    isDanger: true,
    formToSubmit: null,
    callback: null,
    showConfirm(options) {
        this.title = options.title || '{{ __('Confirm Action') }}';
        this.message = options.message || '{{ __('Are you sure you want to proceed?') }}';
        this.confirmText = options.confirmText || '{{ __('Confirm') }}';
        this.cancelText = options.cancelText || '{{ __('Cancel') }}';
        this.isDanger = options.isDanger !== undefined ? options.isDanger : true;
        this.formToSubmit = options.form || null;
        this.callback = options.onConfirm || null;
        this.open = true;
    },
    confirmAction() {
        this.open = false;
        if (this.formToSubmit) {
            if (typeof this.formToSubmit === 'string') {
                const el = document.getElementById(this.formToSubmit);
                if (el) el.submit();
            } else if (this.formToSubmit instanceof HTMLFormElement) {
                this.formToSubmit.submit();
            }
        } else if (this.callback) {
            this.callback();
        }
    }
}"
@open-confirm-modal.window="showConfirm($event.detail)"
x-show="open"
x-cloak
x-transition:enter="transition ease-out duration-200"
x-transition:enter-start="opacity-0"
x-transition:enter-end="opacity-100"
x-transition:leave="transition ease-in duration-150"
x-transition:leave-start="opacity-100"
x-transition:leave-end="opacity-0"
class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-950/60 backdrop-blur-xs"
@click.self="open = false"
@keydown.escape.window="open = false"
role="dialog"
aria-modal="true">

    <div class="relative bg-white rounded-2xl max-w-sm w-full p-6 shadow-2xl border border-gray-100 text-center space-y-4"
        @click.stop>
        
        <!-- Icon -->
        <div class="w-12 h-12 rounded-full flex items-center justify-center mx-auto"
            :class="isDanger ? 'bg-rose-50 text-rose-600 border border-rose-100' : 'bg-brand-50 text-brand-600 border border-brand-100'">
            <template x-if="isDanger">
                <x-heroicon-o-exclamation-triangle class="w-6 h-6" />
            </template>
            <template x-if="!isDanger">
                <x-heroicon-o-question-mark-circle class="w-6 h-6" />
            </template>
        </div>

        <!-- Copy -->
        <div>
            <h4 class="text-base font-bold text-gray-900 tracking-tight" x-text="title"></h4>
            <p class="text-xs text-gray-500 mt-1.5 leading-relaxed" x-text="message"></p>
        </div>

        <!-- Buttons -->
        <div class="flex items-center gap-2.5 pt-1">
            <button type="button" @click="open = false"
                class="flex-1 py-2 px-3.5 text-xs font-semibold rounded-xl text-gray-700 bg-white border border-gray-200/90 hover:bg-gray-50 hover:border-gray-300 transition-all shadow-2xs"
                x-text="cancelText">
            </button>
            <button type="button" @click="confirmAction()"
                class="flex-1 py-2 px-3.5 text-xs font-semibold rounded-xl text-white transition-all shadow-xs"
                :class="isDanger ? 'bg-rose-600 hover:bg-rose-700 shadow-rose-600/20' : 'bg-brand-500 hover:bg-brand-600 shadow-brand-500/20'"
                x-text="confirmText">
            </button>
        </div>
    </div>
</div>
