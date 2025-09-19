@props([
    'variant' => 'header', // header, sidebar
])

@if($variant === 'header')
    <flux:navbar class="-mb-px max-lg:hidden">
        <flux:navbar.item icon="layout-grid" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
            {{ __('Dashboard') }}
        </flux:navbar.item>
        <flux:navbar.item icon="trophy" :href="route('matches.index')" :current="request()->routeIs('matches.*')" wire:navigate>
            {{ __('Challenges') }}
        </flux:navbar.item>
        <flux:navbar.item icon="users" :href="route('teams.assignments')" :current="request()->routeIs('teams.*')" wire:navigate>
            {{ __('Teams') }}
        </flux:navbar.item>
        @can('manage-teams')
            <flux:navbar.item icon="sparkles" :href="route('teams.generate')" :current="request()->routeIs('teams.generate')" wire:navigate>
                {{ __('Teams generieren') }}
            </flux:navbar.item>
        @endcan
    </flux:navbar>
@else
    <flux:navlist variant="outline">
        <flux:navlist.group :heading="__('Platform')" class="grid">
            <flux:navlist.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>{{ __('Dashboard') }}</flux:navlist.item>
            <flux:navlist.item icon="trophy" :href="route('matches.index')" :current="request()->routeIs('matches.*')" wire:navigate>{{ __('Challenges') }}</flux:navlist.item>
            <flux:navlist.item icon="users" :href="route('teams.index')" :current="request()->routeIs('teams.*')" wire:navigate>{{ __('Teams') }}</flux:navlist.item>
            @can('manage-teams')
                <flux:navlist.item icon="sparkles" :href="route('teams.generate')" :current="request()->routeIs('teams.generate')" wire:navigate>
                    {{ __('Teams generieren') }}
                </flux:navlist.item>
            @endcan
        </flux:navlist.group>
    </flux:navlist>
@endif
