@props(['title' => 'الأحداث السريعة', 'actions' => []])

<div
    class="bg-white dark:bg-gray-800/80 rounded-2xl shadow-sm hover:shadow-lg border border-gray-100 dark:border-gray-700/50 p-6 transition-all duration-300">
    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">{{ $title }}</h3>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach ($actions as $action)
            <a
                href="{{ $action['url'] ?? '#' }}"
                @if (isset($action['onclick'])) onclick="{{ $action['onclick'] }}" @endif
                class="group relative flex items-center p-4 bg-gradient-to-br from-blue-50 to-indigo-50 hover:from-blue-100 hover:to-indigo-100 dark:from-blue-900/20 dark:to-indigo-900/20 dark:hover:from-blue-900/30 dark:hover:to-indigo-900/30 rounded-xl border border-blue-200 dark:border-blue-800/50 transition-all duration-300 hover:shadow-md hover:-translate-y-0.5"
            >
                <div
                    class="absolute top-0 right-0 w-16 h-16 bg-gradient-to-br from-blue-500/10 to-indigo-500/10 rounded-xl">
                </div>
                <div class="relative shrink-0">
                    <div
                        class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/20 group-hover:scale-110 transition-transform">
                        @if (isset($action['icon']))
                            <i class="fas {{ $action['icon'] }} text-white text-lg"></i>
                        @else
                            <i class="fas fa-link text-white text-lg"></i>
                        @endif
                    </div>
                </div>
                <div class="mr-4 flex-1">
                    <h4 class="text-sm font-bold text-blue-900 dark:text-blue-100 mb-1">{{ $action['title'] }}</h4>
                    <p class="text-xs text-blue-700 dark:text-blue-300">{{ $action['description'] ?? '' }}</p>
                </div>
                <div class="shrink-0 opacity-0 group-hover:opacity-100 transition-opacity">
                    <i class="fas fa-arrow-left text-blue-600 dark:text-blue-400 text-xs"></i>
                </div>
            </a>
        @endforeach

        {{-- Placeholder for future actions --}}
        @if (count($actions) === 0)
            <div class="col-span-full text-center py-8 text-gray-400 dark:text-gray-500">
                <i class="fas fa-plus-circle text-3xl mb-2"></i>
                <p class="text-sm">سيتم إضافة الأحداث السريعة قريباً</p>
            </div>
        @endif
    </div>
</div>
