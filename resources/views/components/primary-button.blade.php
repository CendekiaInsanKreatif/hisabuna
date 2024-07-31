<button
    {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-2 py-1 bg-emerald-500  border border-transparent rounded-md font-medium text-base text-white focus:bg-emerald-700  focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-custom-strong']) }}>
    {{ $slot }}
</button>
