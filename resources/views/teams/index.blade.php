<x-layouts.app :title="__('Teams')">
    <div class="space-y-6">
        @forelse ($weekLabels as $weekLabel)
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-6">{{ $weekLabel }}</h2>

                <div class="space-y-4">
                    @foreach ($teamsByWeek[$weekLabel] as $team)
                        <div class="border border-gray-200 dark:border-gray-600 p-4 rounded-lg bg-gray-50 dark:bg-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ $team->name }}</h3>

                            <div class="mb-4">
                                <h4 class="font-medium text-sm text-gray-700 dark:text-gray-300 mb-2">Team Members:</h4>
                                <ul class="list-disc ml-5 text-gray-800 dark:text-gray-200">
                                    @foreach ($team->users as $user)
                                        <li>{{ $user->name }}</li>
                                    @endforeach
                                </ul>
                            </div>

                            <div>
                                <h4 class="font-medium text-sm text-gray-700 dark:text-gray-300 mb-2">Matches:</h4>

                                @php
                                    $createdMatches = $team->createdMatches->where('week_label', $weekLabel);
                                    $solvedMatches = $team->solvedMatches->where('week_label', $weekLabel);
                                @endphp

                                @if ($createdMatches->count() > 0 || $solvedMatches->count() > 0)
                                    <ul class="space-y-2">
                                        @foreach ($createdMatches as $match)
                                            <li>
                                                <a href="{{ route('matches.show', $match) }}" class="text-blue-600 dark:text-blue-400 hover:underline">
                                                    Created challenge for {{ $match->solver->name }} ({{ $match->status }})
                                                </a>
                                            </li>
                                        @endforeach

                                        @foreach ($solvedMatches as $match)
                                            <li>
                                                <a href="{{ route('matches.show', $match) }}" class="text-blue-600 dark:text-blue-400 hover:underline">
                                                    Solving challenge from {{ $match->creator->name }} ({{ $match->status }})
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="text-gray-500 dark:text-gray-400 italic">No matches yet.</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                <p class="text-gray-800 dark:text-gray-200">No teams available.</p>
            </div>
        @endforelse
    </div>
</x-layouts.app>
