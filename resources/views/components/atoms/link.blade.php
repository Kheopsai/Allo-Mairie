<a wire:current="!text-primary-700 font-semibold"
    {{ $attributes->merge(['class' => 'cursor-pointer flex items-center rounded-lg px-4 py-2 text-sm font-medium transition-all duration-200 text hover:text-primary-500 group dark:text-gray-300 dark:hover:text-primary-400'])->except('wire:navigate') }}>
    {{ $slot }}
</a>
