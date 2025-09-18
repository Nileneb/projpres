<x-layouts.app :title="__('Match Details')">
    <div class="space-y-6">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-6">{{ __('Challenge Information') }}</h2>

            <div class="space-y-4">
                <div class="bg-gray-100 dark:bg-gray-700 p-4 rounded-lg">
                    <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-2">{{ __('Challenge Text') }}</h3>
                    <div class="text-gray-800 dark:text-gray-200">
                        @if($match->challenge_text)
                            {{ $match->challenge_text }}
                        @else
                            <span class="italic text-gray-600 dark:text-gray-400">No challenge text provided yet.</span>
                        @endif
                    </div>
                </div>

                @if($match->submission_url)
                    <div class="bg-gray-100 dark:bg-gray-700 p-4 rounded-lg">
                        <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-2">{{ __('Submission URL') }}</h3>
                        <div>
                            <a href="{{ $match->submission_url }}" class="text-blue-600 dark:text-blue-400 hover:underline" target="_blank">
                                {{ $match->submission_url }}
                            </a>
                        </div>
                    </div>
                @endif

                @if($match->status === 'submitted')
                    <div class="bg-gray-100 dark:bg-gray-700 p-4 rounded-lg">
                        <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-2">{{ __('Average Score') }}</h3>
                        <div class="text-gray-800 dark:text-gray-200">
                            {{ $match->avgScore() ?? 'No votes yet' }}
                        </div>
                    </div>
                @endif
            </div>
        </div>

        @can('updateChallenge', $match)
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-6">{{ __('Update Challenge') }}</h2>

                <form method="POST" action="{{ route('matches.updateChallenge', $match) }}" class="space-y-6">
                    @csrf

                    <div>
                        <label for="challenge_text" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('Challenge Text') }}
                        </label>
                        <textarea 
                            id="challenge_text"
                            name="challenge_text" 
                            rows="6"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-200"
                        >{{ old('challenge_text', $match->challenge_text) }}</textarea>
                        @error('challenge_text')
                            <div class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        {{ __('Update Challenge') }}
                    </button>
                </form>
            </div>
        @endcan

        @can('submit', $match)
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-6">{{ __('Submit Solution') }}</h2>

                <form method="POST" action="{{ route('matches.submit', $match) }}" class="space-y-6">
                    @csrf

                    <div>
                        <label for="submission_url" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('Submission URL') }}
                        </label>
                        <input 
                            id="submission_url"
                            name="submission_url" 
                            type="url" 
                            value="{{ old('submission_url', $match->submission_url) }}" 
                            required
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-200"
                        />
                        @error('submission_url')
                            <div class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        {{ __('Submit Solution') }}
                    </button>
                </form>
            </div>
        @endcan

        @can('create', ['App\Models\Vote', $match])
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-6">{{ __('Submit Vote') }}</h2>

                <form method="POST" action="{{ route('votes.store', $match) }}" class="space-y-6">
                    @csrf

                    <div>
                        <label for="score" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('Score (1-5)') }}
                        </label>
                        <select 
                            id="score"
                            name="score" 
                            required
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-200"
                        >
                            <option value="">Select a score</option>
                            @for ($i = 1; $i <= 5; $i++)
                                <option value="{{ $i }}" {{ old('score') == $i ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                        @error('score')
                            <div class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label for="comment" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('Comment (Optional)') }}
                        </label>
                        <textarea 
                            id="comment"
                            name="comment" 
                            rows="3"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-200"
                        >{{ old('comment') }}</textarea>
                        @error('comment')
                            <div class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        {{ __('Submit Vote') }}
                    </button>
                </form>
            </div>
        @endcan
    </div>
</x-layouts.app>
