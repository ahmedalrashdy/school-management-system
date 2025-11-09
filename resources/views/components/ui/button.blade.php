@props([
    'as' => 'button',
    'variant' => 'primary', // primary, secondary, danger, success, warning
    'size' => 'md', // sm, md, lg
    'icon' => null,
    'loading' => false,
    'disabled' => false,
    'permissions' => null,
])
@if ($permissions && auth()->user()->cannot($permissions))
@else
    @php
        $baseClasses =
            'inline-flex gap-1 items-center justify-center font-medium rounded-lg transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-inset';

        $variantClasses = [
            'primary' => 'bg-primary-600 text-white hover:bg-primary-700 focus:ring-primary-500',
            'secondary' => 'bg-secondary-600 text-white hover:bg-secondary-700 focus:ring-secondary-500',
            'danger' => 'bg-danger-600 text-white hover:bg-danger-700 focus:ring-danger-500',
            'success' => 'bg-success-600 text-white hover:bg-success-700 focus:ring-success-500',
            'warning' => 'bg-warning-600 text-white hover:bg-warning-700 focus:ring-warning-500',
            'info' => 'bg-info-600 text-white hover:bg-info-700 focus:ring-info-500',
            'outline' =>
                'border-2 border-gray-300 text-gray-700 hover:bg-gray-50 focus:ring-gray-500 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-800',
        ];

        $sizeClasses = [
            'sm' => 'px-3 py-1.5 text-sm',
            'md' => 'px-4 py-2 text-sm',
            'lg' => 'px-6 py-3 text-base ',
        ];

    @endphp

    <{{ $as }}
        {{ $attributes->merge([
            'class' => implode(' ', [
                $baseClasses,
                $variantClasses[$variant] ?? $variantClasses['primary'],
                $sizeClasses[$size] ?? $sizeClasses['md'],
            ]),
        ]) }}
        @disabled($disabled)
    >
        @if ($loading)
            <i class="fas fa-spinner  fa-spin mr-2"></i>
        @elseif($icon)
            <i class="{{ $icon }} mr-2"></i>
        @endif

        {{ $slot }}
        </{{ $as }}>

@endif
