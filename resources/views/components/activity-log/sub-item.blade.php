{{-- Mobile Layout is Vertical Stack, Desktop is Flex Row --}}
<div
    x-data="{ expanded: false }"
    class="relative flex flex-col sm:flex-row gap-0 sm:gap-6 group"
>
    <!-- Timeline Column (HIDDEN on Mobile, VISIBLE on Desktop) -->
    <div class="hidden sm:flex flex-col items-center">
        <!-- The Dot -->
        <div
            class="w-10 h-10 rounded-full border-4 border-white dark:border-gray-900 flex items-center justify-center z-10 shadow-sm shrink-0 {{ $color }}">
            <i class="{{ $icon }} text-sm"></i>
        </div>

        <!-- The Line -->
        @if (!$isLast)
            <div class="w-px bg-gray-200 dark:bg-gray-700 h-full -mt-2 group-last:hidden"></div>
        @endif
    </div>

    <!-- Content Column (The Card) -->
    <div class="flex-1 pb-4 sm:pb-8 min-w-0">

        <div
            class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow duration-200 overflow-hidden">

            <!-- Header -->
            <div
                class="p-2 cursor-pointer select-none flex items-start justify-between gap-3 sm:gap-4"
                @click="expanded = !expanded"
            >
                {{-- Mobile Icon (VISIBLE on Mobile, HIDDEN on Desktop) --}}
                <div class="sm:hidden mt-1 shrink-0">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs {{ $color }}">
                        <i class="{{ $icon }}"></i>
                    </div>
                </div>

                <div class="flex-1 space-y-1.5">
                    <!-- Meta Info Row -->
                    <div class="flex flex-wrap items-center gap-2 text-xs text-gray-500 dark:text-gray-400">


                        @if ($activity->log_name)
                            <span
                                class="hidden xs:inline-block px-2 py-0.5 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-600 font-medium scale-90 origin-right"
                            >
                                {{ __("activity_log.log_names.{$activity->log_name}") }}
                            </span>
                        @endif
                    </div>

                    <!-- Main Description -->
                    <h3 class="text-sm sm:text-base font-medium text-gray-900 dark:text-white leading-snug">
                        {{ $description() }}
                    </h3>

                    {{-- Badge shown below description on very small screens --}}
                    @if ($activity->log_name)
                        <div class="xs:hidden mt-1">
                            <span
                                class="px-2 py-0.5 rounded-full bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400 text-[10px] border border-gray-200 dark:border-gray-600"
                            >
                                {{ __("activity_log.log_names.{$activity->log_name}") }}
                            </span>
                        </div>
                    @endif
                </div>

                <!-- Expand/Collapse Icon -->
                <button
                    class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-400 transition-transform duration-200 focus:outline-none shrink-0 -mr-2 sm:mr-0"
                    :class="expanded ? 'rotate-180 text-primary-600' : ''"
                >
                    <i class="fas fa-chevron-down text-sm"></i>
                </button>
            </div>

            <!-- Body (Collapsible) -->
            <div
                x-show="expanded"
                x-collapse
                x-cloak
                class="border-t border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/30"
            >
                <div class="p-4 sm:p-5 space-y-4">
                    <x-activity-log.visual-diff :$activity />
                </div>
            </div>
        </div>
    </div>
</div>
