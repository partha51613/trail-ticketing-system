<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-brand-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-brand-600 focus:bg-gray-700 dark:focus:bg-brand-600 active:bg-gray-900 dark:active:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-brand-500 focus:ring-offset-2 dark:focus:ring-offset-slate-900 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
