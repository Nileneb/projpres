<x-layouts.app :title="__('History')">
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Info Banner -->
            <div class="bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-400 dark:border-blue-600 p-4 mb-6 rounded">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400 dark:text-blue-300" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2h-1V9a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700 dark:text-blue-300">
                            {{ __('This is a read-only history view of past challenges. You cannot modify content from completed weeks.') }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-white">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-semibold">{{ __('Challenge History') }}</h2>

                        @if(count($availableWeeks) > 0)
                            <div class="flex items-center space-x-4">
                                <div class="flex items-center">
                                    <span class="mr-2">{{ __('Select week:') }}</span>
                                    <form method="GET" action="{{ route('history.index') }}" class="inline-flex items-center space-x-4">
                                        <input type="hidden" name="page" value="{{ $page }}">
                                        <select name="week" onchange="this.form.submit()" class="rounded-md border-gray-300 dark:border-zinc-600 dark:bg-zinc-700">
                                            @foreach($paginatedWeeks as $week)
                                                <option value="{{ $week }}" {{ $week === $selectedWeek ? 'selected' : '' }}>
                                                    {{ $week }}
                                                </option>
                                            @endforeach
                                        </select>

                                        <div class="flex items-center space-x-2">
                                            <input type="checkbox" name="show_archived" value="1" id="show_archived"
                                                {{ $showArchived ? 'checked' : '' }}
                                                onchange="this.form.submit()"
                                                class="rounded border-gray-300 text-indigo-600 dark:border-zinc-600 dark:bg-zinc-700">
                                            <label for="show_archived" class="text-sm text-gray-700 dark:text-gray-300">
                                                {{ __('Show archived teams') }}
                                            </label>
                                        </div>
                                    </form>
                                </div>

                                <!-- Pagination Controls -->
                                @if($totalWeeks > 10)
                                    <div class="flex items-center space-x-2">
                                        @php
                                            $totalPages = ceil($totalWeeks / 10);
                                            $pageParam = ['show_archived' => $showArchived ? '1' : '0'];
                                        @endphp

                                        <!-- First Page -->
                                        <a href="{{ route('history.index', array_merge(['page' => 1, 'week' => $selectedWeek], $pageParam)) }}"
                                           class="px-2 py-1 bg-gray-100 dark:bg-zinc-700 rounded-md text-sm hover:bg-gray-200 dark:hover:bg-zinc-600 {{ $page == 1 ? 'opacity-50 cursor-not-allowed' : '' }}">
                                            &laquo;
                                        </a>

                                        <!-- Previous -->
                                        @if($hasPreviousPages)
                                            <a href="{{ route('history.index', array_merge(['page' => $page - 1, 'week' => $selectedWeek], $pageParam)) }}"
                                               class="px-3 py-1 bg-gray-100 dark:bg-zinc-700 rounded-md text-sm hover:bg-gray-200 dark:hover:bg-zinc-600">
                                                &larr;
                                            </a>
                                        @else
                                            <span class="px-3 py-1 bg-gray-100 dark:bg-zinc-700 rounded-md text-sm opacity-50 cursor-not-allowed">
                                                &larr;
                                            </span>
                                        @endif

                                        <!-- Page indicator -->
                                        <span class="px-3 py-1 bg-indigo-100 dark:bg-indigo-800/30 text-indigo-800 dark:text-indigo-300 rounded-md font-medium">
                                            {{ $page }} / {{ $totalPages }}
                                        </span>

                                        <!-- Next -->
                                        @if($hasMorePages)
                                            <a href="{{ route('history.index', array_merge(['page' => $page + 1, 'week' => $selectedWeek], $pageParam)) }}"
                                               class="px-3 py-1 bg-gray-100 dark:bg-zinc-700 rounded-md text-sm hover:bg-gray-200 dark:hover:bg-zinc-600">
                                                &rarr;
                                            </a>
                                        @else
                                            <span class="px-3 py-1 bg-gray-100 dark:bg-zinc-700 rounded-md text-sm opacity-50 cursor-not-allowed">
                                                &rarr;
                                            </span>
                                        @endif

                                        <!-- Last Page -->
                                        <a href="{{ route('history.index', array_merge(['page' => $totalPages, 'week' => $selectedWeek], $pageParam)) }}"
                                           class="px-2 py-1 bg-gray-100 dark:bg-zinc-700 rounded-md text-sm hover:bg-gray-200 dark:hover:bg-zinc-600 {{ $page == $totalPages ? 'opacity-50 cursor-not-allowed' : '' }}">
                                            &raquo;
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>

                    @if(empty($selectedWeek))
                        <div class="text-center py-12">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-16 w-16 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <h3 class="mt-2 text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('No historical data available.') }}</h3>
                            <p class="mt-1 text-gray-500 dark:text-gray-400">{{ __('Current week is') }} {{ $currentWeek }}</p>
                            <p class="mt-2 text-gray-500 dark:text-gray-400">{{ __('Past challenges will appear here when weeks are completed.') }}</p>
                        </div>
                    @else
                        <div class="mb-8">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-medium">{{ __('Teams for') }} {{ $selectedWeek }}</h3>
                                <span class="inline-flex items-center px-3 py-1 bg-gray-100 dark:bg-zinc-700 text-gray-800 dark:text-gray-300 text-xs rounded-full">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                    </svg>
                                    {{ __('Read-only view') }}
                                </span>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($teams as $team)
                                    <div class="border rounded-lg dark:border-zinc-700 p-4 {{ $team->is_archived ? 'border-l-4 border-l-amber-500 dark:border-l-amber-600' : '' }}">
                                        <div class="flex justify-between">
                                            <h4 class="font-semibold text-lg">{{ $team->name }}</h4>
                                            @if($team->is_archived)
                                                <span class="text-xs px-2 py-1 bg-amber-100 text-amber-800 dark:bg-amber-800/30 dark:text-amber-300 rounded-full">
                                                    {{ __('Archived') }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="mt-2">
                                            <h5 class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Members:') }}</h5>
                                            <div class="flex flex-wrap gap-2 mt-1">
                                                @foreach($team->users as $user)
                                                    <span class="px-2 py-1 text-xs bg-gray-100 dark:bg-zinc-700 rounded">
                                                        {{ $user->name }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div>
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-medium">{{ __('Challenges for') }} {{ $selectedWeek }}</h3>
                                <span class="inline-flex items-center px-3 py-1 bg-gray-100 dark:bg-zinc-700 text-gray-800 dark:text-gray-300 text-xs rounded-full">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                                    </svg>
                                    {{ __('Archived - no modifications allowed') }}
                                </span>
                            </div>

                            @if($matches->count() > 0)
                                <div class="space-y-6">
                                    @foreach($matches as $match)
                                        <div class="bg-white dark:bg-zinc-800 border dark:border-zinc-700 rounded-lg shadow-sm p-6">
                                            <div class="flex justify-between items-start">
                                                <div>
                                                    <div class="text-lg font-bold mb-2">
                                                        {{ $match->creator->name }} vs {{ $match->solver->name }}
                                                    </div>
                                                    <div class="text-gray-700 dark:text-gray-300 mb-4">
                                                        <p class="font-semibold">Challenge:</p>
                                                        <p class="mb-2">{{ $match->challenge_text }}</p>

                                                        @if($match->status == 'submitted' || $match->status == 'closed')
                                                            @if($match->submission_url)
                                                                <p class="font-semibold">Solution:</p>
                                                                <a href="{{ $match->submission_url }}" target="_blank" class="text-blue-600 dark:text-blue-400 font-medium hover:underline">
                                                                    {{ __('View submission') }}
                                                                </a>
                                                            @else
                                                                <p class="text-gray-500 dark:text-gray-400">
                                                                    {{ __('No solution submitted') }}
                                                                </p>
                                                            @endif
                                                        @else
                                                            <p class="text-gray-500 dark:text-gray-400">
                                                                {{ __('No solution submitted (status: :status)', ['status' => $match->status]) }}
                                                            </p>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div>
                                                    <x-match-status :status="$match->status" />

                                                    @if($match->votes->count() > 0)
                                                        <div class="mt-2 text-right">
                                                            <span class="text-gray-700 dark:text-gray-300">
                                                                {{ __('Average rating:') }} {{ number_format($match->votes->avg('score'), 1) }}/5
                                                                ({{ $match->votes->count() }} {{ __('votes') }})
                                                            </span>
                                                        </div>

                                                        @if($match->votes->where('comment', '!=', null)->count() > 0)
                                                            <div class="mt-4 border-t border-gray-200 dark:border-zinc-700 pt-4">
                                                                <h5 class="font-medium mb-2">{{ __('Comments:') }}</h5>
                                                                <div class="space-y-3 max-h-40 overflow-y-auto">
                                                                    @foreach($match->votes->where('comment', '!=', null) as $vote)
                                                                        <div class="text-sm">
                                                                            <div class="flex justify-between">
                                                                                <span class="font-medium">{{ $vote->user->name }}</span>
                                                                                <span class="text-gray-500 dark:text-gray-400">{{ $vote->score }}/5</span>
                                                                            </div>
                                                                            <p class="text-gray-700 dark:text-gray-300">{{ $vote->comment }}</p>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        @endif
                                                    @else
                                                        <div class="mt-2 text-right">
                                                            <span class="text-gray-500 dark:text-gray-400">
                                                                {{ __('No votes') }}
                                                            </span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="bg-gray-50 dark:bg-zinc-700 p-8 rounded-md text-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <h4 class="mt-2 text-lg font-medium text-gray-700 dark:text-gray-300">{{ __('No challenges found') }}</h4>
                                    <p class="text-gray-600 dark:text-gray-400">{{ __('No challenges were created during this week.') }}</p>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <div class="mt-4">
                <a href="{{ route('dashboard') }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">
                    &larr; {{ __('Back to Dashboard') }}
                </a>
            </div>
        </div>
    </div>
</x-layouts.app>
