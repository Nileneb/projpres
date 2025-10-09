<x-layouts.app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        @php
            if (isset($receivedChallenge) && isset($receivedChallenge->deadline)) {
                $now = $timeService->current();
                $deadline = $receivedChallenge->deadline;
                $remainingTime = $now->diffInMinutes($deadline, false);
            }
        @endphp
        <div class="grid auto-rows-min gap-4 md:grid-cols-4">
            <a href="{{ route('teams.index') }}" class="flex flex-col items-center justify-center aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 p-4 transition-colors hover:bg-neutral-100 dark:hover:bg-neutral-800">
                <x-icon name="team" size="lg" class="mb-2" />
                <span class="text-lg font-semibold">Teams</span>
                <span class="text-sm text-neutral-600 dark:text-neutral-300">Show all Teams</span>
            </a>

            <a href="{{ route('matches.create') }}" class="flex flex-col items-center justify-center aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 p-4 transition-colors hover:bg-neutral-100 dark:hover:bg-neutral-800">
                <x-icon name="plus" size="lg" class="mb-2" />
                <span class="text-lg font-semibold">New Challenge</span>
                <span class="text-sm text-neutral-600 dark:text-neutral-300">Create a new challenge</span>
            </a>

            <a href="{{ route('leaderboard.index') }}" class="flex flex-col items-center justify-center aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 p-4 transition-colors hover:bg-neutral-100 dark:hover:bg-neutral-800">
                <x-icon name="chart" size="lg" class="mb-2" />
                <span class="text-lg font-semibold">Leaderboard</span>
                <span class="text-sm text-neutral-600 dark:text-neutral-300">View user rankings</span>
            </a>

            <a href="{{ route('history.index') }}" class="flex flex-col items-center justify-center aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 p-4 transition-colors hover:bg-neutral-100 dark:hover:bg-neutral-800">
                <x-icon name="clock" size="lg" class="mb-2" />
                <span class="text-lg font-semibold">History</span>
                <span class="text-sm text-neutral-600 dark:text-neutral-300">View past challenges</span>
            </a>
        </div>

        <!-- Challenges Section -->
        <div class="p-6 bg-white shadow-sm rounded-xl border border-neutral-200 dark:bg-neutral-800 dark:border-neutral-700">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold">Challenges</h2>
                <a href="{{ route('matches.create') }}" class="inline-flex items-center px-3 py-1.5 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                    <x-icon name="plus" size="xs" class="mr-1" />
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
                            @php
                                $isInCreatorTeam = auth()->user()->teams->contains($match->creator_team_id);
                                $isInSolverTeam = auth()->user()->teams->contains($match->solver_team_id);
                            @endphp
                            <x-match-list-item :match="$match" :isInCreatorTeam="$isInCreatorTeam" :isInSolverTeam="$isInSolverTeam" />
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
                            @php
                                $isInSolverTeam = auth()->user()->teams->contains($match->solver_team_id);
                                $isInCreatorTeam = auth()->user()->teams->contains($match->creator_team_id);
                            @endphp
                            <x-match-list-item :match="$match" :isInCreatorTeam="$isInCreatorTeam" :isInSolverTeam="$isInSolverTeam" />
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
