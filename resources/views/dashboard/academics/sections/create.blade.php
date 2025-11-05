<x-layouts.dashboard page-title="إضافة شعبة دراسية جديدة">
    <x-slot name="breadcrumbs">
        <x-ui.breadcrumbs :items="[
            ['label' => 'لوحة التحكم', 'url' => route('dashboard.index'), 'icon' => 'fas fa-home'],
            ['label' => 'الشعب الدراسية', 'url' => route('dashboard.sections.index'), 'icon' => 'fas fa-users-class'],
            ['label' => 'إضافة شعبة جديدة', 'icon' => 'fas fa-plus'],
        ]" />
    </x-slot>

    <x-ui.main-content-header
        title="إضافة شعبة دراسية جديدة"
        description="إنشاء شعبة دراسية جديدة في النظام"
        button-text="إضافة شعبة دراسية"
        button-link="{{ route('dashboard.sections.index') }}"
    />
    <x-ui.card>
        <form
            method="POST"
            action="{{ route('dashboard.sections.store') }}"
        >
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-form.select
                    name="grade_id"
                    label="الصف الدراسي"
                    :options="lookup()->getGrades()"
                    required
                />

                <x-form.input
                    name="name"
                    label="اسم الشعبة"
                    placeholder="مثال: أ، ب، 1، 2"
                    required
                />

                <x-form.input
                    name="capacity"
                    label="السعة القصوى (اختياري)"
                    type="number"
                    placeholder="مثال: 30"
                    min="1"
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
                    href="{{ route('dashboard.sections.index') }}"
                    variant="outline"
                >
                    إلغاء
                </x-ui.button>
            </div>
        </form>
    </x-ui.card>
</x-layouts.dashboard>
