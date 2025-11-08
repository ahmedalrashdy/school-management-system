@props([
    'name',
    'label' => null,
    'required' => false,
    'placeholder' => null,
    'options' => [],
    'selected' => null,
    'disabled' => false,
    'errorKey' => null,
])

@php
    // 1. تحويل الاسم للصيغة HTML (للإرسال)
    $htmlName = $name;
    if (!str_contains($name, '[') && str_contains($name, '.')) {
        $parts = explode('.', $name);
        $htmlName = array_shift($parts);
        foreach ($parts as $part) {
            $htmlName .= "[$part]";
        }
    }

    // 2. تحويل الاسم للصيغة Dot Notation (للأخطاء والقيم القديمة)
    $dotName = str_replace(['[', ']'], ['.', ''], $htmlName);
    $dotName = preg_replace('/\.+/', '.', $dotName);
    $dotName = rtrim($dotName, '.');

    // 3. تحديد مفتاح الخطأ
    $finalErrorKey = $errorKey ?? $dotName;
    $hasError = $errors->has($finalErrorKey);

    // 4. إنشاء ID
    $id = $attributes->get('id', str_replace('.', '-', $dotName));

    // 5. حالة التعطيل
    $isDisabled = $attributes->get('disabled', false) || $disabled;

    // 6. منطق القيمة المختارة (Selected Logic)
    // إذا كان هناك ربط Livewire/Alpine، نترك التحديد لهم لنتجنب التضارب
    $isWired = $attributes->whereStartsWith(['wire:model', 'x-model'])->isNotEmpty();
    
    // القيمة التي يجب تحديدها في حالة Blade العادي
    $valueToSelect = $isWired ? null : old($dotName, $selected);
@endphp

<div class="mb-4">
    @if ($label)
        <label
            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
            for="{{ $id }}"
        >
            {{ $label }}
            @if ($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    <div class="relative">
        <select
            id="{{ $id }}"
            name="{{ $htmlName }}"
            @if ($required) required @endif
            @if ($isDisabled) disabled @endif
            {{ $attributes->merge([
                'class' =>
                    'mt-1 block w-full pl-3 pr-10 py-2 text-base rounded-lg shadow-sm sm:text-sm transition duration-150 ease-in-out ' .
                    ($isDisabled ? 'bg-gray-100 dark:bg-gray-800 cursor-not-allowed opacity-60 ' : '') .
                    ($hasError
                        ? 'border-red-500 focus:border-red-500 focus:ring-red-500 dark:bg-gray-700 dark:border-red-500 dark:text-white' // أحمر عند الخطأ
                        : 'border-gray-300 focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white'), // رمادي عادي
            ]) }}
        >
            @if ($placeholder)
                <option value="">{{ $placeholder }}</option>
            @endif

            @foreach ($options as $optValue => $optLabel)
                <option
                    value="{{ $optValue }}"
                    @if(!$isWired && $valueToSelect == $optValue) selected @endif
                >
                    {{ $optLabel }}
                </option>
            @endforeach
        </select>
    </div>

    @if ($hasError)
        <p class="mt-2 text-xs text-red-600 dark:text-red-400 flex items-center">
            <i class="fas fa-exclamation-circle mr-1"></i>
            {{ $errors->first($finalErrorKey) }}
        </p>
    @endif
</div>