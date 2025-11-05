<x-layouts.dashboard page-title="تعديل صف دراسي">
    <x-slot name="breadcrumbs">
        <x-ui.breadcrumbs :items="[
            ['label' => 'لوحة التحكم', 'url' => route('dashboard.index'), 'icon' => 'fas fa-home'],
            ['label' => 'الصفوف الدراسية', 'url' => route('dashboard.grades.index'), 'icon' => 'fas fa-layer-group'],
            ['label' => 'تعديل صف', 'icon' => 'fas fa-edit'],
        ]" />
    </x-slot>

    <x-ui.main-content-header
        title="تعديل صف دراسي"
        description="تعديل بيانات الصف الدراسي: {{ $grade->name }}"
        button-text="رجوع"
        button-link="{{ route('dashboard.grades.index') }}"
    />
    <x-ui.card>
        <form
            method="POST"
            action="{{ route('dashboard.grades.update', $grade) }}"
        >
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-form.select
                    name="stage_id"
                    label="المرحلة الدراسية"
                    :options="lookup()->getStages()"
                    selected="{{ $grade->stage_id }}"
                    required
                />
                <x-form.input
                    name="name"
                    label="اسم الصف الدراسي"
                    placeholder="مثال: الصف الأول"
                    value="{{ $grade->name }}"
                    required
                />
            </div>

            <div class="mt-6">
                <x-form.input
                    name="sort_order"
                    label="رقم الترتيب"
                    type="number"
                    min="0"
                    :value="$grade->sort_order"
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
                    href="{{ route('dashboard.grades.index') }}"
                    variant="outline"
                >
                    إلغاء
                </x-ui.button>
            </div>
        </form>
    </x-ui.card>
</x-layouts.dashboard>
