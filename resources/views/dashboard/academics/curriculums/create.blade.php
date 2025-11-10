<x-layouts.dashboard page-title="إضافة منهج دراسي جديد">
    <x-slot name="breadcrumbs">
        <x-ui.breadcrumbs :items="[
            ['label' => 'لوحة التحكم', 'url' => route('dashboard.index'), 'icon' => 'fas fa-home'],
            [
                'label' => 'المناهج الدراسية',
                'url' => route('dashboard.curriculums.index'),
                'icon' => 'fas fa-book-open',
            ],
            ['label' => 'إضافة منهج جديد', 'icon' => 'fas fa-plus'],
        ]" />
    </x-slot>

    <x-ui.main-content-header
        title="إضافة منهج دراسي جديد"
        description="إنشاء منهج دراسي جديد في النظام"
        button-text="إضافة منهج دراسي"
        button-link="{{ route('dashboard.curriculums.index') }}"
    />


    <x-ui.card>
        <form
            method="POST"
            action="{{ route('dashboard.curriculums.store') }}"
        >
            @csrf

            <div
                x-data="academicController({
                    yearsTree: {{ lookup()->activeAndUpcomingYearsTree()->toJson() }},
                    defaultYear: '{{ request('academic_year_id', school()->activeYear()?->id) }}',
                    defaultTerm: '{{ request('academic_term_id', school()->currentAcademicTerm()?->id) }}',
                })"
                class="grid grid-cols-1 md:grid-cols-3 gap-6"
            >
                <x-form.select
                    name="academic_year_id"
                    label="السنة الدراسية"
                    :options="lookup()->activeAndUpcomingYearsTree()->pluck('name', 'id')"
                    required
                    x-bind="yearInput"
                />

                <x-form.select
                    name="grade_id"
                    label="الصف الدراسي"
                    :options="lookup()->getGrades()"
                    required
                />

                <x-form.select
                    name="academic_term_id"
                    label="الفصل الدراسي"
                    :options="[]"
                    required
                    x-bind="termInput"
                />
            </div>

            <div class="mt-6">
                <x-form.multi-select
                    name="subject_ids"
                    label="المواد الدراسية"
                    :options="$subjects
                        ->mapWithKeys(function ($subject) {
                            return [$subject->id => $subject->name];
                        })
                        ->toArray()"
                    :selected="old('subject_ids', [])"
                    placeholder="اختر المواد الدراسية"
                    search-placeholder="ابحث عن مادة..."
                    :searchable="true"
                    :multiple="true"
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
                    href="{{ route('dashboard.curriculums.index') }}"
                    variant="outline"
                >
                    إلغاء
                </x-ui.button>
            </div>
        </form>
    </x-ui.card>
</x-layouts.dashboard>
