<x-layouts.app :title="__('Teams')">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Teams') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @forelse ($weekLabels as $weekLabel)
                        <div class="mb-8">
                            <h2 class="text-xl font-bold mb-4">{{ $weekLabel }}</h2>

                            <div class="space-y-4">
                                @foreach ($teamsByWeek[$weekLabel] as $team)
                                    <div class="border p-4 rounded-lg">
                                        <h3 class="text-lg font-semibold">{{ $team->name }}</h3>

                                        <div class="mt-4">
                                            <h4 class="font-medium text-sm text-gray-700 mb-2">Team Members:</h4>
                                            <ul class="list-disc ml-5">
                                                @foreach ($team->users as $user)
                                                    <li>{{ $user->name }}</li>
                                                @endforeach
                                            </ul>
                                        </div>

                                        <div class="mt-4">
                                            <h4 class="font-medium text-sm text-gray-700 mb-2">Matches:</h4>

                                            @php
                                                $createdMatches = $team->createdMatches->where('week_label', $weekLabel);
                                                $solvedMatches = $team->solvedMatches->where('week_label', $weekLabel);
                                            @endphp

                                            @if ($createdMatches->count() > 0 || $solvedMatches->count() > 0)
                                                <ul class="space-y-2">
                                                    @foreach ($createdMatches as $match)
                                                        <li>
                                                            <a href="{{ route('matches.show', $match) }}" class="text-blue-600 hover:underline">
                                                                Created challenge for {{ $match->solver->name }} ({{ $match->status }})
                                                            </a>
                                                        </li>
                                                    @endforeach

                                                    @foreach ($solvedMatches as $match)
                                                        <li>
                                                            <a href="{{ route('matches.show', $match) }}" class="text-blue-600 hover:underline">
                                                                Solving challenge from {{ $match->creator->name }} ({{ $match->status }})
                                                            </a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                <p class="text-gray-500 italic">No matches yet.</p>
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
