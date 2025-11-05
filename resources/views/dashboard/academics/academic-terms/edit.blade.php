<x-layouts.dashboard page-title="تعديل فصل دراسي">
    <x-slot name="breadcrumbs">
        <x-ui.breadcrumbs :items="[
            ['label' => 'لوحة التحكم', 'url' => route('dashboard.index'), 'icon' => 'fas fa-home'],
            [
                'label' => 'الفصول الدراسية',
                'url' => route('dashboard.academic-terms.index'),
                'icon' => 'fas fa-calendar-week',
            ],
            ['label' => 'تعديل فصل دراسي', 'icon' => 'fas fa-edit'],
        ]" />
    </x-slot>

    <x-ui.main-content-header
        title="تعديل فصل دراسي"
        description="تعديل بيانات الفصل الدراسي: {{ $academicTerm->name }}"
        button-text="رجوع"
        button-link="{{ route('dashboard.academic-terms.index') }}"
    />

    <x-ui.card>
        <form
            method="POST"
            action="{{ route('dashboard.academic-terms.update', $academicTerm) }}"
        >
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-form.select
                    name="academic_year_id"
                    label="السنة الدراسية"
                    :options="lookup()->getActiveAndUpcomingYearsOnly()"
                    :selected="$academicTerm->academic_year_id"
                    required
                />

                <x-form.input
                    name="name"
                    label="اسم الفصل الدراسي"
                    placeholder="مثال: الترم الأول 2024-2025"
                    :value="$academicTerm->name"
                    required
                />

                <x-form.input
                    name="start_date"
                    label="تاريخ البداية"
                    type="date"
                    :value="$academicTerm->start_date->format('Y-m-d')"
                    required
                />

                <x-form.input
                    name="end_date"
                    label="تاريخ النهاية"
                    type="date"
                    :value="$academicTerm->end_date->format('Y-m-d')"
                    required
                />
            </div>

            <div class="mt-6 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                <div class="flex items-start gap-3">
                    <i class="fas fa-info-circle text-blue-600 dark:text-blue-400 mt-1"></i>
                    <div>
                        <h4 class="font-semibold text-blue-900 dark:text-blue-300 mb-2">
                            ملاحظة مهمة
                        </h4>
                        <p class="text-sm text-blue-800 dark:text-blue-400">
                            عند تعديل تواريخ الفصل الدراسي، سيتم مزامنة الأيام الدراسية تلقائياً:
                        </p>
                        <ul class="text-sm text-blue-800 dark:text-blue-400 mt-2 list-disc list-inside">
                            <li>إذا قمت بتمديد الفترة الزمنية، سيتم إضافة الأيام الجديدة فقط.</li>
                            <li>إذا قمت بتقليص الفترة الزمنية، سيتم حذف الأيام الزائدة فقط إذا لم تكن مرتبطة بسجلات
                                حضور.</li>
                            <li>إذا كان هناك سجلات حضور مرتبطة بالأيام المراد حذفها، سيتم رفض التعديل.</li>
                        </ul>
                    </div>
                </div>
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
                    href="{{ route('dashboard.academic-terms.index') }}"
                    variant="outline"
                >
                    إلغاء
                </x-ui.button>
            </div>
        </form>
    </x-ui.card>
</x-layouts.dashboard>
