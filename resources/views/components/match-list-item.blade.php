<div class="bg-white dark:bg-zinc-800 border dark:border-zinc-700 rounded-lg shadow-sm p-4">
    <div class="flex justify-between items-start">
        <div>
            <div class="text-lg font-bold mb-2">
                {{ $match->creator->name }} → {{ $match->solver->name }}
            </div>
            <div class="text-gray-700 dark:text-gray-300 mb-2">
                <p class="line-clamp-2">{{ $match->challenge_text }}</p>
            </div>
            <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                <span class="flex items-center">
                    <x-icon name="clock" size="xs" class="mr-1" />
                    {{ $match->time_limit_minutes }} min
                </span>
            </div>
        </div>
        <div>
            <x-match-status :status="$match->status" />

            @if($match->status == 'in_progress' && $match->deadline)
                <div class="mt-1 text-xs">
                    @php
                        $now = now();
                        $deadline = $match->deadline;
                        $remainingTime = $now->diffInMinutes($deadline, false);
                    @endphp

                    @if($remainingTime > 0)
                        <div class="text-amber-600 dark:text-amber-400 font-medium">
                            {{ $remainingTime }} min verbleibend
                        </div>
                    @else
                        <div class="text-red-600 dark:text-red-400 font-medium">
                            Zeit abgelaufen
                        </div>
                    @endif
                </div>
            @endif

            @if($showActions)
                <div class="mt-2 flex justify-end">
                    @if($match->status == 'in_progress' && $isInSolverTeam)
                        <a href="{{ route('matches.submit', $match) }}" class="inline-flex items-center px-2.5 py-1.5 bg-green-600 border border-transparent rounded text-xs font-medium text-white hover:bg-green-700 mr-2 transition-colors">
                            <x-icon name="check" size="xs" class="mr-1" />
                            Submit
                        </a>
                    @elseif($match->status == 'created' && $isInSolverTeam)
                        <form method="POST" action="{{ route('matches.start', $match) }}" class="inline mr-2">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-2.5 py-1.5 bg-blue-600 border border-transparent rounded text-xs font-medium text-white hover:bg-blue-700 transition-colors">
                                <x-icon name="start" size="xs" class="mr-1" />
                                Start
                            </button>
                        </form>
                    @endif

                    @if($isInCreatorTeam && ($match->status == 'created' || $match->status == 'in_progress'))
                        <a href="{{ route('matches.show', $match) }}#update-challenge" class="inline-flex items-center px-2.5 py-1.5 bg-amber-600 border border-transparent rounded text-xs font-medium text-white hover:bg-amber-700 mr-2 transition-colors">
                            <x-icon name="edit" size="xs" class="mr-1" />
                            Edit
                        </a>
                    @endif

                    <a href="{{ route('matches.show', $match) }}" class="inline-flex items-center px-2.5 py-1.5 bg-gray-200 dark:bg-gray-700 border border-transparent rounded text-xs font-medium text-gray-700 dark:text-white hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                        <x-icon name="details" size="xs" class="mr-1" />
                        Details
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
