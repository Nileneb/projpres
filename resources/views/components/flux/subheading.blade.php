@props(['size' => 'md'])

@php
    $sizeClasses = [
        'sm' => 'text-sm',
        'md' => 'text-base',
        'lg' => 'text-lg',
    ];
    
    $classes = $sizeClasses[$size] ?? $sizeClasses['md'];
@endphp

<p {{ $attributes->merge(['class' => $classes . ' text-gray-500 dark:text-gray-400 mt-2']) }}>
    {{ $slot }}
</p>