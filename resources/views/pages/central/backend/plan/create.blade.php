<x-organismes.section>
    <x-organismes.form title="New plan" description="Create new plans and functionalities for subscriptions">
        <div class="grid grid-cols-5 gap-8">
            <x-card class="col-span-3 shadow-none border h-fit">
                <div class="grid grid-cols-1 gap-4 px-2">
                    <div>
                        <x-input wire:model='name' label="{{ __('Name') }}" />
                    </div>
                    <div>
                        <div>
                            <x-textarea wire:model="short_description" label="{{ __('Description') }}"></x-textarea>
                        </div>

                    </div>
                    <div>
                        <x-input wire:model.live='monthly_price' label="{{ __('Monthly price') }}" />
                    </div>
                    <div>
                        <x-input wire:model.live='yearly_price' label="{{ __('Yearly price') }}" />
                    </div>
                    <div>
                        <x-checkbox wire:model='archived' label="{{ __('Is Archived ?') }}" />
                    </div>
                    <div>
                        <x-checkbox wire:model='selected' label="{{ __('Is Selected ?') }}" />
                    </div>
                    @if (count($this->inputs) > 0)
                        <div>
                            <hr>
                        </div>
                        <x-label label="{{ __('Additional fields') }}" />
                        <div class="border rounded-lg border-secondary-200" wire:sortable="updateInputsOrder"
                            wire:sortable.options="{ animation: 100 }">
                            @foreach ($this->inputs as $key => $input)
                                <div class="items-center border-b border-secondary-200 last:border-b-0"
                                    x-data="{ open: false }" wire:sortable.item="{{ $key }}"
                                    wire:key="input-{{ $key }}">
                                    <div class="flex justify-between px-4 py-2 space-x-4 bg-secondary-50">
                                        <div class="flex space-x-4">
                                            <div class="flex items-center">
                                                <x-heroicon-o-queue-list class="h-4" />
                                            </div>
                                            <div>
                                                <div class="text-sm font-light text-secondary-400">
                                                    {{ $input->action }}
                                                </div>
                                                <div>
                                                    {{ $input->number }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex items-center">
                                            <x-button wire:click="deleteInput({{ $key }})" sm flat
                                                icon="trash" />
                                        </div>

                                    </div>

                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </x-card>
            <x-card class="col-span-2 shadow-none border h-fit">
                <div class="grid grid-cols-1 gap-4 px-2">

                    <div>
                        <x-input wire:model.live='inputname' label="{{ __('Name') }}" />
                    </div>
                    <div>
                        <x-textarea wire:model='action' label="{{ __('Action') }}"></x-textarea>
                    </div>
                    <div>
                        <x-input wire:model.live='number' label="{{ __('Number') }}" />
                    </div>


                </div>
                <x-slot name="footer">
                    <x-button spinner="addForm()" wire:click="addForm()" class="w-full" flat
                        label="{{ __('Add input') }}" />
                </x-slot>
            </x-card>
        </div>
    </x-organismes.form>
</x-organismes.section>
