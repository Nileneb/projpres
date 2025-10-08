@props(['label' => null, 'disabled' => false])

<label class="inline-flex items-center">
    <input type="checkbox" {{ $disabled ? 'disabled' : '' }} {{ $attributes->merge(['class' => 'rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:border-gray-700 dark:bg-gray-900']) }}>

    @if($label)
    <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">{{ $label }}</span>
    @endif
</label>
