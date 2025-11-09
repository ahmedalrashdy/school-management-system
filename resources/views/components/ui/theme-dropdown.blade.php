<div x-data="{ themeOpen: false }" class="relative">
    <button @click="themeOpen = !themeOpen" type="button"
        class="p-2 text-gray-500 hover:text-gray-600 dark:text-gray-400 dark:hover:text-gray-300 focus:outline-none"
        aria-label="Toggle theme">

        <i class="fas fa-sun text-xl" x-show="$store.theme.theme === 'light'" x-cloak></i>

        <template x-if="$store.theme.theme === 'dark'">
            <i class="fas fa-moon text-xl" x-cloak></i>
        </template>

        <template x-if="$store.theme.theme === 'system'">
            <i class="fas fa-desktop text-xl" x-cloak></i>
        </template>

    </button>

    <!-- Theme Dropdown -->
    <div x-show="themeOpen" @click.away="themeOpen = false" x-transition style="display: none;"
        class="absolute left-0 mt-2 w-40 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="py-1">
            <button @click="$store.theme.setTheme('light'); themeOpen = false"
                class="flex items-center gap-2 w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"
                :class="{ 'bg-gray-100 dark:bg-gray-700':  $store.theme.theme === 'light' }">
                <i class="fas fa-sun w-4"></i>
                <span>فاتح</span>
            </button>
            <button @click="$store.theme.setTheme('dark'); themeOpen = false"
                class="flex items-center gap-2 w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"
                :class="{ 'bg-gray-100 dark:bg-gray-700':  $store.theme.theme === 'dark' }">
                <i class="fas fa-moon w-4"></i>
                <span>داكن</span>
            </button>
            <button @click="$store.theme.setTheme('system'); themeOpen = false"
                class="flex items-center gap-2 w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"
                :class="{ 'bg-gray-100 dark:bg-gray-700': $store.theme.theme === 'system' }">
                <i class="fas fa-desktop w-4"></i>
                <span>النظام</span>
            </button>
        </div>
    </div>
</div>

