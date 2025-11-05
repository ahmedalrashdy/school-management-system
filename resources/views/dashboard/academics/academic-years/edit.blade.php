<x-layouts.dashboard page-title="تعديل سنة دراسية">
    <x-slot name="breadcrumbs">
        <x-ui.breadcrumbs :items="[
            ['label' => 'لوحة التحكم', 'url' => route('dashboard.index'), 'icon' => 'fas fa-home'],
            [
                'label' => 'السنوات الدراسية',
                'url' => route('dashboard.academic-years.index'),
                'icon' => 'fas fa-calendar-alt',
            ],
            ['label' => 'تعديل سنة دراسية', 'icon' => 'fas fa-edit'],
        ]" />
    </x-slot>

    <x-ui.main-content-header
        title="تعديل سنة دراسية"
        description="تعديل بيانات السنة الدراسية: {{ $academicYear->name }}"
        button-text="رجوع"
        button-link="{{ route('dashboard.academic-years.index') }}"
    />
    <x-ui.card>
        <form
            method="POST"
            action="{{ route('dashboard.academic-years.update', $academicYear) }}"
        >
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-form.input
                    name="name"
                    label="اسم السنة الدراسية"
                    placeholder="مثال: 2024-2025"
                    :value="$academicYear->name"
                    required
                />

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        الحالة
                    </label>
                    <div class="mt-1">
                        @php
                            $statusVariant = match ($academicYear->status) {
                                \App\Enums\AcademicYearStatus::Active => 'success',
                                \App\Enums\AcademicYearStatus::Upcoming => 'warning',
                                \App\Enums\AcademicYearStatus::Archived => 'default',
                            };
                        @endphp
                        <x-ui.badge variant="{{ $statusVariant }}">
                            {{ $academicYear->status->label() }}
                        </x-ui.badge>
                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                            <i class="fas fa-info-circle mr-1"></i>
                            لا يمكن تغيير الحالة من هنا. استخدم زر "تفعيل" في قائمة السنوات الدراسية.
                        </p>
                    </div>
                </div>

                <x-form.input
                    name="start_date"
                    label="تاريخ البداية"
                    type="date"
                    value="{{ $academicYear->start_date->format('Y-m-d') }}"
                    required
                />
                <x-form.input
                    name="end_date"
                    label="تاريخ النهاية"
                    type="date"
                    value="{{ $academicYear->end_date->format('Y-m-d') }}"
                    required
                />
            </div>

            <div class="mt-6 flex items-center gap-4">
                <x-ui.button
                    type="submit"
                    variant="primary"
                >
                    <i class="fas fa-save mr-2"></i>
                    حفظ التغييرات
                </x-ui.button>
                <x-ui.button
                    as="a"
                    href="{{ route('dashboard.academic-years.index') }}"
                    variant="outline"
                >
                    إلغاء
                </x-ui.button>
            </div>
        </form>
    </x-ui.card>
</x-layouts.dashboard>
