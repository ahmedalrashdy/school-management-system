<div class="space-y-6">
    <x-ui.card>
        {{-- Header & Filters --}}
        <div class="border-b border-gray-200 dark:border-gray-700 pb-4 mb-4">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <i class="fas fa-chalkboard-teacher text-primary-600"></i>
                        جدول الحصص الخاص بي
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">
                        عرض الحصص الفعلية للشعب الدراسية المختلفة
                    </p>
                </div>

                {{-- Filters --}}
                @php
                    $yearsTree = lookup()->yearsTree();
                    $years = $yearsTree->pluck('name', 'id');
                @endphp
                <div
                    x-data="academicController({
                        yearsTree: {{ $yearsTree->toJson() }},
                        defaultYear: @entangle('academicYearId').live,
                        defaultTerm: @entangle('academicTermId').live
                    })"
                    class="flex flex-col sm:flex-row gap-2 w-full md:w-auto"
                >
                    <div class="w-full sm:w-40">
                        <x-form.select
                            name="academicYearId"
                            :options="$years"
                            x-bind="yearInput"
                            class="!mb-0"
                        />
                    </div>
                    <div class="w-full sm:w-40">
                        <x-form.select
                            name="academicTermId"
                            :options="[]"
                            x-bind="termInput"
                            placeholder="الفصل الدراسي"
                            class="!mb-0"
                        />
                    </div>
                </div>
            </div>
        </div>

        {{-- Timetable Content --}}
        @if (empty($this->calendarDays))
            <div class="text-center py-12">
                <div
                    class="mx-auto w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center mb-4">
                    <i class="fas fa-calendar-times text-2xl text-gray-400"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">لا توجد حصص</h3>
                <p class="text-gray-500">لا يوجد جدول دراسي نشط في الفترة المحددة</p>
            </div>
        @else
            <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
                @foreach ($this->calendarDays as $dayIndex => $slots)
                    @php
                        $dayName = \App\Enums\DayOfWeekEnum::from($dayIndex)->label();
                    @endphp

                    <div
                        class="bg-gray-50 dark:bg-gray-800/50 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden flex flex-col h-full">
                        {{-- Day Header --}}
                        <div
                            class="bg-white dark:bg-gray-800 px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                            <h3 class="font-bold text-gray-900 dark:text-primary-400">
                                {{ $dayName }}
                            </h3>
                            <span
                                class="text-xs font-medium px-2 py-1 rounded-full bg-primary-100 text-primary-700 dark:bg-primary-900/30 dark:text-primary-300"
                            >
                                {{ count($slots) }} حصص
                            </span>
                        </div>

                        {{-- Slots List --}}
                        <div class="p-3 space-y-3 flex-1">
                            @foreach ($slots as $slot)
                                <div
                                    class="bg-white dark:bg-gray-800 p-3 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow relative group">
                                    {{-- Time & Period Badge --}}
                                    <div class="flex justify-between items-start mb-2">
                                        <div class="flex items-center gap-1.5">
                                            <span class="flex h-2 w-2 rounded-full bg-green-500"></span>
                                            <span class="text-xs font-bold text-gray-500 dark:text-gray-400">
                                                {{ $slot->formatted_start_time }} - {{ $slot->formatted_end_time }}
                                            </span>
                                        </div>
                                        <span
                                            class="text-[10px] font-semibold bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 px-2 py-0.5 rounded"
                                        >
                                            الحصة {{ $slot->period_number }}
                                        </span>
                                    </div>

                                    {{-- Subject Name --}}
                                    <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-1">
                                        {{ $slot->teacherAssignment->curriculumSubject->subject->name }}
                                    </h4>

                                    {{-- Section Info --}}
                                    <div class="flex items-center text-xs text-gray-600 dark:text-gray-400">
                                        <i class="fas fa-users text-primary-500 ml-1.5 w-3"></i>
                                        <span>
                                            {{ $slot->teacherAssignment->section->grade->name }} -
                                            {{ $slot->teacherAssignment->section->name }}
                                        </span>
                                    </div>

                                    {{-- Duration --}}
                                    <div
                                        class="mt-2 pt-2 border-t border-gray-100 dark:border-gray-700/50 flex justify-end">
                                        <span class="text-[10px] text-gray-400">
                                            <i class="fas fa-hourglass-half ml-1"></i>
                                            {{ $slot->duration_minutes }} دقيقة
                                        </span>
                                    </div>

                                    {{-- Decorative Side Border based on Subject or Grade (Optional) --}}
                                    <div class="absolute right-0 top-0 bottom-0 w-1 bg-primary-500 rounded-r-lg"></div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </x-ui.card>
</div>
