<x-layouts.dashboard page-title="الجدول الدراسي - {{ $section->name }}">
    <x-slot name="breadcrumbs">
        <x-ui.breadcrumbs :items="[
            ['label' => 'لوحة التحكم', 'url' => route('dashboard.index'), 'icon' => 'fas fa-home'],
            ['label' => 'الشعب الدراسية', 'url' => route('dashboard.sections.index'), 'icon' => 'fas fa-users'],
            ['label' => 'الجدول الدراسي', 'icon' => 'fas fa-calendar-alt'],
        ]" />
    </x-slot>

    <x-ui.main-content-header
        title="الجدول الدراسي"
        description="عرض الجدول الدراسي لشعبة {{ $section->grade->name }} - {{ $section->name }}"
        icon="fas fa-calendar-alt"
        button-text="رجوع"
        button-link="{{ route('dashboard.sections.index') }}"
    />

    @if ($timetable)
        {{-- Timetable Info --}}
        <x-ui.card class="mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">الشعبة</p>
                    <p class="text-lg font-medium text-gray-900 dark:text-white">
                        {{ $section->grade->name }} - شعبة {{ $section->name }}
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">قالب الإعدادات</p>
                    <p class="text-lg font-medium text-gray-900 dark:text-white">
                        {{ $timetable->timetableSetting->name }}
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">عدد الحصص</p>
                    <p class="text-lg font-medium text-gray-900 dark:text-white">
                        {{ $timetable->slots()->count() }} حصة
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">الحالة</p>
                    <x-ui.badge variant="success">
                        <i class="fas fa-check-circle ml-1"></i>
                        مفعل
                    </x-ui.badge>
                </div>
            </div>
        </x-ui.card>

        {{-- Visual Timetable Grid --}}
        <x-ui.card>
            <x-ui.timetable
                :slotsGrouped="$slotsGrouped"
                :timetableSetting="$timetable->timetableSetting"
            >
                {{-- Slot Content (Filled) --}}
                <x-slot:slot>
                    <div
                        class="absolute  top-0 left-0 w-full h-full  z-10 bg-transparent"
                        x-data="{ showPopup: false }"
                        @click.outside="showPopup = false"
                        @click="showPopup = !showPopup"
                    >
                        <div
                            class="absolute z-50 mt-2 w-56 rounded-lg bg-white dark:bg-gray-800 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                            style="display: none;"
                            x-show="showPopup"
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95"
                        >
                            <div class="py-1">
                                @php
                                    $attendanceMode = school()->getAttendanceMode();
                                @endphp
                                @if ($attendanceMode->value === \App\Enums\AttendanceModeEnum::PerPeriod->value)
                                    @verbatim
                                        <a
                                            class="flex items-center gap-3 w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition"
                                            href="{{ route('dashboard.attendances.record.per-period', [
                                                'section' => $period->timetable->section,
                                                'timetableSlot' => $period->id,
                                                'date' => now()->format('Y-m-d'),
                                            ]) }}"
                                        >
                                            <i class="fas fa-user-check text-success-600 dark:text-success-400"></i>
                                            <span>تسجيل الحضور</span>
                                        </a>
                                    @endverbatim
                                @else
                                    @php
                                        $activeYear = school()->activeYear();
                                        $schoolDay = $activeYear
                                            ? \App\Models\SchoolDay::where('academic_year_id', $activeYear->id)
                                                ->whereDate('date', now()->format('Y-m-d'))
                                                ->first()
                                            : null;
                                    @endphp
                                    @if ($schoolDay)
                                        <a
                                            class="flex items-center gap-3 w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition"
                                            href="{{ route('dashboard.academic-calendar.index') }}"
                                        >
                                            <i class="fas fa-calendar-alt text-primary-600 dark:text-primary-400"></i>
                                            <span>التحضير من التقويم</span>
                                        </a>
                                    @endif
                                @endif
                                @can(\Perm::TimetablesUpdate->value)
                                    <a
                                        class="flex items-center gap-3 w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition"
                                        href="{{ route('dashboard.timetables.builder', $timetable) }}"
                                    >
                                        <i class="fas fa-edit text-info-600 dark:text-info-400"></i>
                                        <span>تعديل الحصة</span>
                                    </a>
                                @endcan
                                <button
                                    class="flex items-center gap-3 w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition"
                                    type="button"
                                >
                                    <i class="fas fa-info-circle text-warning-600 dark:text-warning-400"></i>
                                    <span>عرض التفاصيل</span>
                                </button>
                            </div>
                        </div>
                    </div>

                </x-slot:slot>
            </x-ui.timetable>
        </x-ui.card>
    @else
        {{-- No Active Timetable --}}
        <x-ui.card>
            <div class="text-center py-12">
                <div
                    class="mx-auto w-24 h-24 rounded-full bg-warning-100 dark:bg-warning-900/20 flex items-center justify-center mb-6">
                    <i class="fas fa-calendar-times text-4xl text-warning-600 dark:text-warning-400"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
                    لا يوجد جدول دراسي نشط
                </h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6 max-w-md mx-auto">
                    لا يوجد جدول دراسي نشط لهذه الشعبة. يمكنك إنشاء جدول جديد أو تفعيل جدول موجود.
                </p>

                <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                    @can(\Perm::TimetablesActivate->value)
                        <x-ui.button
                            as="a"
                            href="{{ route('dashboard.timetables.list', [
                                'sectionId' => $section->id,
                            ]) }}"
                            variant="primary"
                        >
                            <i class="fas fa-toggle-on"></i>
                            تفعيل جدول موجود
                        </x-ui.button>
                    @endcan

                    @can(\Perm::TimetablesCreate->value)
                        <x-ui.button
                            as="a"
                            href="{{ route('dashboard.timetables.create') }}?section_id={{ $section->id }}"
                            variant="primary"
                        >
                            <i class="fas fa-plus"></i>
                            إنشاء جدول جديد
                        </x-ui.button>
                    @endcan
                </div>
            </div>
        </x-ui.card>
    @endif
</x-layouts.dashboard>
