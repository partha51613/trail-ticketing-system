<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm text-gray-500">
            <a href="{{ route('projects.index') }}" class="hover:text-gray-900 transition-colors flex items-center gap-1">
                <x-heroicon-m-folder class="w-4 h-4 text-gray-400" />
                <span>{{ __('Projects') }}</span>
            </a>
            <x-heroicon-m-chevron-right class="w-3.5 h-3.5 text-gray-300" />
            <a href="{{ route('projects.show', $project) }}" class="hover:text-gray-900 transition-colors truncate max-w-xs">
                {{ $project->name }}
            </a>
            <x-heroicon-m-chevron-right class="w-3.5 h-3.5 text-gray-300" />
            <span class="font-medium text-gray-900">{{ __('Edit') }}</span>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl border border-gray-200/80 shadow-xs overflow-hidden">
                <div class="p-6 sm:p-8">
                    <div class="mb-6 flex items-start justify-between">
                        <div>
                            <h2 class="text-xl font-bold tracking-tight text-gray-900">{{ __('Edit project') }}</h2>
                            <p class="text-sm text-gray-500 mt-1">
                                {{ __('Update details and manager assignments for this project.') }}
                            </p>
                        </div>
                        @can('delete', $project)
                            <button type="button"
                                onclick="if(confirm('Are you sure you want to delete this project? Existing tickets will be preserved.')) { document.getElementById('delete-project-form').submit(); }"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-red-600 hover:text-red-700 hover:bg-red-50 transition-colors"
                                title="{{ __('Delete Project') }}">
                                <x-heroicon-m-trash class="w-3.5 h-3.5" />
                                <span>{{ __('Delete') }}</span>
                            </button>
                        @endcan
                    </div>

                    <form method="POST" action="{{ route('projects.update', $project) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Project Name -->
                        <div>
                            <x-input-label for="name" :value="__('Project Name')" class="text-xs font-semibold uppercase tracking-wider text-gray-600" />
                            <x-text-input id="name" name="name" type="text"
                                class="mt-1.5 block w-full text-sm rounded-lg border-gray-300 shadow-2xs focus:border-gray-900 focus:ring-gray-900"
                                :value="old('name', $project->name)" required autofocus />
                            <x-input-error class="mt-1.5" :messages="$errors->get('name')" />
                        </div>

                        <!-- Description -->
                        <div>
                            <x-input-label for="description" :value="__('Description')" class="text-xs font-semibold uppercase tracking-wider text-gray-600" />
                            <textarea id="description" name="description" rows="3"
                                class="mt-1.5 block w-full rounded-lg border-gray-300 shadow-2xs focus:border-gray-900 focus:ring-gray-900 text-sm">{{ old('description', $project->description) }}</textarea>
                            <x-input-error class="mt-1.5" :messages="$errors->get('description')" />
                        </div>

                        <!-- Project Managers Selection -->
                        <div class="pt-2">
                            <x-input-label :value="__('Assign Project Managers')" class="text-xs font-semibold uppercase tracking-wider text-gray-600" />
                            <p class="text-xs text-gray-400 mt-0.5 mb-3">
                                {{ __('Managers assigned here receive management permissions for tickets within this project.') }}
                            </p>

                            @if ($managers->isEmpty())
                                <div class="rounded-lg bg-gray-50 border border-gray-200 p-4 text-center">
                                    <p class="text-xs text-gray-500 italic">
                                        {{ __('No users with the "manager" role exist.') }}
                                    </p>
                                </div>
                            @else
                                @php
                                    $assignedManagerIds = old('manager_ids', $project->managers->pluck('id')->all());
                                @endphp
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 max-h-56 overflow-y-auto border border-gray-200 rounded-lg p-2.5 bg-gray-50/50">
                                    @foreach ($managers as $manager)
                                        <label class="flex items-center gap-3 p-2.5 rounded-md bg-white border border-gray-200/60 hover:border-gray-300 cursor-pointer transition-colors shadow-2xs">
                                            <input type="checkbox" name="manager_ids[]" value="{{ $manager->id }}"
                                                class="rounded border-gray-300 text-gray-900 shadow-2xs focus:ring-gray-900"
                                                @checked(is_array($assignedManagerIds) && in_array($manager->id, $assignedManagerIds))>
                                            <div class="min-w-0 flex-1">
                                                <div class="text-sm font-medium text-gray-900 truncate">{{ $manager->name }}</div>
                                                <div class="text-xs text-gray-400 truncate">{{ $manager->email }}</div>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            @endif
                            <x-input-error class="mt-1.5" :messages="$errors->get('manager_ids')" />
                        </div>

                        <!-- Actions -->
                        <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-3">
                            <a href="{{ route('projects.show', $project) }}"
                                class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors">
                                {{ __('Cancel') }}
                            </a>
                            <button type="submit"
                                class="inline-flex items-center justify-center gap-x-2 px-4 py-2 text-sm font-semibold text-white bg-gray-900 rounded-lg hover:bg-gray-800 shadow-sm focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gray-900 transition-colors">
                                <x-heroicon-m-check class="w-4 h-4" />
                                <span>{{ __('Save Changes') }}</span>
                            </button>
                        </div>
                    </form>

                    @can('delete', $project)
                        <form id="delete-project-form" method="POST" action="{{ route('projects.destroy', $project) }}" class="hidden">
                            @csrf
                            @method('DELETE')
                        </form>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
