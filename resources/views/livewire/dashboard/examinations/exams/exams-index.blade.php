<div>
    <x-ui.main-content-header
        title="الامتحانات"
        description="عرض وإدارة جدول الامتحانات"
    >
        <x-slot name="actions">
            <x-ui.button
                as="a"
                href="{{ route('dashboard.exams.create') }}"
                variant="primary"
                :permissions="\Perm::ExamsCreate"
            >
                <i class="fas fa-plus mr-2"></i>
                إضافة امتحان
            </x-ui.button>
        </x-slot>
    </x-ui.main-content-header>

    <x-ui.card class="mb-6">
        <form wire:submit.prevent>
            <div
                x-data="academicController({
                    yearsTree: {{ $this->yearsTree->toJson() }},
                    defaultYear: @entangle('academic_year_id').live,
                    defaultTerm: @entangle('academic_term_id').live
                })"
                class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 items-end"
            >
                <x-form.select
                    name="academic_year_id"
                    label="السنة الدراسية"
                    :options="$this->yearsTree->pluck('name', 'id')"
                    x-bind="yearInput"
                    wire:model.live="academic_year_id"
                />

                <x-form.select
                    name="academic_term_id"
                    label="الفصل الدراسي"
                    :options="[]"
                    x-bind="termInput"
                    wire:model.live="academic_term_id"
                />

                <x-form.select
                    name="grade_id"
                    label="الصف الدراسي"
                    :options="['' => 'جميع الصفوف'] + $this->grades"
                    wire:model.live="grade_id"
                />

                <x-form.select
                    name="section_id"
                    label="الشعبة"
                    :options="['' => 'جميع الشعب'] + $this->sections"
                    wire:model.live="section_id"
                    :disabled="!$academic_year_id || !$grade_id || !$academic_term_id"
                />

                <x-form.select
                    name="exam_type_id"
                    label="نوع الامتحان"
                    :options="['' => 'جميع الأنواع'] + $this->examTypes"
                    wire:model.live="exam_type_id"
                />

                <div class="lg:col-span-2">
                    <x-form.input
                        type="text"
                        name="search"
                        label="بحث باسم المادة"
                        wire:model.live.debounce.400ms="search"
                        placeholder="ابحث..."
                        icon="fas fa-search"
                    />
                </div>

                @if ($this->hasActiveFilters)
                    <div class="mb-2">
                        <x-ui.button
                            type="button"
                            wire:click="resetFilters"
                            variant="outline"
                            class="w-full"
                        >
                            <i class="fas fa-redo mr-2"></i>
                            إعادة تعيين
                        </x-ui.button>
                    </div>
                @endif
            </div>
        </form>
    </x-ui.card>

    <!-- Table -->
    <x-ui.card>
        @if ($exams->count() > 0)
            <x-table :headers="[
                ['label' => 'تاريخ الامتحان'],
                ['label' => 'المادة'],
                ['label' => 'النوع'],
                ['label' => 'السياق'],
                ['label' => 'الشعبة'],
                ['label' => 'التصنيف'],
                ['label' => 'الدرجة'],
                ['label' => 'الإجراءات'],
            ]">
                @foreach ($exams as $exam)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                        <x-table.td nowrap>
                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $exam->exam_date->format('Y-m-d') }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ $exam->academicYear->name }}
                            </div>
                        </x-table.td>

                        <x-table.td nowrap>
                            <div class="text-sm font-bold text-gray-900 dark:text-white">
                                {{ $exam->curriculumSubject->subject->name }}
                            </div>
                        </x-table.td>

                        <x-table.td nowrap>
                            <x-ui.badge variant="light">
                                {{ $exam->examType->name }}
                            </x-ui.badge>
                        </x-table.td>

                        <x-table.td nowrap>
                            <div class="text-sm text-gray-900 dark:text-white">
                                {{ $exam->curriculumSubject->curriculum->grade->name }}
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $exam->academicTerm->name }}
                            </div>
                        </x-table.td>

                        <x-table.td nowrap>
                            @if ($exam->section)
                                <span class="text-sm text-gray-900 dark:text-white">{{ $exam->section->name }}</span>
                            @else
                                <span class="text-xs text-gray-400 italic">عام لكل الشعب</span>
                            @endif
                        </x-table.td>

                        <x-table.td nowrap>
                            @if ($exam->is_final)
                                <x-ui.badge variant="info">
                                    <i class="fas fa-flag-checkered mr-1"></i> نهائي
                                </x-ui.badge>
                            @else
                                <x-ui.badge variant="default">أعمال فصل</x-ui.badge>
                            @endif
                        </x-table.td>

                        <x-table.td nowrap>
                            <span class="font-bold text-gray-700 dark:text-gray-300">
                                {{ $exam->max_marks }}
                            </span>
                        </x-table.td>

                        <x-table.td nowrap>
                            <div class="flex items-center gap-2">
                                @if ($exam->max_marks > 0)
                                    <x-table.action-view
                                        href="{{ route('dashboard.exams.marks.show', $exam) }}"
                                        title="عرض الدرجات"
                                    />
                                @endif

                                @if ($exam->canEnterMarks())
                                    <x-table.action
                                        href="{{ route('dashboard.exams.enter-marks', $exam) }}"
                                        icon="fas fa-clipboard-check"
                                        variant="success"
                                        title="رصد الدرجات"
                                    />
                                @endif

                                <x-table.action-edit
                                    href="{{ route('dashboard.exams.edit', $exam) }}"
                                    :permissions="\Perm::ExamsUpdate"
                                />

                                @if ($exam->marks_count == 0)
                                    <x-table.action-delete
                                        :permissions="\Perm::ExamsDelete"
                                        @click="$dispatch('open-modal', {
                                           name: 'delete-exam',
                                           exam: {
                                               id: {{ $exam->id }},
                                               name: '{{ $exam->curriculumSubject->subject->name }} - {{ $exam->exam_date->format('Y-m-d') }}',
                                               route: '{{ route('dashboard.exams.destroy', $exam) }}'
                                           }
                                       })"
                                    />
                                @endif
                            </div>
                        </x-table.td>
                    </tr>
                @endforeach
            </x-table>

            <div class="mt-4">
                {{ $exams->links() }}
            </div>
        @else
            <x-ui.empty-state
                icon="fas fa-file-alt"
                title="لا توجد امتحانات"
                description="لم يتم العثور على امتحانات مطابقة لمعايير البحث الحالية."
            >
                @if ($this->hasActiveFilters)
                    <x-ui.button
                        wire:click="resetFilters"
                        variant="outline"
                        class="mt-4"
                    >
                        إلغاء الفلاتر
                    </x-ui.button>
                @endif
            </x-ui.empty-state>
        @endif
    </x-ui.card>

    {{-- Delete Modal --}}
    <x-ui.confirm-action
        name="delete-exam"
        title="تأكيد حذف الامتحان"
        dataKey="exam"
        spoofMethod="DELETE"
        confirmButtonText="حذف"
        confirmButtonVariant="danger"
        :permissions="\Perm::ExamsDelete"
    >
        هل أنت متأكد من حذف امتحان <strong x-text="exam?.name"></strong>؟
        <p class="text-sm text-gray-500 mt-2">لا يمكن التراجع عن هذا الإجراء.</p>
    </x-ui.confirm-action>
</div>
