<div>
    <!-- Filters & Search -->
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
                {{-- السياق الأكاديمي --}}
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

                {{-- البحث وإعادة التعيين --}}
                <div class="lg:col-span-3">
                    <x-form.input
                        type="text"
                        name="search"
                        label="بحث"
                        wire:model.live.debounce.400ms="search"
                        placeholder="اسم المادة أو الشعبة..."
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
    <x-ui.card class="overflow-hidden">
        @if ($gradingRules->count() > 0)
            <div class="overflow-x-auto">
                <x-table :headers="[
                    ['label' => 'المقرر الدراسي'],
                    ['label' => 'الصف / الشعبة'],
                    ['label' => 'المجموع', 'class' => 'text-center'],
                    ['label' => 'أعمال الفصل', 'class' => 'text-center'],
                    ['label' => 'النهائي', 'class' => 'text-center'],
                    ['label' => 'النجاح', 'class' => 'text-center'],
                    ['label' => 'الحالة', 'class' => 'text-center'],
                    ['label' => 'الإجراءات'],
                ]">
                    @foreach ($gradingRules as $rule)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                            <x-table.td nowrap>
                                <div class="flex items-center gap-3">
                                    <div
                                        class="p-2 bg-primary-50 dark:bg-primary-900/20 rounded-lg text-primary-600 dark:text-primary-400">
                                        <i class="fas fa-book"></i>
                                    </div>
                                    <div class="text-sm font-semibold text-gray-900 dark:text-white">
                                        {{ $rule->curriculumSubject->subject->name ?? 'غير محدد' }}
                                    </div>
                                </div>
                            </x-table.td>

                            <x-table.td nowrap>
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $rule->section->grade->name }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $rule->section->name }}
                                </div>
                            </x-table.td>

                            <x-table.td
                                nowrap
                                align="center"
                            >
                                <span
                                    class="px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded text-sm font-bold text-gray-700 dark:text-gray-300"
                                >
                                    {{ $rule->total_marks }}
                                </span>
                            </x-table.td>

                            <x-table.td
                                nowrap
                                align="center"
                                class="text-sm text-gray-600 dark:text-gray-400"
                            >
                                {{ $rule->coursework_max_marks }}
                            </x-table.td>

                            <x-table.td
                                nowrap
                                align="center"
                                class="text-sm text-gray-600 dark:text-gray-400"
                            >
                                {{ $rule->final_exam_max_marks }}
                            </x-table.td>

                            <x-table.td
                                nowrap
                                align="center"
                            >
                                <span class="text-sm text-danger-600 dark:text-danger-400 font-medium">
                                    {{ $rule->passed_mark }}
                                </span>
                            </x-table.td>

                            <x-table.td
                                nowrap
                                align="center"
                            >
                                @if ($rule->is_published)
                                    <x-ui.badge variant="success">
                                        <i class="fas fa-check-circle text-[10px] mr-1"></i>
                                        منشور
                                    </x-ui.badge>
                                @else
                                    <x-ui.badge variant="warning">
                                        <i class="fas fa-pen text-[10px] mr-1"></i>
                                        مسودة
                                    </x-ui.badge>
                                @endif
                            </x-table.td>

                            <x-table.td nowrap>
                                <div class="flex items-center gap-2">
                                    <x-table.action-view
                                        href="{{ route('dashboard.grading-rules.show', $rule) }}"
                                        :permissions="\Perm::GradingRulesView"
                                    />
                                    <x-table.action-edit
                                        href="{{ route('dashboard.grading-rules.edit', $rule) }}"
                                        :permissions="\Perm::GradingRulesUpdate"
                                    />
                                    <x-table.action-delete
                                        :permissions="\Perm::GradingRulesDelete"
                                        @click="$dispatch('open-modal', {
                                            name: 'delete-grading-rule',
                                            rule: {
                                                id: {{ $rule->id }},
                                                name: '{{ $rule->curriculumSubject->subject->name ?? '' }} - {{ $rule->section->name }}',
                                                route: '{{ route('dashboard.grading-rules.destroy', $rule) }}'
                                            }
                                        })"
                                    />
                                </div>
                            </x-table.td>
                        </tr>
                    @endforeach
                </x-table>
            </div>

            <div class="mt-4 px-4 pb-4">
                {{ $gradingRules->links() }}
            </div>
        @else
            <x-ui.empty-state
                icon="fas fa-calculator"
                title="لا توجد قواعد احتساب"
                description="لم يتم العثور على نتائج تطابق بحثك."
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
        name="delete-grading-rule"
        title="تأكيد الحذف"
        dataKey="rule"
        spoofMethod="DELETE"
        confirmButtonText="حذف نهائي"
        confirmButtonVariant="danger"
        :permissions="\Perm::GradingRulesDelete"
    >
        <div class="text-center sm:text-right">
            <p class="mb-2">هل أنت متأكد من حذف قاعدة الاحتساب للمادة:</p>
            <p
                class="font-bold text-lg text-gray-900 dark:text-white mb-2"
                x-text="rule?.name"
            ></p>
            <x-ui.warning-box>
                سوف تحتاج الى إنشاء قاعدة إحتساب جديدة حتى يتمكن المستخدم من مشاهدة الدرجة النهائية بعد الإحتساب
            </x-ui.warning-box>
        </div>
    </x-ui.confirm-action>
</div>
