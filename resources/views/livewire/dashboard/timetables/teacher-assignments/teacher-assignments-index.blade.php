<div>
    <x-slot name="breadcrumbs">
        <x-ui.breadcrumbs :items="[
            ['label' => 'لوحة التحكم', 'url' => route('dashboard.index'), 'icon' => 'fas fa-home'],
            [
                'label' => 'إدارة الجداول الدراسية',
                'url' => route('dashboard.timetables.index'),
                'icon' => 'fas fa-table',
            ],
            ['label' => 'تعيينات المدرسين', 'icon' => 'fas fa-chalkboard-teacher'],
        ]" />
    </x-slot>

    <x-ui.main-content-header
        title="تعيينات المدرسين"
        description="إدارة وتوزيع المهام التدريسية على الكادر التعليمي"
    />

    <!-- Filters Section -->
    <x-ui.card class="mb-6">
        <div
            x-data="academicController({
                yearsTree: {{ lookup()->yearsTree()->toJson() }},
                defaultYear: @entangle('academic_year_id').live,
                defaultTerm: @entangle('academic_term_id').live,
            })"
            class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4"
        >
            <x-form.select
                name="academic_year_id"
                label="السنة الدراسية"
                :options="lookup()->yearsTree()->pluck('name', 'id')"
                x-bind="yearInput"
            />
            <x-form.select
                name="grade_id"
                label="الصف الدراسي"
                :options="lookup()->getGrades(true)"
                wire:model.live="grade_id"
            />
            <x-form.select
                name="academic_term_id"
                label="الفصل الدراسي"
                :options="[]"
                x-bind="termInput"
            />
        </div>
    </x-ui.card>

    <!-- Grid Section -->
    @if ($this->curriculum && $this->sections->isNotEmpty())
        <x-ui.card class="overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th
                                class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider sticky right-0 bg-gray-50 dark:bg-gray-800 z-10 border-l dark:border-gray-700">
                                الشعبة \ المادة
                            </th>
                            @foreach ($this->curriculum->curriculumSubjects as $curriculumSubject)
                                <th
                                    class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider min-w-[160px]">
                                    {{ $curriculumSubject->subject->name }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($this->sections as $section)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                                <td
                                    class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white sticky right-0 bg-white dark:bg-gray-900 z-10 border-l dark:border-gray-700">
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold">{{ $section->name }}</span>
                                        <span
                                            class="text-xs text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-800 px-2 py-0.5 rounded-full"
                                        >
                                            {{ $section->students_count }} / {{ $section->capacity ?? '-' }}
                                        </span>
                                    </div>
                                </td>

                                @foreach ($this->curriculum->curriculumSubjects as $curriculumSubject)
                                    @php
                                        $key = "{$section->id}_{$curriculumSubject->id}";
                                        $assignment = $this->assignments[$key] ?? null;
                                    @endphp
                                    <td class="px-2 py-2 text-center align-middle">
                                        @if ($assignment)
                                            <button
                                                type="button"
                                                x-on:click="$dispatch('open-modal', { name: 'delete-assignment-modal' });
                                                            $dispatch('set-delete-data', {
                                                                id: {{ $assignment->id }},
                                                                teacher: '{{ $assignment->teacher->user->full_name }}',
                                                                subject: '{{ $curriculumSubject->subject->name }}',
                                                                section: '{{ $section->name }}'
                                                            })"
                                                class="group w-full flex items-center justify-between px-3 py-2 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-lg transition shadow-sm"
                                            >
                                                <span
                                                    class="truncate mx-auto">{{ $assignment->teacher->user->full_name }}</span>
                                                <i
                                                    class="fas fa-trash-alt opacity-0 group-hover:opacity-100 transition-opacity text-xs ml-1"></i>
                                            </button>
                                        @else
                                            <button
                                                type="button"
                                                x-on:click="$dispatch('open-modal', { name: 'create-assignment-modal' });
                                                            $dispatch('set-create-data', {
                                                                sectionId: {{ $section->id }},
                                                                curriculumSubjectId: {{ $curriculumSubject->id }},
                                                                subjectName: '{{ $curriculumSubject->subject->name }}',
                                                                sectionName: '{{ $section->name }}'
                                                            })"
                                                class="w-full px-3 py-2 text-sm text-gray-400 dark:text-gray-500 border border-dashed border-gray-300 dark:border-gray-700 rounded-lg hover:border-primary-500 hover:text-primary-600 dark:hover:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/10 transition"
                                            >
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-ui.card>
    @elseif($academic_year_id && $grade_id)
        <x-ui.card>
            <div class="text-center py-12">
                <i class="fas fa-exclamation-circle text-4xl text-gray-400 dark:text-gray-600 mb-4"></i>
                <p class="text-gray-500 dark:text-gray-400">
                    @if (!$this->curriculum)
                        لا يوجد منهج دراسي محدد لهذا الصف والفصل الدراسي.
                    @else
                        لا توجد شعب دراسية متاحة.
                    @endif
                </p>
            </div>
        </x-ui.card>
    @else
        <x-ui.card>
            <div class="text-center py-12">
                <i class="fas fa-filter text-4xl text-gray-400 dark:text-gray-600 mb-4"></i>
                <p class="text-gray-500 dark:text-gray-400">يرجى اختيار المعايير أعلاه لعرض الجدول.</p>
            </div>
        </x-ui.card>
    @endif
    @include('livewire.dashboard.timetables.teacher-assignments.create-teacher-assignment')
    @include('livewire.dashboard.timetables.teacher-assignments.delete-teacher-assignment')
</div>
