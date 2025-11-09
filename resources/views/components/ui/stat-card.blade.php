@props([
    'title',
    'value',
    'icon',
    'color' => 'primary', // primary, secondary, success, danger, warning, info
    'trend' => null, // 'up', 'down', or null
    'trendValue' => null,
])

@php
    $colorClasses = [
        'primary' => 'bg-primary-500',
        'secondary' => 'bg-secondary-500',
        'success' => 'bg-success-500',
        'danger' => 'bg-danger-500',
        'warning' => 'bg-warning-500',
        'info' => 'bg-info-500',
    ];
@endphp

<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
    <div class="flex items-center justify-between">
        <div class="flex-1">
            <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">
                {{ $title }}
            </p>
            <p class="text-3xl font-bold text-gray-900 dark:text-white">
                {{ $value }}
            </p>
            
            @if($trend && $trendValue)
                <div class="flex items-center gap-1 mt-2">
                    <i class="fas fa-arrow-{{ $trend === 'up' ? 'up' : 'down' }} text-sm {{ $trend === 'up' ? 'text-success-600' : 'text-danger-600' }}"></i>
                    <span class="text-sm {{ $trend === 'up' ? 'text-success-600' : 'text-danger-600' }}">
                        {{ $trendValue }}
                    </span>
                    <span class="text-sm text-gray-500 dark:text-gray-400">من الشهر الماضي</span>
                </div>
            @endif
        </div>
        
        <div class="flex-shrink-0">
            <div class="w-12 h-12 rounded-lg {{ $colorClasses[$color] ?? $colorClasses['primary'] }} flex items-center justify-center">
                <i class="{{ $icon }} text-white text-xl"></i>
            </div>
        </div>
    </div>
</div>

