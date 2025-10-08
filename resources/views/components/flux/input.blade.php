@props(['disabled' => false, 'type' => 'text', 'label' => null, 'viewable' => false])

<div class="mb-2">
    @if($label)
        <label class="mb-1 block font-medium text-sm text-gray-700 dark:text-gray-300">
            {{ $label }}
        </label>
    @endif

    <input type="{{ $type }}" {{ $disabled ? 'disabled' : '' }} 
        {{ $attributes->merge(['class' => 'w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300']) }}
    />
</div>