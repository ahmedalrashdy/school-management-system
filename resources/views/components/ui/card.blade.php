@props([
    'title' => null,
    'icon' => null,
    'padding' => true,
])

<div
    {{ $attributes->merge(['class' => 'bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700']) }}>
    @if ($title || $icon)
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center gap-3">
                @if ($icon)
                    <div class="shrink-0">
                        <i class="{{ $icon }} text-primary-600 text-xl"></i>
                    </div>
                @endif
                @if ($title)
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        {{ $title }}
                    </h3>
                @endif
            </div>
        </div>
    @endif
    <div class="{{ $padding ? 'p-6' : '' }}">
        {{ $slot }}
    </div>
</div>
