<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-bold text-2xl text-gray-900 leading-tight">
                {{ __('Dashboard') }}
            </h2>
            <p class="text-sm text-gray-500 mt-1">Welcome back, {{ Auth::user()->name }}. Here is an overview of what needs your attention today.</p>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- 1. Interactive Metric Cards with Subtle Elevation -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Assigned To Me -->
                <a href="{{ route('tickets.assigned-to-me') }}" class="bg-white rounded-2xl border border-gray-200/80 p-6 shadow-xs hover:shadow-md hover:-translate-y-0.5 hover:border-indigo-300 transition-all duration-200 group">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Assigned to Me</p>
                            <h3 class="text-3xl font-extrabold text-gray-900 mt-2 group-hover:text-indigo-600 transition">{{ $assignedCount }}</h3>
                            <p class="text-xs text-gray-400 mt-1">Open tickets</p>
                        </div>
                        <div class="p-3.5 bg-indigo-50/80 text-indigo-600 rounded-2xl group-hover:bg-indigo-600 group-hover:text-white transition-all duration-200 shadow-2xs">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                    </div>
                </a>

                <!-- Urgent / Needs Attention -->
                <div class="bg-white rounded-2xl border border-gray-200/80 p-6 shadow-xs hover:shadow-md transition-all duration-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Urgent & High</p>
                            <h3 class="text-3xl font-extrabold text-red-600 mt-2">{{ $urgentCount }}</h3>
                            <p class="text-xs text-gray-400 mt-1">Needs swift resolution</p>
                        </div>
                        <div class="p-3.5 bg-red-50 text-red-600 rounded-2xl shadow-2xs">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Created By Me -->
                <a href="{{ route('tickets.created-by-me') }}" class="bg-white rounded-2xl border border-gray-200/80 p-6 shadow-xs hover:shadow-md hover:-translate-y-0.5 hover:border-emerald-300 transition-all duration-200 group">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Created By Me</p>
                            <h3 class="text-3xl font-extrabold text-gray-900 mt-2 group-hover:text-emerald-600 transition">{{ $createdCount }}</h3>
                            <p class="text-xs text-gray-400 mt-1">Reported issues</p>
                        </div>
                        <div class="p-3.5 bg-emerald-50 text-emerald-600 rounded-2xl group-hover:bg-emerald-600 group-hover:text-white transition-all duration-200 shadow-2xs">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                    </div>
                </a>

                <!-- Watched -->
                <a href="{{ route('tickets.watched') }}" class="bg-white rounded-2xl border border-gray-200/80 p-6 shadow-xs hover:shadow-md hover:-translate-y-0.5 hover:border-purple-300 transition-all duration-200 group">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Watched</p>
                            <h3 class="text-3xl font-extrabold text-gray-900 mt-2 group-hover:text-purple-600 transition">{{ $watchedCount }}</h3>
                            <p class="text-xs text-gray-400 mt-1">Subscribed tickets</p>
                        </div>
                        <div class="p-3.5 bg-purple-50 text-purple-600 rounded-2xl group-hover:bg-purple-600 group-hover:text-white transition-all duration-200 shadow-2xs">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Generous Breathing Space Between KPI Cards and Main Section -->
            <div class="mt-12"></div>

            <!-- 2. Main Two-Column Layout (Aligned Headers & Elevations) -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">

                <!-- Left 2 Cols: Tickets Needing Attention -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl shadow-xs border border-gray-200/80 overflow-hidden hover:shadow-md transition-all duration-200">

                        <!-- Header with Precise Height & Alignment Match -->
                        <div class="h-16 px-6 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                            <div class="flex items-center space-x-2.5">
                                <span class="h-2.5 w-2.5 rounded-full bg-amber-500 ring-4 ring-amber-100"></span>
                                <h3 class="text-base font-bold text-gray-900">Tickets Requiring Attention</h3>

                                <!-- Premium Tooltip mentioning the personalized criteria -->
                                <div class="relative group inline-flex items-center cursor-help">
                                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-gray-100 text-gray-400 group-hover:bg-indigo-50 group-hover:text-indigo-600 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </span>

                                    <!-- Tooltip Popover (Card UI matching project theme) -->
                                    <div class="absolute left-0 sm:left-1/2 sm:-translate-x-1/2 top-full mt-2.5 hidden group-hover:block w-80 bg-white border border-gray-200/90 rounded-2xl shadow-xl z-50 pointer-events-none transition-all duration-200 animate-in fade-in slide-in-from-top-1">
                                        <!-- Top Header Bar -->
                                        <div class="px-4 py-3 bg-gradient-to-r from-gray-50 to-indigo-50/30 border-b border-gray-100 rounded-t-2xl flex items-center justify-between">
                                            <div class="flex items-center space-x-2">
                                                <div class="w-2 h-2 rounded-full bg-indigo-500 ring-2 ring-indigo-100"></div>
                                                <span class="text-xs font-bold text-gray-900 tracking-tight">Display Criteria</span>
                                            </div>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-indigo-100/70 text-indigo-700 capitalize font-mono">
                                                {{ Auth::user()->role }} Scope
                                            </span>
                                        </div>

                                        <!-- Content Body -->
                                        <div class="p-4 space-y-2.5">
                                            <p class="text-[11px] text-gray-500 leading-normal">
                                                Tickets automatically prioritized for your workspace today:
                                            </p>

                                            <ul class="space-y-2 text-xs">
                                                @if(Auth::user()->role === 'admin')
                                                    <li class="flex items-start gap-2 text-gray-700">
                                                        <span class="text-indigo-500 font-bold shrink-0 mt-0.5">&bull;</span>
                                                        <span><strong class="text-gray-900">Urgent & High</strong> priority active tickets across all projects.</span>
                                                    </li>
                                                    <li class="flex items-start gap-2 text-gray-700">
                                                        <span class="text-indigo-500 font-bold shrink-0 mt-0.5">&bull;</span>
                                                        <span>Any open ticket requiring immediate admin triage.</span>
                                                    </li>
                                                @elseif(Auth::user()->role === 'manager')
                                                    <li class="flex items-start gap-2 text-gray-700">
                                                        <span class="text-indigo-500 font-bold shrink-0 mt-0.5">&bull;</span>
                                                        <span>All active tickets assigned to <strong class="text-gray-900">you</strong>.</span>
                                                    </li>
                                                    <li class="flex items-start gap-2 text-gray-700">
                                                        <span class="text-indigo-500 font-bold shrink-0 mt-0.5">&bull;</span>
                                                        <span><strong class="text-gray-900">Urgent & High</strong> priority tickets in projects you manage.</span>
                                                    </li>
                                                @else
                                                    <li class="flex items-start gap-2 text-gray-700">
                                                        <span class="text-indigo-500 font-bold shrink-0 mt-0.5">&bull;</span>
                                                        <span>Active tickets assigned directly to <strong class="text-gray-900">you</strong>.</span>
                                                    </li>
                                                @endif
                                                <li class="flex items-start gap-2 text-gray-500 text-[11px] pt-1 border-t border-gray-100">
                                                    <svg class="w-3.5 h-3.5 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12" />
                                                    </svg>
                                                    <span>Ranked by severity (Urgent &rarr; Low) then closest due date.</span>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <a href="{{ route('tickets.assigned-to-me') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 transition flex items-center group">
                                View all
                                <svg class="w-3.5 h-3.5 ml-1 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>

                        @if($attentionTickets->isEmpty())
                            <div class="py-16 text-center">
                                <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-emerald-50 text-emerald-600 mb-3">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <h4 class="text-sm font-semibold text-gray-800">All clear!</h4>
                                <p class="text-xs text-gray-500 mt-1">No urgent or assigned tickets pending right now.</p>
                            </div>
                        @else
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse">
                                    <thead>
                                        <tr class="border-b border-gray-100 text-[11px] font-bold uppercase tracking-wider text-gray-400 bg-gray-50/40">
                                            <th scope="col" class="py-3 pl-6 pr-3 w-28">{{ __('Ticket') }}</th>
                                            <th scope="col" class="py-3 px-3">{{ __('Title & Project') }}</th>
                                            <th scope="col" class="py-3 px-3 w-36 whitespace-nowrap">{{ __('Assignee') }}</th>
                                            <th scope="col" class="py-3 px-3 w-32 whitespace-nowrap">{{ __('Due Date') }}</th>
                                            <th scope="col" class="py-3 px-3 w-24 text-center">{{ __('Priority') }}</th>
                                            <th scope="col" class="py-3 pl-3 pr-6 w-36 text-right">{{ __('Status') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100 text-sm">
                                        @foreach($attentionTickets as $ticket)
                                            <tr x-data="{
                                                status: '{{ $ticket->status }}',
                                                isUpdating: false,
                                                isRemoved: false,
                                                open: false,
                                                async transition(newStatus, url) {
                                                    this.isUpdating = true;
                                                    this.open = false;
                                                    try {
                                                        const res = await fetch(url, {
                                                            method: 'PATCH',
                                                            headers: {
                                                                'Content-Type': 'application/json',
                                                                'Accept': 'application/json',
                                                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                                                            },
                                                            body: JSON.stringify({ status: newStatus })
                                                        });
                                                        const data = await res.json();
                                                        if (res.ok && data.success) {
                                                            this.status = newStatus;
                                                            if (newStatus === 'closed') {
                                                                setTimeout(() => { this.isRemoved = true; }, 400);
                                                            }
                                                        } else {
                                                            alert(data.message || 'Error updating status');
                                                        }
                                                    } catch (e) {
                                                        console.error(e);
                                                        alert('Failed to connect to server. Please try again.');
                                                    } finally {
                                                        this.isUpdating = false;
                                                    }
                                                },
                                                statusClasses() {
                                                    const map = {
                                                        'new': 'bg-blue-50 text-blue-700 border-blue-200/60',
                                                        'in_progress': 'bg-brand-50 text-brand-700 border-brand-200/60',
                                                        'waiting': 'bg-purple-50 text-purple-700 border-purple-200/60',
                                                        'blocked': 'bg-red-50 text-red-700 border-red-200/60',
                                                        'closed': 'bg-emerald-50 text-emerald-700 border-emerald-200/60'
                                                    };
                                                    return map[this.status] || 'bg-gray-50 text-gray-700 border-gray-200/60';
                                                },
                                                statusDot() {
                                                    const map = {
                                                        'new': 'bg-blue-500',
                                                        'in_progress': 'bg-brand-500',
                                                        'waiting': 'bg-purple-500',
                                                        'blocked': 'bg-red-500',
                                                        'closed': 'bg-emerald-500'
                                                    };
                                                    return map[this.status] || 'bg-gray-400';
                                                },
                                                statusLabel() {
                                                    return this.status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
                                                },
                                                allowedTransitions() {
                                                    const transitions = {
                                                        'new': ['in_progress', 'closed'],
                                                        'in_progress': ['waiting', 'blocked', 'closed'],
                                                        'waiting': ['in_progress', 'closed'],
                                                        'blocked': ['in_progress', 'closed'],
                                                        'closed': ['new', 'in_progress']
                                                    };
                                                    return transitions[this.status] || ['in_progress', 'closed'];
                                                }
                                            }"
                                            x-show="!isRemoved"
                                            x-transition:leave="transition ease-out duration-300"
                                            x-transition:leave-start="opacity-100 scale-100"
                                            x-transition:leave-end="opacity-0 scale-95"
                                            class="hover:bg-gray-50/70 transition-colors group">
                                                
                                                <!-- Ticket Code & Overdue Flag -->
                                                <td class="py-4 pl-6 pr-3 whitespace-nowrap align-middle">
                                                    <div class="flex items-center gap-1.5">
                                                        <a href="{{ route('tickets.show', $ticket) }}"
                                                            class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-mono font-semibold bg-gray-100 text-gray-700 border border-gray-200 hover:bg-gray-200/80 hover:text-gray-900 transition-colors shadow-2xs">
                                                            {{ $ticket->reference_code }}
                                                        </a>
                                                        @if($ticket->is_overdue)
                                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-red-100 text-red-700">
                                                                !
                                                            </span>
                                                        @endif
                                                    </div>
                                                </td>

                                                <!-- Title & Project Subtext -->
                                                <td class="py-4 px-3 max-w-xs sm:max-w-sm align-middle">
                                                    <a href="{{ route('tickets.show', $ticket) }}" class="font-semibold text-gray-900 hover:text-indigo-600 transition-colors line-clamp-1 block">
                                                        {{ preg_replace('/\s*#\d+$/', '', $ticket->title) }}
                                                    </a>
                                                    <span class="text-xs text-gray-400 font-medium">
                                                        in <strong class="text-gray-600 font-medium">{{ $ticket->project?->name ?? 'General' }}</strong>
                                                    </span>
                                                </td>

                                                <!-- Assignee Column -->
                                                <td class="py-4 px-3 whitespace-nowrap text-xs text-gray-700 align-middle">
                                                    <div class="flex items-center gap-2">
                                                        <span class="w-5 h-5 rounded-full bg-gray-100 text-gray-600 flex items-center justify-center text-[10px] font-bold">
                                                            {{ strtoupper(substr($ticket->assignedTo?->name ?? 'U', 0, 1)) }}
                                                        </span>
                                                        <span class="truncate max-w-[110px]">{{ $ticket->assignedTo?->name ?? 'Unassigned' }}</span>
                                                    </div>
                                                </td>

                                                <!-- Due Date Column -->
                                                <td class="py-4 px-3 whitespace-nowrap text-xs text-gray-500 align-middle">
                                                    @if($ticket->due_date)
                                                        <span class="{{ $ticket->is_overdue ? 'text-red-600 font-semibold' : '' }}">
                                                            {{ $ticket->due_date->format('M d, Y') }}
                                                        </span>
                                                    @else
                                                        <span class="text-gray-400 italic">None</span>
                                                    @endif
                                                </td>

                                                <!-- Priority Column -->
                                                <td class="py-4 px-3 whitespace-nowrap text-center align-middle">
                                                    @php
                                                        $priorityColors = [
                                                            'urgent' => 'bg-red-50 text-red-700 border-red-200',
                                                            'high'   => 'bg-amber-50 text-amber-700 border-amber-200',
                                                            'medium' => 'bg-blue-50 text-blue-700 border-blue-200',
                                                            'low'    => 'bg-gray-50 text-gray-700 border-gray-200',
                                                        ];
                                                    @endphp
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold capitalize border {{ $priorityColors[$ticket->priority] ?? 'bg-gray-50 text-gray-600' }}">
                                                        {{ $ticket->priority }}
                                                    </span>
                                                </td>

                                                <!-- Status Column with Quick Transition -->
                                                <td class="py-4 pl-3 pr-6 whitespace-nowrap text-right align-middle">
                                                    <div class="relative inline-block text-left">
                                                        <button type="button" @click="open = !open" @click.away="open = false"
                                                            :disabled="isUpdating"
                                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold border transition-all duration-150 shadow-2xs hover:shadow-xs disabled:opacity-60"
                                                            :class="statusClasses()"
                                                            title="Click to transition status">

                                                            <!-- Loading Spinner or Status Dot -->
                                                            <template x-if="isUpdating">
                                                                <svg class="animate-spin -ml-0.5 mr-1 h-3 w-3 text-current" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                                </svg>
                                                            </template>
                                                            <template x-if="!isUpdating">
                                                                <span class="w-1.5 h-1.5 rounded-full" :class="statusDot()"></span>
                                                            </template>

                                                            <span x-text="statusLabel()"></span>
                                                            <svg class="w-3 h-3 text-gray-400 ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                            </svg>
                                                        </button>

                                                        <!-- Dropdown Menu -->
                                                        <div x-show="open" x-cloak
                                                            x-transition:enter="transition ease-out duration-100"
                                                            x-transition:enter-start="transform opacity-0 scale-95"
                                                            x-transition:enter-end="transform opacity-100 scale-100"
                                                            x-transition:leave="transition ease-in duration-75"
                                                            x-transition:leave-start="transform opacity-100 scale-100"
                                                            x-transition:leave-end="transform opacity-0 scale-95"
                                                            class="origin-top-right absolute right-0 mt-2 w-44 rounded-xl shadow-lg bg-white ring-1 ring-black/5 divide-y divide-gray-100 focus:outline-none z-30">
                                                            <div class="px-3 py-2 text-[10px] font-bold uppercase tracking-wider text-gray-400 text-left">
                                                                Quick Transition
                                                            </div>
                                                            <div class="py-1">
                                                                <template x-for="target in allowedTransitions()" :key="target">
                                                                    <button type="button"
                                                                        @click="transition(target, '{{ route('tickets.transition', $ticket) }}')"
                                                                        class="w-full text-left px-3 py-1.5 text-xs text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 flex items-center gap-2 transition">
                                                                        <span class="w-2 h-2 rounded-full"
                                                                            :class="{
                                                                                'bg-brand-500': target === 'in_progress',
                                                                                'bg-purple-500': target === 'waiting',
                                                                                'bg-red-500': target === 'blocked',
                                                                                'bg-emerald-500': target === 'closed',
                                                                                'bg-blue-500': target === 'new'
                                                                            }"></span>
                                                                        <span x-text="'Mark as ' + target.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())"></span>
                                                                    </button>
                                                                </template>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>

                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Right 1 Col: Live Activity Feed (Matched Height Header & Elevation) -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl shadow-xs border border-gray-200/80 overflow-hidden hover:shadow-md transition-all duration-200 sticky top-6">

                        <!-- Header with Matched Height (h-16) for Clean Alignment -->
                        <div class="h-16 px-6 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                            <h3 class="text-base font-bold text-gray-900">Recent Activity</h3>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5 animate-pulse"></span>
                                Live
                            </span>
                        </div>

                        <div class="p-6">
                            @if($recentActivities->isEmpty())
                                <p class="text-xs text-gray-500 py-6 text-center">No recent activity logged yet.</p>
                            @else
                                <div class="flow-root">
                                    <ul class="-mb-6 divide-y divide-gray-100">
                                        @foreach($recentActivities as $log)
                                            <li class="py-3.5">
                                                <div class="flex items-start space-x-3">
                                                    <div class="w-8 h-8 rounded-full bg-indigo-50 border border-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-xs shrink-0 shadow-2xs">
                                                        {{ strtoupper(substr($log->user?->name ?? 'S', 0, 1)) }}
                                                    </div>
                                                    <div class="min-w-0 flex-1">
                                                        <p class="text-xs text-gray-800">
                                                            <strong class="font-semibold text-gray-900">{{ $log->user?->name ?? 'System' }}</strong>
                                                            updated
                                                            <span class="font-medium text-indigo-600">{{ $log->formatted_field }}</span>
                                                            on
                                                            <a href="{{ route('tickets.show', $log->ticket_id) }}" class="font-semibold text-gray-900 hover:text-indigo-600 hover:underline">
                                                                {{ $log->ticket?->reference_code ?? ('#' . $log->ticket_id) }}
                                                            </a>
                                                        </p>
                                                        <div class="text-[11px] text-gray-500 mt-1">
                                                            <span class="line-through text-gray-400">{{ Str::limit($log->formatValue($log->old_value), 22) }}</span>
                                                            &rarr;
                                                            <span class="text-gray-700 font-medium">{{ Str::limit($log->formatValue($log->new_value), 22) }}</span>
                                                        </div>
                                                        <p class="text-[10px] text-gray-400 mt-1.5">{{ $log->created_at->diffForHumans() }}</p>
                                                    </div>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>
