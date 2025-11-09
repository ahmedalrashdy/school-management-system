@props([
    'variant' => 'default',
    'lines' => 3,
    'width' => null,
])

@php
    // إعداد الكلاسات الأساسية
    $widthClass = $width ? "w-[$width]" : 'w-full'; // افتراضياً عرض كامل إذا لم يحدد
    
    $variants = [
        'default' => 'h-20 bg-gray-200 dark:bg-gray-700 rounded animate-pulse',
        'text' => 'h-4 bg-gray-200 dark:bg-gray-700 rounded animate-pulse',
        'avatar' => 'h-20 w-20 bg-gray-200 dark:bg-gray-700 rounded-full animate-pulse',
        'card' => 'h-32 bg-gray-200 dark:bg-gray-700 rounded-lg animate-pulse',
        'table' => 'h-12 bg-gray-200 dark:bg-gray-700 rounded animate-pulse',
    ];
    
    $baseClass = $variants[$variant] ?? $variants['default'];
@endphp

<div class="{{ $widthClass }}"> <!-- Root Wrapper: يمنع الخطأ تماماً -->
    @if($variant === 'text' && $lines > 1)
        <div class="space-y-2">
            @for($i = 0; $i < $lines; $i++)
                <div class="{{ $baseClass }} w-full" style="{{ $i === $lines - 1 ? 'width: 60%' : '' }}"></div>
            @endfor
        </div>
    @elseif($variant === 'card')
        <div class="space-y-4">
            <div class="{{ $baseClass }}"></div>
            <div class="space-y-2">
                @for($i = 0; $i < $lines; $i++)
                    <div class="{{ $variants['text'] }} w-full" style="{{ $i === $lines - 1 ? 'width: 70%' : '' }}"></div>
                @endfor
            </div>
        </div>
    @elseif($variant === 'table')
        <div class="space-y-3">
            @for($i = 0; $i < $lines; $i++)
                <div class="flex gap-4">
                    <div class="{{ $baseClass }} flex-1"></div>
                    <div class="{{ $baseClass }} flex-1"></div>
                    <div class="{{ $baseClass }} flex-1"></div>
                    <div class="{{ $baseClass }} w-24"></div>
                </div>
            @endfor
        </div>
    @else
        <div class="{{ $baseClass }} w-full"></div>
    @endif
</div>