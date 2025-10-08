<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-white">
            Team-Zuweisungen für {{ $currentWeekLabel }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="p-4 mb-4 rounded-lg">
                    <x-badge color="green" :pill="false">
                        {{ session('success') }}
                    </x-badge>
                </div>
            @endif

            @if(!$teams->count())
                <div class="p-6 overflow-hidden bg-white shadow-sm dark:bg-zinc-800 sm:rounded-lg">
                    <p class="text-gray-700 dark:text-gray-300">Keine Teams für diese Woche gefunden.</p>
                    <a href="{{ route('teams.generate') }}" class="inline-flex items-center px-4 py-2 mt-4 text-xs font-semibold tracking-widest text-white uppercase transition duration-150 ease-in-out bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25">
                        Teams generieren
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    @foreach($teams as $team)
                        <div class="overflow-hidden bg-white shadow-sm dark:bg-zinc-800 sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="mb-4 text-lg font-medium text-gray-900 dark:text-white">
                                    {{ $team->name }}
                                </h3>

                                <h4 class="font-medium text-gray-700 dark:text-gray-300">Mitglieder:</h4>
                                <ul class="pl-5 mt-2 mb-4 list-disc">
                                    @foreach($team->participants as $participant)
                                        <li class="text-gray-600 dark:text-gray-300">{{ $participant->user->name }}</li>
                                    @endforeach
                                </ul>

                                @if($assignments['success'])
                                    @foreach($assignments['assignments'] as $assignment)
                                        @if($assignment['team']->id === $team->id)
                                            <div class="p-4 mt-4 bg-indigo-50 dark:bg-indigo-900 rounded-lg">
                                                <h4 class="font-medium text-indigo-800 dark:text-indigo-200">Herausforderer:</h4>
                                                <p class="mt-1 text-indigo-700 dark:text-indigo-300">{{ $assignment['opponent']->name }}</p>

                                                @php
                                                    $hasCreatedChallenge = app(App\Services\TeamAssignmentService::class)
                                                        ->hasTeamCreatedChallengeForWeek($team->id, $currentWeekLabel);
                                                @endphp

                                                @if(!$hasCreatedChallenge)
                                                    <a href="{{ route('matches.create', [
                                                        'solver_team_id' => $assignment['opponent']->id,
                                                        'week_label' => $currentWeekLabel
                                                    ]) }}" class="inline-flex items-center px-3 py-1 mt-2 text-xs font-semibold tracking-widest text-white uppercase transition duration-150 ease-in-out bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25">
                                                        Challenge erstellen
                                                    </a>
                                                @else
                                                    <x-badge color="green" :pill="false" class="mt-2">
                                                        Challenge bereits erstellt
                                                    </x-badge>
                                                @endif
                                            </div>
                                        @endif
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
