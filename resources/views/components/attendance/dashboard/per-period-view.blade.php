@props(['grades', 'selectedGradeId', 'selectedSectionId', 'schoolDay'])

{{-- Utility Style for hiding scrollbar but keeping functionality --}}
<style>
    .no-scrollbar::-webkit-scrollbar {
        display: none;
    }

    .no-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
</style>

<div
    class="space-y-6"
    x-data="{
        selectedGradeId: @js($selectedGradeId),
        selectedSectionId: @js($selectedSectionId),
        selectGrade(gradeId) {
            this.selectedGradeId = gradeId;
            this.selectedSectionId = null;
        },
        selectSection(sectionId) {
            this.selectedSectionId = sectionId;
        }
    }"
>

    {{-- 1. Grade Selector (Horizontal Scroll) --}}
    <div>
        <div class="flex items-center justify-between mb-3 px-1">
            <h3 class="text-sm font-bold text-gray-700 dark:text-gray-300">الصفوف الدراسية</h3>
            <span class="text-xs text-gray-400 bg-gray-100 dark:bg-gray-800 px-2 py-1 rounded-full">{{ count($grades) }}
                صفوف</span>
        </div>

        <div class="overflow-x-auto no-scrollbar pb-2 -mx-1 px-1">
            <div class="flex gap-3 min-w-max">
                @foreach ($grades as $grade)
                    @php
                        $totalPeriods = collect($grade['sections'])->sum(fn($s) => count($s['periods']));
                        $recordedPeriods = collect($grade['sections'])->sum(
                            fn($s) => collect($s['periods'])->where('is_recorded', true)->count(),
                        );
                        $isComplete = $totalPeriods > 0 && $recordedPeriods === $totalPeriods;
                    @endphp

                    <button
                        @click="selectGrade({{ $grade['id'] }})"
                        :class="selectedGradeId === {{ $grade['id'] }} ?
                            'bg-linear-to-br from-primary-600 to-primary-700 text-white shadow-lg shadow-primary-500/30 border-transparent transform scale-[1.02]' :
                            'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 border-gray-100 dark:border-gray-700/50 hover:border-primary-200 dark:hover:border-primary-800'"
                        class="relative group flex flex-col items-start gap-3 p-4 rounded-2xl transition-all duration-300 min-w-[160px] border"
                    >

                        <div class="flex items-center justify-between w-full">
                            <div
                                :class="selectedGradeId === {{ $grade['id'] }} ?
                                    'bg-white/20' :
                                    'bg-primary-50 dark:bg-primary-900/20'"
                                class="w-10 h-10 rounded-xl flex items-center justify-center transition-colors"
                            >
                                <i
                                    :class="selectedGradeId === {{ $grade['id'] }} ?
                                        'text-white' :
                                        'text-primary-600 dark:text-primary-400'"
                                    class="fas fa-graduation-cap text-lg"
                                ></i>
                            </div>
                            @if ($isComplete)
                                <div
                                    :class="selectedGradeId === {{ $grade['id'] }} ?
                                        'bg-emerald-400 text-primary-800' :
                                        'bg-emerald-100 text-emerald-600'"
                                    class="w-6 h-6 rounded-full flex items-center justify-center"
                                >
                                    <i class="fas fa-check text-xs"></i>
                                </div>
                            @endif
                        </div>

                        <div>
                            <span class="block font-bold text-lg leading-tight">{{ $grade['name'] }}</span>
                            <span class="text-xs mt-1 block opacity-80">
                                {{ $recordedPeriods }} / {{ $totalPeriods }} حصة
                            </span>
                        </div>

                        {{-- Active Indicator --}}
                        <div
                            x-show="selectedGradeId === {{ $grade['id'] }}"
                            class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-12 h-1 bg-primary-400/50 blur-sm rounded-full"
                        >
                        </div>
                    </button>
                @endforeach
            </div>
        </div>
    </div>

    {{-- 2. Main Content Area --}}
    <div class="min-h-[400px]">
        <template x-if="selectedGradeId">
            <div>
                @php
                    // We'll use Alpine to filter, but we need to prepare the data
                @endphp
                @foreach ($grades as $grade)
                    <div
                        x-show="selectedGradeId === {{ $grade['id'] }}"
                        x-cloak
                    >
                        <div
                            class="bg-white dark:bg-gray-800/80 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700/50 overflow-hidden">

                            {{-- 2.1 Section Tabs (Pills) --}}
                            <div
                                class="border-b border-gray-100 dark:border-gray-700/50 p-4 bg-gray-50/50 dark:bg-gray-900/30">
                                <div class="flex gap-2 overflow-x-auto no-scrollbar">
                                    @foreach ($grade['sections'] as $section)
                                        <button
                                            @click="selectSection({{ $section['id'] }})"
                                            :class="selectedSectionId === {{ $section['id'] }} ?
                                                'bg-white dark:bg-gray-700 text-primary-600 dark:text-primary-400 shadow-sm border-gray-200 dark:border-gray-600 ring-2 ring-primary-500/10' :
                                                'bg-transparent text-gray-500 dark:text-gray-400 border-transparent hover:bg-white/50 dark:hover:bg-gray-700/50'"
                                            class="shrink-0 relative px-4 py-2 rounded-xl text-sm font-bold transition-all duration-300 flex items-center gap-2 border"
                                        >
                                            <span>{{ $section['name'] }}</span>

                                            {{-- Progress Mini Badge --}}
                                            <span
                                                :class="selectedSectionId === {{ $section['id'] }} ?
                                                    'bg-gray-100 dark:bg-gray-800 text-gray-600' :
                                                    'bg-gray-200 dark:bg-gray-700 text-gray-500'"
                                                class="px-1.5 py-0.5 rounded text-[10px]"
                                            >
                                                {{ $section['recorded_periods'] }}/{{ $section['total_periods'] }}
                                            </span>
                                        </button>
                                    @endforeach
                                </div>
                            </div>

                            {{-- 2.2 Selected Section Content --}}
                            <template x-if="selectedSectionId">
                                <div>
                                    @foreach ($grade['sections'] as $section)
                                        <div
                                            x-show="selectedSectionId === {{ $section['id'] }}"
                                            x-cloak
                                        >
                                            @php
                                                $recordedCount = $section['recorded_periods'];
                                                $totalCount = $section['total_periods'];
                                                $progressPercent =
                                                    $totalCount > 0 ? ($recordedCount / $totalCount) * 100 : 0;
                                            @endphp

                                            <div class="p-4 md:p-6 animate-fadeIn">

                                                {{-- Header Stats --}}
                                                <div
                                                    class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-8">
                                                    <div class="flex items-center gap-4">
                                                        <div
                                                            class="relative w-16 h-16 shrink-0 bg-linear-to-br from-gray-800 to-gray-900 dark:from-gray-700 dark:to-gray-800 rounded-2xl flex items-center justify-center shadow-lg">
                                                            <span
                                                                class="text-white font-bold text-2xl">{{ $section['name'] }}</span>
                                                            <div
                                                                class="absolute -top-2 -right-2 w-6 h-6 bg-white dark:bg-gray-800 rounded-full flex items-center justify-center shadow-sm">
                                                                <i class="fas fa-layer-group text-xs text-gray-400"></i>
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                                                                {{ $grade['name'] }} <span
                                                                    class="text-gray-300 mx-1">/</span>
                                                                شعبة {{ $section['name'] }}
                                                            </h2>
                                                            <div
                                                                class="flex flex-wrap items-center gap-3 mt-2 text-sm text-gray-500 dark:text-gray-400">
                                                                <span
                                                                    class="flex items-center gap-1.5 bg-gray-50 dark:bg-gray-700/50 px-2 py-1 rounded-md"
                                                                >
                                                                    <i
                                                                        class="fas fa-users text-primary-500 text-xs"></i>
                                                                    {{ $section['total_students'] }} طالب
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {{-- Big Progress Bar --}}
                                                    <div
                                                        class="w-full md:w-64 bg-gray-50 dark:bg-gray-900/50 rounded-2xl p-4 border border-gray-100 dark:border-gray-700/50">
                                                        <div class="flex justify-between items-end mb-2">
                                                            <span class="text-xs font-semibold text-gray-500">حالة الرصد
                                                                اليومي</span>
                                                            <span
                                                                class="text-lg font-bold {{ $progressPercent == 100 ? 'text-emerald-600' : 'text-primary-600' }}"
                                                            >
                                                                {{ round($progressPercent) }}%
                                                            </span>
                                                        </div>
                                                        <div
                                                            class="h-2.5 w-full bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                                                            <div
                                                                class="h-full rounded-full transition-all duration-1000 ease-out {{ $progressPercent == 100 ? 'bg-gradient-to-r from-emerald-400 to-emerald-600' : 'bg-gradient-to-r from-primary-400 to-violet-500' }}"
                                                                style="width: {{ $progressPercent }}%"
                                                            ></div>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Visual Grid (Quick Look) --}}
                                                <div class="mb-8">
                                                    <h5
                                                        class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-4 flex items-center gap-2">
                                                        <i class="fas fa-th-large text-gray-400"></i>
                                                        نظرة سريعة
                                                    </h5>
                                                    <div
                                                        class="grid grid-cols-5 sm:grid-cols-6 md:grid-cols-8 lg:grid-cols-10 gap-2 sm:gap-3">
                                                        @foreach ($section['periods'] as $period)
                                                            <div
                                                                class="group relative aspect-square rounded-xl flex flex-col items-center justify-center transition-all duration-300 cursor-help hover:-translate-y-1 hover:shadow-md
                                                {{ $period['is_recorded']
                                                    ? 'bg-gradient-to-br from-emerald-500 to-teal-600 text-white shadow-emerald-500/20'
                                                    : 'bg-white dark:bg-gray-700 text-gray-400 dark:text-gray-500 border-2 border-dashed border-gray-200 dark:border-gray-600 hover:border-gray-300' }}">

                                                                <span
                                                                    class="text-lg md:text-xl font-bold">{{ $period['number'] }}</span>

                                                                @if ($period['is_recorded'])
                                                                    <i
                                                                        class="fas fa-check-circle text-[10px] opacity-75 mt-1"></i>
                                                                @else
                                                                    <span
                                                                        class="text-[9px] mt-1 font-medium">متبقي</span>
                                                                @endif

                                                                {{-- Tooltip (Hover) --}}
                                                                <div
                                                                    class="absolute bottom-full mb-2 opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none z-10 w-32 bg-gray-900 text-white text-xs rounded-lg p-2 text-center shadow-xl">
                                                                    <div class="font-bold text-amber-400">
                                                                        {{ $period['subject'] }}
                                                                    </div>
                                                                    <div class="text-gray-300 text-[10px] mt-1">
                                                                        {{ $period['teacher_name'] }}</div>
                                                                    <svg
                                                                        class="absolute text-gray-900 h-2 w-full left-0 top-full"
                                                                        x="0px"
                                                                        y="0px"
                                                                        viewBox="0 0 255 255"
                                                                    >
                                                                        <polygon
                                                                            class="fill-current"
                                                                            points="0,0 127.5,127.5 255,0"
                                                                        />
                                                                    </svg>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>

                                                {{-- Details List --}}
                                                <div>
                                                    <h5
                                                        class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-4 flex items-center gap-2">
                                                        <i class="fas fa-list-ul text-gray-400"></i>
                                                        سجل الحصص التفصيلي
                                                    </h5>
                                                    <div class="flex flex-col gap-3">
                                                        @foreach ($section['periods'] as $period)
                                                            <x-attendance.dashboard.period-accordion
                                                                :period="$period"
                                                                :section="$section"
                                                                :school-day="$schoolDay"
                                                            />
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </template>

                            {{-- Empty Section State --}}
                            <div
                                x-show="!selectedSectionId"
                                x-cloak
                                class="flex flex-col items-center justify-center py-20 text-center animate-fadeIn"
                            >
                                <div
                                    class="w-24 h-24 bg-primary-50 dark:bg-primary-900/20 rounded-3xl flex items-center justify-center mb-6 animate-bounce-slow">
                                    <i class="fas fa-hand-pointer text-4xl text-primary-300"></i>
                                </div>
                                <h3 class="text-xl font-bold text-gray-800 dark:text-white">اختر الشعبة لعرض التفاصيل
                                </h3>
                                <p class="text-gray-500 max-w-xs mt-2">اضغط على إحدى الشعب في الشريط العلوي لعرض الحصص
                                    وحالة
                                    الرصد</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </template>

        {{-- Empty Grade State --}}
        <div
            x-show="!selectedGradeId"
            x-cloak
            class="flex flex-col items-center justify-center h-[400px] bg-white dark:bg-gray-800/50 rounded-3xl border-2 border-dashed border-gray-200 dark:border-gray-700 text-center"
        >
            <div
                class="w-24 h-24 bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-600 rounded-full flex items-center justify-center mb-6 shadow-inner">
                <i class="fas fa-school text-4xl text-gray-400 dark:text-gray-500"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-700 dark:text-gray-200">الرجاء اختيار الصف الدراسي</h3>
            <p class="text-gray-500 dark:text-gray-400 mt-2">ابدأ باختيار الصف من القائمة أعلاه لمتابعة الحضور</p>
        </div>
    </div>
</div>
