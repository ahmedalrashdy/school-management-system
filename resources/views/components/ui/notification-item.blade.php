@props(['payload'])

@php
    $levelStyles = match ($payload['level'] ?? 'info') {
        'success' => 'text-green-600 bg-green-100 dark:text-green-400 dark:bg-green-900/30',
        'error' => 'text-red-600 bg-red-100 dark:text-red-400 dark:bg-red-900/30',
        'warning' => 'text-yellow-600 bg-yellow-100 dark:text-yellow-400 dark:bg-yellow-900/30',
        default => 'text-blue-600 bg-blue-100 dark:text-blue-400 dark:bg-blue-900/30', // info
    };

    $bgClass = !$payload['is_read'] ? 'bg-blue-50 dark:bg-blue-900/10' : 'bg-white dark:bg-gray-800';
@endphp

<a
    href="{{ route('notifications.show', ['notification' => $notification->id]) }}"
    class="group flex items-start gap-3 p-4 border-b border-gray-100 dark:border-gray-700 transition-colors duration-200 hover:bg-gray-50 dark:hover:bg-gray-700/50 {{ $bgClass }}"
>
    <!-- الأيقونة -->
    <div class="flex-shrink-0">
        <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $levelStyles }}">
            <i class="fas fa-{{ $payload['icon'] ?? 'bell' }} text-lg"></i>
        </div>
    </div>

    <!-- المحتوى -->
    <div class="flex-1 min-w-0">
        <div class="flex justify-between items-start">
            <h4 class="text-sm font-bold text-gray-900 dark:text-gray-100 mb-0.5 truncate pr-2">
                {{ $payload['title'] }}
            </h4>

            <!-- الوقت -->
            <span class="text-xs text-gray-400 dark:text-gray-500 whitespace-nowrap">
                {{ $payload['created_at'] }}
            </span>
        </div>

        <p class="text-sm text-gray-600 dark:text-gray-400 leading-snug line-clamp-2">
            {{ $payload['body'] }}
        </p>

        <!-- بيانات إضافية (Tags) -->
        @if (isset($payload['raw_data']['priority']) && $payload['raw_data']['priority'] == 'high')
            <div class="mt-2">
                <span
                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-200"
                >
                    هام جداً
                </span>
            </div>
        @endif
    </div>

    <!-- مؤشر غير مقروء (نقطة) -->
    @if (!$payload['is_read'])
        <div class="flex-shrink-0 self-center">
            <span
                class="block w-2.5 h-2.5 bg-blue-600 dark:bg-blue-500 rounded-full ring-2 ring-white dark:ring-gray-800"
            ></span>
        </div>
    @endif
</a>
