<x-layouts.app :title="__('Create Challenge')">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Create New Challenge') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white dark:bg-zinc-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        {{ __('Create Challenge for Team: ') }} {{ $solverTeam->name }}
                    </h2>

                    <form method="POST" action="{{ route('matches.store') }}" class="mt-6 space-y-6">
                        @csrf
                        <input type="hidden" name="solver_team_id" value="{{ $solverTeam->id }}">
                        <input type="hidden" name="week_label" value="{{ $weekLabel }}">

                        <div>
                            <x-ui.label for="challenge_text" :value="__('Challenge Description')" />
                            <x-ui.textarea id="challenge_text" name="challenge_text" class="mt-1 block w-full" rows="6"
                                placeholder="Describe your challenge here. Be clear and specific about what the other team needs to accomplish.">{{ old('challenge_text') }}</x-ui.textarea>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">This team will have {{ $timeLimit }} minutes to complete this challenge.</p>
                            <x-ui.input-error class="mt-2" :messages="$errors->get('challenge_text')" />
                        </div>

                        <div>
                            <x-ui.label for="time_limit_minutes" :value="__('Time Limit (minutes)')" />
                            <x-ui.input id="time_limit_minutes" name="time_limit_minutes" type="number" class="mt-1 block w-full"
                                value="{{ old('time_limit_minutes', 20) }}" min="5" max="60" />
                            <x-ui.input-error class="mt-2" :messages="$errors->get('time_limit_minutes')" />
                        </div>

                        <div class="flex items-center gap-4">
                            <x-ui.button>{{ __('Create Challenge') }}</x-ui.button>
                            <a href="{{ route('teams.index') }}" class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200">{{ __('Cancel') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
