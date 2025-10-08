@php
    $color = match ($status) {
        'created' => 'blue',
        'in_progress' => 'yellow',
        'submitted' => 'green',
        'closed' => 'gray',
        default => 'blue'
    };
@endphp

<x-badge :color="$color">
    {{ ucfirst($status) }}
</x-badge>
