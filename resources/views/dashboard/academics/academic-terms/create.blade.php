<x-layouts.dashboard page-title="إضافة فصل دراسي جديد">
    <x-slot name="breadcrumbs">
        <x-ui.breadcrumbs :items="[
            ['label' => 'لوحة التحكم', 'url' => route('dashboard.index'), 'icon' => 'fas fa-home'],
            [
                'label' => 'الفصول الدراسية',
                'url' => route('dashboard.academic-terms.index'),
                'icon' => 'fas fa-calendar-week',
            ],
            ['label' => 'إضافة فصل جديد', 'icon' => 'fas fa-plus'],
        ]" />
    </x-slot>

    <x-ui.main-content-header
        title="إضافة فصل دراسي جديد"
        description="إنشاء فصل دراسي جديد في النظام"
        button-text="رجوع"
        button-link="{{ route('dashboard.academic-terms.index') }}"
    />

    <x-ui.card>
        <form
            method="POST"
            action="{{ route('dashboard.academic-terms.store') }}"
        >
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-form.select
                    name="academic_year_id"
                    label="السنة الدراسية"
                    :options="lookup()->getActiveAndUpcomingYearsOnly()"
                    selected="{{ school()->activeYear()?->id }}"
                    required
                />

                <x-form.input
                    name="name"
                    label="اسم الفصل الدراسي"
                    placeholder="مثال: الترم الأول 2024-2025"
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
                    href="{{ route('dashboard.academic-terms.index') }}"
                    variant="outline"
                >
                    إلغاء
                </x-ui.button>
            </div>
        </form>
    </x-ui.card>
</x-layouts.dashboard>
