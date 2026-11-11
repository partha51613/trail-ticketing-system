use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;
use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\DB;
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-slate-400">
            <a href="{{ route('projects.index') }}" class="hover:text-gray-900 dark:hover:text-white transition-colors flex items-center gap-1">
                <x-heroicon-m-folder class="w-4 h-4 text-gray-400 dark:text-slate-500" />
                <span>{{ __('Projects') }}</span>
            </a>
            <x-heroicon-m-chevron-right class="w-3.5 h-3.5 text-gray-300 dark:text-slate-600" />
            <span class="font-medium text-gray-900 dark:text-white">{{ __('Create Project') }}</span>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-gray-200/80 dark:border-slate-800 shadow-xs overflow-hidden">
                <div class="p-6 sm:p-8">
                    <div class="mb-6">
                        <h2 class="text-xl font-bold tracking-tight text-gray-900 dark:text-white">{{ __('Create new project') }}</h2>
                        <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">
                            {{ __('Set up project information and assign team managers.') }}
                        </p>
                    </div>

                    <form method="POST" action="{{ route('projects.store') }}" class="space-y-6">
                        @csrf

                        <!-- Project Name & Project Code in a 2-Column Responsive Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                            <!-- Project Name (Takes 2/3 width) -->
                            <div class="sm:col-span-2">
                                <x-input-label for="name" :value="__('Project Name')"
                                    class="text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-slate-300" />
                                <x-text-input id="name" name="name" type="text"
                                    class="mt-1.5 block w-full text-sm rounded-xl border-gray-200/90 dark:border-slate-700 bg-white dark:bg-slate-950 text-gray-900 dark:text-white dark:placeholder-slate-500 shadow-xs focus:border-brand-500 focus:ring-brand-500"
                                    :value="old('name')" required autofocus placeholder="e.g. Website Redesign" />
                                <x-input-error class="mt-1.5" :messages="$errors->get('name')" />
                            </div>

                            <!-- Project Code (Takes 1/3 width) -->
                            <div>
                                <div class="flex items-center justify-between">
                                    <x-input-label for="code" :value="__('Code / Key')"
                                        class="text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-slate-300" />
                                    <span
                                        class="text-[10px] text-gray-400 dark:text-slate-500 uppercase font-medium">{{ __('2-6 chars') }}</span>
                                </div>
                                <x-text-input id="code" name="code" type="text" maxlength="6"
                                    class="mt-1.5 block w-full text-sm font-mono uppercase tracking-wider rounded-xl border-gray-200/90 dark:border-slate-700 bg-white dark:bg-slate-950 text-gray-900 dark:text-white dark:placeholder-slate-500 shadow-xs focus:border-brand-500 focus:ring-brand-500 placeholder:normal-case placeholder:tracking-normal"
                                    :value="old('code')" required placeholder="e.g. WEB" />
                                <x-input-error class="mt-1.5" :messages="$errors->get('code')" />
                            </div>
                        </div>

                        <!-- Description -->
                        <div>
                            <x-input-label for="description" :value="__('Description')"
                                class="text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-slate-300" />
                            <textarea id="description" name="description" rows="3"
                                class="mt-1.5 block w-full rounded-lg border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-slate-500 shadow-2xs focus:border-gray-900 dark:focus:border-brand-500 focus:ring-gray-900 dark:focus:ring-brand-500 text-sm"
                                placeholder="Brief summary of the goals and deliverables...">{{ old('description') }}</textarea>
                            <x-input-error class="mt-1.5" :messages="$errors->get('description')" />
                        </div>

                        <!-- Project Managers Selection -->
                        <div class="pt-2">
                            <x-input-label :value="__('Assign Project Managers')"
                                class="text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-slate-300" />
                            <p class="text-xs text-gray-400 dark:text-slate-500 mt-0.5 mb-3">
                                {{ __('Assigned managers can close, reassign, and manage tickets in this project.') }}
                            </p>

                            @if ($managers->isEmpty())
                                <div class="rounded-lg bg-gray-50 dark:bg-slate-800/60 border border-gray-200 dark:border-slate-700 p-4 text-center">
                                    <p class="text-xs text-gray-500 dark:text-slate-400 italic">
                                        {{ __('No users with the "manager" role currently exist.') }}
                                    </p>
                                </div>
                            @else
                                <div
                                    class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 max-h-56 overflow-y-auto border border-gray-200 dark:border-slate-800 rounded-lg p-2.5 bg-gray-50/50 dark:bg-slate-950/60">
                                    @foreach ($managers as $manager)
                                        <label
                                            class="flex items-center gap-3 p-2.5 rounded-md bg-white dark:bg-slate-900 border border-gray-200/60 dark:border-slate-800 hover:border-gray-300 dark:hover:border-slate-700 cursor-pointer transition-colors shadow-2xs">
                                            <input type="checkbox" name="manager_ids[]" value="{{ $manager->id }}"
                                                class="rounded border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-900 dark:text-brand-500 shadow-2xs focus:ring-gray-900 dark:focus:ring-brand-500"
                                                @checked(is_array(old('manager_ids')) && in_array($manager->id, old('manager_ids')))>
                                            <div class="min-w-0 flex-1">
                                                <div class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                                    {{ $manager->name }}</div>
                                                <div class="text-xs text-gray-400 dark:text-slate-400 truncate">{{ $manager->email }}</div>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            @endif
                            <x-input-error class="mt-1.5" :messages="$errors->get('manager_ids')" />
                        </div>

                        <!-- Actions -->
                        <div class="pt-4 border-t border-gray-100 dark:border-slate-800 flex items-center justify-end gap-3">
                            <a href="{{ route('projects.index') }}"
                                class="px-4 py-2 text-sm font-medium text-gray-600 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                                {{ __('Cancel') }}
                            </a>
                            <button type="submit"
                                class="inline-flex items-center justify-center gap-x-2 px-4 py-2 text-sm font-semibold text-white bg-gray-900 dark:bg-brand-500 rounded-lg hover:bg-gray-800 dark:hover:bg-brand-600 shadow-sm focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gray-900 dark:focus-visible:outline-brand-500 transition-colors">
                                <x-heroicon-m-check class="w-4 h-4" />
                                <span>{{ __('Create Project') }}</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
