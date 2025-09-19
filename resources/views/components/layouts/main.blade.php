@props([
    'type' => 'app',     // app oder auth
    'variant' => 'sidebar', // auth: card, simple, split | app: sidebar, header
    'title' => null,
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head', ['title' => $title])
    </head>
    <body class="min-h-screen bg-white antialiased dark:bg-zinc-800 {{ $type === 'auth' ? 'dark:bg-linear-to-b dark:from-neutral-950 dark:to-neutral-900' : '' }}">

        {{-- Auth Layouts --}}
        @if ($type === 'auth')
            @if ($variant === 'card')
                <div class="bg-muted flex min-h-svh flex-col items-center justify-center gap-6 p-6 md:p-10">
                    <div class="flex w-full max-w-md flex-col gap-6">
                        <a href="{{ route('home') }}" class="flex flex-col items-center gap-2 font-medium" wire:navigate>
                            <span class="flex h-9 w-9 items-center justify-center rounded-md">
                                <x-ui.brand :showText="false" variant="auth" iconClass="size-9 fill-current text-black dark:text-white" />
                            </span>

                            <span class="sr-only">{{ config('app.name', 'Laravel') }}</span>
                        </a>

                        <div class="flex flex-col gap-6">
                            <div class="rounded-xl border bg-white dark:bg-stone-950 dark:border-stone-800 text-stone-800 shadow-xs">
                                <div class="px-10 py-8">{{ $slot }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif ($variant === 'simple')
                <div class="bg-background flex min-h-svh flex-col items-center justify-center gap-6 p-6 md:p-10">
                    <div class="flex w-full max-w-sm flex-col gap-2">
                        <a href="{{ route('home') }}" class="flex flex-col items-center gap-2 font-medium" wire:navigate>
                            <span class="flex h-9 w-9 mb-1 items-center justify-center rounded-md">
                                <x-ui.brand :showText="false" variant="auth" iconClass="size-9 fill-current text-black dark:text-white" />
                            </span>
                            <span class="sr-only">{{ config('app.name', 'Laravel') }}</span>
                        </a>
                        <div class="flex flex-col gap-6">
                            {{ $slot }}
                        </div>
                    </div>
                </div>
            @elseif ($variant === 'split')
                <div class="relative grid h-dvh flex-col items-center justify-center px-8 sm:px-0 lg:max-w-none lg:grid-cols-2 lg:px-0">
                    <div class="bg-muted relative hidden h-full flex-col p-10 text-white lg:flex dark:border-e dark:border-neutral-800">
                        <div class="absolute inset-0 bg-neutral-900"></div>
                        <a href="{{ route('home') }}" class="relative z-20 flex items-center text-lg font-medium" wire:navigate>
                            <span class="flex h-10 w-10 items-center justify-center rounded-md">
                                <x-ui.brand :showText="false" variant="auth" iconClass="me-2 h-7 fill-current text-white" />
                            </span>
                            {{ config('app.name', 'Laravel') }}
                        </a>

                        @php
                            [$message, $author] = str(Illuminate\Foundation\Inspiring::quotes()->random())->explode('-');
                        @endphp

                        <div class="relative z-20 mt-auto">
                            <blockquote class="space-y-2">
                                <flux:heading size="lg">&ldquo;{{ trim($message) }}&rdquo;</flux:heading>
                                <footer><flux:heading>{{ trim($author) }}</flux:heading></footer>
                            </blockquote>
                        </div>
                    </div>
                    <div class="w-full lg:p-8">
                        <div class="mx-auto flex w-full flex-col justify-center space-y-6 sm:w-[350px]">
                            <a href="{{ route('home') }}" class="z-20 flex flex-col items-center gap-2 font-medium lg:hidden" wire:navigate>
                                <span class="flex h-9 w-9 items-center justify-center rounded-md">
                                    <x-ui.brand :showText="false" variant="auth" iconClass="size-9 fill-current text-black dark:text-white" />
                                </span>

                                <span class="sr-only">{{ config('app.name', 'Laravel') }}</span>
                            </a>
                            {{ $slot }}
                        </div>
                    </div>
                </div>
            @endif

        {{-- App Layouts --}}
        @else
            @if ($variant === 'header')
                <flux:header container class="border-b border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
                    <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

                    <a href="{{ route('dashboard') }}" class="ms-2 me-5 flex items-center space-x-2 rtl:space-x-reverse lg:ms-0" wire:navigate>
                        <x-ui.brand variant="header" />
                    </a>

                    <flux:navbar class="-mb-px max-lg:hidden">
                        <flux:navbar.item icon="layout-grid" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                            Dashboard
                        </flux:navbar.item>
                        <flux:navbar.item icon="trophy" :href="route('matches.index')" :current="request()->routeIs('matches.*')" wire:navigate>
                            Challenges
                        </flux:navbar.item>
                        <flux:navbar.item icon="users" :href="route('teams.assignments')" :current="request()->routeIs('teams.*')" wire:navigate>
                            Teams
                        </flux:navbar.item>
                        @can('manage-teams')
                            <flux:navbar.item icon="sparkles" :href="route('teams.generate')" :current="request()->routeIs('teams.generate')" wire:navigate>
                                Teams generieren
                            </flux:navbar.item>
                        @endcan
                    </flux:navbar>

                    <flux:spacer />

                    <flux:navbar class="me-1.5 space-x-0.5 rtl:space-x-reverse py-0!">
                        <flux:tooltip :content="__('Search')" position="bottom">
                            <flux:navbar.item class="!h-10 [&>div>svg]:size-5" icon="magnifying-glass" href="#" :label="__('Search')" />
                        </flux:tooltip>
                        <flux:tooltip :content="__('Repository')" position="bottom">
                            <flux:navbar.item
                                class="h-10 max-lg:hidden [&>div>svg]:size-5"
                                icon="folder-git-2"
                                href="https://github.com/laravel/livewire-starter-kit"
                                target="_blank"
                                :label="__('Repository')"
                            />
                        </flux:tooltip>
                        <flux:tooltip :content="__('Documentation')" position="bottom">
                            <flux:navbar.item
                                class="h-10 max-lg:hidden [&>div>svg]:size-5"
                                icon="book-open-text"
                                href="https://laravel.com/docs/starter-kits#livewire"
                                target="_blank"
                                label="Documentation"
                            />
                        </flux:tooltip>
                    </flux:navbar>

                    <!-- Desktop User Menu -->
                    <flux:dropdown position="top" align="end">
                        <flux:profile
                            class="cursor-pointer"
                            :initials="auth()->user()->initials()"
                        />

                        <flux:menu>
                            <flux:menu.radio.group>
                                <div class="p-0 text-sm font-normal">
                                    <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                        <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                            <span
                                                class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white"
                                            >
                                                {{ auth()->user()->initials() }}
                                            </span>
                                        </span>

                                        <div class="grid flex-1 text-start text-sm leading-tight">
                                            <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                            <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                        </div>
                                    </div>
                                </div>
                            </flux:menu.radio.group>

                            <flux:menu.separator />

                            <flux:menu.radio.group>
                                <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>Settings</flux:menu.item>
                            </flux:menu.radio.group>

                            <flux:menu.separator />

                            <form method="POST" action="{{ route('logout') }}" class="w-full">
                                @csrf
                                <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full" data-test="logout-button">
                                    Log Out
                                </flux:menu.item>
                            </form>
                        </flux:menu>
                    </flux:dropdown>
                </flux:header>

                <!-- Mobile Menu -->
                <flux:sidebar stashable sticky class="lg:hidden border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
                    <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

                    <a href="{{ route('dashboard') }}" class="ms-1 flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
                        <x-ui.brand variant="header" />
                    </a>

                    <flux:navlist variant="outline">
                        <flux:navlist.group :heading="__('Platform')">
                            <flux:navlist.item icon="layout-grid" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                            Dashboard
                            </flux:navlist.item>
                            <flux:navlist.item icon="trophy" :href="route('matches.index')" :current="request()->routeIs('matches.*')" wire:navigate>
                            Challenges
                            </flux:navlist.item>
                            <flux:navlist.item icon="users" :href="route('teams.assignments')" :current="request()->routeIs('teams.*')" wire:navigate>
                            Teams
                            </flux:navlist.item>
                            @can('manage-teams')
                            <flux:navlist.item icon="sparkles" :href="route('teams.generate')" :current="request()->routeIs('teams.generate')" wire:navigate>
                            Teams generieren
                            </flux:navlist.item>
                            @endcan
                        </flux:navlist.group>
                    </flux:navlist>

                    <flux:spacer />

                    <flux:navlist variant="outline">
                        <flux:navlist.item icon="folder-git-2" href="https://github.com/laravel/livewire-starter-kit" target="_blank">
                        Repository
                        </flux:navlist.item>

                        <flux:navlist.item icon="book-open-text" href="https://laravel.com/docs/starter-kits#livewire" target="_blank">
                        Documentation
                        </flux:navlist.item>
                    </flux:navlist>
                </flux:sidebar>
            @elseif ($variant === 'sidebar')
                <flux:sidebar sticky stashable class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
                    <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

                    <a href="{{ route('dashboard') }}" class="me-5 flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
                        <x-ui.brand variant="sidebar" />
                    </a>

                    <flux:navlist variant="outline">
                        <flux:navlist.group :heading="__('Platform')" class="grid">
                            <flux:navlist.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>Dashboard</flux:navlist.item>
                            <flux:navlist.item icon="trophy" :href="route('matches.index')" :current="request()->routeIs('matches.*')" wire:navigate>Challenges</flux:navlist.item>
                            <flux:navlist.item icon="users" :href="route('teams.index')" :current="request()->routeIs('teams.*')" wire:navigate>Teams</flux:navlist.item>
                        </flux:navlist.group>
                    </flux:navlist>

                    <flux:spacer />

                    <flux:navlist variant="outline">
                        <flux:navlist.item icon="folder-git-2" href="https://github.com/laravel/livewire-starter-kit" target="_blank">
                        Repository
                        </flux:navlist.item>

                        <flux:navlist.item icon="book-open-text" href="https://laravel.com/docs/starter-kits#livewire" target="_blank">
                        Documentation
                        </flux:navlist.item>
                    </flux:navlist>

                    <!-- Desktop User Menu -->
                    <flux:dropdown class="hidden lg:block" position="bottom" align="start">
                        <flux:profile
                            :name="auth()->user()->name"
                            :initials="auth()->user()->initials()"
                            icon:trailing="chevrons-up-down"
                        />

                        <flux:menu class="w-[220px]">
                            <flux:menu.radio.group>
                                <div class="p-0 text-sm font-normal">
                                    <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                        <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                            <span
                                                class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white"
                                            >
                                                {{ auth()->user()->initials() }}
                                            </span>
                                        </span>

                                        <div class="grid flex-1 text-start text-sm leading-tight">
                                            <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                            <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                        </div>
                                    </div>
                                </div>
                            </flux:menu.radio.group>

                            <flux:menu.separator />

                            <flux:menu.radio.group>
                                <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
                            </flux:menu.radio.group>

                            <flux:menu.separator />

                            <form method="POST" action="{{ route('logout') }}" class="w-full">
                                @csrf
                                <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full" data-test="logout-button">
                                    {{ __('Log Out') }}
                                </flux:menu.item>
                            </form>
                        </flux:menu>
                    </flux:dropdown>
                </flux:sidebar>

                <!-- Mobile User Menu -->
                <flux:header class="lg:hidden">
                    <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

                    <flux:spacer />

                    <flux:dropdown position="top" align="end">
                        <flux:profile
                            :initials="auth()->user()->initials()"
                            icon-trailing="chevron-down"
                        />

                        <flux:menu>
                            <flux:menu.radio.group>
                                <div class="p-0 text-sm font-normal">
                                    <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                        <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                            <span
                                                class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white"
                                            >
                                                {{ auth()->user()->initials() }}
                                            </span>
                                        </span>

                                        <div class="grid flex-1 text-start text-sm leading-tight">
                                            <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                            <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                        </div>
                                    </div>
                                </div>
                            </flux:menu.radio.group>

                            <flux:menu.separator />

                            <flux:menu.radio.group>
                                <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
                            </flux:menu.radio.group>

                            <flux:menu.separator />

                            <form method="POST" action="{{ route('logout') }}" class="w-full">
                                @csrf
                                <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full" data-test="logout-button">
                                    {{ __('Log Out') }}
                                </flux:menu.item>
                            </form>
                        </flux:menu>
                    </flux:dropdown>
                </flux:header>
            @endif
        @endif

        {{ $slot }}

        @fluxScripts
    </body>
</html>
