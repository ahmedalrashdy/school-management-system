<x-layouts.dashboard page-title="تعديل مرحلة دراسية">
    <x-slot name="breadcrumbs">
        <x-ui.breadcrumbs :items="[
            ['label' => 'لوحة التحكم', 'url' => route('dashboard.index'), 'icon' => 'fas fa-home'],
            [
                'label' => 'المراحل الدراسية',
                'url' => route('dashboard.stages.index'),
                'icon' => 'fas fa-graduation-cap',
            ],
            ['label' => 'تعديل مرحلة', 'icon' => 'fas fa-edit'],
        ]" />
    </x-slot>

    <x-ui.main-content-header
        title="تعديل مرحلة دراسية"
        description="تعديل بيانات المرحلة الدراسية: {{ $stage->name }}"
        button-text="رجوع"
        button-link="{{ route('dashboard.stages.index') }}"
    />
    <x-ui.card>
        <form
            method="POST"
            action="{{ route('dashboard.stages.update', $stage) }}"
        >
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-form.input
                    name="name"
                    value="{{ $stage->name }}"
                    label="اسم المرحلة الدراسية"
                    placeholder="مثال: المرحلة الابتدائية"
                    required
                />

                <x-form.input
                    name="sort_order"
                    label="رقم الترتيب"
                    type="number"
                    min="0"
                    :value="$stage->sort_order"
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
                    href="{{ route('dashboard.stages.index') }}"
                    variant="outline"
                >
                    إلغاء
                </x-ui.button>
            </div>
        </form>
    </x-ui.card>
</x-layouts.dashboard>
