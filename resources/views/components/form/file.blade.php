@props([
    'name',
    'label' => null,
    'required' => false,
    'preview' => null,
    'accept' => '*/*',
    'deleteName' => null,
    'errorKey' => null,
    'help' => null,
])

@php
    // معالجة الأسماء (Name Processing)
    $htmlName = $name;
    if (!str_contains($name, '[') && str_contains($name, '.')) {
        $parts = explode('.', $name);
        $htmlName = array_shift($parts);
        foreach ($parts as $part) {
            $htmlName .= "[$part]";
        }
    }

    $dotName = str_replace(['[', ']'], ['.', ''], $htmlName);
    $dotName = preg_replace('/\.+/', '.', $dotName);
    $dotName = rtrim($dotName, '.');

    $finalErrorKey = $errorKey ?? $dotName;
    $hasError = $errors->has($finalErrorKey);

    // توليد اسم حقل الحذف تلقائياً
    if (!$deleteName) {
        $nameParts = explode('[', $htmlName);
        $baseName = array_pop($nameParts);
        $baseName = str_replace(']', '', $baseName);
        $prefix = implode('[', $nameParts);
        $prefix = $prefix ? $prefix . '[' : '';
        $deleteName = $prefix . 'delete_' . $baseName . ($prefix ? ']' : '');
    }

    $id = $attributes->get('id', str_replace('.', '-', $dotName));
@endphp

<div
    class="mb-4 w-full"
    x-data="{
        previewUrl: '{{ $preview }}',
        originalUrl: '{{ $preview }}',
        fileName: null,
        isImage: false,
        isDeleted: false, // هل طلب المستخدم حذف الملف الأصلي؟

        init() {
            if (this.previewUrl) {
                this.checkIfImage(this.previewUrl);
                this.fileName = '{{ basename($preview ?? '') }}';
            }
        },

        handleFileChange(event) {
            const file = event.target.files[0];
            if (!file) return;

            // عند اختيار ملف جديد، نلغي حالة الحذف لأننا نستبدل
            this.isDeleted = false;
            this.fileName = file.name;

            if (file.type.startsWith('image/')) {
                this.isImage = true;
                const reader = new FileReader();
                reader.onload = (e) => { this.previewUrl = e.target.result; };
                reader.readAsDataURL(file);
            } else {
                this.isImage = false;
                this.previewUrl = null; // إخفاء المعاينة للملفات غير الصور
            }
        },

        removeFile() {
            // تنظيف الحقول
            this.previewUrl = null;
            this.fileName = null;

            // تصفير الإدخال للسماح باختيار نفس الملف مجدداً إذا أراد
            document.getElementById('{{ $id }}').value = '';

            // إذا كان هناك ملف أصلي، نعتبره محذوفاً
            if (this.originalUrl) {
                this.isDeleted = true;
            }
        },

        restoreOriginal() {
            this.isDeleted = false;
            this.previewUrl = this.originalUrl;
            this.checkIfImage(this.previewUrl);
            this.fileName = '{{ basename($preview ?? '') }}';
            document.getElementById('{{ $id }}').value = '';
        },

        checkIfImage(url) {
            if (!url) return;
            const imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp'];
            // تعامل بسيط مع الروابط أو data:image
            const extension = url.split('.').pop().toLowerCase();
            this.isImage = imageExtensions.includes(extension) || url.startsWith('data:image');
        }
    }"
>
    {{-- Label --}}
    @if ($label)
        <label class="block text-sm font-medium mb-2 text-gray-700 dark:text-gray-300">
            {{ $label }}
            @if ($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    {{-- Main Container --}}
    <div
        class="relative w-full border-2 border-dashed rounded-lg transition-colors duration-150 ease-in-out bg-gray-50 dark:bg-gray-800 min-h-[160px] flex flex-col items-center justify-center text-center overflow-hidden group"
        :class="{
            'border-red-300 bg-red-50 dark:border-red-500': {{ $hasError ? 'true' : 'false' }},
            'border-gray-300 hover:bg-gray-100 dark:border-gray-600 dark:hover:bg-gray-700': !
                {{ $hasError ? 'true' : 'false' }}
        }"
    >

        {{-- ---------------------------------------------------------------- --}}
        {{-- Layer 0: Visuals (Pointer events none allows click through to Input) --}}
        {{-- ---------------------------------------------------------------- --}}

        {{-- State A: Empty or Deleted (Show Upload Placeholder) --}}
        <div
            x-show="!previewUrl && !fileName"
            class="pointer-events-none p-4"
        >
            <i class="fas fa-cloud-upload-alt text-3xl mb-3 text-gray-400"></i>
            <p class="mb-1 text-sm text-gray-500 dark:text-gray-400">
                <span class="font-semibold">اضغط للرفع</span> أو اسحب الملف
            </p>
            <p class="text-xs text-gray-500 dark:text-gray-400">
                {{ $help ?? 'الصور أو المستندات' }}
            </p>
        </div>

        {{-- State B: Image Preview --}}
        <div
            x-show="previewUrl && isImage"
            class="absolute inset-0 w-full h-full p-2 pointer-events-none"
        >
            <img
                :src="previewUrl"
                class="w-full h-full object-contain rounded"
                alt="Preview"
            >
        </div>

        {{-- State C: File Icon (Not Image) --}}
        <div
            x-show="fileName && !isImage"
            class="pointer-events-none p-4"
        >
            <i class="fas fa-file-alt text-4xl text-blue-500 mb-2"></i>
            <p
                class="text-sm font-medium text-gray-700 dark:text-gray-200 truncate max-w-xs"
                x-text="fileName"
            ></p>
        </div>

        {{-- ---------------------------------------------------------------- --}}
        {{-- Layer 10: The Input (Invisible but Clickable Everywhere)         --}}
        {{-- ---------------------------------------------------------------- --}}
        <input
            id="{{ $id }}"
            name="{{ $htmlName }}"
            type="file"
            accept="{{ $accept }}"
            @change="handleFileChange"
            {{ $attributes->except(['class', 'id']) }}
            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
        >

        {{-- ---------------------------------------------------------------- --}}
        {{-- Layer 20: Controls (Buttons must be above the input)             --}}
        {{-- ---------------------------------------------------------------- --}}

        {{-- Remove Button (Shows if there is a file) --}}
        <div
            x-show="previewUrl || fileName"
            class="absolute top-2 right-2 z-20"
        >
            <button
                type="button"
                @click="removeFile()"
                class="bg-white dark:bg-gray-700 text-red-500 rounded-full p-2 hover:bg-red-50 dark:hover:bg-gray-600 focus:outline-none shadow-md border border-gray-200 dark:border-gray-600 transition-transform hover:scale-110"
                title="حذف الملف"
            >
                <svg
                    class="w-4 h-4"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                    ></path>
                </svg>
            </button>
        </div>

        {{-- Restore Button (Shows only if deleted original) --}}
        {{-- We place it at the bottom, Z-20 so it receives clicks --}}
        <div
            x-show="isDeleted"
            class="absolute bottom-2 z-20"
        >
            <button
                type="button"
                @click="restoreOriginal()"
                class="bg-blue-50 dark:bg-blue-900 text-blue-600 dark:text-blue-200 text-xs px-3 py-1 rounded-full hover:bg-blue-100 dark:hover:bg-blue-800 focus:outline-none shadow-sm flex items-center gap-1 transition-colors"
            >
                <i class="fas fa-undo"></i> تراجع عن الحذف
            </button>
        </div>

    </div>

    {{-- Hidden Delete Input --}}
    {{-- Value is 1 if isDeleted is true, otherwise 0 --}}
    @if ($preview)
        <input
            type="hidden"
            name="{{ $deleteName }}"
            :value="isDeleted ? 1 : 0"
        >
    @endif

    {{-- Error Message --}}
    @if ($hasError)
        <p class="mt-2 text-xs text-red-600 dark:text-red-400 flex items-center">
            <i class="fas fa-exclamation-circle mr-1"></i>
            {{ $errors->first($finalErrorKey) }}
        </p>
    @endif
</div>
