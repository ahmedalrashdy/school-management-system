<div>
    <x-slot name="breadcrumbs">
        <x-ui.breadcrumbs :items="[
            ['label' => 'لوحة التحكم', 'url' => route('dashboard.index'), 'icon' => 'fas fa-home'],
            [
                'label' => 'إدارة الجداول الدراسية',
                'url' => route('dashboard.timetables.index'),
                'icon' => 'fas fa-table',
            ],
            ['label' => 'قائمة الجداول', 'icon' => 'fas fa-list'],
        ]" />
    </x-slot>

    <x-ui.main-content-header
        title="الجداول الدراسية"
        description="عرض وإدارة الجداول الدراسية"
        icon="fas fa-table"
        button-text="إنشاء جدول جديد"
        :btnPermissions="\Perm::TimetablesCreate"
        button-link="{{ route('dashboard.timetables.create') }}"
    />
    <x-ui.card>
        {{-- Filters Section --}}
        <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
            <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">
                <i class="fas fa-filter ml-2"></i>
                البحث والتصفية
            </h3>
            @php
                $yearsTree = lookup()->yearsTree();
                $years = $yearsTree->pluck('name', 'id');
                $grades = lookup()->getGrades(true);
            @endphp
            {{-- Cascade Dropdowns --}}
            <div
                x-data="academicController({
                    yearsTree: {{ $yearsTree->toJson() }},
                    defaultYear: @entangle('academicYearId').live,
                    defaultTerm: @entangle('academicTermId').live
                })"
                class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-4"
            >
                {{-- Academic Year --}}
                <x-form.select
                    name="academicYearId"
                    label="العام الدراسي"
                    :options="$years"
                    x-bind="yearInput"
                    required
                />

                {{-- Grade --}}
                <x-form.select
                    name="gradeId"
                    label="الصف الدراسي"
                    :options="$grades"
                    wire:model.live="gradeId"
                />

                {{-- Academic Term --}}
                <x-form.select
                    name="academicTermId"
                    label="الفصل الدراسي"
                    :options="[]"
                    x-bind="termInput"
                />

                {{-- Section --}}
                <x-form.select
                    name="sectionId"
                    label="الشعبة"
                    :options="$this->sections->pluck('name', 'id')->toArray()"
                    wire:model.live="sectionId"
                    placeholder="اختر الشعبة"
                    :disabled="$academicTermId === null"
                />
            </div>

            {{-- Search Bar --}}
            <div class="mt-4">
                <x-form.input
                    name="search"
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="ابحث عن جدول بالاسم أو الشعبة..."
                >
                    <x-slot name="prefix">
                        <i class="fas fa-search text-gray-400"></i>
                    </x-slot>
                </x-form.input>
            </div>
        </div>

        {{-- Timetables Table --}}
        @if ($this->timetables->count() > 0)
            <x-table :headers="[
                ['label' => 'اسم الجدول'],
                ['label' => 'العام الدراسي'],
                ['label' => 'الشعبة'],
                ['label' => 'قالب الإعدادات'],
                ['label' => 'عدد الحصص'],
                ['label' => 'الحالة'],
                ['label' => 'الإجراءات'],
            ]">
                @php
                    $currentTermId = school()->currentAcademicTerm()?->id;
                @endphp
                @foreach ($this->timetables as $timetable)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $timetable->name }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $timetable->section->academicYear->name }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $timetable->section->grade->name }} - شعبة {{ $timetable->section->name }}
                            </div>
                            <div class="text-xs text-gray-400 dark:text-gray-500">
                                {{ $timetable->section->grade->stage->name }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $timetable->timetableSetting->name }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $timetable->slots_count }} حصة
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if ($timetable->is_active)
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200"
                                >
                                    <i class="fas fa-check-circle ml-1"></i>
                                    مفعل
                                </span>
                            @else
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300"
                                >
                                    غير مفعل
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center gap-3">
                                @can(\Perm::TimetablesView->value)
                                    <a
                                        class="text-info-600 hover:text-info-900 dark:text-info-400 dark:hover:text-info-300"
                                        href="{{ route('dashboard.sections.timetable', $timetable->section_id) }}"
                                        title="عرض الجدول الدراسي"
                                    >
                                        <i class="fas fa-calendar-alt mr-1"></i>
                                        عرض الجدول
                                    </a>
                                @endcan
                                @if ($timetable->section->academic_term_id === $currentTermId)
                                    @can(\Perm::TimetablesUpdate->value)
                                        <a
                                            class="text-primary-600 hover:text-primary-900 dark:text-primary-400 dark:hover:text-primary-300"
                                            href="{{ route('dashboard.timetables.builder', $timetable) }}"
                                            title="بناء الجدول"
                                        >
                                            <i class="fas fa-edit ml-1"></i>
                                            بناء
                                        </a>
                                    @endcan
                                @endif

                                @can(\Perm::TimetablesActivate->value)
                                    <button
                                        class="text-info-600 hover:text-info-900 dark:text-info-400 dark:hover:text-info-300"
                                        title="{{ $timetable->is_active ? 'تعطيل' : 'تفعيل' }}"
                                        @click="$dispatch('open-modal', {
                                            name: 'confirm-toggle-modal',
                                            id: {{ $timetable->id }},
                                            isActive: {{ $timetable->is_active ? 'true' : 'false' }}
                                        })"
                                    >
                                        <i
                                            class="fas fa-{{ $timetable->is_active ? 'toggle-on' : 'toggle-off' }} ml-1"></i>
                                        {{ $timetable->is_active ? 'تعطيل' : 'تفعيل' }}
                                    </button>
                                @endcan

                                @if (!$timetable->is_active)
                                    @can(\Perm::TimetablesDelete->value)
                                        <button
                                            class="text-danger-600 hover:text-danger-900 dark:text-danger-400 dark:hover:text-danger-300"
                                            title="حذف"
                                            @click="$dispatch('open-modal', {
                                                name: 'confirm-delete-modal',
                                                id: {{ $timetable->id }},
                                                tableName: '{{ $timetable->name }}'
                                            })"
                                        >
                                            <i class="fas fa-trash ml-1"></i>
                                            حذف
                                        </button>
                                    @endcan
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </x-table>

            {{-- Pagination --}}
            <div class="mt-4">
                {{ $this->timetables->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-table text-4xl text-gray-400 dark:text-gray-600 mb-4"></i>
                <p class="text-gray-500 dark:text-gray-400 mb-4">
                    @if ($search || $sectionId || $gradeId || $academicYearId)
                        لا توجد جداول تطابق معايير البحث
                    @else
                        لا توجد جداول دراسية
                    @endif
                </p>
                @can(\Perm::TimetablesCreate->value)
                    <a
                        class="inline-flex items-center gap-2 text-primary-600 hover:text-primary-700 dark:text-primary-400"
                        href="{{ route('dashboard.timetables.create') }}"
                    >
                        <i class="fas fa-plus"></i>
                        إنشاء جدول جديد
                    </a>
                @endcan
            </div>
        @endif
    </x-ui.card>

    {{-- Toggle Active Modal --}}
    <x-ui.modal
        name="confirm-toggle-modal"
        title="تأكيد تغيير حالة الجدول"
    >
        <div class="p-4">
            <p class="text-gray-700 dark:text-gray-300 mb-4">
                هل أنت متأكد من <span
                    class="font-bold"
                    x-text="data.isActive ? 'تعطيل' : 'تفعيل'"
                ></span> هذا الجدول؟
            </p>

            <x-ui.warning-box x-show="!data.isActive">
                عند تفعيل هذا الجدول، سيتم تعطيل أي جدول نشط آخر لنفس الشعبة تلقائياً
            </x-ui.warning-box>

            <div class="flex justify-end gap-3 mt-6">
                <button
                    @click="$wire.toggleActive(data.id); show = false"
                    class="px-4 py-2 text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition"
                >
                    تأكيد
                </button>
            </div>
        </div>
    </x-ui.modal>

    {{-- Delete Modal --}}
    <x-ui.modal
        name="confirm-delete-modal"
        title="تأكيد حذف الجدول"
    >
        <div class="p-4">
            <p class="text-gray-700 dark:text-gray-300 mb-4">
                هل أنت متأكد من حذف الجدول "<span
                    class="font-bold"
                    x-text="data.tableName"
                ></span>"؟
            </p>

            <div class="mb-4 p-3 bg-red-50 dark:bg-red-900/30 text-red-800 dark:text-red-200 rounded-lg text-sm">
                <i class="fas fa-exclamation-circle ml-1"></i>
                لا يمكن التراجع عن هذا الإجراء.
            </div>

            <div class="flex justify-end gap-3 mt-6">
                <button
                    @click="$wire.delete(data.id); show = false"
                    class="px-4 py-2 text-white bg-red-600 rounded-lg hover:bg-red-700 transition"
                >
                    حذف
                </button>
            </div>
        </div>
    </x-ui.modal>
</div>
