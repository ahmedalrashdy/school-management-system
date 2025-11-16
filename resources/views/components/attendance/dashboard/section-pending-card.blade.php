@props(['section', 'gradeName', 'schoolDay', 'attendanceMode', 'selectedDayPart'])

<div
    class="group bg-linear-to-r from-amber-50 to-orange-50 dark:from-amber-900/20 dark:to-orange-900/20 border border-amber-200/50 dark:border-amber-700/30 rounded-xl p-4 md:p-5 flex flex-col md:flex-row md:items-center justify-between gap-4 hover:shadow-md transition-all duration-300 mb-3">

    {{-- Left Side: Icon + Info --}}
    <div class="flex items-center gap-3 md:gap-4 w-full md:w-auto">
        {{-- Icon --}}
        <div class="relative shrink-0">
            <div
                class="w-12 h-12 md:w-14 md:h-14 bg-linear-to-br from-amber-400 to-orange-500 rounded-xl flex items-center justify-center shadow-lg shadow-amber-500/20 group-hover:scale-105 transition-transform duration-300">
                <span class="text-white font-bold text-lg">{{ $section['name'] }}</span>
            </div>
            <div
                class="absolute -bottom-1 -right-1 w-5 h-5 bg-amber-500 rounded-full flex items-center justify-center animate-pulse ring-2 ring-white dark:ring-gray-800">
                <i class="fas fa-hourglass-half text-white text-[10px]"></i>
            </div>
        </div>

        {{-- Text Info --}}
        <div class="min-w-0 flex-1">
            <h5 class="font-bold text-gray-900 dark:text-white text-base md:text-lg">شعبة {{ $section['name'] }}</h5>

            <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-1">
                {{-- Student Count --}}
                <div class="flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400">
                    <i class="fas fa-users text-xs shrink-0"></i>
                    <span>{{ $section['total_students'] }} طالب</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Right Side: Status Badge + Action Button --}}
    <div
        class="flex items-center justify-between md:justify-end gap-3 w-full md:w-auto pt-3 md:pt-0 border-t border-amber-200/50 md:border-t-0 dark:border-amber-700/30">
        {{-- Record Attendance Button --}}
        @php
            $route = match ($attendanceMode->value) {
                \App\Enums\AttendanceModeEnum::Daily->value => route('dashboard.attendances.record.daily', [
                    'section' => $section['id'],
                    'schoolDay' => $schoolDay,
                ]),
                \App\Enums\AttendanceModeEnum::SplitDaily->value => route('dashboard.attendances.record.split-daily', [
                    'section' => $section['id'],
                    'schoolDay' => $schoolDay,
                    'dayPart' => $selectedDayPart,
                ]),
                default => null,
            };
        @endphp
        @if ($route)
            <a
                href="{{ $route }}"
                class="flex items-center gap-2 px-4 md:px-2 md:w-10 h-10 bg-white dark:bg-gray-800 rounded-xl justify-center text-primary-600 dark:text-primary-400 hover:bg-primary-500 hover:text-white dark:hover:bg-primary-600 transition-all shadow-sm hover:shadow-md group-hover:scale-105 active:scale-95"
                title="تحضير"
            >
                <i class="fas fa-clipboard-check"></i>
                {{-- Text visible only on very small screens if needed, otherwise icon is enough --}}
                <span class="md:hidden text-sm font-bold">تحضير</span>
            </a>
        @endif
    </div>
</div>
