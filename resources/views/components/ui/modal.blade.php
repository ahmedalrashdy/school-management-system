@props(['name', 'title', 'maxWidth' => 'lg', 'skipTeleport' => false])
@php
    $maxWidthClasses = [
        'sm' => 'max-w-sm',
        'md' => 'max-w-md',
        'lg' => 'max-w-lg',
        'xl' => 'max-w-xl',
        '2xl' => 'max-w-2xl',
    ];
@endphp


@if (!$skipTeleport)
    <template x-teleport="body">
@endif
<div
    x-data="{ show: false, name: '{{ $name }}', data: {} }"
    x-show="show"
    @open-modal.window="if ($event.detail.name === name){show = true;data=$event.detail; }"
    @close-modal.window="if ($event.detail.name === name) show = false"
    {{-- اختياري: لإغلاقه برمجياً --}}
    @keydown.escape.window="show = false"
    class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto"
    aria-labelledby="{{ $name }}-title"
    role="dialog"
    aria-modal="true"
    style="display: none;"
>
    {{-- Backdrop --}}
    <div
        x-show="show"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-gray-900/80 backdrop-blur-sm"
    ></div>

    {{-- Modal Panel --}}
    <div
        x-show="show"
        @click.outside="show = false"
        x-trap.inert.noscroll="show"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="relative w-full {{ $maxWidthClasses[$maxWidth] ?? $maxWidthClasses['lg'] }} p-6 text-right transition-all transform bg-white dark:bg-gray-800 shadow-xl rounded-2xl"
    >
        <div class="flex items-center justify-between pb-4 border-b dark:border-gray-700">
            <h3
                class="text-lg font-semibold text-gray-900 dark:text-white"
                id="{{ $name }}-title"
            >
                {{ $title }}
            </h3>

            <button
                @click="show = false"
                type="button"
                class="p-1 -mr-2 text-gray-400 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-600 dark:hover:text-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 transition-colors"
            >
                <span class="sr-only">إغلاق</span>
                <svg
                    class="w-6 h-6"
                    xmlns="http://www.w.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    stroke="currentColor"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M6 18L18 6M6 6l12 12"
                    />
                </svg>
            </button>
        </div>

        <div class="mt-5">
            {{ $slot }}
        </div>
    </div>
</div>
@if (!$skipTeleport)
    </template>
@endif
