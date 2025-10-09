<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ $title ?? config('app.name') }}</title>

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @fluxAppearance
    </head>
    <body class="min-h-screen bg-white antialiased dark:bg-zinc-800">
        <div class="flex min-h-screen overflow-hidden">
            <!-- Sidebar -->
            <aside class="w-64 flex-shrink-0 border-r border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
                <div class="sticky top-0 h-screen overflow-y-auto p-4">
                    <a href="{{ route('dashboard') }}" class="mb-5 flex items-center space-x-2 rtl:space-x-reverse">
                        <x-ui.brand variant="sidebar" />
                    </a>

                    <!-- Navigation -->
                    <nav class="mt-6 space-y-6">
                        <div>
                            <h2 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">{{ __('Platform') }}</h2>
                            <ul class="space-y-2">
                                <li>
                                    <a href="{{ route('dashboard') }}" class="flex items-center px-3 py-2 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-200 dark:hover:bg-gray-700 {{ request()->routeIs('dashboard') ? 'bg-gray-200 dark:bg-gray-700' : '' }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                        </svg>
                                        <span>Dashboard</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('matches.index') }}" class="flex items-center px-3 py-2 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-200 dark:hover:bg-gray-700 {{ request()->routeIs('matches.*') ? 'bg-gray-200 dark:bg-gray-700' : '' }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                        </svg>
                                        <span>Challenges</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('teams.index') }}" class="flex items-center px-3 py-2 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-200 dark:hover:bg-gray-700 {{ request()->routeIs('teams.*') ? 'bg-gray-200 dark:bg-gray-700' : '' }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                        <span>Teams</span>
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <div>
                            <h2 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">{{ __('Links') }}</h2>
                            <ul class="space-y-2">
                                <li>
                                    <a href="https://github.com/laravel/livewire-starter-kit" target="_blank" class="flex items-center px-3 py-2 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-200 dark:hover:bg-gray-700">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                                        </svg>
                                        <span>Repository</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="https://laravel.com/docs/starter-kits#livewire" target="_blank" class="flex items-center px-3 py-2 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-200 dark:hover:bg-gray-700">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                        </svg>
                                        <span>Documentation</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </nav>

                    <!-- User section -->
                    <div class="mt-auto pt-6">
                        <!-- Language Switcher -->
                        <div class="mb-4">
                            <x-language-switcher />
                        </div>

                        <!-- User Menu -->
                        <div class="flex items-center p-3 bg-white dark:bg-gray-800 rounded-lg">
                            <div class="flex-shrink-0">
                                <div class="relative flex h-10 w-10 overflow-hidden rounded-full">
                                    <div class="flex h-full w-full items-center justify-center rounded-full bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                                        {{ auth()->user()->initials() }}
                                    </div>
                                </div>
                            </div>
                            <div class="ml-3 flex-1">
                                <p class="text-sm font-medium text-gray-700 dark:text-white">{{ auth()->user()->name }}</p>
                                <div class="flex items-center text-xs text-gray-500 dark:text-gray-400">
                                    <a href="{{ route('profile.edit') }}" class="hover:underline">Settings</a>
                                    <span class="mx-1">•</span>
                                    <form method="POST" action="{{ route('logout') }}" class="inline">
                                        @csrf
                                        <button type="submit" class="hover:underline">Log Out</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- Main Content -->
            <main class="flex-1 relative overflow-y-auto">
                <div class="p-6 md:p-8">
                    {{ $slot }}
                </div>
            </main>
        </div>

        @fluxScripts
    </body>
</html>
