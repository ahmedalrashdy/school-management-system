@props(['calendar' => [], 'stats'])
@php
    $calendarJson = json_encode($calendar);
    $currentMonthKey = now()->format('Y-m');
    if (!isset($calendar[$currentMonthKey])) {
        $currentMonthKey = array_key_first($calendar);
    }

@endphp
<div>
    <div
        x-data="attendanceViewer({{ $calendarJson }}, '{{ $currentMonthKey }}')"
        class="space-y-8 pb-10"
    >

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <x-ui.stat-card
                icon="fas fa-calendar-day"
                title="إجمالي الأيام المرصودة"
                value="{{ $stats['total_days'] }}"
                color="primary"
            />

            <x-ui.stat-card
                icon="fas fa-check-circle"
                title="حضور كامل"
                value="{{ $stats[\App\Enums\AttendanceStatusEnum::Present->value] }}"
                color="success"
            />

            <x-ui.stat-card
                icon="fas fa-times-circle"
                title="غياب كامل"
                value="{{ $stats[\App\Enums\AttendanceStatusEnum::Absent->value] }}"
                color="danger"
            />

            <x-ui.stat-card
                icon="fas fa-file-medical"
                title='بعذر '
                value="{{ $stats[\App\Enums\AttendanceStatusEnum::Excused->value] }}"
                color="info"
            />

            <x-ui.stat-card
                icon="fas fa-user-clock"
                title='تأخر عن جميع مرات التحضير'
                value="{{ $stats[\App\Enums\AttendanceStatusEnum::Late->value] }}"
                color="warning"
            />

            <x-ui.stat-card
                icon="fas fa-door-open"
                title="غياب جزئي"
                value="{{ $stats['partial_absence'] }}"
                color="danger"
            />

            <x-ui.stat-card
                icon="fas fa-file-signature"
                title='استئذان جزئي'
                value="{{ $stats['partial_excused'] }}"
                color="info"
            />

            <x-ui.stat-card
                icon="fas fa-history"
                title="حضور وتأخير"
                value="{{ $stats['present_with_late'] }}"
                color="warning"
            />

        </div>

        <!-- 2. منطقة المحتوى الرئيسية -->
        <div
            class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden min-h-[600px]">

            <!--Months tabs -->
            <div class="border-b border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50 px-2">
                <div class="flex overflow-x-auto no-scrollbar gap-2 pt-2">
                    <template
                        x-for="(monthData, key) in calendar"
                        :key="key"
                    >
                        <button
                            @click="selectMonth(key)"
                            :class="selectedMonth === key ?
                                'border-primary-500 text-primary-600 dark:text-primary-400 bg-white dark:bg-gray-800 rounded-t-lg' :
                                'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-700/50 rounded-t-lg'"
                            class="shrink-0 px-6 py-3 text-sm font-bold border-b-2 transition-all duration-200 focus:outline-none"
                        >
                            <span x-text="monthData.label"></span>
                        </button>
                    </template>
                </div>
            </div>

            <div class="p-6">

                <!--(Weeks Tabs)-->
                <div class="mb-8 overflow-x-auto no-scrollbar">
                    <div class="flex flex-nowrap md:flex-wrap gap-3">
                        <template
                            x-for="(weekData, weekKey) in calendar[selectedMonth].weeks"
                            :key="weekKey"
                        >
                            <button
                                @click="selectedWeek = weekKey"
                                :class="selectedWeek === weekKey ?
                                    'bg-primary-600 text-white shadow-md ring-2 ring-primary-200 dark:ring-primary-900' :
                                    'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600'"
                                class="px-5 py-2.5 rounded-full text-sm font-medium transition-all duration-200 whitespace-nowrap"
                            >
                                <span x-text="weekData.label"></span>
                            </button>
                        </template>
                    </div>
                </div>

                <!--Days List -->
                <div class="space-y-4">
                    <template
                        x-for="day in calendar[selectedMonth].weeks[selectedWeek].days"
                        :key="day.date_full"
                    >

                        <!-- بطاقة اليوم -->
                        <div
                            x-data="{ expanded: false }"
                            class="group border border-gray-200 dark:border-gray-700 rounded-xl bg-white dark:bg-gray-800 transition-all duration-200 hover:shadow-md"
                        >

                            <!-- رأس البطاقة -->
                            <div
                                @click="if(day.slots.length > 0) expanded = !expanded"
                                :class="{
                                    'cursor-pointer': day.slots.length >
                                        0,
                                    'opacity-75 bg-gray-50 dark:bg-gray-800/50': day.slots.length === 0
                                }"
                                class="p-4 flex flex-col md:flex-row md:items-center justify-between gap-4"
                            >

                                <div class="flex items-center gap-4">
                                    <!-- التاريخ -->
                                    <div
                                        class="flex flex-col items-center justify-center w-14 h-14 rounded-xl shadow-sm border border-gray-100 dark:border-gray-600"
                                        :class="day.is_holiday ? 'bg-blue-50 dark:bg-blue-900/20' : 'bg-white dark:bg-gray-700'"
                                    >
                                        <span
                                            class="text-xs font-medium"
                                            :class="day.is_holiday ? 'text-blue-500' : 'text-gray-500 dark:text-gray-400'"
                                            x-text="day.day_name"
                                        ></span>
                                        <span
                                            class="text-xl font-bold leading-none mt-1"
                                            :class="day.is_holiday ? 'text-blue-600' : 'text-gray-900 dark:text-white'"
                                            x-text="day.date_day"
                                        ></span>
                                    </div>

                                    <!-- المعلومات -->
                                    <div>
                                        <h4
                                            class="text-sm font-bold text-gray-900 dark:text-white"
                                            x-text="day.date_full"
                                        ></h4>
                                        </h4>
                                        <div class="text-xs mt-1">
                                            <template x-if="day.is_holiday ">
                                                <span class="text-blue-500 font-medium">عطلة رسمية</span>
                                            </template>
                                            <template x-if="day.is_partial_holiday">
                                                <span
                                                    class="text-blue-500 font-medium"
                                                    x-text="day.day_part=={{ App\Enums\DayPartEnum::PART_ONE_ONLY->value }}?{{ App\Enums\DayPartEnum::PART_TWO_ONLY->value }}:{{ App\Enums\DayPartEnum::PART_ONE_ONLY->value }}"
                                                ></span>
                                            </template>
                                            <template x-if="!day.is_holiday && day.slots.length > 0">
                                                <span
                                                    class="text-gray-500 dark:text-gray-400"
                                                    x-text="'تم رصد التحصير '+day.slots.length + 'مرة'"
                                                ></span>
                                            </template>
                                            <template x-if="!day.is_holiday && day.slots.length === 0">
                                                <span class="text-gray-400">لم يتم الرصد بعد</span>
                                            </template>
                                        </div>
                                    </div>
                                </div>

                                <!-- الحالة النهائية لليوم -->
                                <div
                                    class="flex items-center justify-between md:justify-end gap-3 w-full md:w-auto mt-2 md:mt-0">
                                    <span
                                        :class="getBadgeClass(day.final_status_color)"
                                        class="px-4 py-1.5 rounded-full text-xs font-bold border shadow-sm flex items-center gap-2"
                                    >
                                        <i :class="getStatusIcon(day.final_status_color)"></i>
                                        <span x-text="day.final_status_label"></span>
                                    </span>

                                    <!-- أيقونة التوسيع -->
                                    <template x-if="day.slots.length > 0">
                                        <div
                                            class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-50 dark:bg-gray-700 text-gray-400 group-hover:bg-primary-50 group-hover:text-primary-500 transition-colors">
                                            <i
                                                class="fas fa-chevron-down transition-transform duration-300"
                                                :class="{ 'rotate-180': expanded }"
                                            ></i>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <!-- التفاصيل (الأكورديون) -->
                            <div
                                x-show="expanded"
                                x-collapse
                            >
                                <div
                                    class="px-4 pb-4 pt-2 border-t border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50 rounded-b-xl">
                                    <div class="grid gap-2">
                                        <template
                                            x-for="(slot, index) in day.slots"
                                            :key="index"
                                        >
                                            <div
                                                class="flex items-center justify-between p-3 rounded-lg bg-white dark:bg-gray-700 shadow-sm border border-gray-100 dark:border-gray-600">
                                                <div class="flex items-center gap-3">
                                                    <span
                                                        class="px-2 py-1 bg-gray-100 dark:bg-gray-600 rounded text-xs font-bold text-gray-600 dark:text-gray-300"
                                                        x-text="slot.label"
                                                    ></span>
                                                    <span
                                                        class="text-sm font-semibold text-gray-800 dark:text-gray-200"
                                                        x-text="slot.subject"
                                                    ></span>
                                                </div>

                                                <div class="flex items-center gap-3">
                                                    <!-- ملاحظات -->
                                                    <template x-if="slot.notes">
                                                        <div class="relative group/tooltip">
                                                            <i
                                                                class="fas fa-comment-dots text-gray-400 hover:text-primary-500 cursor-help"></i>
                                                            <div
                                                                class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-48 p-2 bg-black text-white text-xs rounded shadow-lg hidden group-hover/tooltip:block z-10 text-center">
                                                                <span x-text="slot.notes"></span>
                                                            </div>
                                                        </div>
                                                    </template>

                                                    <!-- حالة الحصة -->
                                                    <span
                                                        :class="getSlotTextColor(slot.status)"
                                                        class="text-xs font-bold flex items-center gap-1"
                                                    >
                                                        <i :class="getSlotIcon(slot.status)"></i>
                                                        <span x-text="slot.status_text"></span>
                                                    </span>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </template>

                    <!-- حالة عدم وجود بيانات -->
                    <template
                        x-if="!calendar[selectedMonth].weeks[selectedWeek] || calendar[selectedMonth].weeks[selectedWeek].days.length === 0"
                    >
                        <div
                            class="flex flex-col items-center justify-center py-12 text-center border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-xl bg-gray-50 dark:bg-gray-800/50">
                            <i class="fas fa-calendar-times text-4xl text-gray-300 mb-3"></i>
                            <p class="text-gray-500">لا توجد سجلات دراسية في هذا الأسبوع</p>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>
