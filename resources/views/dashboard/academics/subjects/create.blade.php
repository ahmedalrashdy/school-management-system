<x-layouts.dashboard page-title="إضافة مادة دراسية جديدة">
    <x-slot name="breadcrumbs">
        <x-ui.breadcrumbs :items="[
            ['label' => 'لوحة التحكم', 'url' => route('dashboard.index'), 'icon' => 'fas fa-home'],
            ['label' => 'المواد الدراسية', 'url' => route('dashboard.subjects.index'), 'icon' => 'fas fa-book'],
            ['label' => 'إضافة مادة جديدة', 'icon' => 'fas fa-plus'],
        ]" />
    </x-slot>

    <x-ui.main-content-header
        title="إضافة مادة دراسية جديدة"
        description="إنشاء مادة دراسية جديدة في بنك المواد"
        button-text="إضافة مادة دراسية"
        button-link="{{ route('dashboard.subjects.index') }}"
    />

    <x-ui.card>
        <form
            method="POST"
            action="{{ route('dashboard.subjects.store') }}"
        >
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-form.input
                    name="name"
                    label="اسم المادة الدراسية"
                    placeholder="مثال: الرياضيات، الفيزياء، التاريخ"
                    required
                />

                <x-form.input
                    name="sort_order"
                    label="رقم الترتيب"
                    type="number"
                    min="0"
                    :value="0"
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
                    href="{{ route('dashboard.subjects.index') }}"
                    variant="outline"
                >
                    إلغاء
                </x-ui.button>
            </div>
        </form>
    </x-ui.card>
</x-layouts.dashboard>
