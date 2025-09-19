<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-4 py-2 font-semibold text-xs uppercase tracking-widest shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50
    bg-zinc-900 text-neon-400 border border-neon-400 hover:bg-zinc-800 focus:ring-neon-400 focus:ring-offset-zinc-900
    dark:bg-neon-100 dark:text-zinc-900 dark:border-zinc-900 dark:hover:bg-neon-200 dark:focus:ring-zinc-900 dark:focus:ring-offset-neon-100']) }}>
    {{ $slot }}
</button>
