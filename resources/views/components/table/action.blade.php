@props([
    'icon' => 'fas fa-circle',
    'variant' => 'primary', // primary, success, danger, warning, info, secondary
    'title' => null,
    'as' => 'button',
    'permissions' => null,
])
@if ($permissions && auth()->user()->cannot($permissions))
@else
    @php
        $as = $attributes->has('href') ? 'a' : $as;
        $baseClasses =
            'relative inline-flex items-center justify-center p-2 rounded-lg transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-inset';

        $variantClasses = [
            'primary' =>
                'text-primary-600 hover:bg-primary-50 hover:text-primary-700 dark:text-primary-400 dark:hover:bg-primary-900/20 dark:hover:text-primary-300 focus:ring-primary-500',
            'success' =>
                'text-success-600 hover:bg-success-50 hover:text-success-700 dark:text-success-400 dark:hover:bg-success-900/20 dark:hover:text-success-300 focus:ring-success-500',
            'danger' =>
                'text-danger-600 hover:bg-danger-50 hover:text-danger-700 dark:text-danger-400 dark:hover:bg-danger-900/20 dark:hover:text-danger-300 focus:ring-danger-500',
            'warning' =>
                'text-warning-600 hover:bg-warning-50 hover:text-warning-700 dark:text-warning-400 dark:hover:bg-warning-900/20 dark:hover:text-warning-300 focus:ring-warning-500',
            'info' =>
                'text-info-600 hover:bg-info-50 hover:text-info-700 dark:text-info-400 dark:hover:bg-info-900/20 dark:hover:text-info-300 focus:ring-info-500',
            'secondary' =>
                'text-secondary-600 hover:bg-secondary-50 hover:text-secondary-700 dark:text-secondary-400 dark:hover:bg-secondary-900/20 dark:hover:text-secondary-300 focus:ring-secondary-500',
        ];

        $variantClass = $variantClasses[$variant] ?? $variantClasses['primary'];
    @endphp

    <{{ $as }}
        {{ $attributes->merge([
            'class' => implode(' ', [$baseClasses, $variantClass]),
        ]) }}
    >
        <i class="{{ $icon }} text-sm"></i>
        @if ($title)
            <span class="sr-only">{{ $title }}</span>
        @endif
        </{{ $as }}>
@endif
