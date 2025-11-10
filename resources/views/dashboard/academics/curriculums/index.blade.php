<x-layouts.dashboard page-title="المناهج الدراسية">
    <x-slot name="breadcrumbs">
        <x-ui.breadcrumbs :items="[
            ['label' => 'لوحة التحكم', 'url' => route('dashboard.index'), 'icon' => 'fas fa-home'],
            ['label' => 'المناهج الدراسية', 'icon' => 'fas fa-book-open'],
        ]" />
    </x-slot>

    <x-ui.main-content-header
        title="المناهج الدراسية"
        description="إدارة المناهج الدراسية في النظام"
        button-text="إضافة منهج دراسي"
        :btnPermissions="\Perm::CurriculumsCreate"
        button-link="{{ route('dashboard.curriculums.create') }}"
    />


    <!-- Filters -->
    <x-ui.filter-section :showReset="request()->anyFilled(['academic_year_id', 'grade_id', 'academic_term_id'])">
        @php
            $yearsTree = lookup()->yearsTree();
            $years = $yearsTree->pluck('name', 'id');
            $grades = lookup()->getGrades(true);
        @endphp
        <div
            class="grid grid-cols-1 md:grid-cols-3 gap-4"
            x-data="academicController({
                yearsTree: {{ $yearsTree->toJson() }},
                defaultYear: '{{ request('academic_year_id', $activeYear?->id) }}',
                defaultTerm: '{{ request('academic_term_id', $currentTerm?->id) }}',
            })"
        >
            <x-form.select
                name="academic_year_id"
                label="السنة الدراسية"
                :options="$years"
                x-bind="yearInput"
            />
            <x-form.select
                name="grade_id"
                label="الصف الدراسي"
                :options="$grades"
                selected="{{ request('grade_id') }}"
            />
            <x-form.select
                name="academic_term_id"
                label="الفصل الدراسي"
                :options="[]"
                x-bind="termInput"
            />
        </div>
    </x-ui.filter-section>


    <!-- Table -->
    <x-ui.card>
        @if ($curriculums->count() > 0)
            <x-table :headers="[
                ['label' => 'السنة الدراسية'],
                ['label' => 'الصف والمرحلة'],
                ['label' => 'الفصل الدراسي'],
                ['label' => 'عدد المواد'],
                ['label' => 'الإجراءات'],
            ]">
                @foreach ($curriculums as $curriculum)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                        <x-table.td nowrap>
                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $curriculum->academicYear->name }}
                            </div>
                        </x-table.td>
                        <x-table.td nowrap>
                            <div class="text-sm text-gray-900 dark:text-white">
                                {{ $curriculum->grade->name }}
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $curriculum->grade->stage->name }}
                            </div>
                        </x-table.td>
                        <x-table.td nowrap>
                            <x-ui.badge variant="info">
                                {{ $curriculum->academicTerm->name }}
                            </x-ui.badge>
                        </x-table.td>
                        <x-table.td nowrap>
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $curriculum->subjects_count }} مادة
                            </div>
                        </x-table.td>
                        <x-table.td nowrap>
                            <div class="flex items-center gap-2">
                                <x-table.action-view href="{{ route('dashboard.curriculums.show', $curriculum) }}" />
                                <x-table.action-delete
                                    :permissions="\Perm::CurriculumsDelete"
                                    @click="$dispatch('open-modal', {
                                                        name: 'delete-curriculum',
                                                        curriculum: {
                                                            id: {{ $curriculum->id }},
                                                            name: '{{ $curriculum->grade->name }} - {{ $curriculum->academicTerm->name }}',
                                                            route: '{{ route('dashboard.curriculums.destroy', $curriculum) }}'
                                                        }
                                                    })"
                                />
                            </div>
                        </x-table.td>
                    </tr>
                @endforeach
            </x-table>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $curriculums->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-book-open text-4xl text-gray-400 dark:text-gray-600 mb-4"></i>
                <p class="text-gray-500 dark:text-gray-400">لا توجد مناهج دراسية</p>
                <x-ui.button
                    as="a"
                    :permissions="\Perm::CurriculumsCreate"
                    href="{{ route('dashboard.curriculums.create') }}"
                >
                    <i class="fas fa-plus"></i>
                    إضافة منهج دراسي جديد
                </x-ui.button>
            </div>
        @endif
    </x-ui.card>

    {{-- Delete Confirmation Modal --}}
    <x-ui.confirm-action
        name="delete-curriculum"
        title="تأكيد حذف المنهج الدراسي"
        dataKey="curriculum"
        spoofMethod="DELETE"
        confirmButtonText="حذف"
        confirmButtonVariant="danger"
        :permissions="\Perm::CurriculumsDelete"
    >
        <p class="mb-4">هل أنت متأكد من حذف المنهج الدراسي <strong x-text="curriculum?.name"></strong>؟</p>
        <x-ui.warning-box>
            سيتم حذف جميع المواد المرتبطة بهذا المنهج الدراسي.
        </x-ui.warning-box>
    </x-ui.confirm-action>
</x-layouts.dashboard>
