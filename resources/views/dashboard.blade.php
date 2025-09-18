<x-layouts.app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            <a href="{{ route('teams.index') }}" class="flex flex-col items-center justify-center aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 p-4 transition-colors hover:bg-neutral-100 dark:hover:bg-neutral-800">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-12 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <span class="text-lg font-semibold">Teams</span>
                <span class="text-sm text-neutral-600 dark:text-neutral-400">Zeige alle Teams</span>
            </a>

            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 p-4 flex flex-col items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-12 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                <span class="text-lg font-semibold">Meine Challenges</span>
                <span class="text-sm text-neutral-600 dark:text-neutral-400">Du hast {{ auth()->user()->participants->flatMap->team->flatMap->createdMatches->count() }} erstellte Challenges</span>
            </div>

            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 p-4 flex flex-col items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-12 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                </svg>
                <span class="text-lg font-semibold">Meine Votes</span>
                <span class="text-sm text-neutral-600 dark:text-neutral-400">Du hast {{ auth()->user()->votes->count() }} Bewertungen abgegeben</span>
            </div>
        </div>

        <div class="p-6 bg-white shadow-sm rounded-xl border border-neutral-200 dark:bg-neutral-800 dark:border-neutral-700">
            <h2 class="text-xl font-semibold mb-4">Deine Teams</h2>

            @if(auth()->user()->teams->count() > 0)
                <div class="space-y-4">
                    @foreach(auth()->user()->teams as $team)
                    <div class="p-4 border rounded-lg dark:border-neutral-700">
                        <div class="flex justify-between items-center">
                            <div>
                                <h3 class="font-semibold text-lg">{{ $team->name }}</h3>
                                <p class="text-sm text-neutral-600 dark:text-neutral-400">{{ $team->week_label }}</p>
                            </div>
                            <div>
                                <span class="px-3 py-1 text-xs rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                    {{ $team->participants->count() }} Mitglieder
                                </span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <p class="font-medium text-zinc-800 dark:text-white">Du bist noch keinem Team zugewiesen.</p>
            @endif
        </div>
    </div>
</x-layouts.app>
