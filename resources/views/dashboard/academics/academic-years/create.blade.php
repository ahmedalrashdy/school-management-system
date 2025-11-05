<x-layouts.dashboard page-title="إضافة سنة دراسية جديدة">
    <x-slot name="breadcrumbs">
        <x-ui.breadcrumbs :items="[
            ['label' => 'لوحة التحكم', 'url' => route('dashboard.index'), 'icon' => 'fas fa-home'],
            [
                'label' => 'السنوات الدراسية',
                'url' => route('dashboard.academic-years.index'),
                'icon' => 'fas fa-calendar-alt',
            ],
            ['label' => 'إضافة سنة جديدة', 'icon' => 'fas fa-plus'],
        ]" />
    </x-slot>

    <x-ui.main-content-header
        title="إضافة سنة دراسية جديدة"
        description="إنشاء سنة دراسية جديدة في النظام"
        button-text="إضافة سنة دراسية"
        button-link="{{ route('dashboard.academic-years.index') }}"
    />


    <x-ui.card>
        @if ($hasActiveYear)
            <x-ui.alert
                type="info"
                class="mb-6"
            >
                يوجد سنة دراسية نشطة حالياً. السنة الجديدة ستكون بحالة "قادمة" فقط.
            </x-ui.alert>
        @endif

        <form
            method="POST"
            action="{{ route('dashboard.academic-years.store') }}"
        >
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-form.input
                    name="name"
                    label="اسم السنة الدراسية"
                    placeholder="مثال: 2024-2025"
                    required
                />

                <x-form.select
                    name="status"
                    label="الحالة"
                    :options="$statuses"
                    selected="{{ $hasActiveYear ? \App\Enums\AcademicYearStatus::Upcoming->value : \App\Enums\AcademicYearStatus::Active->value }}"
                    required
                />

                <x-form.input
                    name="start_date"
                    label="تاريخ البداية"
                    type="date"
                    required
                />
                <x-form.input
                    name="end_date"
                    label="تاريخ النهاية"
                    type="date"
                    required
                />
            </div>

            <div class="mt-6 flex items-center gap-4">
                <x-ui.button
                    type="submit"
                    variant="primary"
                >
                    <i class="fas fa-save mr-2"></i>
                    حفظ
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
