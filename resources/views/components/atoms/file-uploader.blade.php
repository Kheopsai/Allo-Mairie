@php
    $inputName = $attributes->wire('model')->value();
    $maxFileSize = $attributes->get('max-size', config('filesystems.max_upload_size', 10240));
    $acceptedType = $attributes->get('accept');
    $mimeToFriendly = [
        'application/pdf' => 'PDF',
        'application/msword' => 'DOC',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'DOCX',
        'application/vnd.ms-excel' => 'XLS',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'XLSX',
        'application/vnd.ms-powerpoint' => 'PPT',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'PPTX',
        'text/plain' => 'Text',
        'image/*' => 'Image',
        'audio/*' => 'Audio',
        'video/*' => 'Video',
        'application/zip' => 'ZIP',
        'text/csv' => 'CSV',
    ];

    $acceptParts = explode(',', $acceptedType);
    $formattedAccept = [];
    foreach ($acceptParts as $part) {
        $mime = trim($part);
        if (array_key_exists($mime, $mimeToFriendly)) {
            $formattedAccept[] = $mimeToFriendly[$mime];
            continue;
        }
        if (str_ends_with($mime, '/*')) {
            $type = str_replace('/*', '', $mime);
            $formattedAccept[] = ucfirst($type) . ' files';
            continue;
        }
        $parts = explode('/', $mime, 2);
        $formattedPart = isset($parts[1]) ? strtoupper($parts[1]) : strtoupper($parts[0]);
        $formattedAccept[] = str_replace(['vnd.', 'microsoft.', 'officedocument.'], '', $formattedPart);
    }
    $acceptedFormats = implode(', ', $formattedAccept);
@endphp

@props([
    'accept'
])

<div class="w-full flex flex-col justify-center h-full" x-data="chunkedFileUpload()">
    <div class="w-full relative" role="presentation" tabindex="0"
         @drop.prevent="handleDrop"
         @dragover.prevent="isDragging = true"
         @dragleave.prevent="isDragging = false">
        <input
            type="file"
            class="absolute h-full w-full opacity-0 cursor-pointer"
            x-ref="fileInput"
            accept="{{ $accept }}"
            @change="handleFileSelect"
            aria-label="{{ __('Upload file') }}"
        />

        <div {{ $attributes->merge(['class' => "flex flex-col items-center bg-white justify-center p-8 rounded-xl border border-dashed border-secondary-200 transition-colors duration-300"]) }}>
            <div class="flex flex-col items-center justify-center">
                <div class="p-3 bg-primary-50 rounded-full border border-primary-100">
                    <x-kheops-upload class="h-6 w-6 text-primary-500"/>
                </div>
                <p class="mt-6 text-dark-1 dark:text-white text-center text-sm">
                    {{ __('Drag & drop your documents here') }}
                </p>
                <p class="mt-2 text-center text-gray-1 text-xs font-medium">
                    {{ __('Maximum file size:') }} {{ round($maxFileSize / 1024) }}MB
                </p>
                <p class="mt-1 text-center text-gray-1 text-xs font-medium">
                    ({{ __('Accepted formats: ') . $acceptedFormats }})
                </p>
            </div>

            <template x-if="uploadStatus === 'idle'">
                <button
                    type="button"
                    class="text-primary-500 font-medium text-sm mt-4"
                    @click="triggerFileInput">
                    {{ __('Click here to upload') }}
                </button>
            </template>

            <div class="w-full mt-4" x-show="uploadStatus !== 'idle'">
                <div class="flex items-center justify-between text-sm">
                    <span x-text="fileName"></span>
                    <span x-text="progress + '%'"></span>
                </div>
                <div class="h-2 bg-gray-200 rounded-full mt-1">
                    <div class="h-2 bg-primary-500 rounded-full transition-all duration-300"
                         :style="`width: ${progress}%`"></div>
                </div>

                <div class="flex justify-end mt-2 space-x-2">
                    <button x-show="uploadStatus === 'uploading'"
                            type="button"
                            class="text-red-500 text-sm"
                            @click="cancelUpload">
                        {{ __('Cancel') }}
                    </button>
                </div>
            </div>
        </div>

        @error($inputName)
        <div class="text-red-500 mt-2">
            <span>{{ $message }}</span>
        </div>
        @enderror
    </div>
</div>

@script
<script>
    Alpine.data('chunkedFileUpload', () => ({
        uploadStatus: 'idle',
        progress: 0,
        fileName: '',
        isDragging: false,
        currentFile: null,
        controller: null,
        chunkSize: 5 * 1024 * 1024, // 5MB
        uploadId: null,
        totalChunks: 0,

        init() {
            window.addEventListener('beforeunload', (e) => {
                if (this.uploadStatus === 'uploading') {
                    e.preventDefault();
                }
            });
        },
        generateUUID() {

            if (typeof crypto !== 'undefined' && crypto.randomUUID) {
                return crypto.randomUUID();
            }
            return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function (c) {
                const r = Math.random() * 16 | 0,
                    v = c === 'x' ? r : (r & 0x3 | 0x8);
                return v.toString(16);
            });
        },
        triggerFileInput() {
            this.$refs.fileInput.click();
        },

        handleDrop(e) {
            this.isDragging = false;
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                this.handleFile(files[0]);
            }
        },

        handleFileSelect(e) {
            const files = e.target.files;
            if (files.length > 0) {
                this.handleFile(files[0]);
            }
        },

        async handleFile(file) {
            if (!this.validateFile(file)) return;

            this.currentFile = file;
            this.fileName = file.name;
            this.uploadStatus = 'uploading';
            this.progress = 0;
            this.uploadId = this.generateUUID();

            try {
                await this.uploadChunks();
                await this.finalizeUpload();
                this.uploadStatus = 'completed';
                setTimeout(() => this.reset(), 3000);
            } catch (error) {
                if (error.message !== 'UPLOAD_CANCELLED') {
                    console.error('Upload failed:', error);
                    this.uploadStatus = 'error';
                }
            }
        },

        async uploadChunks() {
            this.totalChunks = Math.ceil(this.currentFile.size / this.chunkSize);
            this.controller = new AbortController();

            for (let chunkNumber = 0; chunkNumber < this.totalChunks; chunkNumber++) {
                if (this.uploadStatus !== 'uploading') break;

                const chunk = this.getChunk(chunkNumber);
                const formData = this.createFormData(chunk, chunkNumber, this.totalChunks);

                await this.sendChunk(formData, chunkNumber, this.totalChunks);
                this.progress = Math.round((chunkNumber + 1) / this.totalChunks * 100);
            }
        },

        async sendChunk(formData, chunkNumber, totalChunks) {
            try {
                axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                await axios.post('/api/upload-chunk', formData, {
                    signal: this.controller.signal,
                    headers: {
                        'Content-Type': 'multipart/form-data',
                        'X-Progress-ID': this.uploadId
                    },
                    onUploadProgress: progressEvent => {
                        const chunkProgress = Math.round(
                            (progressEvent.loaded / progressEvent.total) * (100 / totalChunks)
                        );
                        this.progress = Math.min(
                            Math.round((chunkNumber / totalChunks) * 100) + chunkProgress,
                            100
                        );
                    }
                });
            } catch (error) {
                if (axios.isCancel(error)) {
                    throw new Error('UPLOAD_CANCELLED');
                }
                throw error;
            }
        },

        async finalizeUpload() {
            console.log(this.uploadId);
            axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const response = await axios.post('/api/finalize-upload', {
                uploadId: this.uploadId,
                fileName: this.currentFile.name,
                totalChunks: this.totalChunks,
                inputName: '{{ $inputName }}'
            });

            this.$wire.set('{{ $inputName }}', response.data.path, true);
        },

        getChunk(chunkNumber) {
            const start = chunkNumber * this.chunkSize;
            const end = Math.min(start + this.chunkSize, this.currentFile.size);
            return this.currentFile.slice(start, end);
        },

        createFormData(chunk, chunkNumber, totalChunks) {
            const formData = new FormData();
            formData.append('file', chunk);
            formData.append('uploadId', this.uploadId);
            formData.append('chunkNumber', chunkNumber);
            formData.append('totalChunks', totalChunks);
            formData.append('fileName', this.currentFile.name);
            formData.append('inputName', '{{ $inputName }}');
            return formData;
        },

        validateFile(file) {
            const allowedTypes = '{{ $accept }}'.split(',').map(t => t.trim());
            const maxSize = {{ $maxFileSize }} * 1024 * 1024;

            if (allowedTypes.length > 0) {
                const isTypeValid = allowedTypes.some(allowedType => {
                    if (allowedType.endsWith('/*')) {
                        const mimePrefix = allowedType.replace('/*', '');
                        return file.type.startsWith(mimePrefix);
                    } else {
                        return file.type === allowedType;
                    }
                });

                if (!isTypeValid) {
                    console.log('Invalid file type');
                    return false;
                }
            }

            if (file.size > maxSize) {
                console.log('File size exceeds maximum allowed');
                return false;
            }

            return true;
        },

        cancelUpload() {
            this.uploadStatus = 'idle';
            this.controller.abort();
            this.reset();
        },

        reset() {
            this.uploadStatus = 'idle';
            this.progress = 0;
            this.fileName = '';
            this.currentFile = null;
            this.uploadId = null;
            this.$refs.fileInput.value = '';
        }
    }));
</script>
@endscript
