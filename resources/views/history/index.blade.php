<x-layouts.app :title="__('History')">
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-zinc-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-white">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-semibold">{{ __('Challenge History') }}</h2>

                        @if(count($availableWeeks) > 0)
                            <div class="flex items-center">
                                <span class="mr-2">{{ __('Select week:') }}</span>
                                <form method="GET" action="{{ route('history.index') }}" class="inline-flex">
                                    <select name="week" onchange="this.form.submit()" class="rounded-md border-gray-300 dark:border-zinc-600 dark:bg-zinc-700">
                                        @foreach($availableWeeks as $week)
                                            <option value="{{ $week }}" {{ $week === $selectedWeek ? 'selected' : '' }}>
                                                {{ $week }}
                                            </option>
                                        @endforeach
                                    </select>
                                </form>
                            </div>
                        @endif
                    </div>

                    @if(empty($selectedWeek))
                        <div class="text-center py-8">
                            <p class="text-gray-500 dark:text-gray-400">{{ __('No historical data available.') }}</p>
                            <p class="mt-2 text-gray-500 dark:text-gray-400">{{ __('Current week is') }} {{ $currentWeek }}</p>
                        </div>
                    @else
                        <div class="mb-8">
                            <h3 class="text-lg font-medium mb-4">{{ __('Teams for') }} {{ $selectedWeek }}</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($teams as $team)
                                    <div class="border rounded-lg dark:border-zinc-700 p-4">
                                        <h4 class="font-semibold text-lg">{{ $team->name }}</h4>
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
                            <h3 class="text-lg font-medium mb-4">{{ __('Challenges for') }} {{ $selectedWeek }}</h3>

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
                                <div class="bg-gray-50 dark:bg-zinc-700 p-4 rounded-md text-center">
                                    <p class="text-gray-600 dark:text-gray-300">{{ __('No challenges found for this week.') }}</p>
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
