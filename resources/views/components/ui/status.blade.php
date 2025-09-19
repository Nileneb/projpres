@props([
    'type' => 'success',
    'on' => null,
    'status' => null,
    'dismissable' => false,
    'autoHide' => false,
    'hideDelay' => 2000,
])

@php
    // Styling basierend auf dem Status-Typ
    $typeStyles = [
        'success' => 'bg-green-50 text-green-700 dark:bg-green-900/20 dark:text-green-400 dark:border-green-800',
        'error' => 'bg-red-50 text-red-700 dark:bg-red-900/20 dark:text-red-400 dark:border-red-800',
        'warning' => 'bg-yellow-50 text-yellow-700 dark:bg-yellow-900/20 dark:text-yellow-400 dark:border-yellow-800',
        'info' => 'bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400 dark:border-blue-800',
    ];

    // Icon je nach Status-Typ
    $icons = [
        'success' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5"><path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm13.36-1.814a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z" clip-rule="evenodd" /></svg>',
        'error' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5"><path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25zm-1.72 6.97a.75.75 0 10-1.06 1.06L10.94 12l-1.72 1.72a.75.75 0 101.06 1.06L12 13.06l1.72 1.72a.75.75 0 101.06-1.06L13.06 12l1.72-1.72a.75.75 0 10-1.06-1.06L12 10.94l-1.72-1.72z" clip-rule="evenodd" /></svg>',
        'warning' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5"><path fill-rule="evenodd" d="M9.401 3.003c1.155-2 4.043-2 5.197 0l7.355 12.748c1.154 2-.29 4.5-2.599 4.5H4.645c-2.309 0-3.752-2.5-2.598-4.5L9.4 3.003zM12 8.25a.75.75 0 01.75.75v3.75a.75.75 0 01-1.5 0V9a.75.75 0 01.75-.75zm0 8.25a.75.75 0 100-1.5.75.75 0 000 1.5z" clip-rule="evenodd" /></svg>',
        'info' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5"><path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm8.706-1.442c1.146-.573 2.437.463 2.126 1.706l-.709 2.836.042-.02a.75.75 0 01.67 1.34l-.04.022c-1.147.573-2.438-.463-2.127-1.706l.71-2.836-.042.02a.75.75 0 11-.671-1.34l.041-.022zM12 9a.75.75 0 100-1.5.75.75 0 000 1.5z" clip-rule="evenodd" /></svg>',
    ];

    $classes = 'rounded-md border px-4 py-3 text-sm flex items-start ' . ($typeStyles[$type] ?? $typeStyles['info']);
@endphp

<div
    @if($on || $autoHide)
        x-data="{ shown: {{ $status ? 'true' : 'false' }}, timeout: null }"
        @if($on)
            x-init="@this.on('{{ $on }}', () => {
                clearTimeout(timeout);
                shown = true;
                @if($autoHide) timeout = setTimeout(() => { shown = false }, {{ $hideDelay }}); @endif
            })"
        @elseif($autoHide && $status)
            x-init="timeout = setTimeout(() => { shown = false }, {{ $hideDelay }})"
        @endif
        x-show="shown"
        x-transition:leave.opacity.duration.1000ms
        style="{{ $status ? '' : 'display: none' }}"
    @endif
    {{ $attributes->merge(['class' => $classes]) }}
    role="alert"
>
    <div class="mr-3 flex-shrink-0">
        {!! $icons[$type] !!}
    </div>
    <div>
        @if($status)
            {{ $status }}
        @elseif($slot->isEmpty())
            {{ __('Status message.') }}
        @else
            {{ $slot }}
        @endif
    </div>

    @if($dismissable)
        <button type="button" class="ml-auto -mx-1.5 -my-1.5 rounded-lg p-1.5 inline-flex items-center justify-center text-gray-400 hover:text-gray-900 focus:ring-2 focus:ring-gray-300 dark:text-gray-500 dark:hover:text-white"
            @if($on || $autoHide) @click="shown = false" @endif>
            <span class="sr-only">Dismiss</span>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    @endif
</div>
