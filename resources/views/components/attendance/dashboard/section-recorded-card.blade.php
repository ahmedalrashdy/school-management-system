@props(['section', 'gradeName', 'schoolDay', 'attendanceMode', 'selectedDayPart'])

@php
    $presentPercentage =
        $section['total_students'] > 0 ? round(($section['present'] / $section['total_students']) * 100) : 0;

    // Generate student list route based on attendance mode
    $studentListRoute = match ($attendanceMode->value) {
        \App\Enums\AttendanceModeEnum::Daily->value => route('dashboard.attendances.students.daily', [
            'section' => $section['id'],
            'schoolDay' => $schoolDay,
        ]),
        \App\Enums\AttendanceModeEnum::SplitDaily->value => route('dashboard.attendances.students.split-daily', [
            'section' => $section['id'],
            'schoolDay' => $schoolDay,
            'dayPart' => $selectedDayPart,
        ]),
        default => null, // PerPeriod needs timetable slot, will be handled in period-accordion
    };
@endphp

<div
    x-data="{ showDetails: false, isLoading: false }"
    class="bg-white dark:bg-gray-800/80 border border-gray-100 dark:border-gray-700/50 rounded-xl overflow-hidden transition-all duration-300 hover:shadow-md mb-3"
    :class="{
        'ring-2 ring-primary-500/50 shadow-lg': showDetails,
        'opacity-75 pointer-events-none': isLoading
    }"
>

    {{-- Section Header Container --}}
    <div class="px-4 py-4 md:px-5 flex flex-col md:flex-row md:items-center justify-between gap-4">

        {{-- Top Part: Icon + Info --}}
        <div class="flex items-start justify-between w-full md:w-auto">
            <div class="flex items-center gap-4">
                {{-- Section Badge --}}
                <div class="relative shrink-0">
                    <div
                        class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-500/20">
                        <span class="text-white font-bold text-lg">{{ $section['name'] }}</span>
                    </div>
                    {{-- Lock/Check Indicator --}}
                    @if ($section['is_locked'])
                        <div
                            class="absolute -top-1 -right-1 w-5 h-5 bg-gray-500 rounded-full flex items-center justify-center shadow-md ring-2 ring-white dark:ring-gray-800">
                            <i class="fas fa-lock text-white text-[10px]"></i>
                        </div>
                    @else
                        <div
                            class="absolute -bottom-1 -right-1 w-5 h-5 bg-emerald-500 rounded-full flex items-center justify-center shadow-md ring-2 ring-white dark:ring-gray-800">
                            <i class="fas fa-check text-white text-[10px]"></i>
                        </div>
                    @endif
                </div>

                {{-- Section Info --}}
                <div>
                    <h5 class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        شعبة {{ $section['name'] }}
                        {{-- Loading Indicator inside title --}}
                        <i
                            x-show="isLoading"
                            class="fas fa-circle-notch fa-spin text-primary-500 text-xs"
                            style="display: none;"
                        ></i>
                    </h5>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5 flex items-center gap-2">
                        <span>{{ $section['total_students'] }} طالب</span>
                        <span class="text-gray-300 dark:text-gray-600">•</span>
                        <span>{{ $presentPercentage }}% حضور</span>
                    </p>
                </div>
            </div>

            {{-- Mobile Toggle --}}
            <button
                @click.stop="showDetails = !showDetails"
                class="md:hidden w-8 h-8 rounded-lg bg-gray-50 dark:bg-gray-700/50 flex items-center justify-center transition-transform"
                :class="{ 'rotate-180': showDetails }"
            >
                <i class="fas fa-chevron-down text-gray-500 text-xs"></i>
            </button>
        </div>

        {{-- Bottom/Right Part: Stats & Actions --}}
        <div
            class="flex items-center justify-between md:justify-end gap-4 w-full md:w-auto pt-3 md:pt-0 border-t border-gray-100 md:border-t-0 dark:border-gray-700/50">

            {{-- Quick Stats Pills --}}
            <x-attendance.dashboard.attendance-stats-badges
                :present="$section['present']"
                :absent="$section['absent']"
                :late="$section['late']"
                :excused="$section['excused']"
                size="xs"
                variant="compact"
                :show-dark-mode="false"
            />

            <div class="flex items-center gap-4 pl-1">
                {{-- Lock Toggle --}}
                <label
                    class="relative inline-flex items-center cursor-pointer"
                    title="إقفال الرصد"
                    @click.stop
                >
                    <input
                        type="checkbox"
                        {{ $section['is_locked'] ? 'checked' : '' }}
                        class="sr-only peer"
                    >
                    <div
                        class="w-9 h-5 bg-gray-200 peer-focus:ring-4 peer-focus:ring-primary-300 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-primary-600">
                    </div>
                </label>

                {{-- Desktop Expand Button --}}
                <button
                    @click.stop="showDetails = !showDetails"
                    class="hidden md:flex w-9 h-9 rounded-lg bg-gray-100 items-center justify-center hover:bg-gray-200 transition-all"
                    :class="{ 'rotate-180 bg-primary-100 text-primary-600': showDetails }"
                >
                    <i class="fas fa-chevron-down text-xs"></i>
                </button>
            </div>
        </div>
    </div>

    {{-- Details (Collapsed) --}}
    <div
        x-show="showDetails"
        x-collapse
        x-cloak
    >
        <div class="border-t border-gray-100 p-4 md:p-5 bg-gray-50/50">

            {{-- Record Attendance Button --}}
            @php
                $route = match ($attendanceMode->value) {
                    \App\Enums\AttendanceModeEnum::Daily->value => route('dashboard.attendances.record.daily', [
                        'section' => $section['id'],
                        'schoolDay' => $schoolDay,
                    ]),
                    \App\Enums\AttendanceModeEnum::SplitDaily->value => route(
                        'dashboard.attendances.record.split-daily',
                        [
                            'section' => $section['id'],
                            'schoolDay' => $schoolDay,
                            'dayPart' => $selectedDayPart,
                        ],
                    ),
                    default => null,
                };
            @endphp
            @if ($route)
                <a
                    href="{{ $route }}"
                    class="w-full mb-3 py-3 px-4 bg-primary-50 hover:bg-primary-100 dark:bg-primary-900/30 dark:hover:bg-primary-900/50 text-primary-700 dark:text-primary-300 rounded-xl transition-colors font-bold flex items-center justify-center gap-2 border border-primary-200 dark:border-primary-800"
                >
                    <i class="fas fa-list-ul"></i>
                    إعادة التحضير
                </a>
            @endif
            {{-- Student List Button --}}
            @if ($studentListRoute)
                <a
                    href="{{ $studentListRoute }}"
                    class="w-full mb-3 py-3 px-4 bg-primary-50 hover:bg-primary-100 dark:bg-primary-900/30 dark:hover:bg-primary-900/50 text-primary-700 dark:text-primary-300 rounded-xl transition-colors font-bold flex items-center justify-center gap-2 border border-primary-200 dark:border-primary-800"
                >
                    <i class="fas fa-list-ul"></i>
                    عرض قائمة الطلاب الكاملة ({{ $section['total_students'] }})
                </a>
            @endif

            {{-- Absent Students Button --}}
            @if ($section['absent'] > 0)
                <button
                    @click.stop="
                        isLoading = true;
                        $wire.loadSectionStudents({{ $section['id'] }})
                            .then(students => {
                                $dispatch('open-modal', {
                                    name: 'section-students-modal',
                                    sectionName: '{{ $section['name'] }}',
                                    students: students
                                });
                                isLoading = false;
                            })
                            .catch(() => isLoading = false);
                    "
                    class="w-full py-3 text-sm text-rose-600 bg-rose-50 hover:bg-rose-100 rounded-xl transition-colors font-bold flex items-center justify-center gap-2"
                >
                    <i class="fas fa-users"></i> عرض تفاصيل الغياب ({{ $section['absent'] }})
                </button>
            @else
                <div class="text-center py-4 text-gray-500 text-sm">جميع الطلاب حاضرون</div>
            @endif
        </div>
    </div>
</div>
