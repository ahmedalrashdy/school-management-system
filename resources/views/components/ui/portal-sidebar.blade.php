<div
    x-data="{ sidebarOpen: false }"
    @toggle-sidebar.window="sidebarOpen = !sidebarOpen"
>
    <!-- Sidebar -->
    <div
        class="fixed h-screen inset-y-0 right-0 z-50 w-64 transform transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static"
        :class="{ 'translate-x-0': sidebarOpen, 'translate-x-full': !sidebarOpen }"
    >
        <div
            class="flex flex-col h-full bg-white dark:bg-gray-800 shadow-lg border-l border-gray-200 dark:border-gray-700">
            <!-- Logo -->
            <div class="flex items-center justify-center h-16 px-4 border-b border-gray-200 dark:border-gray-700">
                <a
                    href="{{ $homeRoute }}"
                    class="flex items-center gap-3"
                >
                    <i class="fas fa-graduation-cap text-primary-500 text-2xl"></i>
                    <span class="text-xl font-bold text-gray-900 dark:text-white">نظام المدرسة</span>
                </a>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 overflow-y-auto px-3 py-4">
                <ul class="space-y-1">
                    @foreach ($menuItems as $item)
                        @if (isset($item['separator']) && $item['separator'])
                            <li class="my-3 border-t border-gray-200 dark:border-gray-700"></li>
                        @elseif(isset($item['header']))
                            <li
                                class="px-3 pt-4 pb-2 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ $item['header'] }}
                            </li>
                        @else
                            @php
                                $isActive = request()->routeIs($item['active'] ?? '');
                            @endphp
                            <li>
                                <a
                                    wire:navigate
                                    href="{{ $item['route'] ?? '#' }}"
                                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors duration-150 {{ $isActive ? 'bg-primary-500 dark:bg-primary-600 text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                                >
                                    @if (isset($item['icon']))
                                        <i class="{{ $item['icon'] }} text-lg w-5"></i>
                                    @endif
                                    <span class="font-medium">{{ $item['label'] }}</span>

                                    @if (isset($item['badge']))
                                        <span
                                            class="mr-auto bg-danger-500 text-white text-xs font-bold px-2 py-0.5 rounded-full"
                                        >
                                            {{ $item['badge'] }}
                                        </span>
                                    @endif
                                </a>
                            </li>
                        @endif
                    @endforeach
                </ul>
            </nav>

            <!-- User Profile -->
            <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                <div class="flex items-center gap-3">
                    <div class="flex-shrink-0">
                        @if (auth()->user()->avatar)
                            <img
                                src="{{ \Storage::url(auth()->user()->avatar) }}"
                                alt="{{ auth()->user()->first_name }}"
                                class="w-10 h-10 rounded-full"
                            >
                        @else
                            <div class="w-10 h-10 rounded-full bg-primary-500 flex items-center justify-center">
                                <span class="text-white font-bold text-sm">
                                    {{ substr(auth()->user()->first_name, 0, 1) }}{{ substr(auth()->user()->last_name, 0, 1) }}
                                </span>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                            {{ auth()->user()->first_name }} {{ auth()->user()->last_name }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                            {{ auth()->user()->email ?? auth()->user()->phone_number }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile overlay -->
    <div
        x-show="sidebarOpen"
        @click="sidebarOpen = false"
        x-transition:enter="transition-opacity ease-linear duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-linear duration-300"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-40 bg-gray-600 bg-opacity-75 lg:hidden"
        x-cloak
    ></div>
</div>
