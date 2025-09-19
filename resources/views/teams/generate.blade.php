<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-white">
            Teams generieren
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm dark:bg-zinc-800 sm:rounded-lg">
                <div class="p-6">
                    @if(session('error'))
                        <div class="p-4 mb-4 text-sm text-red-800 bg-red-100 rounded-lg dark:bg-red-900 dark:text-red-300">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('teams.generate.store') }}">
                        @csrf
                        <div class="mb-6">
                            <x-label for="week_label" value="Woche" />
                            <x-input id="week_label" name="week_label" type="text" class="block w-full mt-1" required value="{{ $currentWeekLabel }}" />
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Format: YYYY-KWnn (z.B. {{ $currentWeekLabel }})</p>
                        </div>

                        <div class="mb-6">
                            <x-label for="team_size" value="Team-Größe" />
                            <x-input id="team_size" name="team_size" type="number" min="2" max="10" class="block w-full mt-1" required value="4" />
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Anzahl der Personen pro Team</p>
                        </div>

                        @if($existingTeams > 0)
                            <div class="mb-6">
                                <label for="force" class="inline-flex items-center">
                                    <input type="checkbox" id="force" name="force" class="text-indigo-600 border-gray-300 rounded shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-600 dark:text-gray-300">Bestehende Teams überschreiben ({{ $existingTeams }} Teams)</span>
                                </label>
                                <p class="mt-1 text-sm text-red-500">Warnung: Dies wird alle bestehenden Teams und Zuweisungen für diese Woche löschen!</p>
                            </div>
                        @endif

                        <div class="flex items-center">
                            <x-ui.button>
                                Teams generieren
                            </x-ui.button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="mt-6 overflow-hidden bg-white shadow-sm dark:bg-zinc-800 sm:rounded-lg">
                <div class="p-6">
                    <h3 class="mb-4 text-lg font-medium text-gray-900 dark:text-white">
                        Verfügbare Benutzer: {{ count($users) }}
                    </h3>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                        @foreach($users as $user)
                            <div class="p-3 border border-gray-200 rounded-md dark:border-zinc-700">
                                <span class="font-medium dark:text-white">{{ $user->name }}</span>
                                <span class="block text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
