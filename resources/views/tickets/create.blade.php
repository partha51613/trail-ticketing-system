<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm text-gray-500">
            <a href="{{ route('tickets.index') }}" class="hover:text-gray-900 transition-colors flex items-center gap-1">
                <x-heroicon-m-ticket class="w-4 h-4 text-gray-400" />
                <span>{{ __('Tickets') }}</span>
            </a>
            <x-heroicon-m-chevron-right class="w-3.5 h-3.5 text-gray-300" />
            <span class="font-medium text-gray-900">{{ __('Create Ticket') }}</span>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-xs overflow-hidden">
                <div class="p-6 sm:p-8">
                    <!-- Heading -->
                    <div class="mb-6">
                        <h2 class="text-xl font-bold tracking-tight text-gray-900">{{ __('Create new ticket') }}</h2>
                        <p class="text-sm text-gray-500 mt-1">
                            {{ __('Fill in the ticket details to track and assign the issue.') }}
                        </p>
                    </div>

                    <form method="POST" action="{{ route('tickets.store') }}" class="space-y-6">
                        @csrf

                        <!-- Project & Assignee Row -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <!-- Project Selector -->
                            <div>
                                <x-input-label for="project_id" :value="__('Project')"
                                    class="text-xs font-semibold uppercase tracking-wider text-gray-600" />
                                <select id="project_id" name="project_id" required
                                    class="mt-1.5 block w-full text-sm rounded-xl border-gray-200/90 shadow-xs focus:border-brand-500 focus:ring-brand-500 bg-white">
                                    <option value="" disabled
                                        {{ !old('project_id', request('project_id')) ? 'selected' : '' }}>
                                        {{ __('Select a project...') }}
                                    </option>
                                    @foreach ($projects as $project)
                                        <option value="{{ $project->id }}" @selected(old('project_id', request('project_id')) == $project->id)>
                                            {{ $project->name }} ({{ $project->code }})
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error class="mt-1.5" :messages="$errors->get('project_id')" />
                            </div>

                            <!-- Assignee Selector (Optional) -->
                            <div>
                                <x-input-label for="assigned_to_id" :value="__('Assignee (Optional)')"
                                    class="text-xs font-semibold uppercase tracking-wider text-gray-600" />
                                <select id="assigned_to_id" name="assigned_to_id"
                                    class="mt-1.5 block w-full text-sm rounded-xl border-gray-200/90 shadow-xs focus:border-brand-500 focus:ring-brand-500 bg-white">
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
                                class="text-xs font-semibold uppercase tracking-wider text-gray-600" />
                            <x-text-input id="title" name="title" type="text"
                                class="mt-1.5 block w-full text-sm rounded-xl border-gray-200/90 shadow-xs focus:border-brand-500 focus:ring-brand-500"
                                :value="old('title')" required autofocus
                                placeholder="e.g. Fix SSL renewal failure on staging" />
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
                                        class="text-xs font-semibold uppercase tracking-wider text-gray-600" />
                                    <span
                                        class="text-[10px] text-gray-400 font-medium">{{ __('End of day IST') }}</span>
                                </div>
                                <x-text-input id="due_date" name="due_date" type="date"
                                    class="mt-1.5 block w-full text-sm rounded-xl border-gray-200/90 shadow-xs focus:border-brand-500 focus:ring-brand-500"
                                    :value="old('due_date')" />
                                <x-input-error class="mt-1.5" :messages="$errors->get('due_date')" />
                            </div>
                        </div>

                        <!-- Description -->
                        <div>
                            <x-input-label for="description" :value="__('Description')"
                                class="text-xs font-semibold uppercase tracking-wider text-gray-600" />
                            <textarea id="description" name="description" rows="5" required
                                class="mt-1.5 block w-full rounded-xl border-gray-200/90 shadow-xs focus:border-brand-500 focus:ring-brand-500 text-sm leading-relaxed"
                                placeholder="Describe the steps to reproduce, expected vs actual behavior, or task scope...">{{ old('description') }}</textarea>
                            <x-input-error class="mt-1.5" :messages="$errors->get('description')" />
                        </div>

                        <!-- Actions -->
                        <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-3">
                            <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('tickets.index') }}"
                                class="px-4 py-2 text-xs font-semibold text-gray-600 hover:text-gray-900 transition-colors">
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
