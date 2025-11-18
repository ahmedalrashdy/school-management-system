<div>
    <x-slot name="breadcrumbs">
        <x-ui.breadcrumbs :items="[
        ['label' => 'لوحة التحكم', 'url' => route('dashboard.index'), 'icon' => 'fas fa-home'],
        ['label' => 'إدارة الامتحانات', 'url' => route('dashboard.exams.index'), 'icon' => 'fas fa-clipboard-list'],
        ['label' => 'الامتحانات', 'url' => route('dashboard.exams.list'), 'icon' => 'fas fa-list'],
        ['label' => 'رصد الدرجات', 'icon' => 'fas fa-pen'],
    ]" />
    </x-slot>

    <x-ui.main-content-header title="رصد الدرجات" description="رصد الدرجات للامتحان" button-text="رجوع"
        button-link="{{ route('dashboard.exams.index') }}" />
    @if(session('message'))
        <div class="mb-4 rounded-lg bg-green-50 dark:bg-green-900/20 p-4 text-green-800 dark:text-green-200">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                <span>{{ session('message') }}</span>
            </div>
        </div>
    @endif

    <!-- رأس الصفحة: معلومات الامتحان -->
    <x-ui.card class="mb-6">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">المادة</p>
                <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">
                    {{ $exam->curriculumSubject->subject->name }}
                </p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">نوع الامتحان</p>
                <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">
                    {{ $exam->examType->name }}
                </p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">الصف الدراسي</p>
                <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">
                    {{ $exam->curriculumSubject->curriculum->grade->name }}
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    {{ $exam->curriculumSubject->curriculum->grade->stage->name }}
                </p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">تاريخ الامتحان</p>
                <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">
                    {{ $exam->exam_date->format('Y-m-d') }}
                </p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">الدرجة القصوى</p>
                <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">
                    {{ $exam->max_marks }}
                </p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">الفصل الدراسي</p>
                <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">
                    {{ $exam->academicTerm->name }}
                </p>
            </div>
        </div>
    </x-ui.card>
    <!-- جدول إدخال الدرجات -->
    @if($students->count() > 0)
    <x-ui.card>
        <form wire:submit.prevent="save">
            <x-table :headers="[
                ['label' => '#'],
                ['label' => 'اسم الطالب'],
                ['label' => 'الدرجة'],
                ['label' => 'ملاحظات'],
            ]">
                @foreach($students as $index => $student)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800" wire:key="student-{{ $student->id }}">
                        <x-table.td nowrap class="text-sm text-gray-900 dark:text-white">
                            {{ $students->firstItem() + $index }}
                        </x-table.td>
                        <x-table.td nowrap class="text-sm text-gray-900 dark:text-white">
                            {{ $student->first_name }} {{ $student->last_name }}
                            <div class="text-xs text-gray-500">{{ $student->admission_number }}</div>
                        </x-table.td>

                        {{-- حقل الدرجة --}}
                        <x-table.td nowrap>
                            <x-form.input label=""  name="marks[{{ $student->id }}][marks_obtained]"
                                wire:model="marks.{{ $student->id }}.marks_obtained"
                                min="0" :max="$exam->max_marks" step=".5"
                                type="number"
                                placeholder="الدرجة"
                            />
                        </x-table.td>

                        {{-- حقل الملاحظات --}}
                        <x-table.td>
                             <x-form.input label=""  name="marks[{{ $student->id }}][notes]"
                                wire:model="marks.{{ $student->id }}.notes"
                                min="0" :max="$exam->max_marks" step=".5"
                                placeholder="ملاحظه"
                            />
                        </x-table.td>
                    </tr>
                @endforeach
            </x-table>

            {{-- Pagination --}}
            <div class="mt-4 px-4">
                {{ $students->links() }}
            </div>

            {{-- Actions --}}
            <div class="mt-6 flex items-center justify-between border-t border-gray-200 pt-4 px-4 dark:border-gray-700">
                <x-ui.button
                    as="a"
                    href="{{ route('dashboard.exams.index') }}"
                    variant="outline"
                    size="sm"
                >
                    إلغاء
                </x-ui.button>

                <div class="flex gap-3">
                    <x-ui.button
                        type="button"
                        wire:click="save"
                        wire:loading.attr="disabled"
                        variant="primary"
                    >
                        <span wire:loading.remove target="save">
                            <i class="fas fa-save mr-2"></i>
                            حفظ
                        </span>
                        <span wire:loading target="save">
                            <i class="fas fa-spinner fa-spin mr-2"></i>
                            جاري الحفظ...
                        </span>
                    </x-ui.button>

                    @if($students->hasMorePages())
                        <x-ui.button
                            type="button"
                            wire:click="saveAndGoToNextPage"
                            wire:loading.attr="disabled"
                            variant="outline"
                        >
                            <span wire:loading.remove target="saveAndGoToNextPage">
                                <i class="fas fa-arrow-left mr-2"></i>
                                حفظ والتالي
                            </span>
                            <span wire:loading target="saveAndGoToNextPage">
                                <i class="fas fa-spinner fa-spin mr-2"></i>
                                جاري الحفظ...
                            </span>
                        </x-ui.button>
                    @endif
                </div>
            </div>
        </form>
    </x-ui.card>
    @elseif($students->count()==0)
        <x-ui.card>
            <x-ui.empty-state
                icon="fas fa-users"
                title="لا يوجد طلاب في هذه الشعبة"
            />
        </x-ui.card>
    @endif
</div>
