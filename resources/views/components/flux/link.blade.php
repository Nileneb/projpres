@props(['href' => '#', 'class' => ''])

<a href="{{ $href }}" {{ $attributes->merge(['class' => 'text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 ' . $class]) }}>
    {{ $slot }}
</a>