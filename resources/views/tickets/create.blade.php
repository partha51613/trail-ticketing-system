<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-slate-400">
            <a href="{{ route('tickets.index') }}" class="hover:text-gray-900 dark:hover:text-white transition-colors flex items-center gap-1">
                <x-heroicon-m-ticket class="w-4 h-4 text-gray-400 dark:text-slate-500" />
                <span>{{ __('Tickets') }}</span>
            </a>
            <x-heroicon-m-chevron-right class="w-3.5 h-3.5 text-gray-300 dark:text-slate-600" />
            <span class="font-medium text-gray-900 dark:text-white">{{ __('Create Ticket') }}</span>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-slate-800 shadow-xs overflow-hidden">
                <div class="p-6 sm:p-8">
                    <!-- Heading -->
                    <div class="mb-6">
                        <h2 class="text-xl font-bold tracking-tight text-gray-900 dark:text-white">{{ __('Create new ticket') }}</h2>
                        <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">
                            {{ __('Fill in the ticket details to track and assign the issue.') }}
                        </p>
                    </div>

                    <form method="POST" action="{{ route('tickets.store') }}" enctype="multipart/form-data"
                        class="space-y-6" x-data="{
                            selectedFiles: [],
                            maxFiles: 10,
                            maxFileSize: 10 * 1024 * 1024,
                            sizeErrorModal: false,
                            oversizedFileName: '',
                            oversizedFileSize: '',
                            errorMessage: '',
                            handleFileSelect(event) {
                                const newFiles = Array.from(event.target.files);
                                this.errorMessage = '';
                        
                                for (let file of newFiles) {
                                    if (file.size > this.maxFileSize) {
                                        this.oversizedFileName = file.name;
                                        this.oversizedFileSize = this.formatBytes(file.size);
                                        this.sizeErrorModal = true;
                                        continue;
                                    }
                        
                                    if (this.selectedFiles.length >= this.maxFiles) {
                                        this.errorMessage = 'You can attach up to ' + this.maxFiles + ' files.';
                                        break;
                                    }
                        
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
                        @csrf

                        <!-- Hidden File Input submitted with Form -->
                        <input type="file" name="attachments[]" x-ref="actualFileInput" multiple class="hidden">

                        <!-- Project & Assignee Row -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <!-- Project Selector -->
                            <div>
                                <x-input-label for="project_id" :value="__('Project')"
                                    class="text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-slate-300" />
                                <select id="project_id" name="project_id" required
                                    class="mt-1.5 block w-full text-sm rounded-xl border-gray-200/90 dark:border-slate-700 shadow-xs focus:border-brand-500 focus:ring-brand-500 bg-white dark:bg-slate-950 text-gray-900 dark:text-white">
                                    <option value="" disabled
                                        {{ !old('project_id', request('project_id')) ? 'selected' : '' }}>
                                        {{ __('Select a project...') }}
                                    </option>
                                    @foreach ($projects as $project)
                                        <option value="{{ $project->id }}" @selected(old('project_id', request('project_id')) == $project->id)>
                                            {{ $project->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error class="mt-1.5" :messages="$errors->get('project_id')" />
                            </div>

                            <!-- Assignee Selector (Optional) -->
                            <div>
                                <x-input-label for="assigned_to_id" :value="__('Assignee (Optional)')"
                                    class="text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-slate-300" />
                                <select id="assigned_to_id" name="assigned_to_id"
                                    class="mt-1.5 block w-full text-sm rounded-xl border-gray-200/90 dark:border-slate-700 shadow-xs focus:border-brand-500 focus:ring-brand-500 bg-white dark:bg-slate-950 text-gray-900 dark:text-white">
                                    <option value="">{{ __('Unassigned') }}</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}" @selected(old('assigned_to_id') == $user->id)>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error class="mt-1.5" :messages="$errors->get('assigned_to_id')" />
                            </div>
                        </div>

                        <!-- Ticket Title -->
                        <div>
                            <x-input-label for="title" :value="__('Ticket Title')"
                                class="text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-slate-300" />
                            <x-text-input id="title" name="title" type="text"
                                class="mt-1.5 block w-full text-sm rounded-xl border-gray-200/90 dark:border-slate-700 bg-white dark:bg-slate-950 text-gray-900 dark:text-white dark:placeholder-slate-500 shadow-xs focus:border-brand-500 focus:ring-brand-500"
                                :value="old('title')" required autofocus
                                placeholder="e.g. Fix SSL renewal failure on staging" />
                            <x-input-error class="mt-1.5" :messages="$errors->get('title')" />
                        </div>

                        <!-- Priority & Due Date Row -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <!-- Priority -->
                            <div>
                                <x-input-label for="priority" :value="__('Priority')"
                                    class="text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-slate-300" />
                                <select id="priority" name="priority" required
                                    class="mt-1.5 block w-full text-sm rounded-xl border-gray-200/90 dark:border-slate-700 shadow-xs focus:border-brand-500 focus:ring-brand-500 bg-white dark:bg-slate-950 text-gray-900 dark:text-white">
                                    <option value="low" @selected(old('priority') === 'low')>{{ __('Low') }}</option>
                                    <option value="medium" @selected(old('priority', 'medium') === 'medium')>{{ __('Medium') }}</option>
                                    <option value="high" @selected(old('priority') === 'high')>{{ __('High') }}</option>
                                    <option value="urgent" @selected(old('priority') === 'urgent')>{{ __('Urgent') }}</option>
                                </select>
                                <x-input-error class="mt-1.5" :messages="$errors->get('priority')" />
                            </div>

                            <!-- Due Date -->
                            <div>
                                <div class="flex items-center justify-between">
                                    <x-input-label for="due_date" :value="__('Due Date (Optional)')"
                                        class="text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-slate-300" />
                                    <span
                                        class="text-[10px] text-gray-400 dark:text-slate-500 font-medium">{{ __('End of day IST') }}</span>
                                </div>
                                <x-text-input id="due_date" name="due_date" type="date"
                                    class="mt-1.5 block w-full text-sm rounded-xl border-gray-200/90 dark:border-slate-700 bg-white dark:bg-slate-950 text-gray-900 dark:text-white dark:[color-scheme:dark] shadow-xs focus:border-brand-500 focus:ring-brand-500"
                                    :value="old('due_date')" />
                                <x-input-error class="mt-1.5" :messages="$errors->get('due_date')" />
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="space-y-3">
                            <x-input-label for="description" :value="__('Description')"
                                class="text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-slate-300" />
                            <textarea id="description" name="description" rows="5" required
                                class="mt-1.5 block w-full rounded-xl border-gray-200/90 dark:border-slate-700 bg-white dark:bg-slate-950 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-slate-500 shadow-xs focus:border-brand-500 focus:ring-brand-500 text-sm leading-relaxed"
                                placeholder="Describe the steps to reproduce, expected vs actual behavior, or task scope...">{{ old('description') }}</textarea>
                            <x-input-error class="mt-1.5" :messages="$errors->get('description')" />

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
                                                    <span
                                                        class="text-[9px] font-semibold uppercase tracking-wider text-gray-500 dark:text-slate-400"
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
                            <div x-show="errorMessage" x-cloak class="text-xs font-medium text-rose-600 dark:text-rose-400">
                                <span x-text="errorMessage"></span>
                            </div>

                            <!-- Attachment Trigger Button -->
                            <div class="flex items-center gap-2.5 pt-0.5">
                                <label
                                    class="cursor-pointer inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-medium text-gray-700 dark:text-slate-300 hover:bg-gray-50 dark:hover:bg-slate-750 hover:border-gray-300 dark:hover:border-slate-600 transition-all shadow-2xs">
                                    <x-heroicon-m-paper-clip class="w-4 h-4 text-gray-500 dark:text-slate-400" />
                                    <span>{{ __('Attach Files') }}</span>
                                    <input type="file" multiple class="hidden" @change="handleFileSelect($event)"
                                        accept="image/png,image/jpeg,image/webp,image/gif,application/pdf,.doc,.docx,.txt,.csv,.log,.zip">
                                </label>

                                <span x-show="selectedFiles.length > 0" x-cloak
                                    class="text-xs font-semibold px-2 py-0.5 rounded-full bg-brand-50 dark:bg-brand-950/50 text-brand-700 dark:text-brand-300 border border-brand-200 dark:border-brand-800/50">
                                    <span x-text="selectedFiles.length"></span>/10 {{ __('files') }}
                                </span>
                                <span class="text-[11px] text-gray-400 dark:text-slate-500">
                                    {{ __('Up to 10 files (max 10MB each: images, PDF, docs, logs, zip)') }}
                                </span>
                            </div>
                            <x-input-error class="mt-1.5" :messages="$errors->get('attachments')" />
                            <x-input-error class="mt-1.5" :messages="$errors->get('attachments.*')" />
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
                                        {{ __('The maximum allowed file size is') }} <strong class="text-gray-800 dark:text-slate-200">10
                                            MB</strong>.
                                    </p>
                                    <div class="mt-3 p-2.5 rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200/80 dark:border-slate-700 text-left">
                                        <p class="text-xs font-semibold text-gray-800 dark:text-slate-200 truncate"
                                            x-text="oversizedFileName"></p>
                                        <p class="text-[11px] text-rose-600 dark:text-rose-400 font-medium mt-0.5">
                                            {{ __('Size:') }} <span x-text="oversizedFileSize"></span>
                                            ({{ __('exceeds limit') }})
                                        </p>
                                    </div>
                                </div>

                                <button type="button" @click="sizeErrorModal = false"
                                    class="w-full py-2.5 px-4 text-xs font-semibold rounded-xl text-white bg-gray-900 dark:bg-brand-500 hover:bg-gray-800 dark:hover:bg-brand-600 transition-colors shadow-xs">
                                    {{ __('Got it') }}
                                </button>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="pt-4 border-t border-gray-100 dark:border-slate-800 flex items-center justify-end gap-3">
                            <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('tickets.index') }}"
                                class="px-4 py-2 text-xs font-semibold text-gray-600 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                                {{ __('Cancel') }}
                            </a>
                            <button type="submit"
                                class="inline-flex items-center gap-1.5 px-5 py-2.5 text-xs font-semibold text-white bg-brand-500 rounded-xl hover:bg-brand-600 shadow-sm shadow-brand-500/20 transition-all">
                                <x-heroicon-m-check class="w-4 h-4" />
                                <span>{{ __('Create Ticket') }}</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
