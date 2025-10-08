<flux:dropdown position="bottom" align="start">
    <flux:button size="xs" variant="ghost" class="flex items-center gap-1.5">
        <img src="{{ asset('images/flags/' . app()->getLocale() . '.svg') }}" alt="{{ app()->getLocale() }}" class="size-4">
        <span>{{ strtoupper(app()->getLocale()) }}</span>
    </flux:button>

    <flux:menu>
        <flux:menu.item as="a" href="{{ route('language.switch', 'de') }}" class="flex gap-2">
            <img src="{{ asset('images/flags/de.svg') }}" alt="Deutsch" class="size-4">
            <span>Deutsch</span>
        </flux:menu.item>

        <flux:menu.item as="a" href="{{ route('language.switch', 'en') }}" class="flex gap-2">
            <img src="{{ asset('images/flags/en.svg') }}" alt="English" class="size-4">
            <span>English</span>
        </flux:menu.item>
    </flux:menu>
</flux:dropdown>
