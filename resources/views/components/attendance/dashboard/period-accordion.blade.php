@props(['period', 'section', 'schoolDay'])

@php
    $presentPercentage =
        $period['total_students'] > 0 ? round(($period['present'] / $period['total_students']) * 100) : 0;
@endphp

<div
    x-data="{ expanded: false, isLoading: false }"
    class="border rounded-xl overflow-hidden transition-all duration-300
    {{ $period['is_recorded']
        ? 'border-emerald-200/50 dark:border-emerald-700/30 bg-white dark:bg-gray-800/80'
        : 'border-gray-200 dark:border-gray-700/50 bg-gray-50/50 dark:bg-gray-900/30' }}"
    :class="{ 'ring-2 ring-primary-500/30 shadow-md': expanded, 'opacity-75 pointer-events-none': isLoading }"
>

    {{-- Period Header --}}
    <button
        @click="expanded = !expanded"
        class="w-full px-4 py-3 flex items-center justify-between hover:bg-gray-50/50 dark:hover:bg-gray-700/20 transition-colors"
    >
        <div class="flex items-center gap-3">
            {{-- Period Number Badge --}}
            <div class="relative">
                <div
                    class="w-11 h-11 rounded-xl flex items-center justify-center font-bold text-lg transition-transform hover:scale-110
                    {{ $period['is_recorded']
                        ? 'bg-gradient-to-br from-emerald-400 to-emerald-500 text-white shadow-lg shadow-emerald-500/20'
                        : 'bg-gray-200 dark:bg-gray-700 text-gray-500 dark:text-gray-400' }}">
                    {{ $period['number'] }}
                </div>
            </div>

            {{-- Period Info --}}
            <div class="text-right">
                <div class="flex items-center gap-2">
                    <h5 class="font-bold text-gray-900 dark:text-white">الحصة {{ $period['number'] }}</h5>
                    @if ($period['is_recorded'])
                        <span
                            class="inline-flex items-center gap-1 px-2 py-0.5 bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-400 rounded-lg text-xs font-semibold"
                        >
                            <i class="fas fa-check"></i> تم
                        </span>
                    @else
                        <span
                            class="inline-flex items-center gap-1 px-2 py-0.5 bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-400 rounded-lg text-xs font-semibold"
                        >
                            <i class="fas fa-clock"></i> انتظار
                        </span>
                    @endif
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400 flex items-center gap-2 mt-0.5">
                    <span class="inline-flex items-center gap-1">
                        <i class="fas fa-book text-xs"></i>
                        {{ $period['subject'] }}
                    </span>
                    <span class="text-gray-300 dark:text-gray-600">•</span>
                    <span class="inline-flex items-center gap-1">
                        <i class="fas fa-user text-xs"></i>
                        {{ $period['teacher_name'] }}
                    </span>
                </p>
            </div>
        </div>

        {{-- Stats & Actions --}}
        <div class="flex items-center gap-4">
            @if ($period['is_recorded'])
                {{-- Mini Stats --}}
                <div class="hidden sm:flex">
                    <x-attendance.dashboard.attendance-stats-badges
                        :present="$period['present']"
                        :absent="$period['absent']"
                        :late="$period['late']"
                        :excused="$period['excused']"
                        size="sm"
                        variant="compact"
                        :show-dark-mode="true"
                        excused-color="sky"
                        container-class="flex items-center gap-2"
                    />
                </div>
            @endif

            {{-- Expand Icon --}}
            <div
                class="w-9 h-9 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center transition-all duration-300"
                :class="{ 'rotate-180 bg-primary-100 dark:bg-primary-900/30': expanded }"
            >
                <i
                    class="fas fa-chevron-down text-gray-400 dark:text-gray-500 text-sm"
                    :class="{ 'text-primary-600 dark:text-primary-400': expanded }"
                ></i>
            </div>
        </div>
    </button>

    {{-- Period Details (Expandable) --}}
    <div
        x-show="expanded"
        x-collapse
        x-cloak
    >
        <div class="border-t border-gray-100 dark:border-gray-700/50 p-5">
            @if ($period['is_recorded'])
                {{-- Stats Cards --}}
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-5">
                    <div
                        class="bg-gradient-to-br from-emerald-50 to-emerald-100/50 dark:from-emerald-900/30 dark:to-emerald-900/10 rounded-xl p-4 text-center border border-emerald-200/50 dark:border-emerald-700/30">
                        <div class="text-2xl font-black text-emerald-600 dark:text-emerald-400">
                            {{ $period['present'] }}
                        </div>
                        <div class="text-xs text-emerald-600/70 dark:text-emerald-400/70 font-medium mt-1">حضور</div>
                        <div class="text-xs text-emerald-500 mt-1">{{ $presentPercentage }}%</div>
                    </div>
                    <div
                        class="bg-gradient-to-br from-rose-50 to-rose-100/50 dark:from-rose-900/30 dark:to-rose-900/10 rounded-xl p-4 text-center border border-rose-200/50 dark:border-rose-700/30">
                        <div class="text-2xl font-black text-rose-600 dark:text-rose-400">{{ $period['absent'] }}</div>
                        <div class="text-xs text-rose-600/70 dark:text-rose-400/70 font-medium mt-1">غياب</div>
                    </div>
                    <div
                        class="bg-linear-to-br from-amber-50 to-amber-100/50 dark:from-amber-900/30 dark:to-amber-900/10 rounded-xl p-4 text-center border border-amber-200/50 dark:border-amber-700/30">
                        <div class="text-2xl font-black text-amber-600 dark:text-amber-400">{{ $period['late'] }}</div>
                        <div class="text-xs text-amber-600/70 dark:text-amber-400/70 font-medium mt-1">تأخر</div>
                    </div>
                    <div
                        class="bg-linear-to-br from-sky-50 to-sky-100/50 dark:from-sky-900/30 dark:to-sky-900/10 rounded-xl p-4 text-center border border-sky-200/50 dark:border-sky-700/30">
                        <div class="text-2xl font-black text-sky-600 dark:text-sky-400">{{ $period['excused'] }}</div>
                        <div class="text-xs text-sky-600/70 dark:text-sky-400/70 font-medium mt-1">معذور</div>
                    </div>
                </div>

                {{-- Absent Students Button --}}
                @if ($period['absent'] > 0)
                    <div class="mb-5">
                        <button
                            @click="
                                isLoading = true;
                                $wire.loadPeriodStudents({{ $section['id'] }}, {{ $period['slot_id'] }})
                                    .then(students => {
                                        $dispatch('open-modal', {
                                            name: 'section-students-modal',
                                            sectionName: '{{ $section['name'] }} - الحصة {{ $period['number'] }}',
                                            students: students
                                        });
                                        isLoading = false;
                                    })
                                    .catch(() => isLoading = false);
                            "
                            class="w-full py-3 px-4 bg-rose-50 hover:bg-rose-100 dark:bg-rose-900/30 dark:hover:bg-rose-900/50 text-rose-700 dark:text-rose-400 rounded-xl transition-colors font-bold flex items-center justify-center gap-2 border border-rose-200 dark:border-rose-700/50"
                        >
                            <i class="fas fa-users"></i>
                            عرض الطلاب الغائبين ({{ $period['absent'] }})
                        </button>
                    </div>
                @endif

                {{-- Teacher Info Card --}}
                <div
                    class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-900/30 rounded-xl border border-gray-100 dark:border-gray-700/50">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-12 h-12 bg-gradient-to-br from-primary-400 to-violet-500 rounded-xl flex items-center justify-center shadow-lg shadow-primary-500/20">
                            <i class="fas fa-chalkboard-teacher text-white text-lg"></i>
                        </div>
                        <div>
                            <p class="font-bold text-gray-900 dark:text-white">{{ $period['teacher_name'] }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">المادة: {{ $period['subject'] }}</p>
                        </div>
                    </div>
                    @php
                        $date = $schoolDay->date->format('Y-m-d');
                        $studentListRoute = route('dashboard.attendances.students.per-period', [
                            'section' => $section['id'],
                            'timetableSlot' => $period['slot_id'],
                            'date' => $date,
                        ]);
                    @endphp
                    <a
                        href="{{ $studentListRoute }}"
                        class="px-4 py-2 bg-white dark:bg-gray-800 text-primary-600 dark:text-primary-400 rounded-xl hover:bg-primary-50 dark:hover:bg-primary-900/30 transition-colors text-sm font-semibold flex items-center gap-2 border border-gray-200 dark:border-gray-700 hover:border-primary-200 dark:hover:border-primary-700"
                    >
                        <i class="fas fa-external-link-alt"></i>
                        عرض التفاصيل
                    </a>
                </div>
            @else
                {{-- Not Recorded State --}}
                <div class="text-center py-6">
                    <div
                        class="w-16 h-16 bg-linear-to-br from-amber-100 to-orange-100 dark:from-amber-900/30 dark:to-orange-900/30 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-inner">
                        <i class="fas fa-hourglass-half text-amber-500 dark:text-amber-400 text-2xl animate-pulse"></i>
                    </div>
                    <p class="text-gray-700 dark:text-gray-300 font-bold mb-1">لم يتم رصد الحضور بعد</p>
                    <p class="text-gray-500 dark:text-gray-400 text-sm mb-4">{{ $period['subject'] }} -
                        {{ $period['teacher_name'] }}</p>
                    @php
                        $sectionModel = \App\Models\Section::find($section['id']);
                        $timetableSlot = \App\Models\TimetableSlot::find($period['slot_id']);
                        $date = $schoolDay->date->format('Y-m-d');
                        $route = route('dashboard.attendances.record.per-period', [
                            'section' => $sectionModel,
                            'timetableSlot' => $timetableSlot,
                            'date' => $date,
                        ]);
                    @endphp
                    <a
                        href="{{ $route }}"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-primary-500 to-violet-600 hover:from-primary-600 hover:to-violet-700 text-white rounded-xl transition-all font-semibold shadow-lg shadow-primary-500/20 hover:shadow-xl hover:shadow-primary-500/30 hover:-translate-y-0.5"
                    >
                        <i class="fas fa-clipboard-check"></i>
                        تحضير
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
