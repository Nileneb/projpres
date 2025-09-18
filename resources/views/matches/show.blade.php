<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Match Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <h2 class="text-lg font-medium text-gray-900">
                        {{ __('Challenge Information') }}
                    </h2>

                    <div class="mt-6">
                        <div class="bg-gray-100 p-4 rounded-lg mb-4">
                            <h3 class="font-bold">{{ __('Challenge Text') }}</h3>
                            <div class="mt-2">
                                {{ $match->challenge_text ?? 'No challenge text provided yet.' }}
                            </div>
                        </div>

                        @if($match->submission_url)
                            <div class="bg-gray-100 p-4 rounded-lg mb-4">
                                <h3 class="font-bold">{{ __('Submission URL') }}</h3>
                                <div class="mt-2">
                                    <a href="{{ $match->submission_url }}" class="text-blue-500 hover:underline" target="_blank">
                                        {{ $match->submission_url }}
                                    </a>
                                </div>
                            </div>
                        @endif

                        @if($match->status === 'submitted')
                            <div class="bg-gray-100 p-4 rounded-lg mb-4">
                                <h3 class="font-bold">{{ __('Average Score') }}</h3>
                                <div class="mt-2">
                                    {{ $match->avgScore() ?? 'No votes yet' }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            @can('updateChallenge', $match)
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <div class="max-w-xl">
                        <h2 class="text-lg font-medium text-gray-900">
                            {{ __('Update Challenge') }}
                        </h2>

                        <form method="POST" action="{{ route('matches.updateChallenge', $match) }}" class="mt-6 space-y-6">
                            @csrf

                            <div>
                                <x-input-label for="challenge_text" :value="__('Challenge Text')" />
                                <x-text-area id="challenge_text" name="challenge_text" class="mt-1 block w-full" rows="6">{{ old('challenge_text', $match->challenge_text) }}</x-text-area>
                                <x-input-error class="mt-2" :messages="$errors->get('challenge_text')" />
                            </div>

                            <div class="flex items-center gap-4">
                                <x-primary-button>{{ __('Update Challenge') }}</x-primary-button>
                            </div>
                        </form>
                    </div>
                </div>
            @endcan

            @can('submit', $match)
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <div class="max-w-xl">
                        <h2 class="text-lg font-medium text-gray-900">
                            {{ __('Submit Solution') }}
                        </h2>

                        <form method="POST" action="{{ route('matches.submit', $match) }}" class="mt-6 space-y-6">
                            @csrf

                            <div>
                                <x-input-label for="submission_url" :value="__('Submission URL')" />
                                <x-text-input id="submission_url" name="submission_url" type="url" class="mt-1 block w-full"
                                    value="{{ old('submission_url', $match->submission_url) }}" required />
                                <x-input-error class="mt-2" :messages="$errors->get('submission_url')" />
                            </div>

                            <div class="flex items-center gap-4">
                                <x-primary-button>{{ __('Submit Solution') }}</x-primary-button>
                            </div>
                        </form>
                    </div>
                </div>
            @endcan

            @can('create', ['App\Models\Vote', $match])
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <div class="max-w-xl">
                        <h2 class="text-lg font-medium text-gray-900">
                            {{ __('Submit Vote') }}
                        </h2>

                        <form method="POST" action="{{ route('votes.store', $match) }}" class="mt-6 space-y-6">
                            @csrf

                            <div>
                                <x-input-label for="score" :value="__('Score (1-5)')" />
                                <select id="score" name="score" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="">Select a score</option>
                                    @for ($i = 1; $i <= 5; $i++)
                                        <option value="{{ $i }}" {{ old('score') == $i ? 'selected' : '' }}>{{ $i }}</option>
                                    @endfor
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('score')" />
                            </div>

                            <div>
                                <x-input-label for="comment" :value="__('Comment (Optional)')" />
                                <x-text-area id="comment" name="comment" class="mt-1 block w-full" rows="3">{{ old('comment') }}</x-text-area>
                                <x-input-error class="mt-2" :messages="$errors->get('comment')" />
                            </div>

                            <div class="flex items-center gap-4">
                                <x-primary-button>{{ __('Submit Vote') }}</x-primary-button>
                            </div>
                        </form>
                    </div>
                </div>
            @endcan
        </div>
    </div>
</x-app-layout>
