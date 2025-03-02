<div class="mb-9 p-4 text-center border border-gray-200 rounded">
    <div class="mb-11 p-px bg-gradient-to-r from-cyan-300 via-red-300  to-purple-300 max-w-max mx-auto rounded-full">
        <p
            class="font-heading font-semibold px-5 py-1.5 text-xs text-gray-900 tracking-px uppercase bg-white rounded-full">
        {{ $product->name }}
        </p>
    </div>
    <h3 class="font-heading font-bold sm:text-6xl text-7xl text-gray-900">{{ $product->price ."€" }}</h3>
    <p class="mb-12 text-gray-500">{{ $product->value . __(' credits')}} </p>
    <x-button label="{{ __('Buy') }}" primary rounded-md md  wire:click="save({{ $product->id }})"/>
</div>
{{-- <div
    class="box-border px-4 py-8 mb-6 text-center bg-white border-solid lg:mb-0 sm:px-4 sm:py-8 md:px-8 md:py-12 lg:px-10">
    <h3
        class="m-0 text-2xl font-semibold leading-tight tracking-tight text-black border-0 border-solid sm:text-3xl md:text-4xl">
        {{ $title }}
    </h3>
    <p class="mt-3 leading-7 text-gray-900 border-0 border-solid">
        {{ $description }}
    </p>
    <div class="flex items-center justify-center mt-6 leading-7 text-gray-900 border-0 border-solid sm:mt-8">
        <p class="box-border m-0 text-6xl font-semibold leading-normal text-center border-0 border-gray-200">
            {{ $price }}
        </p>
    </div>

    @isset($button)
        {{ $button }}
    @endisset

</div> --}}
