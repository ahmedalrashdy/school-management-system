<x-layouts.dashboard page-title="السنوات الدراسية">
    <x-slot name="breadcrumbs">
        <x-ui.breadcrumbs :items="[
            ['label' => 'لوحة التحكم', 'url' => route('dashboard.index'), 'icon' => 'fas fa-home'],
            ['label' => 'السنوات الدراسية', 'icon' => 'fas fa-calendar-alt'],
        ]" />
    </x-slot>

    <x-ui.main-content-header
        title="السنوات الدراسية"
        description="إدارة السنوات الدراسية في النظام"
        button-text="إضافة سنة دراسية"
        :btnPermissions="\Perm::AcademicYearsCreate"
        button-link="{{ route('dashboard.academic-years.create') }}"
    />

    <!-- Filters -->
    <x-ui.filter-section :showReset="request('search') || request('status')">
        <div class="flex-1 min-w-[200px]">
            <x-form.input
                name="search"
                label="البحث"
                placeholder="ابحث بالاسم..."
                value="{{ request('search') }}"
            />
        </div>
        <div class="w-48">
            <x-form.select
                name="status"
                label="الحالة"
                :options="['' => 'جميع الحالات'] + $statuses"
                selected="{{ request('status') }}"
            />
        </div>
    </x-ui.filter-section>

    <!-- Table -->
    <x-ui.card>
        @if ($academicYears->count() > 0)
            <x-table :headers="[
                ['label' => 'الاسم'],
                ['label' => 'تاريخ البداية'],
                ['label' => 'تاريخ النهاية'],
                ['label' => 'الحالة'],
                ['label' => 'الإجراءات'],
            ]">
                @foreach ($academicYears as $year)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                        <x-table.td nowrap>
                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $year->name }}
                            </div>
                        </x-table.td>
                        <x-table.td nowrap>
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $year->start_date->format('Y-m-d') }}
                            </div>
                        </x-table.td>
                        <x-table.td nowrap>
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $year->end_date->format('Y-m-d') }}
                            </div>
                        </x-table.td>
                        <x-table.td nowrap>
                            @php
                                $statusVariant = match ($year->status) {
                                    \App\Enums\AcademicYearStatus::Active => 'success',
                                    \App\Enums\AcademicYearStatus::Upcoming => 'warning',
                                    \App\Enums\AcademicYearStatus::Archived => 'default',
                                };
                            @endphp
                            <x-ui.badge variant="{{ $statusVariant }}">
                                {{ $year->status->label() }}
                            </x-ui.badge>
                        </x-table.td>
                        <x-table.td nowrap>
                            <div class="flex items-center gap-2">
                                @if ($year->status === \App\Enums\AcademicYearStatus::Upcoming && auth()->user()->can(\Perm::AcademicYearsActivate))
                                    <form
                                        method="POST"
                                        action="{{ route('dashboard.academic-years.activate', $year) }}"
                                        class="inline"
                                        onsubmit="return confirm('هل أنت متأكد من تفعيل هذه السنة الدراسية؟ سيتم أرشفة السنة النشطة الحالية.')"
                                    >
                                        @csrf
                                        <x-table.action
                                            icon="fas fa-check-circle"
                                            variant="success"
                                            title="تفعيل"
                                            type="submit"
                                        />
                                    </form>
                                @endif

                                @if ($year->status !== \App\Enums\AcademicYearStatus::Archived)
                                    <x-table.action-edit
                                        :permissions="\Perm::AcademicYearsUpdate"
                                        href="{{ route('dashboard.academic-years.edit', $year) }}"
                                    />
                                @else
                                    <span
                                        class="text-gray-400 dark:text-gray-600 cursor-not-allowed p-2"
                                        title="مؤرشفة"
                                    >
                                        <i class="fas fa-lock text-sm"></i>
                                    </span>
                                @endif
                                <x-table.action-delete
                                    :permissions="\Perm::AcademicYearsDelete"
                                    @click="$dispatch('open-modal', {
                                    name: 'delete-academic-year',
                                    academicYear: {
                                        id: {{ $year->id }},
                                        name: '{{ $year->name }}',
                                        route: '{{ route('dashboard.academic-years.destroy', $year) }}'
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
                {{ $academicYears->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-calendar-times text-4xl text-gray-400 dark:text-gray-600 mb-4"></i>
                <p class="text-gray-500 dark:text-gray-400">لا توجد سنوات دراسية</p>
                <x-ui.button
                    as="a"
                    :permissions="\Perm::AcademicYearsCreate"
                    href="{{ route('dashboard.academic-years.create') }}"
                >
                    <i class="fas fa-plus"></i>
                    إضافة سنة دراسية جديدة
                </x-ui.button>
            </div>
        @endif
    </x-ui.card>

    {{-- Delete Confirmation Modal --}}
    <x-ui.confirm-action
        name="delete-academic-year"
        title="تأكيد حذف السنة الدراسية"
        dataKey="academicYear"
        spoofMethod="DELETE"
        confirmButtonText="حذف"
        confirmButtonVariant="danger"
        :permissions="\Perm::AcademicYearsDelete"
    >
        <p class="mb-4">هل أنت متأكد من حذف السنة الدراسية <strong x-text="academicYear?.name"></strong>؟</p>
        <x-ui.warning-box>
            لن تنجح العملية إذا كان هناك بيانات مرتبطة بهذه السنة الدراسية
        </x-ui.warning-box>
    </x-ui.confirm-action>
</x-layouts.dashboard>
