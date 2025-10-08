<x-dynamic-component
    :component="'badge'"
    :variant="$variant ?? 'default'"
>
    {{ ucfirst($status) }}
</x-dynamic-component>
