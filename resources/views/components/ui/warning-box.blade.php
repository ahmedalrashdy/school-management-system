@props([
    'title' => 'تحذير',
    'icon' => 'fas fa-exclamation-triangle',
])

<div
    {{ $attributes->merge([
        'class' => 'bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4',
    ]) }}>
    <div class="flex items-start gap-3">
        <i class="{{ $icon }} text-yellow-600 dark:text-yellow-400 mt-1"></i>
        <div>
            <p class="text-sm text-yellow-800 dark:text-yellow-400">
                @if ($title)
                    <strong>{{ $title }}:</strong>
                @endif
                {{ $slot }}
            </p>
        </div>
    </div>
</div>
