<x-layouts.app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="grid auto-rows-min gap-4 md:grid-cols-4">
            <a href="{{ route('teams.index') }}" class="flex flex-col items-center justify-center aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 p-4 transition-colors hover:bg-neutral-100 dark:hover:bg-neutral-800">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-12 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <span class="text-lg font-semibold">Teams</span>
                <span class="text-sm text-neutral-600 dark:text-neutral-300">Show all Teams</span>
            </a>

            <a href="{{ route('matches.create') }}" class="flex flex-col items-center justify-center aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 p-4 transition-colors hover:bg-neutral-100 dark:hover:bg-neutral-800">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-12 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span class="text-lg font-semibold">New Challenge</span>
                <span class="text-sm text-neutral-600 dark:text-neutral-300">Create a new challenge</span>
            </a>

            <a href="{{ route('leaderboard.index') }}" class="flex flex-col items-center justify-center aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 p-4 transition-colors hover:bg-neutral-100 dark:hover:bg-neutral-800">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-12 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                </svg>
                <span class="text-lg font-semibold">Leaderboard</span>
                <span class="text-sm text-neutral-600 dark:text-neutral-300">View user rankings</span>
            </a>

            <a href="{{ route('history.index') }}" class="flex flex-col items-center justify-center aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 p-4 transition-colors hover:bg-neutral-100 dark:hover:bg-neutral-800">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-12 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-lg font-semibold">History</span>
                <span class="text-sm text-neutral-600 dark:text-neutral-300">View past challenges</span>
            </a>
        </div>

        <!-- Challenges Section -->
        <div class="p-6 bg-white shadow-sm rounded-xl border border-neutral-200 dark:bg-neutral-800 dark:border-neutral-700">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold">Challenges</h2>
                <a href="{{ route('matches.create') }}" class="inline-flex items-center px-3 py-1.5 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    New Challenge
                </a>
            </div>

            <!-- Challenges created by my team -->
            @php
                $teamAssignmentService = app(\App\Services\TeamAssignmentService::class);
                $currentWeek = $teamAssignmentService->getCurrentWeekLabel();

                // Nur aktive (nicht archivierte) Teams des Benutzers für die aktuelle Woche laden
                $activeTeams = auth()->user()->teams()->where('week_label', $currentWeek)
                                    ->where('is_archived', false)->get();

                // Challenges filtern nach aktueller Woche und aktiven Teams
                $createdMatches = collect();
                $solverMatches = collect();

                foreach ($activeTeams as $team) {
                    $createdMatches = $createdMatches->merge(
                        $team->createdMatches()->where('week_label', $currentWeek)->get()
                    );

                    $solverMatches = $solverMatches->merge(
                        $team->solvedMatches()->where('week_label', $currentWeek)->get()
                    );
                }
            @endphp

            <div class="mb-6">
                <h3 class="text-lg font-medium mb-3">Created by your team ({{ $currentWeek }})</h3>

                @if($createdMatches->count() > 0)
                    <div class="space-y-4">
                        @foreach($createdMatches as $match)
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
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                {{ $match->time_limit_minutes }} min
                                            </span>
                                        </div>
                                    </div>
                                    <div>
                                        <span class="px-2.5 py-1 text-xs font-medium rounded-full
                                            {{ $match->status == 'submitted' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' :
                                               ($match->status == 'in_progress' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' :
                                               'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200') }}">
                                            {{ ucfirst($match->status) }}
                                        </span>
                                        <div class="mt-2">
                                            <a href="{{ route('matches.show', $match) }}" class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:underline">
                                                Details →
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="bg-gray-50 dark:bg-zinc-700 p-4 rounded-md text-center">
                        <p class="text-gray-600 dark:text-gray-300">No challenges created yet.</p>
                        <a href="{{ route('matches.create') }}" class="mt-2 inline-block text-indigo-600 dark:text-indigo-400 hover:underline">
                            Create your first challenge
                        </a>
                    </div>
                @endif
            </div>

            <!-- Challenges assigned to my team -->
            <div>
                <h3 class="text-lg font-medium mb-3">Assigned to your team ({{ $currentWeek }})</h3>

                @if($solverMatches->count() > 0)
                    <div class="space-y-4">
                        @foreach($solverMatches as $match)
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
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                {{ $match->time_limit_minutes }} min
                                            </span>
                                        </div>
                                    </div>
                                    <div>
                                        <x-match-status :status="$match->status" />

                                        <div class="mt-2 flex justify-end">
                                            @if($match->status == 'created')
                                                <a href="{{ route('matches.submit', $match) }}" class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:underline mr-2">
                                                    Submit →
                                                </a>
                                            @endif
                                            <a href="{{ route('matches.show', $match) }}" class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:underline">
                                                Details →
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="bg-gray-50 dark:bg-zinc-700 p-4 rounded-md text-center">
                        <p class="text-gray-600 dark:text-gray-300">No challenges assigned to your team.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Teams Section -->
        <div class="p-6 bg-white shadow-sm rounded-xl border border-neutral-200 dark:bg-neutral-800 dark:border-neutral-700">
            <h2 class="text-xl font-semibold mb-4">Your Teams</h2>

            @if(auth()->user()->teams->count() > 0)
                <div class="space-y-4">
                    @foreach(auth()->user()->teams as $team)
                    <div class="p-4 border rounded-lg dark:border-neutral-700">
                        <div class="flex justify-between items-center">
                            <div>
                                <h3 class="font-semibold text-lg">{{ $team->name }}</h3>
                                <p class="text-sm text-neutral-600 dark:text-neutral-300">{{ $team->week_label }}</p>
                            </div>
                            <div>
                                <x-badge color="blue">
                                    {{ $team->participants->count() }} Members
                                </x-badge>
                            </div>
                        </div>
                        <div class="mt-3">
                            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">Members:</h4>
                            <div class="flex flex-wrap gap-2 mt-1">
                                @foreach($team->participants as $participant)
                                    <x-badge color="gray" :pill="false" size="sm">
                                        {{ $participant->user->name }}
                                    </x-badge>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <p class="font-medium text-zinc-800 dark:text-white">You are not assigned to any team yet.</p>
            @endif
        </div>
    </div>
</x-layouts.app>
