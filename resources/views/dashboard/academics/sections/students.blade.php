<x-layouts.dashboard page-title="طلاب الشعبة">
    <x-slot name="breadcrumbs">
        <x-ui.breadcrumbs :items="[
            ['label' => 'لوحة التحكم', 'url' => route('dashboard.index'), 'icon' => 'fas fa-home'],
            ['label' => 'الشعب الدراسية', 'url' => route('dashboard.sections.index'), 'icon' => 'fas fa-users-class'],
            ['label' => 'طلاب الشعبة', 'icon' => 'fas fa-users'],
        ]" />
    </x-slot>

    <x-ui.main-content-header
        title="طلاب الشعبة: {{ $section->name }}"
        description="{{ $section->grade->name }} - {{ $section->grade->stage->name }} | {{ $section->academicYear->name }} | {{ $section->academicTerm->name }}"
        button-text="رجوع"
        button-link="{{ route('dashboard.sections.index') }}"
    />

    <!-- Section Info Card -->
    <x-ui.card class="mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <div class="text-sm text-gray-500 dark:text-gray-400 mb-1">اسم الشعبة</div>
                <div class="text-lg font-semibold text-gray-900 dark:text-white">{{ $section->name }}</div>
            </div>
            <div>
                <div class="text-sm text-gray-500 dark:text-gray-400 mb-1">عدد الطلاب</div>
                <div class="text-lg font-semibold text-gray-900 dark:text-white">
                    {{ $students->total() }} طالب
                </div>
            </div>
            <div>
                <div class="text-sm text-gray-500 dark:text-gray-400 mb-1">السعة</div>
                <div class="text-lg font-semibold text-gray-900 dark:text-white">
                    @if ($section->capacity)
                        {{ $section->capacity }} طالب
                    @else
                        <span class="text-gray-400 dark:text-gray-600">غير محدد</span>
                    @endif
                </div>
            </div>
        </div>
    </x-ui.card>

    <!-- Search Filter -->
    <x-ui.filter-section
        :href="route('dashboard.sections.students', $section)"
        :showReset="request()->filled('search')"
    >
        <div class="flex-1 min-w-[300px]">
            <x-form.input
                name="search"
                label="البحث"
                placeholder="ابحث بالاسم أو رقم القيد..."
                value="{{ request('search') }}"
            />
        </div>
    </x-ui.filter-section>

    <!-- Students Table -->
    <x-ui.card>
        @if ($students->count() > 0)
            <x-table :headers="[
                ['label' => 'الصورة'],
                ['label' => 'اسم الطالب'],
                ['label' => 'رقم القيد'],
                ['label' => 'تاريخ الميلاد'],
                ['label' => 'الحالة'],
                ['label' => 'الإجراءات'],
            ]">
                @foreach ($students as $student)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                        <x-table.td nowrap>
                            <div class="flex-shrink-0 h-10 w-10">
                                @if ($student->user->avatar)
                                    <img
                                        class="h-10 w-10 rounded-full object-cover"
                                        src="{{ $student->user->avatar }}"
                                        alt="{{ $student->user->full_name }}"
                                    >
                                @else
                                    <div
                                        class="h-10 w-10 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                                        <i class="fas fa-user text-gray-500 dark:text-gray-400"></i>
                                    </div>
                                @endif
                            </div>
                        </x-table.td>
                        <x-table.td nowrap>
                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $student->user->full_name }}
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $student->user->gender->label() }}
                            </div>
                        </x-table.td>
                        <x-table.td nowrap>
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $student->admission_number }}
                            </div>
                        </x-table.td>
                        <x-table.td nowrap>
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $student->date_of_birth->format('Y-m-d') }}
                            </div>
                            <div class="text-xs text-gray-400 dark:text-gray-600">
                                {{ $student->date_of_birth->age }} سنة
                            </div>
                        </x-table.td>
                        <x-table.td nowrap>
                            @if ($student->user->is_active)
                                <x-ui.badge variant="success">نشط</x-ui.badge>
                            @else
                                <x-ui.badge variant="danger">غير نشط</x-ui.badge>
                            @endif
                        </x-table.td>
                        <x-table.td nowrap>
                            <div class="flex items-center gap-2">
                                <x-table.action-view href="{{ route('dashboard.students.show', $student) }}" />
                            </div>
                        </x-table.td>
                    </tr>
                @endforeach
            </x-table>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $students->links() }}
            </div>
        @else
            <x-ui.empty-state
                icon="fas fa-users"
                :title="request()->filled('search') ? 'لا توجد نتائج للبحث' : 'لا يوجد طلاب مسجلين في هذه الشعبة'"
                :action="request()->filled('search')
                    ? [
                        'label' => 'إلغاء البحث',
                        'url' => route('dashboard.sections.students', $section),
                        'icon' => 'fas fa-times',
                    ]
                    : null"
            />
        @endif
    </x-ui.card>
</x-layouts.dashboard>
