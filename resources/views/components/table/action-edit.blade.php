@props([
    'as' => 'button',
    'title' => 'تعديل',
    'permissions' => null,
])
@if ($permissions && auth()->user()->cannot($permissions))
@else
    @php
        $as = $attributes->has('href') ? 'a' : $as;
        $baseClasses =
            'relative inline-flex items-center justify-center p-2 rounded-lg transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-inset';
        $variantClasses =
            'text-primary-600 hover:bg-primary-50 hover:text-primary-700 dark:text-primary-400 dark:hover:bg-primary-900/20 dark:hover:text-primary-300 focus:ring-primary-500';
    @endphp


    <{{ $as }}
        {{ $attributes->merge([
            'class' => implode(' ', [$baseClasses, $variantClasses]),
        ]) }}
    >
        <i class="fas fa-edit text-sm"></i>
        <span class="sr-only">{{ $title }}</span>
        </{{ $as }}>
@endif
