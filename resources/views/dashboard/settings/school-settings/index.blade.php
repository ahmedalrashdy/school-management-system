<x-layouts.dashboard page-title="إعدادات النظام">
    <x-slot name="breadcrumbs">
        <x-ui.breadcrumbs :items="[
            ['label' => 'لوحة التحكم', 'url' => route('dashboard.index'), 'icon' => 'fas fa-home'],
            ['label' => 'إعدادات النظام', 'icon' => 'fas fa-cog'],
        ]" />
    </x-slot>

    <x-ui.main-content-header
        title="إعدادات النظام"
        description="إدارة إعدادات النظام العامة"
    />

    <form
        method="POST"
        action="{{ route('dashboard.school-settings.update') }}"
        enctype="multipart/form-data"
        x-data="settingsForm()"
    >
        @csrf
        @method('PUT')

        @foreach ($settings as $group => $groupSettings)
            <x-ui.card
                :title="$group"
                class="mb-6"
            >
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach ($groupSettings as $setting)
                        @php
                            $currentValue = $setting->value;
                            $settingKey = "settings[{$setting->key}]";
                        @endphp

                        @if ($setting->type === 'file')
                            {{-- معالجة رفع الملفات --}}
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ $setting->label }}
                                </label>

                                @if ($currentValue)
                                    <div class="mb-3 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                        <div class="flex items-center gap-4">
                                            <img
                                                src="{{ Storage::url($currentValue) }}"
                                                alt="{{ $setting->label }}"
                                                class="w-24 h-24 object-cover rounded-lg border border-gray-300 dark:border-gray-600"
                                                onerror="this.src='{{ asset('images/placeholder.png') }}'"
                                            >
                                            <div class="flex-1">
                                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                                                    الصورة الحالية
                                                </p>
                                                <x-ui.button
                                                    type="button"
                                                    variant="danger"
                                                    size="sm"
                                                    @click="deleteFile('{{ $setting->key }}', $event)"
                                                >
                                                    <i class="fas fa-trash"></i>
                                                    حذف الصورة
                                                </x-ui.button>
                                            </div>
                                        </div>
                                        <template x-if="deletedFiles['{{ $setting->key }}']">
                                            <input
                                                type="hidden"
                                                name="settings[{{ $setting->key }}_delete]"
                                                value="1"
                                            >
                                        </template>
                                    </div>
                                @endif

                                <input
                                    type="file"
                                    name="{{ $settingKey }}"
                                    id="file-{{ $setting->key }}"
                                    accept="image/*"
                                    class="mt-1 block w-full text-sm text-gray-500
                                                                                        file:mr-4 file:py-2 file:px-4
                                                                                        file:rounded-lg file:border-0
                                                                                        file:text-sm file:font-semibold
                                                                                        file:bg-primary-50 file:text-primary-700
                                                                                        hover:file:bg-primary-100
                                                                                        dark:file:bg-primary-900 dark:file:text-primary-300
                                                                                        dark:hover:file:bg-primary-800
                                                                                        file:cursor-pointer
                                                                                        cursor-pointer
                                                                                        border border-gray-300 dark:border-gray-600 rounded-lg
                                                                                        dark:bg-gray-700 dark:text-white"
                                    @change="previewImage($event, '{{ $setting->key }}')"
                                >

                                <div
                                    x-show="previews['{{ $setting->key }}']"
                                    class="mt-3"
                                    style="display: none;"
                                >
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">معاينة الصورة الجديدة:</p>
                                    <img
                                        :src="previews['{{ $setting->key }}']"
                                        alt="Preview"
                                        class="w-24 h-24 object-cover rounded-lg border border-gray-300 dark:border-gray-600"
                                    >
                                </div>

                                @error("settings.{$setting->key}")
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">
                                        <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                    </p>
                                @enderror
                            </div>
                        @elseif($setting->type === 'boolean' || $setting->type === 'bool')
                            {{-- معالجة القيم المنطقية --}}
                            <div class="mb-4">
                                {{-- Hidden input لإرسال '0' عندما يكون checkbox غير محدد --}}
                                <input
                                    type="hidden"
                                    name="{{ $settingKey }}"
                                    value="0"
                                >
                                <x-form.checkbox
                                    name="{{ $settingKey }}"
                                    label="{{ $setting->label }}"
                                    :checked="old(
                                        'settings.' . $setting->key,
                                        filter_var($currentValue, FILTER_VALIDATE_BOOLEAN),
                                    )"
                                />
                            </div>
                        @elseif($setting->type === 'int' || $setting->type === 'integer')
                            {{-- معالجة الأرقام الصحيحة --}}
                            @if (isset($enumSettings[$setting->key]))
                                {{-- إذا كان الإعداد موجوداً في enumSettings، اعرض select --}}
                                <x-form.select
                                    name="{{ $settingKey }}"
                                    label="{{ $setting->label }}"
                                    :options="$enumSettings[$setting->key]"
                                    selected="{{ old('settings.' . $setting->key, $currentValue) }}"
                                />
                            @else
                                {{-- إذا لم يكن enum، اعرض input رقمي --}}
                                <x-form.input
                                    name="{{ $settingKey }}"
                                    label="{{ $setting->label }}"
                                    type="number"
                                    value="{{ old('settings.' . $setting->key, $currentValue) }}"
                                />
                            @endif
                        @elseif($setting->type === 'array')
                            @if (isset($enumSettings[$setting->key]))
                                <x-form.multi-select
                                    :name="$settingKey"
                                    :label="$setting->label"
                                    :selected="$currentValue ?? []"
                                    :options="$enumSettings[$setting->key]"
                                />
                            @else
                                <x-ui.alert
                                    type="warning"
                                    class="mb-4"
                                >
                                    <i class="fas fa-exclamation-triangle mr-2"></i>
                                    لا توجد خيارات محددة لهذا الإعداد من نوع array.
                                </x-ui.alert>
                            @endif
                        @elseif($setting->type === 'json')
                            {{-- معالجة JSON مع واجهة key-value ديناميكية --}}
                            @php
                                $jsonData = [];
                                if ($currentValue) {
                                    if (is_string($currentValue)) {
                                        $decoded = json_decode($currentValue, true);
                                        $jsonData = is_array($decoded) ? $decoded : [];
                                    } elseif (is_array($currentValue) || is_object($currentValue)) {
                                        $jsonData = (array) $currentValue;
                                    }
                                }
                                // التأكد من أن البيانات مصفوفة من أزواج key-value
                                if (empty($jsonData)) {
                                    $jsonData = [['key' => '', 'value' => '']];
                                } else {
                                    // تحويل المصفوفة الترابطية إلى مصفوفة من أزواج key-value
                                    $jsonData = collect($jsonData)
                                        ->map(function ($value, $key) {
                                            return ['key' => $key, 'value' => $value];
                                        })
                                        ->values()
                                        ->toArray();
                                }
                            @endphp
                            <div
                                class="mb-4"
                                x-data="jsonKeyValue('{{ $setting->key }}', @js($jsonData))"
                            >
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ $setting->label }}
                                </label>

                                <div class="space-y-3">
                                    <template
                                        x-for="(item, index) in items"
                                        :key="index"
                                    >
                                        <div class="flex gap-2 items-start">
                                            <div class="flex-1">
                                                <label
                                                    x-show="index === 0"
                                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                                                >
                                                    المفتاح
                                                </label>
                                                <input
                                                    type="text"
                                                    x-model="item.key"
                                                    placeholder="مثال: facebook"
                                                    class="mt-1 block w-full rounded-lg px-3 py-2 border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm transition duration-150 ease-in-out"
                                                />
                                            </div>
                                            <div class="flex-1">
                                                <label
                                                    x-show="index === 0"
                                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                                                >
                                                    القيمة
                                                </label>
                                                <input
                                                    type="text"
                                                    x-model="item.value"
                                                    placeholder="مثال: https://facebook.com/school"
                                                    class="mt-1 block w-full rounded-lg px-3 py-2 border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm transition duration-150 ease-in-out"
                                                />
                                            </div>
                                            <div
                                                class="flex items-end gap-2"
                                                :class="index === 0 ? 'pt-6' : ''"
                                            >
                                                <x-ui.button
                                                    type="button"
                                                    variant="danger"
                                                    size="sm"
                                                    x-on:click="removeItem(index)"
                                                    x-bind:disabled="items.length === 1"
                                                >
                                                    <i class="fas fa-trash"></i>
                                                </x-ui.button>
                                            </div>
                                        </div>
                                    </template>

                                    <x-ui.button
                                        type="button"
                                        variant="outline"
                                        size="sm"
                                        x-on:click="addItem()"
                                        class="w-full mt-2"
                                    >
                                        <i class="fas fa-plus"></i>
                                        إضافة زوج جديد
                                    </x-ui.button>
                                </div>

                                {{-- Hidden input لتخزين البيانات كـ JSON --}}
                                <input
                                    type="hidden"
                                    name="{{ $settingKey }}"
                                    :value="JSON.stringify(getJsonObject())"
                                >

                                @error("settings.{$setting->key}")
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">
                                        <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                    </p>
                                @enderror
                            </div>
                        @elseif($setting->type === 'date')
                            <x-form.input
                                name="{{ $settingKey }}"
                                label="{{ $setting->label }}"
                                type="date"
                                value="{{ old('settings.' . $setting->key, $currentValue) }}"
                            />
                        @else
                            {{-- معالجة النصوص العادية --}}
                            <x-form.input
                                name="{{ $settingKey }}"
                                label="{{ $setting->label }}"
                                type="text"
                                value="{{ old('settings.' . $setting->key, $currentValue) }}"
                            />
                        @endif
                    @endforeach
                </div>
            </x-ui.card>
        @endforeach

        <div class="flex items-center justify-end gap-4 mt-6">
            <x-ui.button
                type="submit"
                variant="primary"
            >
                <i class="fas fa-save mr-2"></i>
                حفظ الإعدادات
            </x-ui.button>
        </div>
    </form>

    @push('scripts')
        <script>
            function settingsForm() {
                return {
                    previews: {},
                    deletedFiles: {},
                    previewImage(event, key) {
                        const file = event.target.files[0];
                        if (file) {
                            const reader = new FileReader();
                            reader.onload = (e) => {
                                this.previews[key] = e.target.result;
                            };
                            reader.readAsDataURL(file);
                        } else {
                            this.previews[key] = null;
                        }
                    },

                    deleteFile(key, event) {
                        this.deletedFiles[key] = true;
                        // إخفاء معاينة الصورة الحالية
                        const previewContainer = event.target.closest('.bg-gray-50, .bg-gray-700');
                        if (previewContainer) {
                            previewContainer.style.display = 'none';
                        }
                        // إعادة تعيين حقل الملف
                        const fileInput = document.getElementById(`file-${key}`);
                        if (fileInput) {
                            fileInput.value = null;
                        }
                        // إخفاء معاينة الصورة الجديدة
                        this.previews[key] = null;
                    }
                }
            }

            function jsonKeyValue(settingKey, initialData) {
                return {
                    items: initialData && initialData.length > 0 ? initialData : [{
                        key: '',
                        value: ''
                    }],

                    addItem() {
                        this.items.push({
                            key: '',
                            value: ''
                        });
                    },

                    removeItem(index) {
                        if (this.items.length > 1) {
                            this.items.splice(index, 1);
                        }
                    },

                    getJsonObject() {
                        const obj = {};
                        this.items.forEach(item => {
                            if (item.key && item.key.trim() !== '') {
                                obj[item.key.trim()] = item.value || '';
                            }
                        });
                        // إرجاع null إذا كان الكائن فارغاً
                        return Object.keys(obj).length === 0 ? null : obj;
                    }
                }
            }
        </script>
    @endpush
</x-layouts.dashboard>
