@props([
    'name',
    'label' => null,
    'options' => [],
    'selected' => [],
    'placeholder' => 'اختر من القائمة',
    'searchPlaceholder' => 'ابحث...',
    'noResultsText' => 'لا توجد نتائج مطابقة',
    'multiple' => true,
    'searchable' => false,
    'model' => null,
    'disabled' => false,
])

@php
    
    $normalizeOption = static function ($value, $label, $meta = []) {
        return [
            'value' => $value,
            'label' => $label,
            'meta' => (object) $meta,
        ];
    };

    $normalizedOptions = collect($options)->map(function ($option, $value) use ($normalizeOption) {
        if (is_array($option)) {
            $actualValue = $option['value'] ?? $option['id'] ?? $value;
            $label = $option['label'] ?? $option['text'] ?? $actualValue;

            return $normalizeOption($actualValue, $label, $option['meta'] ?? []);
        }

        if (is_object($option) && isset($option->value, $option->label)) {
            return $normalizeOption($option->value, $option->label, (array) ($option->meta ?? []));
        }

        if (! is_string($value) && ! is_numeric($value)) {
            return $normalizeOption($option, $option);
        }

        return $normalizeOption($value, $option);
    })->values();

    $normalizedSelected = collect($selected)->map(function ($item) use ($normalizeOption) {
        if (is_array($item)) {
            $value = $item['value'] ?? $item['id'] ?? null;
            $label = $item['label'] ?? $item['text'] ?? $value;

            return $value ? $normalizeOption($value, $label) : null;
        }

        if (is_object($item)) {
            $value = $item->value ?? $item->id ?? null;
            $label = $item->label ?? $item->text ?? $value;

            return $value ? $normalizeOption($value, $label) : null;
        }

        return $item ? $normalizeOption($item, $item) : null;
    })->filter()->values();

    $preselectedOptions = $normalizedOptions
        ->filter(fn ($option) => $normalizedSelected->contains(fn ($selectedOption) => (string) $selectedOption['value'] === (string) $option['value']))
        ->values();

    $preselectedOptions = $preselectedOptions
        ->merge(
            $normalizedSelected->reject(fn ($selectedOption) => $preselectedOptions->contains(fn ($option) => (string) $option['value'] === (string) $selectedOption['value']))
        )
        ->values();

    $config = [
        'name' => $name,
        'multiple' => (bool) $multiple,
        'searchEnabled' => (bool) $searchable,
        'placeholder' => $placeholder,
        'searchPlaceholder' => $searchPlaceholder,
        'noResultsText' => $noResultsText,
        'options' => $normalizedOptions->all(),
        'initialSelected' => $preselectedOptions->all(),
        'disabled' => (bool) $disabled,
    ];
@endphp

<div class="mb-4">
    @if($label)
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            {{ $label }}
        </label>
    @endif

    <div
        x-data="multiSelect(@js($config)@if($model), @entangle($model).defer @endif)"
        x-on:keydown.escape.window="closeDropdown"
        class="relative"
    >
        <button
            type="button"
            class="flex min-h-[44px] w-full items-center gap-2 rounded-lg border bg-white px-3 py-2 text-right text-sm text-gray-700 shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 disabled:cursor-not-allowed disabled:bg-gray-100 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"
            :class="{
                'ring-2 ring-primary-500 border-primary-500': isOpen,
                'text-gray-400': !hasSelection,
            }"
            x-on:click="toggleDropdown"
            :disabled="disabled"
        >
            <template x-if="hasSelection">
                <div class="flex flex-wrap items-center gap-2">
                    <template x-for="option in selectedOptions" :key="option.value">
                        <span class="inline-flex items-center gap-2 rounded-full bg-primary-100 px-3 py-1 text-xs font-medium text-primary-700 dark:bg-primary-900 dark:text-primary-200">
                            <span x-text="option.label"></span>
                            <button
                                type="button"
                                class="text-primary-500 hover:text-primary-700"
                                x-on:click.stop="removeOption(option.value)"
                            >
                                <i class="fas fa-times"></i>
                            </button>
                        </span>
                    </template>
                </div>
            </template>

            <span x-show="!hasSelection" class="text-gray-400" x-text="placeholder"></span>

            <span class="ml-auto text-gray-400">
                <i class="fas fa-chevron-down" :class="{'rotate-180': isOpen}"></i>
            </span>
        </button>

        <div
            x-cloak
            x-show="isOpen"
            x-transition
            class="absolute z-40 mt-2 w-full rounded-lg border border-gray-200 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-800"
            x-on:click.away="closeDropdown"
        >
            <div x-show="searchEnabled" class="border-b border-gray-100 p-3 dark:border-gray-700">
                <div class="relative">
                    <i class="fas fa-search absolute right-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input
                        type="text"
                        x-model.debounce.200ms="searchTerm"
                        :placeholder="searchPlaceholder"
                        class="w-full rounded-lg border border-gray-200 py-2 pr-9 pl-3 text-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"
                    >
                </div>
            </div>

            <div class="max-h-64 overflow-y-auto p-2">
                <template x-if="!filteredOptions.length">
                    <p class="px-4 py-3 text-center text-sm text-gray-500 dark:text-gray-400" x-text="noResultsText"></p>
                </template>

                <template x-for="option in filteredOptions" :key="option.value">
                    <button
                        type="button"
                        class="flex w-full items-center justify-between rounded-md px-4 py-2 text-sm hover:bg-gray-50 dark:hover:bg-gray-700"
                        :class="{'bg-primary-50 dark:bg-primary-900/40': isSelected(option.value)}"
                        x-on:click="selectOption(option)"
                    >
                        <span class="text-gray-700 dark:text-gray-100" x-text="option.label"></span>
                        <span x-show="isSelected(option.value)" class="text-primary-500">
                            <i class="fas fa-check"></i>
                        </span>
                    </button>
                </template>
            </div>
        </div>

        <template x-for="value in selectedValues" :key="value">
            <input type="hidden" name="{{ $name }}@if($multiple)[]@endif" :value="value">
        </template>
    </div>

    @error($model ?? $name)
        <p class="mt-2 text-sm text-danger-600 dark:text-danger-400">
            <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
        </p>
    @enderror
</div>

