@props([])

@php
    $notifications = auth()->user()->unreadNotifications;
    $count = $notifications?->count() ?? 0;
@endphp
<div
    x-data="{ notificationsOpen: false }"
    class="relative"
>
    <button
        @click="notificationsOpen = !notificationsOpen"
        type="button"
        class="relative p-2 text-gray-500 hover:text-gray-600 dark:text-gray-400 dark:hover:text-gray-300 focus:outline-none"
    >
        <i class="fas fa-bell text-xl"></i>
        @if ($count > 0)
            <span
                class="absolute top-0 right-0 inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-danger-500 rounded-full"
            >
                {{ $count }}
            </span>
        @endif
    </button>

    <!-- Notifications Dropdown -->
    <div
        x-show="notificationsOpen"
        @click.away="notificationsOpen = false"
        x-transition
        style="display: none;"
        class="absolute -left-[100px] mt-2 w-80 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden"
    >
        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">الإشعارات</h3>
        </div>
        <div class="max-h-96 overflow-y-auto">
            @if ($notifications->count() > 0)
                @foreach ($notifications as $notification)
                    <x-ui.notification-item :notification="$notification" />
                @endforeach
            @else
                <div class="px-4 py-8 text-center">
                    <i class="fas fa-bell-slash text-gray-400 text-3xl mb-2"></i>
                    <p class="text-sm text-gray-500 dark:text-gray-400">لا توجد إشعارات</p>
                </div>
            @endif
        </div>
        <div class="p-3 border-t border-gray-200 dark:border-gray-700">
            <a
                href="{{ route('notifications.index') }}"
                class="text-sm text-primary-600 hover:text-primary-700 font-medium"
            >
                عرض جميع الإشعارات
            </a>
        </div>
    </div>
</div>
