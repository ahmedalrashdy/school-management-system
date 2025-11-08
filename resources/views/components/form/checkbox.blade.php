@props(['name', 'label' => null, 'checked' => false, 'value' => '1', 'errorKey' => null])

@php
    // 1. تحويل الاسم للصيغة HTML (للإرسال)
    // يحول name.x => name[x]
    $htmlName = $name;
    if (!str_contains($name, '[') && str_contains($name, '.')) {
        $parts = explode('.', $name);
        $htmlName = array_shift($parts);
        foreach ($parts as $part) {
            $htmlName .= "[$part]";
        }
    }

    // 2. تحويل الاسم للصيغة Dot Notation (للتحقق من الأخطاء)
    // يحول name[x] => name.x
    $dotName = str_replace(['[', ']'], ['.', ''], $htmlName);
    $dotName = preg_replace('/\.+/', '.', $dotName);
    $dotName = rtrim($dotName, '.');

    // 3. تحديد مفتاح الخطأ النهائي
    $finalErrorKey = $errorKey ?? $dotName;
    $hasError = $errors->has($finalErrorKey);

    // 4. إنشاء ID فريد
    $id = $attributes->get('id', str_replace('.', '-', $dotName));

    // 5. التحقق من وجود ربط Livewire أو Alpine
    // إذا وجدنا wire:model أو x-model، نتوقف عن فرض حالة checked يدوياً
    $isWired = $attributes->whereStartsWith(['wire:model', 'x-model'])->isNotEmpty();

    // تحديد حالة الاختيار (فقط إذا لم يكن مربوطاً بـ Livewire/Alpine)
    // ملاحظة: old() يعيد "1" أو "on" أحياناً، لذا نتحقق بمرونة
    $oldVal = old($dotName);
    $isChecked = $isWired ? false : ($oldVal !== null ? $oldVal == $value : $checked);
@endphp

<div class="mb-4">
    <div class="flex items-center gap-2">
        <input
            id="{{ $id }}"
            name="{{ $htmlName }}"
            type="checkbox"
            value="{{ $value }}"
            {{-- إذا لم يكن هناك wire:model، نستخدم منطق Blade العادي للتحديد --}}
            @if (!$isWired) @checked($isChecked) @endif
            {{ $attributes->merge([
                'class' =>
                    'h-4 w-4 rounded transition duration-150 ease-in-out ' .
                    ($hasError
                        ? 'border-red-500 text-red-600 focus:ring-red-500' // عند الخطأ: حدود حمراء
                        : 'border-gray-300 text-primary-600 focus:ring-primary-500'), // الوضع الطبيعي
            ]) }}
        >

        @if ($label)
            <label
                class="text-sm text-gray-700 dark:text-gray-300 select-none"
                for="{{ $id }}"
            >
                {{ $label }}
            </label>
        @endif
    </div>

    @if ($hasError)
        <p class="mt-2 text-xs text-red-600 dark:text-red-400 flex items-center">
            <i class="fas fa-exclamation-circle mr-1"></i>
            {{ $errors->first($finalErrorKey) }}
        </p>
    @endif
</div>
