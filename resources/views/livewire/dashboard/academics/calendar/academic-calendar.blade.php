<div>
    <x-slot name="breadcrumbs">
        <x-ui.breadcrumbs :items="[
            ['label' => 'لوحة التحكم', 'url' => route('dashboard.index'), 'icon' => 'fas fa-home'],
            ['label' => 'التقويم الدراسي', 'icon' => 'fas fa-calendar-alt'],
        ]" />
    </x-slot>
    @php
        $attendanceMode = school()->getAttendanceMode();
        $weekendDays = school()->getArrayData('weekend_days') ?? [];
    @endphp
    {{-- Main Header --}}
    <x-ui.main-content-header
        title="إدارة التقويم الدراسي"
        description="إدارة الأيام الدراسية والعطل"
    />

    @if (!$term || $this->currentMonthDays->count() == 0)
        {{-- Empty State (No Changes) --}}
        <div
            class="flex min-h-[400px] flex-col items-center justify-center rounded-2xl border border-dashed border-gray-300 bg-gray-50 p-12 text-center dark:border-gray-700 dark:bg-gray-800/50">
            <div
                class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-white shadow-sm ring-1 ring-gray-200 dark:bg-gray-800 dark:ring-gray-700">
                <i class="fas fa-calendar-plus text-3xl text-primary-500"></i>
            </div>
            @if (!$term)
                <h3 class="mb-2 text-lg font-bold text-gray-900 dark:text-white">لا يوجد فصل دراسي نشط </h3>
            @else
                <h3 class="mb-2 text-lg font-bold text-gray-900 dark:text-white">لا يوجد تقويم دراسي نشط</h3>
            @endif
            <p class="mb-8 max-w-md text-sm text-gray-500 dark:text-gray-400">
                لم يتم العثور على تواريخ للترم الدراسي الحالي.
            </p>
            <a
                href="{{ route('dashboard.academic-terms.index') }}"
                class="inline-flex items-center gap-2 rounded-lg bg-primary-600 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-primary-700"
            >
                <i class="fas fa-cog"></i>
                إعدادات الترم الحالي
            </a>
        </div>
    @else
        <div
            class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-200 dark:bg-gray-800 dark:ring-gray-700">
            {{-- Calendar Header --}}
            <div
                class="flex items-center justify-between border-b border-gray-200 bg-gray-50/50 px-6 py-4 dark:border-gray-700 dark:bg-gray-800/50">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                    {{ $currentMonth?->locale('ar')->translatedFormat('F Y') }}
                </h3>
                <div class="flex items-center gap-1">
                    <button
                        class="flex h-9 w-9 items-center justify-center rounded-lg transition {{ $canGoPrev ? 'text-gray-500 hover:bg-gray-200 dark:text-gray-400 dark:hover:bg-gray-700' : 'cursor-not-allowed text-gray-300 dark:text-gray-600' }}"
                        type="button"
                        wire:click="previousMonth"
                        @if (!$canGoPrev) disabled @endif
                    >
                        <i class="fas fa-chevron-right"></i>
                    </button>
                    <button
                        class="flex h-9 w-9 items-center justify-center rounded-lg transition {{ $canGoNext ? 'text-gray-500 hover:bg-gray-200 dark:text-gray-400 dark:hover:bg-gray-700' : 'cursor-not-allowed text-gray-300 dark:text-gray-600' }}"
                        type="button"
                        wire:click="nextMonth"
                        @if (!$canGoNext) disabled @endif
                    >
                        <i class="fas fa-chevron-left"></i>
                    </button>
                </div>
            </div>

            <div class="p-6">
                {{-- Calendar Grid --}}
                <div
                    class="grid grid-cols-7 gap-px overflow-hidden rounded-xl border border-gray-200 bg-gray-200 dark:border-gray-700 dark:bg-gray-700">
                    {{-- Headers --}}
                    @foreach (\App\Enums\DayOfWeekEnum::options() as $dayName)
                        <div
                            class="bg-gray-50 py-3 text-center text-sm font-semibold text-gray-500 dark:bg-gray-800 dark:text-gray-400">
                            {{ $dayName }}
                        </div>
                    @endforeach

                    {{-- Days Loop --}}
                    @foreach ($this->currentMonthDays as $cell)
                        @if ($cell === null)
                            <div class="min-h-[120px] bg-gray-50/30 dark:bg-gray-800/30"></div>
                        @else
                            @php
                                $cellClasses = 'group relative min-h-[120px] p-2 transition-colors';
                                $bgClass = 'bg-gray-100/80 dark:bg-gray-900/80';
                                $textClass = 'text-gray-400 dark:text-gray-600';
                                $isWeekend = $cell->model?->isWeekendDay($weekendDays);

                                if ($cell->is_in_term) {
                                    $bgClass = 'bg-white hover:bg-gray-50 dark:bg-gray-800 dark:hover:bg-gray-700';
                                    $textClass = 'text-gray-700 dark:text-gray-300';

                                    if ($cell->model != null) {
                                        if ($isWeekend) {
                                            // Weekend Styling if needed
                                        } elseif ($cell->model->isSchoolDay) {
                                            $bgClass =
                                                'bg-white hover:bg-emerald-50/50 dark:bg-gray-800 dark:hover:bg-emerald-900/10';
                                        } elseif ($cell->model->isHoliday) {
                                            $bgClass =
                                                'bg-rose-50/50 hover:bg-rose-50 dark:bg-rose-900/10 dark:hover:bg-rose-900/20';
                                        } elseif ($cell->model->isPartialHoliday) {
                                            $bgClass =
                                                'bg-amber-50/50 hover:bg-amber-50 dark:bg-amber-900/10 dark:hover:bg-amber-900/20';
                                        }
                                    }
                                }
                                if ($cell->is_today) {
                                    $bgClass .= ' ring-1 ring-inset ring-primary-500';
                                }
                            @endphp

                            <div class="{{ $cellClasses }} {{ $bgClass }}">
                                <div class="flex items-start justify-between">
                                    {{-- Date Number --}}
                                    <span
                                        class="flex h-7 w-7 items-center justify-center rounded-full text-sm font-medium {{ $cell->is_today ? 'bg-primary-600 text-white' : $textClass }}"
                                    >
                                        {{ $cell->date->day }}
                                    </span>
                                    {{-- Day Name --}}
                                    <span class="text-[10px] {{ $textClass }}">
                                        {{ $cell->date->locale('ar')->dayName }}
                                    </span>

                                    {{-- POPUP MENU ACTION --}}
                                    {{-- يظهر فقط داخل الترم وإذا لم يكن عطلة نهاية أسبوع --}}
                                    @if ($cell->is_in_term && $cell->model && !$isWeekend)
                                        <div>
                                            <x-ui.popup width="w-48">
                                                <x-slot:trigger>
                                                    <button
                                                        class="invisible rounded p-1 text-gray-400 hover:bg-gray-100 hover:text-gray-600 group-hover:visible dark:hover:bg-gray-700 dark:hover:text-gray-300"
                                                    >
                                                        <i class="fas fa-ellipsis-v text-xs"></i>
                                                    </button>
                                                </x-slot:trigger>

                                                {{-- Popup Content --}}
                                                <div class="flex flex-col text-right">
                                                    {{-- خيار التحضير --}}
                                                    @if (!$cell->model->isHoliDay && !$cell->model->isPartialHoliDay)
                                                        @if (
                                                            $attendanceMode->value === \App\Enums\AttendanceModeEnum::Daily->value ||
                                                                $attendanceMode->value === \App\Enums\AttendanceModeEnum::SplitDaily->value)
                                                            <a
                                                                href="{{ route('dashboard.attendances.select-section', $cell->date->format('Y-m-d')) }}"
                                                                class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 dark:text-gray-200 dark:hover:bg-gray-700/50"
                                                            >
                                                                <i
                                                                    class="fas fa-clipboard-check w-4 text-center text-emerald-500"></i>
                                                                <span>تحضير اليوم</span>
                                                            </a>
                                                        @endif
                                                    @endif

                                                    <button
                                                        type="button"
                                                        wire:click="editDay('{{ $cell->date->format('Y-m-d') }}')"
                                                        class="flex w-full items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 dark:text-gray-200 dark:hover:bg-gray-700/50"
                                                    >
                                                        <i class="fas fa-edit w-4 text-center text-primary-500"></i>
                                                        <span>تعديل اليوم الدراسي</span>
                                                    </button>
                                                </div>
                                            </x-ui.popup>
                                        </div>
                                    @endif
                                </div>

                                {{-- Cell Content --}}
                                <div class="mt-2 space-y-1.5">
                                    @if (!$cell->is_in_term)
                                        <div class="flex items-center justify-center h-full pt-4">
                                            <span
                                                class="text-[10px] text-gray-400 border border-gray-300 rounded px-1 dark:border-gray-600"
                                            >خارج
                                                الترم</span>
                                        </div>
                                    @elseif ($cell->model)
                                        @if ($isWeekend)
                                            <div class="flex items-center justify-center pt-2 opacity-50">
                                                <span class="text-[10px] text-gray-400">عطلة نهاية أسبوع</span>
                                            </div>
                                        @elseif ($cell->model->isHoliday)
                                            <div
                                                class="flex items-center gap-1.5 rounded px-1.5 py-1 text-xs font-medium text-rose-700 bg-rose-100/50 dark:text-rose-400 dark:bg-rose-900/30 border border-rose-100 dark:border-rose-800">
                                                <div class="h-1.5 w-1.5 rounded-full bg-rose-500"></div>
                                                عطلة
                                            </div>
                                        @elseif($cell->model->isPartialHoliday)
                                            <div
                                                class="flex flex-col gap-1 rounded px-1.5 py-1 bg-amber-50 dark:bg-amber-900/20 border border-amber-100 dark:border-amber-800">
                                                <span class="text-[10px] text-amber-600">عطلة جزئية</span>
                                                <span
                                                    class="text-[9px] text-gray-500">{{ $cell->model->day_part->label() }}</span>
                                            </div>
                                        @elseif($cell->model->isSchoolDay)
                                            <div class="flex items-center gap-1.5">
                                                <div class="h-1.5 w-1.5 rounded-full bg-emerald-500"></div>
                                                <span class="text-xs text-gray-500 dark:text-gray-400">يوم دراسي</span>
                                            </div>
                                        @endif

                                        @if ($cell->model->notes)
                                            <div
                                                class="flex items-center gap-1 text-[10px] text-gray-500 dark:text-gray-400"
                                                title="{{ $cell->model->notes }}"
                                            >
                                                <i class="fas fa-sticky-note text-gray-400"></i>
                                                <span class="truncate">{{ $cell->model->notes }}</span>
                                            </div>
                                        @endif
                                    @else
                                        <div class="flex items-center justify-center pt-2 opacity-50">
                                            <span class="text-[10px] text-gray-400">-</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            <!-- Legend-->
            <div class="border-t border-gray-200 bg-gray-50/50 px-6 py-4 dark:border-gray-700 dark:bg-gray-800/50">
                <div class="flex flex-wrap items-center justify-center gap-6">
                    <div class="flex items-center gap-2">
                        <div class="h-3 w-3 rounded-full bg-emerald-500"></div>
                        <span class="text-sm text-gray-600 dark:text-gray-400">يوم دراسي</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="h-3 w-3 rounded-full bg-rose-500"></div>
                        <span class="text-sm text-gray-600 dark:text-gray-400">عطلة</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="h-3 w-3 bg-gray-100 border border-gray-300 dark:bg-gray-900 dark:border-gray-600">
                        </div>
                        <span class="text-sm text-gray-600 dark:text-gray-400">خارج الترم</span>
                    </div>
                </div>
            </div>
        </div>
    @endif
    @include('livewire.dashboard.academics.calendar.update-school-day-status')
</div>
