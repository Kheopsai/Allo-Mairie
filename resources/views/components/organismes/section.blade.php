<div {{ $attributes->merge(['class' => 'space-y-5 p-10']) }}>
    <div class="flex justify-between items-center">
        @isset($title)
            <div>
                <h1>
                    {{ $title }}
                </h1>
            </div>
        @endisset
        @isset($options)
            <div>
                {{$options}}
            </div>
        @endisset
    </div>

    @isset($description)
        <div  {{ $description->attributes->merge(['class' => 'sm:mt-6 font-pj max-w-screen-lg']) }}>
            <p class="mt-4 text-sm text-gray-700">
                {{ $description }}
            </p>
        </div>
    @endisset
    {{ $slot }}
</div>
