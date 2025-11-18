<div x-data="editGradeRule">
    <x-ui.card>
        <form wire:submit="update">
            @php
                $yearsTree = lookup()->activeAndUpcomingYearsTree();
                $years = $yearsTree->pluck('name', 'id');
                $grades = lookup()->getGrades();
            @endphp
            <!-- Academic Context -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    <i class="fas fa-edit mr-2"></i>
                    تعديل السياق الأكاديمي
                </h3>

                <div
                    x-data="academicController({
                        yearsTree: {{ $yearsTree->toJson() }},
                        defaultYear: @entangle('academic_year_id').live,
                        defaultTerm: @entangle('academic_term_id').live
                    })"
                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4"
                >
                    {{-- ملاحظة: في التعديل غالباً لا نفضل تغيير هذه القيم لكن تركتها متاحة --}}
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
                        :options="['' => 'اختر الصف الدراسي'] + $grades"
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

                    <x-form.select
                        name="curriculum_subject_id"
                        label="المقرر الدراسي"
                        :options="['' => 'اختر المقرر الدراسي'] + $subjects"
                        wire:model.live="curriculum_subject_id"
                        :disabled="empty($subjects)"
                        required
                    />

                    <x-form.select
                        name="section_id"
                        label="الشعبة"
                        :options="['' => 'اختر الشعبة'] + $sections"
                        wire:model.live="section_id"
                        :disabled="empty($sections)"
                        required
                    />
                </div>
            </div>

            <hr class="my-6 border-gray-200 dark:border-gray-700">

            <!-- Grade Structure -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    <i class="fas fa-chart-bar mr-2"></i>
                    هيكل الدرجات
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-form.input
                        type="number"
                        name="total_marks"
                        label="الدرجة الكلية"
                        wire:model.live="total_marks"
                        step="1"
                        min="1"
                        required
                    />

                    <x-form.input
                        type="number"
                        name="passed_mark"
                        label="درجة النجاح"
                        wire:model="passed_mark"
                        step="0.5"
                        min="0"
                        x-bind:max="totalMarks"
                        required
                    />
                </div>
            </div>

            <hr class="my-6 border-gray-200 dark:border-gray-700">

            <!-- Distribution -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    <i class="fas fa-balance-scale mr-2"></i>
                    توزيع الدرجات
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-form.input
                        type="number"
                        name="coursework_max_marks"
                        label="أعمال الفصل"
                        wire:model.live="coursework_max_marks"
                        step="1"
                        min="0"
                        x-bind:max="totalMarks"
                        required
                    />

                    <x-form.input
                        type="number"
                        name="final_exam_max_marks"
                        label="الاختبار النهائي"
                        wire:model.live="final_exam_max_marks"
                        step="1"
                        min="0"
                        x-bind:max="totalMarks"
                        required
                    />
                </div>
            </div>

            <hr class="my-6 border-gray-200 dark:border-gray-700">

            <!-- Coursework Items -->
            <div class="mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        <i class="fas fa-tasks mr-2"></i>
                        بنود أعمال الفصل
                    </h3>
                    <x-ui.button
                        type="button"
                        wire:click="addCourseworkItem"
                        x-bind:disabled="!section_id || availableExams.length === 0"
                        variant="primary"
                        size="sm"
                    >
                        <i class="fas fa-plus"></i>
                        إضافة بند
                    </x-ui.button>
                </div>

                @if (empty($courseworkItems))
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-6 text-center">
                        <i class="fas fa-info-circle text-3xl text-gray-400 dark:text-gray-600 mb-2"></i>
                        <p class="text-gray-500 dark:text-gray-400">لم يتم إضافة أي بنود بعد. اضغط "إضافة بند" للبدء.
                        </p>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach ($courseworkItems as $index => $item)
                            <div
                                class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4"
                                wire:key="coursework-item-{{ $index }}"
                            >
                                <div class="flex items-start gap-4">
                                    <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                                            >
                                                الاختبار
                                                <span class="text-danger-600">*</span>
                                            </label>
                                            <select
                                                wire:model="courseworkItems.{{ $index }}.exam_id"
                                                class="w-full rounded-lg p-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-primary-500 focus:ring-primary-500 shadow-sm"
                                                required
                                            >
                                                <option value="">اختر الاختبار</option>
                                                <template
                                                    x-for="exam in getFilteredExams({{ $index }})"
                                                    x-bind:key="exam.id"
                                                >
                                                    <option
                                                        x-bind:value="exam.id"
                                                        x-text="exam.text"
                                                        x-bind:selected="exam.id == courseworkItems[{{ $index }}].exam_id"
                                                    ></option>
                                                </template>
                                            </select>
                                        </div>

                                        <x-form.input
                                            type="number"
                                            name="courseworkItems.{{ $index }}.weight"
                                            label="الوزن (%)"
                                            wire:model="courseworkItems.{{ $index }}.weight"
                                            step="0.5"
                                            min="0"
                                            max="100"
                                            required
                                        />
                                    </div>
                                    <x-table.action-delete
                                        type="button"
                                        wire:click="removeCourseworkItem({{ $index }})"
                                        title="حذف البند"
                                        class="mt-8"
                                    />
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Weights Summary -->
                    <div class="mt-4 flex items-center justify-between text-sm px-4">
                        <div class="flex items-center gap-2">
                            <span class="font-medium text-gray-700 dark:text-gray-300">مجموع الأوزان:</span>
                            <span
                                x-bind:class="isCourseworkWeightsValid ? 'text-success-600 font-bold' : 'text-danger-600 font-bold'"
                                x-text="courseworkWeightsSum.toFixed(1) + '%'"
                            ></span>
                        </div>
                        <div
                            x-show="!isCourseworkWeightsValid"
                            class="text-danger-600 text-xs"
                        >
                            يجب أن يكون مجموع الأوزان 100%
                        </div>
                    </div>
                @endif
            </div>

            <hr class="my-6 border-gray-200 dark:border-gray-700">

            <!-- Final Exam -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    <i class="fas fa-file-alt mr-2"></i>
                    الاختبار النهائي (اختياري)
                </h3>

                <x-form.select
                    name="final_exam_id"
                    label="الاختبار النهائي"
                    :options="['' => 'اختر الاختبار النهائي (اختياري)'] + $availableFinalExams"
                    wire:model="final_exam_id"
                />
            </div>

            <!-- Errors -->
            @if ($errors->any())
                <div
                    class="mb-6 bg-danger-50 dark:bg-danger-900/20 border border-danger-200 dark:border-danger-800 rounded-lg p-4">
                    <h4 class="text-sm font-semibold text-danger-800 dark:text-danger-200 mb-2">
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        يرجى تصحيح الأخطاء التالية:
                    </h4>
                    <ul class="list-disc list-inside text-sm text-danger-700 dark:text-danger-300 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Actions -->
            <div class="flex items-center justify-end gap-4">
                <x-ui.button
                    as="a"
                    href="{{ route('dashboard.grading-rules.index') }}"
                    variant="outline"
                >
                    <i class="fas fa-times"></i>
                    إلغاء
                </x-ui.button>
                <x-ui.button
                    type="submit"
                    x-bind:disabled="!canSubmit"
                    variant="primary"
                >
                    <i class="fas fa-save"></i>
                    حفظ التعديلات
                </x-ui.button>
            </div>
        </form>
    </x-ui.card>

    @push('scripts')
        <script>
            document.addEventListener('alpine:init', function() {
                Alpine.data('editGradeRule', () => ({
                    totalMarks: @entangle('total_marks'),
                    courseworkMax: @entangle('coursework_max_marks'),
                    finalExamMax: @entangle('final_exam_max_marks'),
                    passedMark: @entangle('passed_mark'),
                    courseworkItems: @entangle('courseworkItems'),
                    section_id: @entangle('section_id'),
                    availableExams: @entangle('availableExams'),

                    init() {
                        // Watch courseworkMax and auto-update finalExamMax
                        this.$watch('courseworkMax', value => {
                            if (value !== null && value !== '' && this.totalMarks) {
                                this.finalExamMax = parseInt(this.totalMarks) - parseInt(value);
                            }
                        });

                        // Watch finalExamMax and auto-update courseworkMax
                        this.$watch('finalExamMax', value => {
                            if (value !== null && value !== '' && this.totalMarks) {
                                this.courseworkMax = parseInt(this.totalMarks) - parseInt(value);
                            }
                        });

                        // Watch totalMarks and adjust if coursework + final != total
                        this.$watch('totalMarks', value => {
                            // Optional: Logic to reset or warn, currently manual adjustment expected
                        });
                    },

                    getFilteredExams(currentIndex) {
                        // Get IDs selected in other items
                        const selectedIds = this.courseworkItems
                            .map((item, idx) => idx !== currentIndex ? item.exam_id : null)
                            .filter(id => id);
                        // Return exams that are NOT in the selected list, OR the exam currently selected for this item
                        return this.availableExams.filter(exam => {
                            return !selectedIds.includes(exam.id) && !selectedIds.includes(String(
                                exam.id));
                        });
                    },

                    get isDistributionValid() {
                        return ((parseInt(this.courseworkMax) + parseInt(this.finalExamMax)) ==
                            parseInt(this.totalMarks));
                    },

                    get courseworkWeightsSum() {
                        return this.courseworkItems.reduce((sum, item) => sum + (parseFloat(item
                            .weight) || 0), 0);
                    },

                    get isCourseworkWeightsValid() {
                        // Validation: Sum must be 100 (percentage)
                        // Using small epsilon for float comparison safety
                        return Math.abs(this.courseworkWeightsSum - 100) < 0.1;
                    },

                    get isPassedMarkValid() {
                        return parseFloat(this.passedMark) <= parseInt(this.totalMarks);
                    },

                    get canSubmit() {
                        return this.isDistributionValid &&
                            this.isCourseworkWeightsValid &&
                            this.isPassedMarkValid &&
                            this.courseworkItems.length > 0 &&
                            this.courseworkItems.every(item => item.exam_id && item.weight);
                    }
                }))
            });
        </script>
    @endpush
</div>
