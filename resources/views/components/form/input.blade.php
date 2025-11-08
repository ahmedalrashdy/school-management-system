@props([
    'name',
    'label' => null,
    'type' => 'text',
    'required' => false,
    'value' => '',
    'icon' => null,
    'readonly' => false,
    'errorKey' => null,
    'removeMargin' => false,
])

@php
    //convert name to html name formate (name.x=>name[x]) for submit form
    $htmlName = $name;
    if (!str_contains($name, '[') && str_contains($name, '.')) {
        $parts = explode('.', $name);
        $htmlName = array_shift($parts);
        foreach ($parts as $part) {
            $htmlName .= "[$part]";
        }
    }

    //convert html name to dotname (name.x=>name[x]) for display errors
    $dotName = str_replace(['[', ']'], ['.', ''], $htmlName);
    $dotName = preg_replace('/\.+/', '.', $dotName); // تنظيف النقاط المزدوجة
    $dotName = rtrim($dotName, '.');

    //use passed error key if exists for  display error (this for name[] case)
    $finalErrorKey = $errorKey ?? $dotName;

    $hasError = $errors->has($finalErrorKey);

    // 5. إنشاء ID فريد
    $id = $attributes->get('id', str_replace('.', '-', $dotName));

    // 6. التحقق من وجود ربط Livewire
    $isWired = $attributes->whereStartsWith(['wire:model'])->isNotEmpty();
    // تحديد القيمة القديمة
    $oldValue = $isWired ? null : old($dotName, $value);
@endphp

<div class="{{ $removeMargin ? '' : ' mb-4' }}">
    {{-- Label --}}
    @if ($label)
        <label
            class="block text-sm font-medium mb-2 'text-gray-700 dark:text-gray-300"
            for="{{ $id }}"
        >
            {{ $label }}
            @if ($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    <div class="relative">
        {{-- Icon --}}
        @if ($icon)
            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                <i class="{{ $icon }} {{ $hasError ? 'text-red-400' : 'text-gray-400' }}"></i>
            </div>
        @endif
        {{-- Input Field --}}
        <input
            id="{{ $id }}"
            name="{{ $htmlName }}"
            type="{{ $type }}"
            @if (!is_null($oldValue)) value="{{ $oldValue }}" @endif
            @if ($required) required @endif
            @if ($readonly) readonly @endif
            {{ $attributes->merge([
                'class' =>
                    'mt-1 block w-full  py-2 rounded-lg shadow-sm sm:text-sm transition duration-150 ease-in-out ' .
                    ($icon ? 'ps-10 pe-3 ' : 'px-3 ') .
                    ($readonly ? 'bg-gray-100 dark:bg-gray-800 cursor-not-allowed ' : '') .
                    ($hasError
                        ? 'border border-red-300  focus:border-red-500 focus:ring-red-500 dark:bg-gray-700 dark:border-red-500 dark:text-white'
                        : 'border-gray-300 focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white'),
            ]) }}
        >
    </div>

    {{-- Error Message --}}
    @if ($hasError)
        <p class="mt-2 text-xs text-red-600 dark:text-red-400 flex items-center ">
            <i class="fas fa-exclamation-circle mr-1"></i>
            {{ $errors->first($finalErrorKey) }}
        </p>
    @endif
</div>
