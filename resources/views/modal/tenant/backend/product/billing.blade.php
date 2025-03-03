<x-card title="{{ __('Pricing Options') }}" >
    <x-slot name="action">
        <x-button icon="x-mark" flat wire:click="forceCloseModal()" />
    </x-slot>
    <section
        class="box-border py-4 leading-7 text-gray-900 bg-white border-0 border-gray-200 border-solid sm:py-5 md:py-8 lg:py-10">
        <div class="box-border px-4 pb-7 mx-auto border-solid sm:px-6 md:px-6 ">
            {{-- <div class="flex flex-col items-center leading-7 text-center text-gray-900">
            <h2 class="box-border m-0 text-3xl font-semibold leading-tight tracking-tight text-black border-solid sm:text-4xl md:text-5xl">
                Pricing Options
            </h2>
            <p class="box-border mt-4 text-2xl leading-normal text-gray-900 border-solid">
                We've got a plan for companies of any size
            </p>
        </div> --}}
            @empty($products)

                <h2
                    class="box-border m-0 text-3xl font-semibold leading-tight tracking-tight text-black border-solid sm:text-4xl md:text-5xl">
                    {{ __('No Products available for the moment') }}
                </h2>
            @else
                <div x-data="{ activeIndex: 0 }" class="flex justify-evenly">
                    {{-- <button @click="activeIndex = (activeIndex === 0) ? {{ $products->count() - 1 }} : activeIndex - 1;"
                        class="absolute left-0 p-3  bg-primary-500 text-white rounded-full ">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button> --}}
                    @foreach ($products as $index => $product)
                    <div
                         x-show="Math.abs(activeIndex - {{ $index }}) <= 1 ||
                             (activeIndex === 0 &&  {{ $index }} === 2) ||
                             (activeIndex === {{ $products->count() - 1 }} && activeIndex - {{ $index }} === 2)"
                             @click="activeIndex = {{ $index }}"
                    >

                        <x-organismes.product.billing-card
                            :product="$product" />
                    </div>
                    @endforeach
                    {{-- <button
                        @click="activeIndex = (activeIndex === {{ $products->count() - 1 }}) ? 0 : activeIndex + 1;"
                        class="absolute right-0 p-3 bg-primary-400 text-white rounded-full">
                        <svg class="w-8 h-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button> --}}
                </div>
            @endempty
        </div>
    </section>
</x-card>
