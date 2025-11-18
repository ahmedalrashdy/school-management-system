<x-layouts.dashboard page-title="تعديل نوع امتحان">
    <x-slot name="breadcrumbs">
        <x-ui.breadcrumbs :items="[
            ['label' => 'لوحة التحكم', 'url' => route('dashboard.index'), 'icon' => 'fas fa-home'],
            ['label' => 'إدارة الامتحانات', 'url' => route('dashboard.exams.index'), 'icon' => 'fas fa-clipboard-list'],
            ['label' => 'أنواع الامتحانات', 'url' => route('dashboard.exam-types.index'), 'icon' => 'fas fa-file-alt'],
            ['label' => 'تعديل نوع امتحان', 'icon' => 'fas fa-edit'],
        ]" />
    </x-slot>

    <x-ui.main-content-header
        title="تعديل نوع امتحان"
        description="تعديل بيانات نوع الامتحان: {{ $examType->name }}"
        button-text="رجوع"
        button-link="{{ route('dashboard.exam-types.index') }}"
    />

    <x-ui.card>
        <form
            method="POST"
            action="{{ route('dashboard.exam-types.update', $examType) }}"
        >
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-form.input
                    name="name"
                    label="اسم نوع الامتحان"
                    placeholder="مثال: امتحان شهري، امتحان نهائي، اختبار قصير"
                    value="{{ old('name', $examType->name) }}"
                    required
                />

                <x-form.input
                    name="sort_order"
                    label="رقم الترتيب"
                    type="number"
                    min="0"
                    :value="$examType->sort_order"
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
                    href="{{ route('dashboard.exam-types.index') }}"
                    variant="outline"
                >
                    إلغاء
                </x-ui.button>
            </div>
        </form>
    </x-ui.card>
</x-layouts.dashboard>
