<x-layouts.dashboard page-title="تعديل شعبة دراسية">
    <x-slot name="breadcrumbs">
        <x-ui.breadcrumbs :items="[
            ['label' => 'لوحة التحكم', 'url' => route('dashboard.index'), 'icon' => 'fas fa-home'],
            ['label' => 'الشعب الدراسية', 'url' => route('dashboard.sections.index'), 'icon' => 'fas fa-users-class'],
            ['label' => 'تعديل شعبة', 'icon' => 'fas fa-edit'],
        ]" />
    </x-slot>

    <x-ui.main-content-header
        title="تعديل شعبة دراسية"
        description="تعديل بيانات الشعبة الدراسية: {{ $section->name }}"
        button-text="رجوع"
        button-link="{{ route('dashboard.sections.index') }}"
    />


    <x-ui.card>
        <x-ui.alert
            class="mb-6"
            type="info"
        >
            لا يمكن تعديل السنة الدراسية، الصف الدراسي، أو الفصل الدراسي بعد الإنشاء. يمكنك فقط تعديل اسم الشعبة وسعتها.
        </x-ui.alert>

        <form
            method="POST"
            action="{{ route('dashboard.sections.update', $section) }}"
        >
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        السنة الدراسية
                    </label>
                    <div class="mt-1 text-sm text-gray-900 dark:text-white">
                        {{ $section->academicYear->name }}
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        الصف والمرحلة
                    </label>
                    <div class="mt-1 text-sm text-gray-900 dark:text-white">
                        {{ $section->grade->stage->name }} - {{ $section->grade->name }}
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        الفصل الدراسي
                    </label>
                    <div class="mt-1">
                        <x-ui.badge variant="info">
                            {{ $section->academicTerm->name }}
                        </x-ui.badge>
                    </div>
                </div>

                <x-form.input
                    name="name"
                    label="اسم الشعبة"
                    placeholder="مثال: أ، ب، 1، 2"
                    :value="$section->name"
                    required
                />

                <x-form.input
                    name="capacity"
                    type="number"
                    label="السعة القصوى (اختياري)"
                    placeholder="مثال: 30"
                    :value="$section->capacity"
                    min="1"
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
                    href="{{ route('dashboard.sections.index') }}"
                    variant="outline"
                >
                    إلغاء
                </x-ui.button>
            </div>
        </form>
    </x-ui.card>
</x-layouts.dashboard>
