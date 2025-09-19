<x-layouts.app :title="__('Match Details')">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Match Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white dark:bg-zinc-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-white">
                        {{ __('Challenge Information') }}
                    </h2>

                    <div class="mt-6">
                        <div class="flex justify-between items-center mb-4">
                            <div>
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Creator:</span>
                                <span class="ml-1 text-gray-800 dark:text-gray-200">{{ $match->creator->name }}</span>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Solver:</span>
                                <span class="ml-1 text-gray-800 dark:text-gray-200">{{ $match->solver->name }}</span>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Status:</span>
                                <span class="ml-1 px-2 py-1 text-xs rounded-full
                                    @if($match->status == 'pending') bg-yellow-100 text-yellow-800 @endif
                                    @if($match->status == 'submitted') bg-green-100 text-green-800 @endif
                                    @if($match->status == 'closed') bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 @endif
                                ">{{ ucfirst($match->status) }}</span>
                            </div>
                        </div>

                        <div class="bg-gray-100 dark:bg-zinc-700 p-4 rounded-lg mb-4">
                            <div class="flex justify-between items-center">
                                <h3 class="font-bold text-gray-900 dark:text-white">{{ __('Challenge Description') }}</h3>
                                <span class="text-sm text-gray-500 dark:text-gray-400">Time Limit: {{ $match->time_limit_minutes }} minutes</span>
                            </div>
                            <div class="mt-2 whitespace-pre-line text-gray-800 dark:text-gray-200">
                                {{ $match->challenge_text ?? 'No challenge text provided yet.' }}
                            </div>
                        </div>

                        @if($match->submission_url)
                            <div class="bg-gray-100 dark:bg-zinc-700 p-4 rounded-lg mb-4">
                                <div class="flex justify-between items-center">
                                    <h3 class="font-bold text-gray-900 dark:text-white">{{ __('Submission URL') }}</h3>
                                    @if($match->submitted_at)
                                        <span class="text-sm text-gray-500 dark:text-gray-400">Submitted on: {{ $match->submitted_at->format('M d, Y H:i') }}</span>
                                    @endif
                                </div>
                                <div class="mt-2">
                                    <a href="{{ $match->submission_url }}" class="text-blue-500 dark:text-blue-400 hover:underline flex items-center" target="_blank">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                        </svg>
                                        {{ $match->submission_url }}
                                    </a>
                                </div>
                            </div>
                        @endif

                        @if($match->status === 'submitted')
                            <div class="bg-gray-100 dark:bg-zinc-700 p-4 rounded-lg mb-4">
                                <h3 class="font-bold text-gray-900 dark:text-white">{{ __('Average Score') }}</h3>
                                <div class="mt-2 flex items-center">
                                    @php
                                        $avgScore = $match->avgScore() ?: 0;
                                        $fullStars = floor($avgScore);
                                        $halfStar = $avgScore - $fullStars >= 0.5;
                                        $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);
                                    @endphp

                                    <div class="flex text-yellow-400">
                                        @for ($i = 0; $i < $fullStars; $i++)
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                        @endfor

                                        @if ($halfStar)
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" clip-path="polygon(0 0, 10px 0, 10px 20px, 0 20px)"></path>
                                            </svg>
                                        @endif

                                        @for ($i = 0; $i < $emptyStars; $i++)
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                        @endfor
                                    </div>

                                    <span class="ml-2 text-lg font-bold text-gray-800 dark:text-gray-200">{{ number_format($avgScore, 1) }}</span>
                                    <span class="ml-2 text-gray-500 dark:text-gray-400">({{ $match->votes->count() }} votes)</span>
                                </div>
                            </div>

                            <div class="bg-gray-100 dark:bg-zinc-700 p-4 rounded-lg mb-4">
                                <h3 class="font-bold text-gray-900 dark:text-white">{{ __('Recent Votes') }}</h3>
                                <div class="mt-2 space-y-3">
                                    @forelse ($match->votes->take(3) as $vote)
                                        <div class="border-b pb-2 last:border-0">
                                            <div class="flex justify-between items-center">
                                                <span class="font-medium text-gray-800 dark:text-gray-200">{{ $vote->user->name }}</span>
                                                <div class="flex text-yellow-400">
                                                    @for ($i = 0; $i < $vote->score; $i++)
                                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                        </svg>
                                                    @endfor
                                                </div>
                                            </div>
                                            @if ($vote->comment)
                                                <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">{{ $vote->comment }}</p>
                                            @endif
                                        </div>
                                    @empty
                                        <p class="text-gray-500 dark:text-gray-400 italic">No votes yet.</p>
                                    @endforelse
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            @can('updateChallenge', $match)
                <div class="p-4 sm:p-8 bg-white dark:bg-zinc-800 shadow sm:rounded-lg">
                    <div class="max-w-xl">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-white">
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
                <div class="p-4 sm:p-8 bg-white dark:bg-zinc-800 shadow sm:rounded-lg">
                    <div class="max-w-xl">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-white">
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
                <div class="p-4 sm:p-8 bg-white dark:bg-zinc-800 shadow sm:rounded-lg">
                    <div class="max-w-xl">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-white">
                            {{ __('Submit Vote') }}
                        </h2>

                        <form method="POST" action="{{ route('votes.store', $match) }}" class="mt-6 space-y-6">
                            @csrf

                            <div>
                                <x-input-label for="score" :value="__('Score (1-5)')" />
                                <select id="score" name="score" class="mt-1 block w-full border-gray-300 dark:border-zinc-600 dark:bg-zinc-700 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
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
</x-layouts.app>
