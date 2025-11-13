@props(['start_at', 'end_at', 'day', 'periodNumber'])

<div
    class="min-h-20 p-3 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800/50 flex flex-col items-center justify-center">
    {{ $slot }}
    <p class="text-xs text-gray-400 dark:text-gray-600">
        {{ $start_at }} - {{ $end_at }}
    </p>
</div>
