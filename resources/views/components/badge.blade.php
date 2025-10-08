@props([
    'color' => 'blue', // default color
    'size' => 'md',    // sm, md, lg
    'pill' => true     // rounded-full if true
])

@php
    $baseClasses = 'inline-flex items-center font-medium';

    $colorClasses = match ($color) {
        'green' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
        'red' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
        'yellow' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
        'amber' => 'bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-300',
        'blue' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
        'gray' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
        'indigo' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-300',
        default => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300'
    };

    $sizeClasses = match ($size) {
        'sm' => 'px-2 py-0.5 text-xs',
        'md' => 'px-2.5 py-1 text-xs',
        'lg' => 'px-3 py-1.5 text-sm',
        default => 'px-2.5 py-1 text-xs'
    };

    $roundedClasses = $pill ? 'rounded-full' : 'rounded-md';

    $classes = "{$baseClasses} {$colorClasses} {$sizeClasses} {$roundedClasses}";
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</span>
