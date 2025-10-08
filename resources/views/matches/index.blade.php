<x-layouts.app :title="__('Challenges')">
    <div class="flex justify-between items-center mb-4">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Challenges') }}
            </h2>
            <p class="text-sm text-gray-600 dark:text-gray-400">
                {{ __('Current week') }}: {{ $currentWeek }}
            </p>
        </div>
                        <a href="{{ route('matches.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 group relative">
                    {{ __('New Challenge') }}
                    <span class="absolute -bottom-10 left-1/2 -translate-x-1/2 hidden group-hover:block bg-black/80 text-white text-xs rounded py-1 px-2 whitespace-nowrap z-10">
                        Create a new challenge for another team
                    </span>
                </a>
    </div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-zinc-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
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

                                                @if($match->status == 'submitted' && $match->submission_url)
                                                    <p class="font-semibold">Solution:</p>
                                                    <a href="{{ $match->submission_url }}" target="_blank" class="text-blue-600 dark:text-blue-400 font-medium hover:underline">
                                                        View submission
                                                    </a>
                                                @endif

                                                <div class="flex items-center mt-2">
                                                    <span class="font-semibold mr-2">Status:</span>
                                                    <x-match-status :status="$match->status" />

                                                    @php
                                                        $isInSolverTeam = auth()->user()->teams->contains($match->solver_team_id);
                                                    @endphp

                                                    @if($match->status == 'in_progress' && $isInSolverTeam)
                                                        <a href="{{ route('matches.submit', $match) }}" class="ml-2 text-blue-600 dark:text-blue-400 font-medium hover:underline">
                                                            Submit Solution
                                                        </a>
                                                    @elseif($match->status == 'created' && $isInSolverTeam)
                                                        <form method="POST" action="{{ route('matches.start', $match) }}" class="inline">
                                                            @csrf
                                                            <button type="submit" class="ml-2 text-blue-600 dark:text-blue-400 font-medium hover:underline">
                                                                Start Challenge
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div>
                                            @if($match->status == 'submitted' && auth()->user()->team_id != $match->creator_team_id && auth()->user()->team_id != $match->solver_team_id)
                                                <div class="text-right">
                                                    @if(!$match->votes()->where('user_id', auth()->id())->exists())
                                                        <form method="POST" action="{{ route('votes.store', $match) }}" class="inline-flex space-x-2">
                                                            @csrf
                                                            <select name="score" class="rounded-md border-gray-300 dark:border-zinc-600 dark:bg-zinc-700 dark:text-white">
                                                                <option value="1">1 - Poor</option>
                                                                <option value="2">2 - Fair</option>
                                                                <option value="3">3 - Good</option>
                                                                <option value="4">4 - Very Good</option>
                                                                <option value="5">5 - Excellent</option>
                                                            </select>
                                                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                                                                Vote
                                                            </button>
                                                        </form>
                                                    @else
                                                        <span class="text-green-600 dark:text-green-400">
                                                            You voted: {{ $match->votes()->where('user_id', auth()->id())->first()->score }}/5
                                                        </span>
                                                    @endif
                                                </div>
                                            @endif

                                            @if($match->votes->count() > 0)
                                                <div class="mt-2 text-right">
                                                    <span class="text-gray-700 dark:text-gray-300">
                                                        Average Rating: {{ number_format($match->votes->avg('score'), 1) }}/5
                                                        ({{ $match->votes->count() }} votes)
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <p class="text-gray-600 dark:text-gray-400">No challenges for the current week ({{ $currentWeek }}).</p>
                            <p class="mt-2">
                                <a href="{{ route('matches.create') }}" class="text-blue-600 dark:text-blue-400 font-medium hover:underline">
                                    Create a challenge
                                </a>
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
