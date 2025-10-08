<x-layouts.app :title="__('Teams')">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
            {{ __('Teams') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Archive Filter Toggle -->
            <div class="mb-4 flex justify-between">
                <div>
                    <a href="{{ route('teams.index', ['show_archived' => $showArchived ? 0 : 1]) }}"
                       class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                        {{ $showArchived ? 'Show Active Teams' : 'Show Archived Teams' }}
                    </a>
                </div>
                <div>
                    @if($showArchived)
                        <span class="px-3 py-1 bg-amber-100 text-amber-800 text-sm rounded-full">
                            Showing Archived Teams
                        </span>
                    @else
                        <span class="px-3 py-1 bg-green-100 text-green-800 text-sm rounded-full">
                            Showing Active Teams
                        </span>
                    @endif
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @forelse ($weekLabels as $weekLabel)
                        <div class="mb-8">
                            <div class="flex items-center justify-between mb-4">
                                <h2 class="text-xl font-bold">{{ $weekLabel }}</h2>

                                @can('manage-teams')
                                <form method="POST" action="{{ route('teams.archive') }}" class="inline">
                                    @csrf
                                    <input type="hidden" name="week_label" value="{{ $weekLabel }}">
                                    <button type="submit" class="ml-2 inline-flex items-center px-2 py-1 bg-amber-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-amber-600">
                                        {{ $showArchived ? 'Unarchive Week' : 'Archive Week' }}
                                    </button>
                                </form>
                                @endcan
                            </div>

                            <div class="space-y-4">
                                @foreach ($teamsByWeek[$weekLabel] as $team)
                                    <div class="border dark:border-zinc-700 p-4 rounded-lg">
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $team->name }}</h3>

                                        <div class="mt-4">
                                            <h4 class="font-medium text-sm text-gray-700 dark:text-gray-300 mb-2">Team Members:</h4>
                                            <ul class="list-disc ml-5">
                                                @foreach ($team->users as $user)
                                                    <li class="dark:text-gray-300">{{ $user->name }}</li>
                                                @endforeach
                                            </ul>
                                        </div>

                                        <div class="mt-4">
                                            <h4 class="font-medium text-sm text-gray-700 dark:text-gray-300 mb-2">Matches:</h4>

                                            @php
                                                $createdMatches = $team->createdMatches->where('week_label', $weekLabel);
                                                $solvedMatches = $team->solvedMatches->where('week_label', $weekLabel);
                                                // Check if user is part of this team
                                                $userInTeam = $team->users->contains(auth()->user());
                                                // Get other teams from the same week that user can challenge
                                                $otherTeams = collect($teamsByWeek[$weekLabel])->reject(function($otherTeam) use ($team) {
                                                    return $otherTeam->id === $team->id;
                                                });
                                            @endphp

                                            @if ($createdMatches->count() > 0 || $solvedMatches->count() > 0)
                                                <ul class="space-y-2">
                                                    @foreach ($createdMatches as $match)
                                                        <li>
                                                            <a href="{{ route('matches.show', $match) }}" class="text-blue-600 dark:text-blue-400 font-medium hover:underline">
                                                                Created challenge for {{ $match->solver->name }} ({{ $match->status }})
                                                            </a>
                                                        </li>
                                                    @endforeach

                                                    @foreach ($solvedMatches as $match)
                                                        <li>
                                                            <a href="{{ route('matches.show', $match) }}" class="text-blue-600 dark:text-blue-400 font-medium hover:underline">
                                                                Solving challenge from {{ $match->creator->name }} ({{ $match->status }})
                                                            </a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                <p class="text-gray-500 dark:text-gray-400 italic">No matches yet.</p>
                                            @endif

                                            @if ($userInTeam && $otherTeams->count() > 0)
                                                <div class="mt-3">
                                                    <h5 class="font-medium text-sm text-gray-700 dark:text-gray-300 mb-2">Create Challenge:</h5>
                                                    <div class="flex flex-col space-y-2">
                                                        @foreach($otherTeams as $otherTeam)
                                                            @php
                                                                // Check if we already created a challenge for this team
                                                                $challengeExists = $createdMatches->contains(function ($match) use ($otherTeam) {
                                                                    return $match->solver_team_id == $otherTeam->id;
                                                                });
                                                            @endphp

                                                            @if(!$challengeExists)
                                                                <form method="GET" action="{{ route('matches.create') }}">
                                                                    <input type="hidden" name="solver_team_id" value="{{ $otherTeam->id }}">
                                                                    <input type="hidden" name="week_label" value="{{ $weekLabel }}">
                                                                    <button type="submit" class="px-2 py-1 text-xs bg-green-600 text-white rounded hover:bg-green-700 focus:bg-green-700 active:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                                        Challenge {{ $otherTeam->name }}
                                                                    </button>
                                                                </form>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <p>No teams available.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
