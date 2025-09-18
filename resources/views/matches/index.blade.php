<x-layouts.app :title="__('Challenges')">
    <div class="flex justify-between items-center mb-4">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Challenges') }}
        </h2>
            <a href="{{ route('matches.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                {{ __('New Challenge') }}
            </a>
        </div>
    </div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-zinc-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if($matches->count() > 0)
                        <div class="space-y-6">
                            @foreach($matches as $match)
                                <div class="bg-white border rounded-lg shadow-sm p-6">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <div class="text-lg font-bold mb-2">
                                                {{ $match->creator->name }} vs {{ $match->solver->name }}
                                            </div>
                                            <div class="text-gray-700 mb-4">
                                                <p class="font-semibold">Challenge:</p>
                                                <p class="mb-2">{{ $match->challenge_text }}</p>

                                                @if($match->status == 'completed' && $match->submission_url)
                                                    <p class="font-semibold">Solution:</p>
                                                    <a href="{{ $match->submission_url }}" target="_blank" class="text-blue-600 hover:underline">
                                                        View submission
                                                    </a>
                                                @elseif($match->status == 'in_progress')
                                                    <p class="text-amber-600">
                                                        <span class="font-semibold">Status:</span> In Progress
                                                        @if($match->solver_id == auth()->user()->team_id)
                                                            <a href="{{ route('matches.submit', $match) }}" class="ml-2 text-blue-600 hover:underline">
                                                                Submit Solution
                                                            </a>
                                                        @endif
                                                    </p>
                                                @elseif($match->status == 'created')
                                                    <p class="text-gray-600">
                                                        <span class="font-semibold">Status:</span> Not Started
                                                        @if($match->solver_id == auth()->user()->team_id)
                                                            <form method="POST" action="{{ route('matches.start', $match) }}" class="inline">
                                                                @csrf
                                                                <button type="submit" class="ml-2 text-blue-600 hover:underline">
                                                                    Start Challenge
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                        <div>
                                            @if($match->status == 'completed' && auth()->user()->team_id != $match->creator_id && auth()->user()->team_id != $match->solver_id)
                                                <div class="text-right">
                                                    @if(!$match->votes()->where('user_id', auth()->id())->exists())
                                                        <form method="POST" action="{{ route('votes.store', $match) }}" class="inline-flex space-x-2">
                                                            @csrf
                                                            <select name="rating" class="rounded-md border-gray-300">
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
                                                        <span class="text-green-600">
                                                            You voted: {{ $match->votes()->where('user_id', auth()->id())->first()->score }}/5
                                                        </span>
                                                    @endif
                                                </div>
                                            @endif

                                            @if($match->votes->count() > 0)
                                                <div class="mt-2 text-right">
                                                    <span class="text-gray-700">
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
                            <p class="text-gray-600">No challenges yet.</p>
                            <p class="mt-2">
                                <a href="{{ route('matches.create') }}" class="text-blue-600 hover:underline">
                                    Create your first challenge
                                </a>
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
