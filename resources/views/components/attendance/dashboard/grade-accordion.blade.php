@props(['grade', 'isExpanded' => false, 'schoolDay', 'attendanceMode', 'selectedDayPart'])

@php
    $percentage = $grade['total_sections'] > 0 ? ($grade['recorded_sections'] / $grade['total_sections']) * 100 : 0;
    $isComplete = $percentage == 100;
    $pendingSections = collect($grade['sections'])->where('is_recorded', false);
    $recordedSections = collect($grade['sections'])->where('is_recorded', true);
@endphp

<div
    x-data="{ expanded: false }"
    class="bg-white dark:bg-gray-800/80 rounded-2xl shadow-sm hover:shadow-md border border-gray-100 dark:border-gray-700/50 overflow-hidden transition-all duration-300"
>

    {{-- Grade Header Button --}}
    <button
        @click="expanded = !expanded"
        class="w-full text-right group"
    >
        <div
            class="px-5 py-4 flex flex-col md:flex-row md:items-center justify-between gap-4 transition-colors hover:bg-gray-50/50 dark:hover:bg-gray-700/30">

            {{-- Top Section: Icon + Info + Arrow (on Mobile) --}}
            <div class="flex items-start justify-between w-full md:w-auto">
                <div class="flex items-center gap-4">
                    {{-- Grade Icon --}}
                    <div class="relative shrink-0">
                        <div
                            class="w-14 h-14 bg-linear-to-br from-primary-500 to-primary-600 rounded-xl flex items-center justify-center shadow-lg shadow-primary-500/20 group-hover:scale-105 transition-transform duration-300">
                            <i class="fas fa-graduation-cap text-white text-xl"></i>
                        </div>
                        {{-- Status Badge Overlay --}}
                        @if ($isComplete)
                            <div
                                class="absolute -top-1 -right-1 w-5 h-5 bg-emerald-500 rounded-full flex items-center justify-center shadow-md ring-2 ring-white dark:ring-gray-800">
                                <i class="fas fa-check text-white text-[10px]"></i>
                            </div>
                        @elseif($pendingSections->count() > 0)
                            <div
                                class="absolute -top-1 -right-1 w-5 h-5 bg-amber-500 rounded-full flex items-center justify-center shadow-md ring-2 ring-white dark:ring-gray-800 animate-pulse">
                                <span class="text-white text-[10px] font-bold">{{ $pendingSections->count() }}</span>
                            </div>
                        @endif
                    </div>

                    {{-- Grade Info (Name & Subtitles) --}}
                    <div>
                        <h3
                            class="text-lg font-bold text-gray-900 dark:text-white group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
                            {{ $grade['name'] }}
                        </h3>

                        {{-- Subtitle Info (Moved here as requested) --}}
                        <div class="flex items-center gap-3 mt-1 text-sm text-gray-500 dark:text-gray-400">
                            <span
                                class="inline-flex items-center gap-1.5 bg-gray-100 dark:bg-gray-700/50 px-2 py-0.5 rounded-md"
                            >
                                <i class="fas fa-door-open text-xs text-gray-400"></i>
                                <span class="font-medium">{{ $grade['total_sections'] }}</span>
                            </span>
                            <span
                                class="inline-flex items-center gap-1.5 bg-gray-100 dark:bg-gray-700/50 px-2 py-0.5 rounded-md"
                            >
                                <i class="fas fa-user-graduate text-xs text-gray-400"></i>
                                <span class="font-medium">{{ $grade['total_students'] }}</span>
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Mobile Arrow (Visible only on mobile) --}}
                <div class="md:hidden pt-2">
                    <div
                        class="w-8 h-8 rounded-lg bg-gray-50 dark:bg-gray-700/50 flex items-center justify-center transition-transform duration-300"
                        :class="{ 'rotate-180 bg-primary-50 dark:bg-primary-900/20': expanded }"
                    >
                        <i
                            class="fas fa-chevron-down text-gray-400 text-xs"
                            :class="{ 'text-primary-600': expanded }"
                        ></i>
                    </div>
                </div>
            </div>

            {{-- Bottom/Right Section: Stats & Progress --}}
            {{-- On Mobile: This becomes a new row with border-top. On Desktop: It sits on the right --}}
            <div
                class="flex items-center justify-between md:justify-end gap-4 w-full md:w-auto md:border-t-0 border-t border-gray-100 dark:border-gray-700/50 pt-3 md:pt-0">

                {{-- Quick Stats Pills (Visible on Mobile now too) --}}
                <x-attendance.dashboard.attendance-stats-badges
                    :present="$grade['present']"
                    :absent="$grade['absent']"
                    :late="$grade['late']"
                    :excused="$grade['excused']"
                    size="xs"
                    variant="default"
                    :show-dark-mode="true"
                />

                {{-- Progress Ring & Desktop Arrow --}}
                <div class="flex items-center gap-4 pl-1">
                    {{-- Progress Ring --}}
                    <div class="flex items-center gap-3">
                        <div class="relative w-10 h-10 md:w-11 md:h-11">
                            <svg
                                class="w-full h-full -rotate-90"
                                viewBox="0 0 36 36"
                            >
                                <path
                                    class="text-gray-100 dark:text-gray-700"
                                    stroke="currentColor"
                                    stroke-width="3"
                                    fill="none"
                                    d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                                />
                                <path
                                    class="{{ $isComplete ? 'text-emerald-500' : ($percentage > 50 ? 'text-violet-500' : 'text-amber-500') }}"
                                    stroke="currentColor"
                                    stroke-width="3"
                                    fill="none"
                                    stroke-linecap="round"
                                    stroke-dasharray="{{ $percentage }}, 100"
                                    d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                                />
                            </svg>
                            <span
                                class="absolute inset-0 flex items-center justify-center text-[10px] md:text-xs font-bold text-gray-700 dark:text-gray-300"
                            >
                                {{ round($percentage) }}%
                            </span>
                        </div>
                        {{-- Text next to ring (Hidden on very small screens, shown on desktop) --}}
                        <div class="hidden md:block text-right">
                            <p class="text-[10px] text-gray-500 dark:text-gray-400">رصد</p>
                            <p class="text-xs font-bold text-gray-700 dark:text-gray-300">
                                {{ $grade['recorded_sections'] }}<span
                                    class="text-gray-300 dark:text-gray-600 mx-0.5">/</span>{{ $grade['total_sections'] }}
                            </p>
                        </div>
                    </div>

                    {{-- Desktop Arrow (Hidden on Mobile) --}}
                    <div
                        class="hidden md:flex w-9 h-9 rounded-lg bg-gray-50 dark:bg-gray-700/50 items-center justify-center transition-all duration-300"
                        :class="{ 'rotate-180 bg-primary-50 dark:bg-primary-900/20': expanded }"
                    >
                        <i
                            class="fas fa-chevron-down text-gray-400 text-xs"
                            :class="{ 'text-primary-600': expanded }"
                        ></i>
                    </div>
                </div>
            </div>

        </div>
    </button>

    {{-- Sections Content (Expandable) --}}
    <div
        x-show="expanded"
        x-collapse
        x-cloak
    >
        <div class="border-t border-gray-100 dark:border-gray-700/50 p-5 bg-gray-50/50 dark:bg-gray-900/30">
            {{-- Pending Sections --}}
            @if ($pendingSections->count() > 0)
                <div class="mb-6">
                    <h4 class="text-sm font-bold text-amber-600 dark:text-amber-400 mb-4 flex items-center gap-2">
                        <div
                            class="w-8 h-8 bg-amber-100 dark:bg-amber-900/30 rounded-lg flex items-center justify-center">
                            <i class="fas fa-hourglass-half text-amber-600 dark:text-amber-400"></i>
                        </div>
                        <span>شعب قيد الانتظار</span>
                        <span
                            class="bg-amber-100 dark:bg-amber-900/50 text-amber-700 dark:text-amber-300 px-2 py-0.5 rounded-full text-xs"
                        >
                            {{ $pendingSections->count() }}
                        </span>
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3">
                        @foreach ($pendingSections as $section)
                            <x-attendance.dashboard.section-pending-card
                                :section="$section"
                                :grade-name="$grade['name']"
                                :school-day="$schoolDay"
                                :attendance-mode="$attendanceMode"
                                :selected-day-part="$selectedDayPart"
                            />
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Recorded Sections --}}
            @if ($recordedSections->count() > 0)
                <div>
                    <h4 class="text-sm font-bold text-emerald-600 dark:text-emerald-400 mb-4 flex items-center gap-2">
                        <div
                            class="w-8 h-8 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg flex items-center justify-center">
                            <i class="fas fa-check-double text-emerald-600 dark:text-emerald-400"></i>
                        </div>
                        <span>شعب تم رصدها</span>
                        <span
                            class="bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-300 px-2 py-0.5 rounded-full text-xs"
                        >
                            {{ $recordedSections->count() }}
                        </span>
                    </h4>
                    <div class="space-y-3">
                        @foreach ($recordedSections as $section)
                            <x-attendance.dashboard.section-recorded-card
                                :section="$section"
                                :school-day="$schoolDay"
                                :attendance-mode="$attendanceMode"
                                :selected-day-part="$selectedDayPart"
                                :grade-name="$grade['name']"
                            />
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Empty State --}}
            @if ($recordedSections->count() === 0 && $pendingSections->count() === 0)
                <div class="text-center py-8">
                    <div
                        class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-inbox text-gray-400 text-2xl"></i>
                    </div>
                    <p class="text-gray-500 dark:text-gray-400">لا توجد شعب في هذا الصف</p>
                </div>
            @endif
        </div>
    </div>
</div>
