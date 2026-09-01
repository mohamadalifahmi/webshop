<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-4 py-2 bg-cosmic-500 border border-transparent rounded-xl font-semibold text-xs text-white uppercase tracking-wider hover:bg-cosmic-600 active:bg-cosmic-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-cosmic-500 focus-visible:ring-offset-2 focus-visible:ring-offset-deep disabled:opacity-50 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
