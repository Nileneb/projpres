@props(['disabled' => false])

<textarea {{ $attributes->merge(['class' => 'w-full rounded-md border-gray-300 shadow-sm focus:border-neon-400 focus:ring focus:ring-neon-400/20 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white dark:focus:border-neon-400 dark:focus:ring-neon-400/20', 'disabled' => $disabled]) }}>{{ $slot }}</textarea>
