<x-card title="{{ __('Add resources') }}">
    <x-slot name="action">
        <x-button icon="x-mark" flat wire:click="$dispatch('closeModal')"/>
    </x-slot>
    <div x-data="{ step: @entangle('step') }" class="grid grid-cols-1 gap-4 px-2">
        <div x-cloak x-show="step==1" x-data="{ value: @entangle('value') }" class="grid grid-cols-1 gap-4 px-2">
            <div class="">
                <h2 class="text-base font-semibold text-secondary-900 dark:text-secondary-100 ">
                    {{ __('Import your content') }}
                </h2>
                <div>
                    <div class="text-xs text-secondary-700 font-pj">
                        {{ __('We can capture your voice from your website and texts.') }}
                    </div>
                    <div class="text-xs text-secondary-700 font-pj">
                        {{ 'Choose a method below to integrate your brand content' }}
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-6">
                <div :class="{ 'border-primary-500 bg-primary-100': value == 'text' }"
                     class="border border-gray-300 cursor-pointer rounded-xl">
                    <div class="flex justify-center px-2 py-4 rounded-t-lg item-center" wire:click="setValue('text')">
                        <div class="space-y-1">
                            <div class="flex justify-center">
                                <x-heroicon-o-chat-bubble-bottom-center-text class="flex-shrink-0 w-5 h-5"/>
                            </div>
                            <div class="flex justify-center">
                                <span class="text-base font-medium">{{ __('From text') }}</span>
                            </div>
                            <div class="flex justify-center text-xs text-center">
                                {{ __('Write or copy and past your text') }}
                            </div>
                        </div>
                    </div>
                </div>
                <div :class="{ 'border-primary-500 bg-primary-100': value == 'file' }"
                     class="border border-gray-300 cursor-pointer rounded-xl">
                    <div class="flex justify-center px-2 py-4 rounded-t-lg item-center" wire:click="setValue('file')">
                        <div class="space-y-1">
                            <div class="flex justify-center">
                                <x-heroicon-o-arrow-up-on-square class="flex-shrink-0 w-5 h-5"/>
                            </div>
                            <div class="flex justify-center">
                                <span class="text-base font-medium">{{ __('Upload  file') }}</span>
                            </div>
                            <div class="flex justify-center text-xs text-center">
                                {{ __('pdf, doc, txt') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div x-cloak x-show="step==2" class="grid grid-cols-1 gap-4 px-2 justify-center">
            @if ($value == 'file')
                <div>
                    <div class="cursor-pointer justify-center flex flex-col space-x-2 text-center w-full">
                        @if ($file)
                            <div
                                class="p-4 cursor-pointer hover:bg-secondary-50 text-center flex justify-center items-center rounded-lg border border-dashed flex-1"
                                wire:click="removeFile()" wire:loading.remove wire:target="file">
                                {{ __('Remove file') }}
                            </div>
                        @else
                            <label for="dropzone-file"
                                   class="p-4 cursor-pointer hover:bg-secondary-50 flex justify-center items-center rounded-lg border border-dashed flex-1">
                                <div class="flex space-x-2">
                                    <div>
                                        <x-heroicon-o-arrow-up-on-square class="h-6 w-6"/>
                                    </div>
                                    <div wire:loading wire:target="file">{{ __('Uploading...') }}</div>
                                    <div wire:loading.remove wire:target="file" class="text-sm">
                                        {{ __('Select file to upload') }}
                                    </div>
                                </div>
                                <input id="dropzone-file" type="file" class="hidden" wire:model.live="file"/>
                            </label>
                        @endif
                    </div>
                    <x-atoms.error name="file"/>
                </div>
            @endif
            <div>
                <x-input class="w-full" placeholder="{{ __('e.g  Social media voice') }}"
                                label="{{ __('Name') }}" wire:model.blur='name'/>
            </div>
            <div class="relative">
                <div class="absolute left-1">
                    <x-atoms.spinner spinner="getContentAndTags"/>
                </div>
                {{-- <x-textarea :disabled="!$loadContent" primary wire:model.blur="content"
                                   class="resize-none rounded-xl "></x-textarea> --}}
            </div>
            <div class="flex items-end space-x-5">
                <x-checkbox md primary/>
                <p class=" text-xs text-gray-700 md:max-w-6xl md:mx-auto sm:mt-6 font-pj">
                    {{ __('By uploading, I attest to having the appropriate permissions for this content') }}
                </p>
            </div>
        </div>
    </div>

    @if ($step == 2)
        <x-slot name="footer">
            <div class="flex space-x-4">
                <x-button spinner="back" wire:click='back()' class="w-full" flat secondary
                                 label="{{ __('Back') }}"/>
                <x-button spinner="save" wire:click='save()' class="w-full" primary
                                 label="{{ __('Add to resources') }}"/>
            </div>
        </x-slot>
    @endif
</x-card>
