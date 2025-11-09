@props([
    // Props أساسية
    'name', // اسم المودال الفريد لتشغيله
    'title' => 'تأكيد الإجراء', // عنوان المودال الافتراضي

    // Props للبيانات الديناميكية
    'dataKey' => 'item', // المفتاح المستخدم في Alpine.js لتخزين البيانات

    'actionMethod' => 'POST', // نوع الطلب HTTP الافتراضي
    'spoofMethod' => null, // لتمرير 'DELETE', 'PUT', 'PATCH'
    'confirmButtonText' => 'تأكيد', // نص زر التأكيد الافتراضي
    'confirmButtonVariant' => 'primary', // شكل زر التأكيد (primary, danger, success, etc.)

    // Props لإلغاء الزر
    'cancelButtonText' => 'إلغاء',
    'permissions' => null,
])
@if ($permissions && auth()->user()->cannot($permissions))
@else
    @php
        // مصفوفة لربط أشكال الأزرار بكلاسات Tailwind CSS
        $variants = [
            'primary' => 'bg-indigo-600 hover:bg-indigo-700 focus:ring-indigo-500',
            'danger' => 'bg-danger-600 hover:bg-danger-700 focus:ring-danger-500',
            'success' => 'bg-green-600 hover:bg-green-700 focus:ring-green-500',
            'warning' => 'bg-yellow-500 hover:bg-yellow-600 focus:ring-yellow-400',
        ];
        // اختيار الكلاس المناسب أو استخدام الكلاس الأساسي إذا لم يتم العثور على الشكل
        $confirmButtonClasses = $variants[$confirmButtonVariant] ?? $variants['primary'];
    @endphp

    <x-ui.modal
        :name="$name"
        :title="$title"
    >
        <div
            class="space-y-4"
            x-data="{ {{ $dataKey }}: null }"
            @open-modal.window="if ($event.detail.name === '{{ $name }}' && $event.detail.{{ $dataKey }}) {{ $dataKey }} = $event.detail.{{ $dataKey }}"
            x-show="{{ $dataKey }}"
            x-transition
        >
            {{-- خانة الرسالة الرئيسية (Default Slot) --}}
            <div class="text-gray-700 dark:text-gray-300">
                {{ $slot }}
            </div>

            {{-- خانة اختيارية لإضافة تحذير --}}
            @isset($warning)
                <div
                    class="p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                    {{ $warning }}
                </div>
            @endisset

            {{-- منطقة الأزرار (Actions) --}}
            <div class="flex items-center justify-end gap-3 pt-4">
                {{-- خانة مخصصة للأزرار لتوفير مرونة قصوى --}}
                @isset($actions)
                    {{ $actions }}
                @else
                    {{-- الأزرار الافتراضية إذا لم يتم توفير خانة actions --}}
                    <button
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 transition-colors"
                        type="button"
                        @click="{{ $dataKey }} = null; $dispatch('close-modal', { name: '{{ $name }}' })"
                    >
                        {{ $cancelButtonText }}
                    </button>

                    <form
                        class="inline"
                        method="{{ strtoupper($actionMethod) === 'GET' ? 'GET' : 'POST' }}"
                        x-bind:action="{{ $dataKey }}?.route"
                        x-show="{{ $dataKey }}"
                    >
                        @csrf
                        @if ($spoofMethod)
                            @method(strtoupper($spoofMethod))
                        @endif

                        <button
                            class="px-4 py-2 text-sm font-medium text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition-colors {{ $confirmButtonClasses }}"
                            type="submit"
                        >
                            {{ $confirmButtonText }}
                        </button>
                    </form>
                @endisset
            </div>
        </div>
    </x-ui.modal>
@endif
