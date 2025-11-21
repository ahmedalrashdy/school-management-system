<div>
    <!-- Filters -->
    <x-ui.card class="mb-6">
        <form class="flex flex-wrap gap-4">
            <div x-data="academicController({
                yearsTree: {{ lookup()->activeAndUpcomingYearsTree()->toJson() }},
                defaultYear: @entangle('academic_year_id').live,
                defaultTerm: @entangle('academic_term_id').live,
            })" class="flex flex-wrap gap-4 w-full">
                <div class="flex-1 min-w-[200px]">
                    <x-form.select name="academic_year_id" label="السنة الدراسية" :options="['' => 'اختر السنة الدراسية'] + $this->academicYears->pluck('name', 'id')->toArray()"
                        wire:model.live="academic_year_id" x-bind="yearInput" />
                </div>

                <div class="flex-1 min-w-[200px]">
                    <x-form.select name="grade_id" label="الصف الدراسي" :options="['' => 'اختر الصف الدراسي'] + $this->grades->pluck('name', 'id')->toArray()" wire:model.live="grade_id"
                        :disabled="!$academic_year_id" />
                    @if (!$academic_year_id)
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            <i class="fas fa-info-circle mr-1"></i>
                            يرجى اختيار السنة الدراسية أولاً
                        </p>
                    @endif
                </div>

                <div class="flex-1 min-w-[200px]">
                    <x-form.select name="academic_term_id" label="الفصل الدراسي" :options="[]"
                        wire:model.live="academic_term_id" x-bind="termInput" />
                </div>
            </div>
        </form>
    </x-ui.card>

    @if ($academic_year_id && $grade_id && $academic_term_id)
        <div x-data="{
            baseCounts: @js($sections->mapWithKeys(fn($s) => [$s->id => $s->students_count])->toArray()),
            changes: {},
            getCount(sectionId) {
                return (this.baseCounts[sectionId] || 0) + (this.changes[sectionId] || 0);
            },
            updateCount(sectionId, change) {
                if (!this.changes[sectionId]) this.changes[sectionId] = 0;
                this.changes[sectionId] += change;
            }
        }">
            <!-- عدادات الشعب -->
            @if ($sections->count() > 0)
                <x-ui.card class="mb-6">
                    <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">عدادات الشعب</h3>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                        @foreach ($sections as $section)
                            <div
                                class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300">شعبة
                                            {{ $section->name }}</p>
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                            السعة: {{ $section->capacity ?? 'غير محددة' }}
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <p x-text="getCount({{ $section->id }})"
                                            :class="{
                                                'text-danger-600 dark:text-danger-400': getCount({{ $section->id }}) >=
                                                    ({{ $section->capacity ?? 999999 }}),
                                                'text-gray-900 dark:text-white': getCount({{ $section->id }}) < (
                                                    {{ $section->capacity ?? 999999 }})
                                            }"
                                            class="text-lg font-bold">
                                            {{ $section->students_count }}
                                        </p>
                                        @if ($section->capacity)
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                / {{ $section->capacity }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </x-ui.card>
            @endif

            <!-- جدول التوزيع -->
            @if ($students->count() > 0)
                <x-ui.card>
                    <form wire:submit.prevent="save" x-data="{
                        assignments: @js($assignments),
                        init() {
                            // تحديث العدادات عند تغيير الاختيارات
                            this.$watch('assignments', (newVal, oldVal) => {
                                const parentData = Alpine.$data(this.$el.closest('[x-data]'));
                                Object.keys(newVal).forEach(studentId => {
                                    const oldSectionId = oldVal[studentId];
                                    const newSectionId = newVal[studentId];
                                    if (oldSectionId !== newSectionId) {
                                        // تحديث العداد للشعبة القديمة
                                        if (oldSectionId && parentData) {
                                            parentData.updateCount(oldSectionId, -1);
                                        }
                                        // تحديث العداد للشعبة الجديدة
                                        if (newSectionId && parentData) {
                                            parentData.updateCount(newSectionId, 1);
                                        }
                                    }
                                });
                            }, { deep: true });
                        }
                    }">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-800">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                            #
                                        </th>
                                        <th
                                            class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                            اسم الطالب
                                        </th>
                                        <th
                                            class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                            رقم القيد
                                        </th>
                                        <th
                                            class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                            الشعبة الحالية
                                        </th>
                                        <th
                                            class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                            الشعبة الجديدة
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-900">
                                    @foreach ($students as $index => $student)
                                        @php
                                            $studentIndex =
                                                ($students->currentPage() - 1) * $students->perPage() + $index + 1;
                                            $currentSection = $student->sections
                                                ->where('academic_year_id', $academic_year_id)
                                                ->where('grade_id', $grade_id)
                                                ->where('academic_term_id', $academic_term_id)
                                                ->first();
                                            $hasMarks = $student->hasMarksInTerm($academic_year_id, $academic_term_id);
                                        @endphp
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                            <td
                                                class="whitespace-nowrap px-6 py-4 text-sm text-gray-900 dark:text-white">
                                                {{ $studentIndex }}
                                            </td>
                                            <td
                                                class="whitespace-nowrap px-6 py-4 text-sm text-gray-900 dark:text-white">
                                                {{ $student->user->full_name }}
                                            </td>
                                            <td
                                                class="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                                {{ $student->admission_number }}
                                            </td>
                                            <td
                                                class="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                                @if ($currentSection)
                                                    <span
                                                        class="inline-flex items-center rounded-full bg-primary-100 px-2.5 py-0.5 text-xs font-medium text-primary-800 dark:bg-primary-900 dark:text-primary-200">
                                                        {{ $currentSection->name }}
                                                    </span>
                                                @else
                                                    <span class="text-gray-400 dark:text-gray-600">غير مسكن</span>
                                                @endif
                                            </td>
                                            <td class="whitespace-nowrap px-6 py-4">
                                                @if ($hasMarks)
                                                    <div
                                                        class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                                                        <i class="fas fa-lock"></i>
                                                        <span>يوجد درجات مسجلة</span>
                                                    </div>
                                                @else
                                                    <select wire:model.live="assignments.{{ $student->id }}"
                                                        x-model="assignments[{{ $student->id }}]"
                                                        class="rounded-lg border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                                        <option value="">-- اختر الشعبة --</option>
                                                        @foreach ($sections as $section)
                                                            <option value="{{ $section->id }}">شعبة
                                                                {{ $section->name }}</option>
                                                        @endforeach
                                                    </select>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if ($students->hasPages())
                            <div class="mt-4">
                                {{ $students->links() }}
                            </div>
                        @endif

                        <!-- أزرار الحفظ -->
                        <div
                            class="mt-6 flex items-center justify-end gap-3 border-t border-gray-200 pt-4 dark:border-gray-700">
                            <button type="button" wire:click="save"
                                class="inline-flex items-center gap-2 rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-primary-700">
                                <i class="fas fa-save"></i>
                                حفظ
                            </button>

                            @if ($students->hasMorePages())
                                <button type="button" wire:click="saveAndGoToNextPage"
                                    class="inline-flex items-center gap-2 rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-primary-700">
                                    <i class="fas fa-save"></i>
                                    حفظ والانتقال للتالي
                                </button>
                            @endif
                        </div>
                    </form>
                </x-ui.card>
            @else
                <x-ui.card>
                    <div class="py-12 text-center">
                        <i class="fas fa-users text-4xl text-gray-400 dark:text-gray-600 mb-4"></i>
                        <p class="text-gray-500 dark:text-gray-400">لا يوجد طلاب يحتاجون إلى توزيع</p>
                    </div>
                </x-ui.card>
            @endif
        </div>
    @else
        <x-ui.card>
            <div class="text-center py-12">
                <i class="fas fa-filter text-4xl text-gray-400 dark:text-gray-600 mb-4"></i>
                <p class="text-gray-500 dark:text-gray-400">يرجى تحديد السنة الدراسية والصف والفصل الدراسي لعرض الطلاب
                </p>
            </div>
        </x-ui.card>
    @endif
</div>
