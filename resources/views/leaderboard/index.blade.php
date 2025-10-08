<x-layouts.app :title="__('Leaderboard')">
    <div class="p-6 bg-white shadow-sm rounded-xl border border-neutral-200 dark:bg-neutral-800 dark:border-neutral-700">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">{{ __('User Points Leaderboard') }}</h1>
            
            <div class="flex space-x-3">
                <!-- Timeframe filter -->
                <div>
                    <a href="{{ route('leaderboard.index', ['timeframe' => 'all_time', 'include_archived' => $includeArchived]) }}" 
                       class="px-3 py-1 rounded-l-md {{ $timeframe === 'all_time' ? 'bg-blue-600 text-white' : 'bg-gray-200 dark:bg-gray-700' }}">
                        All Time
                    </a>
                    <a href="{{ route('leaderboard.index', ['timeframe' => 'current_week', 'include_archived' => $includeArchived]) }}" 
                       class="px-3 py-1 rounded-r-md {{ $timeframe === 'current_week' ? 'bg-blue-600 text-white' : 'bg-gray-200 dark:bg-gray-700' }}">
                        Current Week
                    </a>
                </div>
                
                <!-- Archive filter -->
                <div>
                    <a href="{{ route('leaderboard.index', ['timeframe' => $timeframe, 'include_archived' => $includeArchived ? 0 : 1]) }}" 
                       class="px-3 py-1 rounded {{ $includeArchived ? 'bg-amber-500 text-white' : 'bg-gray-200 dark:bg-gray-700' }}">
                        {{ $includeArchived ? 'Hide Archived' : 'Include Archived' }}
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Filter information -->
        <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
            @if($timeframe === 'all_time')
                <p>Showing all-time scores {{ $includeArchived ? 'including archived teams' : 'for active teams only' }}.</p>
            @else
                <p>Showing scores for the current week ({{ $currentWeekLabel }}).</p>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">Rank</th>
                        <th scope="col" class="px-6 py-3">User</th>
                        <th scope="col" class="px-6 py-3">Total Points</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($leaderboard as $index => $user)
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700
                            {{ auth()->id() === $user->id ? 'bg-blue-50 dark:bg-blue-900' : '' }}">
                            <td class="px-6 py-4 font-medium">
                                {{ $index + 1 }}
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                {{ $user->name }}
                                @if(auth()->id() === $user->id)
                                    <span class="ml-2 text-xs text-blue-600 dark:text-blue-400">(You)</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 font-bold">
                                {{ $user->total_points }}
                            </td>
                        </tr>
                    @empty
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                            <td colspan="3" class="px-6 py-4 text-center">No data available</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.app>
