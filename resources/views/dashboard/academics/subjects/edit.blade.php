<x-layouts.dashboard page-title="تعديل مادة دراسية">
    <x-slot name="breadcrumbs">
        <x-ui.breadcrumbs :items="[
            ['label' => 'لوحة التحكم', 'url' => route('dashboard.index'), 'icon' => 'fas fa-home'],
            ['label' => 'المواد الدراسية', 'url' => route('dashboard.subjects.index'), 'icon' => 'fas fa-book'],
            ['label' => 'تعديل مادة', 'icon' => 'fas fa-edit'],
        ]" />
    </x-slot>

    <x-ui.main-content-header
        title="تعديل مادة دراسية"
        description="تعديل بيانات المادة الدراسية: {{ $subject->name }}"
        button-text="رجوع"
        button-link="{{ route('dashboard.subjects.index') }}"
    />

    <x-ui.card>
        <form
            method="POST"
            action="{{ route('dashboard.subjects.update', $subject) }}"
        >
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-form.input
                    name="name"
                    label="اسم المادة الدراسية"
                    placeholder="مثال: الرياضيات، الفيزياء، التاريخ"
                    :value="$subject->name"
                    required
                />

                <x-form.input
                    name="sort_order"
                    label="رقم الترتيب"
                    type="number"
                    min="0"
                    :value="$subject->sort_order"
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
                    href="{{ route('dashboard.subjects.index') }}"
                    variant="outline"
                >
                    إلغاء
                </x-ui.button>
            </div>
        </form>
    </x-ui.card>
</x-layouts.dashboard>
