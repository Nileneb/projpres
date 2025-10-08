<x-layouts.app :title="__('Select Team for Challenge')">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Create New Challenge') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white dark:bg-zinc-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-6">
                        {{ __('Select a Team to Challenge') }}
                    </h2>

                    @php
                        $currentUser = auth()->user();
                        $currentUserTeam = $currentUser->teams()->where('week_label', $weekLabel)->first();
                    @endphp

                    @if (!$currentUserTeam)
                        <div class="rounded-md bg-yellow-50 dark:bg-yellow-900 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">Not in a team</h3>
                                    <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">
                                        <p>You are not assigned to a team for the current week ({{ $weekLabel }}). Please contact an administrator.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @elseif ($solverTeams->isEmpty())
                        <div class="rounded-md bg-yellow-50 dark:bg-yellow-900 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">No teams available</h3>
                                    <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">
                                        <p>There are no other teams available for challenges this week.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="bg-white dark:bg-zinc-800 overflow-hidden">
                            <ul role="list" class="divide-y divide-gray-200 dark:divide-zinc-700">
                                @php
                                    // Filter out the current user's team
                                    $filteredTeams = $solverTeams->reject(function ($team) use ($currentUserTeam) {
                                        return $team->id === $currentUserTeam->id;
                                    });

                                    // Get TeamAssignmentService to check if teams already have challenges
                                    $teamAssignmentService = app(\App\Services\TeamAssignmentService::class);
                                @endphp

                                @foreach ($filteredTeams as $team)
                                    @php
                                        $hasReceivedChallenge = $teamAssignmentService->hasTeamReceivedChallengeForWeek($team->id, $weekLabel);
                                        $disabled = $hasReceivedChallenge;
                                    @endphp
                                    <li class="px-4 py-4 sm:px-6 {{ $disabled ? 'opacity-50' : '' }}">
                                        <div class="flex items-center justify-between">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                <span class="font-semibold">{{ $team->name }}</span>
                                                <span class="text-xs text-gray-500 dark:text-gray-400 ml-2">({{ $team->participants->count() }} members)</span>
                                            </div>
                                            <div>
                                                @if ($disabled)
                                                    <x-badge color="gray" size="sm">
                                                        Already has challenge
                                                    </x-badge>
                                                @else
                                                    <a href="{{ route('matches.create', ['solver_team_id' => $team->id, 'week_label' => $weekLabel]) }}"
                                                       class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:border-indigo-700 focus:shadow-outline-indigo active:bg-indigo-700 transition ease-in-out duration-150">
                                                        Select Team
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                            <ul class="list-disc space-y-1 pl-5">
                                                @foreach ($team->participants as $participant)
                                                    <li>{{ $participant->user->name }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </li>
                                @endforeach

                                @if ($filteredTeams->isEmpty())
                                    <li class="px-4 py-4 sm:px-6 text-center">
                                        <p class="text-sm text-gray-500 dark:text-gray-400">No other teams available for this week.</p>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    @endif

                    <div class="mt-8 border-t border-gray-200 dark:border-zinc-700 pt-6">
                        <a href="{{ route('dashboard') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300">
                            &larr; Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
