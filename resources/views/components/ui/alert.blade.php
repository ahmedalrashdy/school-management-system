@props([
    'type' => 'info', // info, success, warning, danger
    'dismissible' => false,
])

@php
    $typeConfig = [
        'info' => [
            'bg' => 'bg-info-50 dark:bg-info-900/20',
            'border' => 'border-info-200 dark:border-info-800',
            'text' => 'text-info-800 dark:text-info-300',
            'icon' => 'fas fa-info-circle',
        ],
        'success' => [
            'bg' => 'bg-success-50 dark:bg-success-900/20',
            'border' => 'border-success-200 dark:border-success-800',
            'text' => 'text-success-800 dark:text-success-300',
            'icon' => 'fas fa-check-circle',
        ],
        'warning' => [
            'bg' => 'bg-warning-50 dark:bg-warning-900/20',
            'border' => 'border-warning-200 dark:border-warning-800',
            'text' => 'text-warning-800 dark:text-warning-300',
            'icon' => 'fas fa-exclamation-triangle',
        ],
        'danger' => [
            'bg' => 'bg-danger-50 dark:bg-danger-900/20',
            'border' => 'border-danger-200 dark:border-danger-800',
            'text' => 'text-danger-800 dark:text-danger-300',
            'icon' => 'fas fa-exclamation-circle',
        ],
    ];
    
    $config = $typeConfig[$type] ?? $typeConfig['info'];
@endphp

<div
    x-data="{ show: true }"
    x-show="show"
    x-transition
    {{ $attributes->merge([
        'class' => implode(' ', [
            'border rounded-lg p-4',
            $config['bg'],
            $config['border'],
            $config['text'],
        ])
    ]) }}
>
    <div class="flex items-start gap-3">
        <div class="flex-shrink-0">
            <i class="{{ $config['icon'] }} text-lg"></i>
        </div>
        
        <div class="flex-1">
            {{ $slot }}
        </div>
        
        @if($dismissible)
            <button
                @click="show = false"
                type="button"
                class="flex-shrink-0 hover:opacity-75 transition"
            >
                <i class="fas fa-times"></i>
            </button>
        @endif
    </div>
</div>

