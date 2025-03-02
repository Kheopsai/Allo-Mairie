<x-organismes.section>
    <x-organismes.form title="New Product"
        description="Define a credit package that users can purchase. Set the number of credits included and the price to offer flexible options for your customers.">

        <x-card class="col-span-3 shadow-none border h-fit">
            <div class="grid grid-cols-1 gap-4 px-2">
                <div>
                    <x-input wire:model='name' label="{{ __('Name') }}" />
                </div>
                <div>
                    <div>
                        <x-textarea wire:model="description" label="{{ __('Description') }}"></x-textarea>
                    </div>

                </div>
                {{-- <div>
                    <x-input wire:model.live='price' label="{{ __('Price') }}" />
                </div> --}}
                <div>
                    <x-input wire:model.live='value' label="{{ __('Credits') }}" />
                </div>
            </div>
        </x-card>
    </x-organismes.form>

</x-organismes.section>

