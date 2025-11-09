@props([
    'icon' => 'fas fa-inbox',
    'title' => null,
    'description' => null,
    'action' => null, // ['label' => '...', 'url' => '...', 'icon' => '...']
    'padding' => true,
])

@php
    $paddingClass = $padding ? 'py-12' : '';
@endphp

<div class="text-center {{ $paddingClass }}">
    @if ($icon)
        <i class="{{ $icon }} text-4xl text-gray-400 dark:text-gray-600 mb-4"></i>
    @endif

    @if ($title)
        <p class="text-gray-500 dark:text-gray-400 {{ $description ? 'mb-4' : ($action ? 'mb-4' : '') }}">
            {{ $title }}
        </p>
    @endif

    @if ($description)
        <p class="text-sm text-gray-500 dark:text-gray-400 {{ $action ? 'mb-4' : '' }}">
            {{ $description }}
        </p>
    @endif

    @if ($action)
        @if (is_array($action))
            @php
                $actionLabel = $action['label'] ?? null;
                $actionUrl = $action['url'] ?? null;
                $actionIcon = $action['icon'] ?? 'fas fa-plus';
                $actionVariant = $action['variant'] ?? 'primary';
            @endphp

            @if ($actionUrl && $actionLabel)
                <x-ui.button
                    as="a"
                    href="{{ $actionUrl }}"
                    variant="{{ $actionVariant }}"
                    class="mt-4"
                >
                    @if ($actionIcon)
                        <i class="{{ $actionIcon }}"></i>
                    @endif
                    {{ $actionLabel }}
                </x-ui.button>
            @endif
        @else
            {{-- Slot للعمل المخصص --}}
            <div class="mt-4">
                {{ $action }}
            </div>
        @endif
    @endif

    {{-- Slot إضافي للمحتوى المخصص --}}
    {{ $slot }}
</div>
