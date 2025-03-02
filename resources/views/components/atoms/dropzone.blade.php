@props([
    'Label' => ""
])
<div
    x-cloak
    x-data="dropzone({
        _this: @this,
        uuid: @js($uuid),
        multiple: @js($multiple),
    })"
    @dragenter.prevent.document="onDragenter($event)"
    @dragleave.prevent="onDragleave($event)"
    @dragover.prevent="onDragover($event)"
    @drop.prevent="onDrop"
    class="w-full antialiased"
>
    <div>

        @if(!is_null($files))
            @if(count($files) > 0)
                <div class="flex flex-wrap gap-x-10 gap-y-2 justify-start w-full mt-5">
                    @foreach($files as $file)
                        <div
                            class="flex items-center gap-2 w-full h-auto overflow-hidden dark:border-secondary-700 bg-white">
                            <div class="flex items-center gap-3">
                                @if($this->isImageMime($file['extension']))
                                    <div class="flex-none w-14 h-14">
                                        <img src="{{ $file['temporaryUrl'] }}"
                                             class="object-cover w-full h-full rounded-lg"
                                             alt="{{ $file['name'] }}">
                                    </div>
                                @else
                                    <div
                                        class="flex justify-center items-center w-14 h-14 bg-secondary-50 dark:bg-secondary-700">
                                        <x-icon name="file" collection="lucide"
                                                       class="w-6 h-6 text-secondary-500"/>
                                    </div>
                                @endif
                            </div>
                            <div class="flex items-center mr-3">
                                <x-button right-icon="trash" negative collection="lucide" flat sm label="{{ __('Delete') }}" @click="removeUpload('{{ $file['tmpFilename'] }}')"/>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        @endif
        <div @click="$refs.input.click()"
             class="shadow-sm w-full cursor-pointer bg-white border border-secondary-300 rounded-lg border-dashed mt-2">
            <div class="">
                <div x-show="!isDragging" class="flex items-center justify-center gap-2 py-8 h-full w-full">
                    <x-atoms.icon name="cloud-upload" collection="lucide" class="w-6 h-6 text-secondary-300"/>
                    <p class="text-sm font-light text-secondary-600 dark:text-secondary-400">{{ __('Drop here or') }}
                        <span class="font-medium text-secondary-600 dark:text-white">{{ __('Browse files') }}</span>
                    </p>
                </div>
                <div x-show="isDragging" class="flex items-center justify-center gap-2 py-8 h-full w-full">
                    <x-atoms.icon name="image-up" collection="lucide" class="w-6 h-6 text-secondary-300"/>
                    <p class="text-md text-secondary-600 dark:text-secondary-400">{{ __('Drop here to upload') }}</p>
                </div>
            </div>
            <input
                x-ref="input"
                wire:model="upload"
                type="file"
                class="hidden"
                x-on:livewire-upload-start="isLoading = true"
                x-on:livewire-upload-finish="isLoading = false"
                x-on:livewire-upload-error="console.log('livewire-dropzone upload error', error)"
                @if(! is_null($this->accept)) accept="{{ $this->accept }}" @endif
                @if($multiple === true) multiple @endif
            >
        </div>

        <div class="flex items-center gap-2 text-secondary-500 text-xs mt-2">
            @php
                $hasMaxFileSize = ! is_null($this->maxFileSize);
                $hasMimes = ! empty($this->mimes);
            @endphp

            @if($hasMaxFileSize)
                <p>{{ __('Up to :size', ['size' => \Illuminate\Support\Number::fileSize($this->maxFileSize * 1024)]) }}</p>
            @endif

            @if($hasMaxFileSize && $hasMimes)
                <span class="text-secondary-400">·</span>
            @endif

            @if($hasMimes)
                <p>{{ Str::upper($this->mimes) }}</p>
            @endif
        </div>
        @if(! is_null($error))
            <div class="flex gap-3 items-start">
                <h3 class="mt-2 text-sm text-negative-600">{{ $error }}</h3>
            </div>
        @endif

    </div>

    @script
    <script>
        Alpine.data('dropzone', ({_this, uuid, multiple}) => {
            return ({
                isDragging: false,
                isDropped: false,
                isLoading: false,

                onDrop(e) {
                    this.isDropped = true
                    this.isDragging = false

                    const file = multiple ? e.dataTransfer.files : e.dataTransfer.files[0]

                    const args = ['upload', file, () => {
                        this.isLoading = false
                    }, (error) => {
                        console.log('livewire-dropzone upload error', error);
                    }, () => {
                        this.isLoading = true
                    }];
                    multiple ? _this.uploadMultiple(...args) : _this.upload(...args)
                },
                onDragenter() {
                    this.isDragging = true
                },
                onDragleave() {
                    this.isDragging = false
                },
                onDragover() {
                    this.isDragging = true
                },
                removeUpload(tmpFilename) {
                    // Dispatch an event to remove the temporarily uploaded file
                    _this.dispatch(uuid + ':fileRemoved', {tmpFilename})
                },
            });
        })
    </script>
    @endscript
</div>

