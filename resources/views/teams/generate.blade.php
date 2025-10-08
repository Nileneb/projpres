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
                        <div class="p-4 mb-4 rounded-lg">
                            <x-badge color="red" :pill="false">
                                {{ session('error') }}
                            </x-badge>
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
                        Verfügbare Benutzer: {{ count($users) }} ({{ count($activeUsers) }} aktiv)
                    </h3>

                    @if(count($activeUsers) < 4)
                        <div class="p-4 mb-4 border border-yellow-500 rounded-lg bg-yellow-50 dark:bg-yellow-900/20 dark:border-yellow-700">
                            <div class="flex items-start gap-2">
                                <svg class="w-5 h-5 text-yellow-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                    <path fill-rule="evenodd" d="M9.401 3.003c1.155-2 4.043-2 5.197 0l7.355 12.748c1.154 2-.29 4.5-2.599 4.5H4.645c-2.309 0-3.752-2.5-2.598-4.5L9.4 3.003zM12 8.25a.75.75 0 01.75.75v3.75a.75.75 0 01-1.5 0V9a.75.75 0 01.75-.75zm0 8.25a.75.75 0 100-1.5.75.75 0 000 1.5z" clip-rule="evenodd" />
                                </svg>
                                <p class="text-sm text-yellow-700 dark:text-yellow-300">
                                    <strong>Warnung:</strong> Es gibt zu wenige aktive Benutzer, um sinnvolle Teams zu erstellen. Mindestens 4 aktive Benutzer werden empfohlen.
                                </p>
                            </div>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                        @foreach($users as $user)
                            <div class="p-3 border border-gray-200 rounded-md dark:border-zinc-700 {{ $user->is_active ? '' : 'opacity-60' }}">
                                <div class="flex justify-between">
                                    <span class="font-medium dark:text-white">{{ $user->name }}</span>
                                    @if($user->is_active)
                                        <span class="px-2 py-1 text-xs text-green-800 bg-green-100 rounded-full dark:bg-green-800/20 dark:text-green-300">Aktiv</span>
                                    @else
                                        <span class="px-2 py-1 text-xs text-gray-800 bg-gray-100 rounded-full dark:bg-gray-700/30 dark:text-gray-300">Inaktiv</span>
                                    @endif
                                </div>
                                <span class="block text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
