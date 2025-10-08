@props(['status'])

@if ($status)
    <x-ui.status :status="$status" type="info" {{ $attributes }} />
@endif
