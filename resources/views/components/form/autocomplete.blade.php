@props([
    'name',
    'label' => null,
    'resource',
    'endpoint' => route('common.autocomplete'),
    'multiple' => false,
    'placeholder' => 'ابدأ بالكتابة للبحث...',
    'searchPlaceholder' => 'ابحث باسم الطالب أو رقم القيد',
    'noResultsText' => 'لا توجد نتائج لعرضها',
    'loadingText' => 'جارِ التحميل...',
    'fetchMoreText' => 'جارِ تحميل المزيد...',
    'errorText' => 'حدث خطأ أثناء جلب البيانات',
    'retryText' => 'حاول مجدداً',
    'selected' => [],
    'perPage' => 10,
    'minCharacters' => 0,
    'xModel' => null,
    'disabled' => false,
])

@php
    $normalizedSelected = collect($selected)->map(function ($item) {
        if (is_array($item)) {
            return [
                'id' => $item['id'] ?? null,
                'text' => $item['text'] ?? ($item['label'] ?? null),
            ];
        }

        if (is_object($item)) {
            return [
                'id' => $item->id ?? null,
                'text' => $item->text ?? $item->label ?? null,
            ];
        }

        return [
            'id' => $item,
            'text' => (string) $item,
        ];
    })->filter(fn ($item) => ! is_null($item['id']) && ! is_null($item['text']))->values();

    $config = [
        'name' => $name,
        'resource' => $resource,
        'endpoint' => $endpoint,
        'multiple' => (bool) $multiple,
        'placeholder' => $placeholder,
        'searchPlaceholder' => $searchPlaceholder,
        'noResultsText' => $noResultsText,
        'loadingText' => $loadingText,
        'fetchMoreText' => $fetchMoreText,
        'errorText' => $errorText,
        'retryText' => $retryText,
        'perPage' => (int) $perPage,
        'minCharacters' => (int) $minCharacters,
        'initialSelected' => $normalizedSelected->all(),
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
        x-data="autocompleteField(@js($config))"
        @if($xModel)
         @if($multiple) x-modelable='selectedValues' @else x-modelable='selectedValue'  @endif
         x-model="{{ $xModel }}" 
        @endif
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
                    <template x-for="item in selectedItems" :key="item.id">
                        <span class="inline-flex items-center gap-2 rounded-full bg-primary-100 px-3 py-1 text-xs font-medium text-primary-700 dark:bg-primary-900 dark:text-primary-200">
                            <span x-text="item.text"></span>
                            <button
                                type="button"
                                class="text-primary-500 hover:text-primary-700"
                                x-on:click.stop="removeItem(item.id)"
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
            class="absolute z-200 mt-2 w-full rounded-lg border border-gray-200 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-800"
            x-on:click.away="closeDropdown"
        >
            <div class="border-b border-gray-100 p-3 dark:border-gray-700">
                <div class="relative">
                    <i class="fas fa-search absolute right-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input
                        type="text"
                        x-model="search"
                        x-on:input.debounce.300ms="handleSearchInput"
                        :placeholder="searchPlaceholder"
                        class="w-full rounded-lg border border-gray-200 py-2 pr-9 pl-3 text-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"
                    >
                </div>
            </div>

            <div class="max-h-72 overflow-y-auto p-1" x-on:scroll.passive="handleScroll">
                <template x-if="isLoading && !items.length">
                    <p class="px-4 py-3 text-center text-sm text-gray-500 dark:text-gray-400" x-text="loadingText"></p>
                </template>

                <template x-if="!isLoading && !items.length && !error">
                    <p class="px-4 py-3 text-center text-sm text-gray-500 dark:text-gray-400" x-text="noResultsText"></p>
                </template>

                <template x-for="item in items" :key="item.id">
                    <button
                        type="button"
                        class="flex w-full items-center justify-between rounded-md px-4 py-2 text-sm hover:bg-gray-50 dark:hover:bg-gray-700"
                        :class="{'bg-primary-50 dark:bg-primary-900/40': isSelected(item.id)}"
                        x-on:click="selectItem(item)"
                    >
                        <span class="text-gray-700 dark:text-gray-100" x-text="item.text"></span>
                        <span x-show="isSelected(item.id)" class="text-primary-500">
                            <i class="fas fa-check"></i>
                        </span>
                    </button>
                </template>

                <div x-show="isFetchingMore" class="px-4 py-3 text-center text-sm text-gray-500 dark:text-gray-400" x-text="fetchMoreText"></div>
            </div>

            <div x-show="error" class="border-t border-gray-100 px-4 py-3 text-sm text-danger-600 dark:border-gray-700 dark:text-danger-400">
                <div class="flex items-center justify-between gap-4">
                    <span x-text="error"></span>
                    <button type="button" class="text-primary-600 hover:text-primary-700 dark:text-primary-300" x-on:click="retryFetch">
                        <i class="fas fa-rotate-right mr-1"></i>
                        <span x-text="retryText"></span>
                    </button>
                </div>
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

