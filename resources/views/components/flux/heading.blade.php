@props(['size' => 'lg'])

@php
    $sizeClasses = [
        'sm' => 'text-lg font-semibold',
        'md' => 'text-xl font-semibold',
        'lg' => 'text-2xl font-bold',
        'xl' => 'text-3xl font-bold',
    ];
    
    $classes = $sizeClasses[$size] ?? $sizeClasses['lg'];
@endphp

<h1 {{ $attributes->merge(['class' => $classes . ' text-gray-900 dark:text-white']) }}>
    {{ $slot }}
</h1>