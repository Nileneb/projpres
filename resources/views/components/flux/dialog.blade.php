@props(['id' => null, 'open' => false, 'variant' => 'default'])

@php
    $modalClasses = [
        'default' => 'relative transform overflow-hidden rounded-lg bg-white px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6 dark:bg-gray-800',
        'overlay' => 'relative transform overflow-hidden rounded-lg bg-white px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6 dark:bg-gray-800 mx-auto'
    ];

    $classes = $modalClasses[$variant] ?? $modalClasses['default'];
@endphp

<div
    x-data="{
        open: {{ $open ? 'true' : 'false' }},
        init() {
            if(this.open) {
                document.body.classList.add('overflow-hidden');
            }
            this.$watch('open', value => {
                if (value) {
                    document.body.classList.add('overflow-hidden');
                } else {
                    document.body.classList.remove('overflow-hidden');
                }
            });

            window.$flux = window.$flux || {};
            window.$flux.closeDialog = (id) => {
                if(id === '{{ $id }}') {
                    this.open = false;
                }
            };
            window.$flux.openDialog = (id) => {
                if(id === '{{ $id }}') {
                    this.open = true;
                }
            };
        }
    }"
    x-show="open"
    x-transition:enter="ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    id="{{ $id }}"
    {{ $attributes->merge(['class' => 'fixed inset-0 z-50 overflow-y-auto']) }}
>
    <div class="flex min-h-full items-center justify-center p-4 text-center">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" x-show="open"></div>
        <div class="{{ $classes }}">
            {{ $slot }}
        </div>
    </div>
</div>
