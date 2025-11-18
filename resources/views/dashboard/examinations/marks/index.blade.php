<x-layouts.dashboard page-title="كشوف الدرجات">
    <x-slot name="breadcrumbs">
        <x-ui.breadcrumbs :items="[
            ['label' => 'لوحة التحكم', 'url' => route('dashboard.index'), 'icon' => 'fas fa-home'],
            ['label' => 'كشوف الدرجات', 'icon' => 'fas fa-file-alt'],
        ]" />
    </x-slot>

    <x-ui.main-content-header
        title="كشوف الدرجات"
        description="لوحة التحكم المركزية لعرض ومراقبة نتائج الشعب الدراسية"
        button-text="رجوع"
        button-link="{{ route('dashboard.index') }}"
    />

    <!-- Filters -->
    <x-ui.filter-section :showReset="request()->anyFilled(['academic_year_id', 'grade_id', 'academic_term_id'])">
        @php
            $grades = lookup()->getGrades(true);
            $yearsTree = lookup()->yearsTree();
            $years = $yearsTree->pluck('name', 'id');

        @endphp
        <div
            x-data="academicController({
                yearsTree: {{ $yearsTree->toJson() }},
                defaultYear: '{{ request('academic_year_id', $activeYear?->id) }}',
                defaultTerm: '{{ request('academic_term_id') }}',
            })"
            class="grid grid-cols-1 md:grid-cols-3 gap-4"
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
        @if ($sections->count() > 0)
            <x-table :headers="[
                ['label' => 'اسم الشعبة'],
                ['label' => 'الصف والمرحلة'],
                ['label' => 'السنة الدراسية'],
                ['label' => 'الفصل الدراسي'],
                ['label' => 'إجمالي المواد'],
                ['label' => 'المواد المربوطة'],
                ['label' => 'المواد المكتملة'],
                ['label' => 'حالة الجاهزية'],
                ['label' => 'الإجراءات'],
            ]">
                @foreach ($sectionsWithStats as $item)
                    @php
                        $section = $item['section'];
                        $stats = $item['stats'];
                    @endphp
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                        <x-table.td nowrap>
                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $section->name }}
                            </div>
                        </x-table.td>
                        <x-table.td nowrap>
                            <div class="text-sm text-gray-900 dark:text-white">
                                {{ $section->grade->name }}
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $section->grade->stage->name }}
                            </div>
                        </x-table.td>
                        <x-table.td nowrap>
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $section->academicYear->name }}
                            </div>
                        </x-table.td>
                        <x-table.td nowrap>
                            <x-ui.badge variant="info">
                                {{ $section->academicTerm->name }}
                            </x-ui.badge>
                        </x-table.td>
                        <x-table.td
                            nowrap
                            align="center"
                        >
                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $stats['total_subjects'] }}
                            </div>
                        </x-table.td>
                        <x-table.td
                            nowrap
                            align="center"
                        >
                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $stats['subjects_with_rules'] }}
                            </div>
                        </x-table.td>
                        <x-table.td
                            nowrap
                            align="center"
                        >
                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $stats['completed_subjects'] }}
                            </div>
                        </x-table.td>
                        <x-table.td nowrap>
                            @if ($stats['is_ready'])
                                <x-ui.badge variant="success">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    جاهزة
                                </x-ui.badge>
                            @else
                                <x-ui.badge variant="warning">
                                    <i class="fas fa-clock mr-1"></i>
                                    غير جاهزة
                                </x-ui.badge>
                            @endif
                        </x-table.td>
                        <x-table.td nowrap>
                            <div class="flex items-center gap-2">
                                @if ($stats['is_ready'])
                                    <x-table.action-view
                                        href="{{ route('dashboard.marks.show', $section) }}"
                                        title="عرض النتائج"
                                    />
                                @else
                                    <span
                                        class="text-gray-400 dark:text-gray-600 cursor-not-allowed p-2"
                                        title="لا توجد مواد مكتملة الرصد"
                                    >
                                        <i class="fas fa-eye-slash text-sm"></i>
                                    </span>
                                @endif
                                <x-table.action
                                    href="{{ route('dashboard.marks.audit', $section) }}"
                                    icon="fas fa-search"
                                    variant="info"
                                    title="التدقيق والمتابعة"
                                />
                            </div>
                        </x-table.td>
                    </tr>
                @endforeach
            </x-table>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $sections->links() }}
            </div>
        @else
            <x-ui.empty-state
                icon="fas fa-users"
                title="لا توجد شعب دراسية"
            />
        @endif
    </x-ui.card>

</x-layouts.dashboard>
