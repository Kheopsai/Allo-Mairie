<div x-data="{ open: {{ $open ? 'true' : 'false' }} }" {{ $attributes->merge(['class' => 'mb-4']) }}>
    <div class="flex justify-between items-center cursor-pointer px-4 py-2 bg-secondary-50 dark:bg-secondary-950 rounded-lg" @click="open = !open">
        <h4 class="text-xs font-medium uppercase text-secondary-400 dark:text-secondary-300">{{ $title }}</h4>
        <x-lucide-chevron-up x-show="!open" class="h-5 w-5 text-secondary-500 dark:text-secondary-400" />
        <x-lucide-minus x-show="open" class="h-5 w-5 text-secondary-500 dark:text-secondary-400" />
    </div>

    <div x-show="open" x-cloak class="mt-2 space-y-1 dark:text-secondary-200">
        {{ $slot }}
    </div>
</div>
