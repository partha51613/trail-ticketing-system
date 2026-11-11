@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-gray-900 dark:text-white dark:placeholder-slate-500 focus:border-indigo-500 dark:focus:border-brand-500 focus:ring-indigo-500 dark:focus:ring-brand-500 rounded-md shadow-sm']) }}>
