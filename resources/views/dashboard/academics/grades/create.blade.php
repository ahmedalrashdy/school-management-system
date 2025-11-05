<x-layouts.dashboard page-title="إضافة صف دراسي جديد">
    <x-slot name="breadcrumbs">
        <x-ui.breadcrumbs :items="[
            ['label' => 'لوحة التحكم', 'url' => route('dashboard.index'), 'icon' => 'fas fa-home'],
            ['label' => 'الصفوف الدراسية', 'url' => route('dashboard.grades.index'), 'icon' => 'fas fa-layer-group'],
            ['label' => 'إضافة صف جديد', 'icon' => 'fas fa-plus'],
        ]" />
    </x-slot>

    <x-ui.main-content-header
        title="إضافة صف دراسي جديد"
        description="إنشاء صف دراسي جديد في النظام"
        button-text="إضافة صف دراسي"
        button-link="{{ route('dashboard.grades.index') }}"
    />

    <x-ui.card>
        <form
            method="POST"
            action="{{ route('dashboard.grades.store') }}"
        >
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-form.select
                    name="stage_id"
                    label="المرحلة الدراسية"
                    :options="lookup()->getStages()"
                    required
                />

                <x-form.input
                    name="name"
                    label="اسم الصف الدراسي"
                    placeholder="مثال: الصف الأول"
                    required
                />
            </div>

            <div class="mt-6">
                <x-form.input
                    name="sort_order"
                    label="رقم الترتيب"
                    type="number"
                    min="0"
                    value="0"
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
                    href="{{ route('dashboard.grades.index') }}"
                    variant="outline"
                >
                    إلغاء
                </x-ui.button>
            </div>
        </form>
    </x-ui.card>
</x-layouts.dashboard>
