@props([
    'name',
    'label' => null,
    'required' => false,
    'placeholder' => '',
    'value' => '',
    'rows' => 4,
    'readonly' => false,
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

    // 5. التحقق من Livewire/Alpine لتجنب تضارب المحتوى
    $isWired = $attributes->whereStartsWith(['wire:model', 'x-model'])->isNotEmpty();
    
    // محتوى الـ Textarea (لـ Blade التقليدي)
    $content = $isWired ? null : old($dotName, $value);
@endphp

<div class="mb-4">
    @if($label)
        <label 
            for="{{ $id }}" 
            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
        >
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif
    
    <textarea
        id="{{ $id }}"
        name="{{ $htmlName }}"
        rows="{{ $rows }}"
        placeholder="{{ $placeholder }}"
        @if($required) required @endif
        @if($readonly) readonly @endif
        {{ $attributes->merge([
            'class' => 'mt-1 block w-full rounded-lg shadow-sm sm:text-sm transition duration-150 ease-in-out ' . 
            ($readonly ? 'bg-gray-100 dark:bg-gray-800 cursor-not-allowed ' : '') .
            ($hasError 
                ? 'border-red-500 focus:border-red-500 focus:ring-red-500 dark:bg-gray-700 dark:border-red-500 dark:text-white' 
                : 'border-gray-300 focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white'
            )
        ]) }}
    >{{ $content }}</textarea>
    
    @if($hasError)
        <p class="mt-2 text-xs text-red-600 dark:text-red-400 flex items-center">
            <i class="fas fa-exclamation-circle mr-1"></i>
            {{ $errors->first($finalErrorKey) }}
        </p>
    @endif
</div>