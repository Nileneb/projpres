<x-layouts.app :title="__('Submit Solution')">
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight mb-4">
        {{ __('Submit Solution') }}
    </h2>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-zinc-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="mb-6">
                        <h3 class="text-lg font-bold mb-2">Challenge Details:</h3>
                        <p><span class="font-medium">From:</span> {{ $match->creator->name }}</p>
                        <p><span class="font-medium">To:</span> {{ $match->solver->name }}</p>
                        <p class="mt-2 p-4 bg-gray-100 dark:bg-zinc-700 text-gray-800 dark:text-gray-200 rounded-md">{{ $match->challenge_text }}</p>
                    </div>

                    <form method="POST" action="{{ route('matches.submitSolution', $match) }}" class="mt-6">
                        @csrf

                        <div>
                            <x-ui.label for="submission_url" :value="__('Submission URL')" />
                            <x-ui.input id="submission_url" class="block mt-1 w-full" type="url" name="submission_url" :value="old('submission_url')" required />
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Provide a URL to your solution (e.g., YouTube video, GitHub repository, etc.)</p>
                            <x-ui.input-error :messages="$errors->get('submission_url')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-ui.label for="submission_notes" :value="__('Submission Notes (optional)')" />
                            <x-ui.textarea id="submission_notes" class="block mt-1 w-full" name="submission_notes" rows="4">{{ old('submission_notes') }}</x-ui.textarea>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Add any additional notes or context about your solution.</p>
                            <x-ui.input-error :messages="$errors->get('submission_notes')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-ui.button class="ml-4">
                                {{ __('Submit Solution') }}
                            </x-ui.button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
