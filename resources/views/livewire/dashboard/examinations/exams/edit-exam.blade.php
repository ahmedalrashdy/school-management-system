<div>
    <x-ui.main-content-header
        title="تعديل امتحان"
        description="تعديل بيانات الامتحان"
        button-text="رجوع"
        button-link="{{ route('dashboard.exams.list') }}"
    />

    <x-ui.card>
        @if ($exam->hasMarks())
            <x-ui.alert
                variant="warning"
                class="mb-6"
            >
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <strong>تحذير:</strong> تم رصد درجات لهذا الامتحان. يمكنك تعديل تاريخ الامتحان فقط.
            </x-ui.alert>
        @endif

        <form wire:submit="save">
            {{-- حالة القفل: عرض البيانات كنص فقط --}}
            @if ($exam->hasMarks())
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">المادة
                            الدراسية</label>
                        <div class="px-4 py-2 bg-gray-100 dark:bg-gray-800 rounded-lg text-gray-700 dark:text-gray-300">
                            {{ $exam->curriculumSubject->subject->name }}
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">نوع
                            الامتحان</label>
                        <div class="px-4 py-2 bg-gray-100 dark:bg-gray-800 rounded-lg text-gray-700 dark:text-gray-300">
                            {{ $exam->examType->name }}
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">الشعبة</label>
                        <div class="px-4 py-2 bg-gray-100 dark:bg-gray-800 rounded-lg text-gray-700 dark:text-gray-300">
                            {{ $exam->section ? $exam->section->name : 'كل الشعب' }}
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">الدرجة
                            القصوى</label>
                        <div class="px-4 py-2 bg-gray-100 dark:bg-gray-800 rounded-lg text-gray-700 dark:text-gray-300">
                            {{ $exam->max_marks }}
                        </div>
                    </div>
                </div>

                <x-form.input
                    name="exam_date"
                    label="تاريخ الامتحان"
                    type="date"
                    wire:model="exam_date"
                    required
                />
            @else
                @php
                    $yearsTree = lookup()->activeAndUpcomingYearsTree();
                    $years = $yearsTree->pluck('name', 'id');
                    $grades = lookup()->getGrades();
                @endphp

                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">السياق الأكاديمي</h3>
                    <div
                        x-data="academicController({
                            yearsTree: {{ $yearsTree->toJson() }},
                            defaultYear: @entangle('academic_year_id').live,
                            defaultTerm: @entangle('academic_term_id').live
                        })"
                        class="grid grid-cols-1 md:grid-cols-3 gap-6"
                    >
                        <x-form.select
                            name="academic_year_id"
                            label="السنة الدراسية"
                            :options="$years"
                            x-bind="yearInput"
                            wire:model.live="academic_year_id"
                            required
                        />

                        <x-form.select
                            name="grade_id"
                            label="الصف الدراسي"
                            :options="['' => 'اختر الصف'] + $grades"
                            wire:model.live="grade_id"
                            required
                        />

                        <x-form.select
                            name="academic_term_id"
                            label="الفصل الدراسي"
                            :options="[]"
                            x-bind="termInput"
                            wire:model.live="academic_term_id"
                            required
                        />
                    </div>
                </div>

                <div class="mb-6">
                    <x-form.select
                        name="curriculum_subject_id"
                        label="المادة الدراسية"
                        :options="['' => 'اختر المادة الدراسية'] + $this->curriculumSubjects"
                        wire:model="curriculum_subject_id"
                        :disabled="empty($this->curriculumSubjects)"
                        required
                    />

                    @if (empty($this->curriculumSubjects) && $academic_year_id && $grade_id && $academic_term_id)
                        <p class="mt-1 text-xs text-warning-600 dark:text-warning-400">
                            <i class="fas fa-info-circle mr-1"></i>
                            لا توجد مواد مرتبطة بهذا المنهج. يرجى إعداد المنهج أولاً.
                        </p>
                    @endif
                </div>

                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">تفاصيل الامتحان</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <x-form.select
                            name="exam_type_id"
                            label="نوع الامتحان"
                            :options="['' => 'اختر نوع الامتحان'] + $this->examTypes"
                            wire:model="exam_type_id"
                            required
                        />

                        <x-form.input
                            name="exam_date"
                            label="تاريخ الامتحان"
                            type="date"
                            wire:model="exam_date"
                            required
                        />

                        <x-form.input
                            name="max_marks"
                            label="الدرجة القصوى"
                            type="number"
                            min="1"
                            wire:model="max_marks"
                            required
                        />

                        <div class="flex items-end mb-2">
                            <x-form.checkbox
                                name="is_final"
                                label="إختبار نهائي"
                                wire:model="is_final"
                            />
                        </div>
                    </div>
                </div>

                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">النطاق</h3>
                    <x-form.select
                        name="section_id"
                        label="الشعبة"
                        :options="['' => 'لجميع شعب الصف'] + $this->sections"
                        wire:model="section_id"
                    />
                </div>
            @endif

            <div class="mt-6 flex items-center gap-4">
                <x-ui.button
                    type="submit"
                    variant="primary"
                >
                    <i class="fas fa-save mr-2"></i>
                    حفظ التغييرات
                </x-ui.button>

                <x-ui.button
                    as="a"
                    href="{{ route('dashboard.exams.list') }}"
                    variant="outline"
                >
                    إلغاء
                </x-ui.button>
            </div>
        </form>
    </x-ui.card>
</div>
