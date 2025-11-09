@props([
    'variant' => 'default', // default, primary, secondary, success, danger, warning, info
    'size' => 'md', // sm, md, lg
])

@php
    $variantClasses = [
        'default' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
        'primary' => 'bg-primary-100 text-primary-800 dark:bg-primary-900 dark:text-primary-300',
        'secondary' => 'bg-secondary-100 text-secondary-800 dark:bg-secondary-900 dark:text-secondary-300',
        'success' => 'bg-success-100 text-success-800 dark:bg-success-900 dark:text-success-300',
        'danger' => 'bg-danger-100 text-danger-800 dark:bg-danger-900 dark:text-danger-300',
        'warning' => 'bg-warning-100 text-warning-800 dark:bg-warning-900 dark:text-warning-300',
        'info' => 'bg-info-100 text-info-800 dark:bg-info-900 dark:text-info-300',
    ];
    
    $sizeClasses = [
        'sm' => 'text-xs px-2 py-0.5',
        'md' => 'text-sm px-2.5 py-0.5',
        'lg' => 'text-base px-3 py-1',
    ];
@endphp

<span {{ $attributes->merge([
    'class' => implode(' ', [
        'inline-flex items-center font-medium rounded-full',
        $variantClasses[$variant] ?? $variantClasses['default'],
        $sizeClasses[$size] ?? $sizeClasses['md'],
    ])
]) }}>
    {{ $slot }}
</span>

