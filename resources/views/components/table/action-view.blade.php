@props([
    'as' => 'button',
    'title' => 'عرض',
    'permissions' => null,
])
@if ($permissions && auth()->user()->cannot($permissions))
@else
    @php
        $as = $attributes->has('href') ? 'a' : $as;
        $baseClasses =
            'relative inline-flex items-center justify-center p-2 rounded-lg transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-inset';
        $variantClasses =
            'text-info-600 hover:bg-info-50 hover:text-info-700 dark:text-info-400 dark:hover:bg-info-900/20 dark:hover:text-info-300 focus:ring-info-500';
    @endphp

    <{{ $as }}
        {{ $attributes->merge([
            'class' => implode(' ', [$baseClasses, $variantClasses]),
        ]) }}
    >
        <i class="fas fa-eye text-sm"></i>
        <span class="sr-only">{{ $title }}</span>
        </{{ $as }}>
@endif
