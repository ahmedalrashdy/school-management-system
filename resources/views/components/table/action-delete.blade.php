@props([
    'as' => 'button',
    'title' => 'حذف',
    'permissions' => null,
])

@if ($permissions && auth()->user()->cannot($permissions))
@else
    @php
        $as = $attributes->has('href') ? 'a' : $as;
        $baseClasses =
            'relative inline-flex items-center justify-center p-2 rounded-lg transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-inset';
        $variantClasses =
            'text-danger-600 hover:bg-danger-50 hover:text-danger-700 dark:text-danger-400 dark:hover:bg-danger-900/20 dark:hover:text-danger-300 focus:ring-danger-500';

        // إذا كان as='button' ولم يتم تحديد type، استخدم 'button' كافتراضي
        $type = $as === 'button' ? $attributes->get('type', 'button') : null;

        // استثناء type و title من attributes
        $filteredAttributes = $attributes->except(['type', 'title']);
    @endphp

    <{{ $as }}
        @if ($type) type="{{ $type }}" @endif
        @if ($title) title="{{ $title }}" @endif
        {{ $filteredAttributes->merge([
            'class' => implode(' ', [$baseClasses, $variantClasses]),
        ]) }}
    >
        <i class="fas fa-trash text-sm"></i>
        @if ($title)
            <span class="sr-only">{{ $title }}</span>
        @endif
        </{{ $as }}>

@endif
