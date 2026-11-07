<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm text-gray-500">
            <a href="{{ route('tickets.index') }}" class="hover:text-gray-900 transition-colors flex items-center gap-1">
                <x-heroicon-m-ticket class="w-4 h-4 text-gray-400" />
                <span>{{ __('Tickets') }}</span>
            </a>
            <x-heroicon-m-chevron-right class="w-3.5 h-3.5 text-gray-300" />
            <a href="{{ route('tickets.show', $ticket) }}"
                class="hover:text-gray-900 transition-colors truncate max-w-xs font-mono font-bold text-gray-700">
                {{ $ticket->reference_code }}
            </a>
            <x-heroicon-m-chevron-right class="w-3.5 h-3.5 text-gray-300" />
            <span class="font-medium text-gray-900">{{ __('Edit') }}</span>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-xs overflow-hidden">
                <div class="p-6 sm:p-8">
                    <!-- Heading & Delete Action -->
                    <div class="mb-6 flex items-start justify-between gap-4">
                        <div>
                            <div class="flex items-center gap-2.5">
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-mono font-bold bg-amber-50 text-amber-800 border border-amber-200/80">
                                    {{ $ticket->reference_code }}
                                </span>
                                <h2 class="text-xl font-bold tracking-tight text-gray-900">
                                    {{ __('Edit ticket details') }}</h2>
                            </div>
                            <p class="text-sm text-gray-500 mt-1">
                                {{ __('Update metadata, task scope, or assignment details.') }}
                            </p>
                        </div>

                        @can('delete', $ticket)
                            <button type="button"
                                @click="$dispatch('open-confirm-modal', {
                                    title: '{{ __('Delete Ticket?') }}',
                                    message: '{{ __('Are you sure you want to delete this ticket? It will be soft-deleted and preserved for audit.') }}',
                                    confirmText: '{{ __('Delete') }}',
                                    isDanger: true,
                                    form: 'delete-ticket-form'
                                })"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold text-rose-600 hover:text-rose-700 hover:bg-rose-50 transition-colors shrink-0">
                                <x-heroicon-m-trash class="w-4 h-4" />
                                <span>{{ __('Delete') }}</span>
                            </button>
                        @endcan
                    </div>

                    <form method="POST" action="{{ route('tickets.update', $ticket) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Project & Assignee Row -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <!-- Project Selector -->
                            <div>
                                <x-input-label for="project_id" :value="__('Project')"
                                    class="text-xs font-semibold uppercase tracking-wider text-gray-600" />
                                <select id="project_id" name="project_id" required
                                    class="mt-1.5 block w-full text-sm rounded-xl border-gray-200/90 shadow-xs focus:border-brand-500 focus:ring-brand-500 bg-white">
                                    @foreach ($projects as $project)
                                        <option value="{{ $project->id }}" @selected(old('project_id', $ticket->project_id) == $project->id)>
                                            {{ $project->name }} ({{ $project->code }})
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error class="mt-1.5" :messages="$errors->get('project_id')" />
                            </div>

                            <!-- Assignee Selector -->
                            <div>
                                <x-input-label for="assigned_to_id" :value="__('Assignee (Optional)')"
                                    class="text-xs font-semibold uppercase tracking-wider text-gray-600" />
                                <select id="assigned_to_id" name="assigned_to_id"
                                    class="mt-1.5 block w-full text-sm rounded-xl border-gray-200/90 shadow-xs focus:border-brand-500 focus:ring-brand-500 bg-white">
                                    <option value="">{{ __('Unassigned') }}</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}" @selected(old('assigned_to_id', $ticket->assigned_to_id) == $user->id)>
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
                                class="text-xs font-semibold uppercase tracking-wider text-gray-600" />
                            <x-text-input id="title" name="title" type="text"
                                class="mt-1.5 block w-full text-sm rounded-xl border-gray-200/90 shadow-xs focus:border-brand-500 focus:ring-brand-500"
                                :value="old('title', $ticket->title)" required autofocus />
                            <x-input-error class="mt-1.5" :messages="$errors->get('title')" />
                        </div>

                        <!-- Priority & Due Date Row -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <!-- Priority -->
                            <div>
                                <x-input-label for="priority" :value="__('Priority')"
                                    class="text-xs font-semibold uppercase tracking-wider text-gray-600" />
                                <select id="priority" name="priority" required
                                    class="mt-1.5 block w-full text-sm rounded-xl border-gray-200/90 shadow-xs focus:border-brand-500 focus:ring-brand-500 bg-white">
                                    <option value="low" @selected(old('priority', $ticket->priority) === 'low')>{{ __('Low') }}</option>
                                    <option value="medium" @selected(old('priority', $ticket->priority) === 'medium')>{{ __('Medium') }}</option>
                                    <option value="high" @selected(old('priority', $ticket->priority) === 'high')>{{ __('High') }}</option>
                                    <option value="urgent" @selected(old('priority', $ticket->priority) === 'urgent')>{{ __('Urgent') }}</option>
                                </select>
                                <x-input-error class="mt-1.5" :messages="$errors->get('priority')" />
                            </div>

                            <!-- Due Date -->
                            <div>
                                <div class="flex items-center justify-between">
                                    <x-input-label for="due_date" :value="__('Due Date (Optional)')"
                                        class="text-xs font-semibold uppercase tracking-wider text-gray-600" />
                                    <span
                                        class="text-[10px] text-gray-400 font-medium">{{ __('End of day IST') }}</span>
                                </div>
                                <x-text-input id="due_date" name="due_date" type="date"
                                    class="mt-1.5 block w-full text-sm rounded-xl border-gray-200/90 shadow-xs focus:border-brand-500 focus:ring-brand-500"
                                    :value="old('due_date', $ticket->due_date?->format('Y-m-d'))" />
                                <x-input-error class="mt-1.5" :messages="$errors->get('due_date')" />
                            </div>
                        </div>

                        <!-- Description -->
                        <div>
                            <x-input-label for="description" :value="__('Description')"
                                class="text-xs font-semibold uppercase tracking-wider text-gray-600" />
                            <textarea id="description" name="description" rows="5" required
                                class="mt-1.5 block w-full rounded-xl border-gray-200/90 shadow-xs focus:border-brand-500 focus:ring-brand-500 text-sm leading-relaxed">{{ old('description', $ticket->description) }}</textarea>
                            <x-input-error class="mt-1.5" :messages="$errors->get('description')" />
                        </div>

                        <!-- Actions -->
                        <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-3">
                            <a href="{{ route('tickets.show', $ticket) }}"
                                class="px-4 py-2 text-xs font-semibold text-gray-600 hover:text-gray-900 transition-colors">
                                {{ __('Cancel') }}
                            </a>
                            <button type="submit"
                                class="inline-flex items-center gap-1.5 px-5 py-2.5 text-xs font-semibold text-white bg-brand-500 rounded-xl hover:bg-brand-600 shadow-sm shadow-brand-500/20 transition-all">
                                <x-heroicon-m-check class="w-4 h-4" />
                                <span>{{ __('Save Changes') }}</span>
                            </button>
                        </div>
                    </form>

                    <!-- Hidden Delete Form -->
                    @can('delete', $ticket)
                        <form id="delete-ticket-form" action="{{ route('tickets.destroy', $ticket) }}" method="POST"
                            class="hidden">
                            @csrf
                            @method('DELETE')
                        </form>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
