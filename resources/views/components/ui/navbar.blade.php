@props([
    'pageTitle' => '',
])

<header class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700 sticky top-0 z-40">
    <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
        <!-- Mobile menu button -->
        <button
            @click="$dispatch('toggle-sidebar')"
            type="button"
            class="lg:hidden text-gray-500 hover:text-gray-600 dark:text-gray-400 dark:hover:text-gray-300 focus:outline-none"
        >
            <i class="fas fa-bars text-xl"></i>
        </button>

        <!-- Page title -->
        <div class="flex-1 lg:flex-none">
            @if ($pageTitle)
                <h1 class="text-xl font-semibold text-gray-900 dark:text-white">
                    {{ $pageTitle }}
                </h1>
            @endif
        </div>

        <!-- Right section -->
        <div class="flex items-center gap-4">
            <!-- Search -->
            <div class="hidden md:block">
                <x-form.input
                    name="search"
                    label=""
                    placeholder="البحث"
                    icon="fas fa-search text-gray-400"
                    removeMargin
                />
            </div>

            <!-- Notifications -->
            <x-ui.notifications-dropdown :count="3" />

            <!-- Theme toggle -->
            <x-ui.theme-dropdown />

            <!-- User menu -->
            <x-ui.user-dropdown />
        </div>
    </div>
</header>
