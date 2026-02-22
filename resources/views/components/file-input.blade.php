@props([
    'name' => 'file',
    'id' => null,
    'label' => 'Upload Foto Profil (Opsional)',
    'hint' => null,
])

@php $inputId = $id ?? $name; @endphp

<!-- Alpine js untuk drag & drop file -->
<div x-data="{
    fileName: '',
    hasFile: false,
    isDragging: false,
    handleFile(files) {
        if (files.length > 0) {
            this.fileName = files[0].name;
            this.hasFile = true;
        }
    }}" class="w-full">

    <label
        for="{{ $inputId }}"
        @dragover.prevent="isDragging = true"
        @dragleave.prevent="isDragging = false"
        @drop.prevent="isDragging = false; handleFile($event.dataTransfer.files); $refs.input.files = $event.dataTransfer.files"
        :class="isDragging ? 'border-green-600 bg-green-50' : hasFile ? 'border-green-600 bg-green-50' : 'border-gray-300 bg-white hover:border-green-500 hover:bg-green-50'"
        class="flex flex-col items-center justify-center w-full gap-2 px-4 py-6 border-2 border-dashed rounded-lg cursor-pointer transition-all duration-0 group"
    >

    {{-- Icon --}}
    <div :class="isDragging || hasFile ? 'bg-green-100' : 'bg-gray-100 group-hover:bg-green-100'"
        class="p-3 rounded-full transition-colors duration-0">
        <i :class="isDragging || hasFile ? 'text-green-600' : 'text-gray-400 group-hover:text-green-600'" class="fa-solid fa-cloud-arrow-up text-xl transition-colors duration-0"></i>
    </div>

    {{-- Text --}}
    <div class="text-center">
        <template x-if="fileName === ''">
            <div>
                <p class="text-sm font-medium text-gray-600 group-hover:text-green-600 transition-colors duration-0">
                    {{ $label }}
                </p>
                <p class="text-xs text-gray-400 mt-0.5">Klik atau seret file kesini</p>
            </div>
        </template>
        <template x-if="fileName !== ''">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-circle-check text-green-600"></i>
                <p class="text-sm font-medium text-green-600" x-text="fileName"></p>
            </div>
        </template>
    </div>

    @if($hint)
        <p class="text-xs text-gray-400">{{ $hint }}</p>
    @endif

    <input
        x-ref="input"
        id="{{ $inputId }}"
        name="{{ $name }}"
        type="file"
        class="hidden"
        @change="handleFile($event.target.files)"
        {{ $attributes }}
    >
</label>
</div>