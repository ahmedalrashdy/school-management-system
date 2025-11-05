<x-layouts.dashboard page-title="الشعب الدراسية">
    <x-slot name="breadcrumbs">
        <x-ui.breadcrumbs :items="[
            ['label' => 'لوحة التحكم', 'url' => route('dashboard.index'), 'icon' => 'fas fa-home'],
            ['label' => 'الشعب الدراسية', 'icon' => 'fas fa-users-class'],
        ]" />
    </x-slot>

    <x-ui.main-content-header
        title="الشعب الدراسية"
        description="إدارة الشعب الدراسية في النظام"
        button-text="إضافة شعبة دراسية"
        :btnPermissions="\Perm::SectionsCreate"
        button-link="{{ route('dashboard.sections.create') }}"
    />


    <!-- Action Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <!-- توزيع الطلاب على الشعب -->
        <a
            class="group relative bg-linear-to-br from-primary-500 to-primary-600 dark:from-primary-600 dark:to-primary-700 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden"
            href="{{ route('dashboard.section-assignments.index') }}"
        >
            <div class="absolute inset-0 bg-white/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
            </div>
            <div class="relative p-6 text-white">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-14 h-14 bg-white/20 rounded-lg flex items-center justify-center backdrop-blur-sm">
                        <i class="fas fa-users-cog text-2xl"></i>
                    </div>
                    <i
                        class="fas fa-arrow-left text-xl opacity-70 group-hover:opacity-100 group-hover:translate-x-1 transition-all duration-300"></i>
                </div>
                <h3 class="text-lg font-bold mb-2">توزيع الطلاب على الشعب</h3>
                <p class="text-sm text-white/90 leading-relaxed">
                    قم بتوزيع الطلاب على الشعب الدراسية المختلفة
                </p>
            </div>
        </a>
    </div>

    <!-- Filters -->
    <x-ui.filter-section :showReset="request()->anyFilled(['academic_year_id', 'grade_id', 'academic_term_id'])">
        @php
            $yearsTree = lookup()->yearsTree();
            $years = $yearsTree->pluck('name', 'id');
            $grades = lookup()->getGrades(true);
        @endphp
        <div
            x-data="academicController({
                yearsTree: {{ $yearsTree }},
                defaultYear: '{{ request('academic_year_id', school()->activeYear()?->id) }}',
                defaultTerm: ' {{ request('academic_term_id', school()->currentAcademicTerm()?->id) }}',
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
                ['label' => 'السعة / العدد الحالي'],
                ['label' => 'الإجراءات'],
            ]">
                @foreach ($sections as $section)
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
                        <x-table.td nowrap>
                            <div class="text-sm text-gray-900 dark:text-white">
                                @if ($section->capacity)
                                    <span
                                        class="{{ $section->students_count >= $section->capacity ? 'text-danger-600 dark:text-danger-400' : '' }}"
                                    >
                                        {{ $section->students_count }} / {{ $section->capacity ?? 'NA' }}
                                    </span>
                                @else
                                    <span>{{ $section->students_count }}</span>
                                @endif
                            </div>
                        </x-table.td>
                        <x-table.td nowrap>
                            <div class="flex items-center gap-2">
                                <x-table.action
                                    :permissions="\Perm::SectionsViewStudents"
                                    href="{{ route('dashboard.sections.students', $section) }}"
                                    icon="fas fa-users"
                                    variant="success"
                                    title="عرض طلاب الشعبة"
                                />
                                @if (!$section->belongsToArchivedYear())
                                    <x-table.action
                                        href="{{ route('dashboard.sections.timetable', $section) }}"
                                        icon="fas fa-calendar-alt"
                                        variant="info"
                                        title="عرض الجدول الدراسي"
                                    />
                                    <x-table.action-edit
                                        :permissions="\Perm::SectionsUpdate"
                                        href="{{ route('dashboard.sections.edit', $section) }}"
                                    />
                                    <x-table.action-delete
                                        :permissions="\Perm::SectionsDelete"
                                        @click="$dispatch('open-modal', {
                                                                    name: 'delete-section',
                                                                    section: {
                                                                        id: {{ $section->id }},
                                                                        name: '{{ $section->name }}',
                                                                        route: '{{ route('dashboard.sections.destroy', $section) }}'
                                                                    }
                                                                })"
                                    />
                                @else
                                    <span
                                        class="text-gray-400 dark:text-gray-600 cursor-not-allowed p-2"
                                        title="مؤرشفة"
                                    >
                                        <i class="fas fa-lock text-sm"></i>
                                    </span>
                                @endif
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
            >
                <x-ui.button
                    as="a"
                    :permissions="\Perm::SectionsCreate"
                    href="{{ route('dashboard.sections.create') }}"
                >
                    <i class="fas fa-plus"></i>
                    إضافة شعبة دراسية جديدة
                </x-ui.button>
            </x-ui.empty-state>
        @endif
    </x-ui.card>

    {{-- Delete Confirmation Modal --}}
    <x-ui.confirm-action
        name="delete-section"
        title="تأكيد حذف الشعبة الدراسية"
        dataKey="section"
        spoofMethod="DELETE"
        confirmButtonText="حذف"
        confirmButtonVariant="danger"
        :permissions="\Perm::SectionsDelete"
    >
        <p class="mb-4">هل أنت متأكد من حذف الشعبة الدراسية <strong x-text="section?.name"></strong>؟</p>
        <x-ui.warning-box>
            لن تنجح العملية إذا كان هناك بيانات مرتبطة بهذه الشعبة الدراسية (طلاب، جداول دراسية، تعيينات مدرسين، إلخ).
        </x-ui.warning-box>
    </x-ui.confirm-action>


</x-layouts.dashboard>
