<x-layouts.dashboard page-title="تفاصيل المنهج الدراسي">
    <x-slot name="breadcrumbs">
        <x-ui.breadcrumbs :items="[
            ['label' => 'لوحة التحكم', 'url' => route('dashboard.index'), 'icon' => 'fas fa-home'],
            [
                'label' => 'المناهج الدراسية',
                'url' => route('dashboard.curriculums.index'),
                'icon' => 'fas fa-book-open',
            ],
            ['label' => 'تفاصيل المنهج', 'icon' => 'fas fa-eye'],
        ]" />
    </x-slot>

    <x-ui.main-content-header
        title="   تفاصيل المنهج الدراسي"
        description="عرض المواد الدراسية في المنهج و إمكانية إضافة مواد اخرى"
        buttonText="رجوع"
        buttonLink="{{ route('dashboard.curriculums.index') }}"
    />

    <!-- معلومات المنهج -->
    <x-ui.card class="mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="text-sm font-medium text-gray-500 dark:text-gray-400">السنة الدراسية</label>
                <p class="text-lg font-semibold text-gray-900 dark:text-white mt-1">
                    {{ $curriculum->academicYear->name }}</p>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-500 dark:text-gray-400">المرحلة</label>
                <p class="text-lg font-semibold text-gray-900 dark:text-white mt-1">
                    {{ $curriculum->grade->stage->name }}</p>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-500 dark:text-gray-400">الصف الدراسي</label>
                <p class="text-lg font-semibold text-gray-900 dark:text-white mt-1">{{ $curriculum->grade->name }}</p>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-500 dark:text-gray-400">الفصل الدراسي</label>
                <p class="mt-1">
                    <x-ui.badge variant="info">
                        {{ $curriculum->academicTerm->name }}
                    </x-ui.badge>
                </p>
            </div>
        </div>
    </x-ui.card>

    <!-- المواد المضافة -->
    <x-ui.card class="mb-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                المواد الدراسية ({{ $curriculum->curriculumSubjects->count() }})
            </h3>
        </div>

        @if ($curriculum->curriculumSubjects->count() > 0)
            <x-table :headers="[['label' => 'اسم المادة'], ['label' => 'الإجراءات']]">
                @foreach ($curriculum->curriculumSubjects as $curriculumSubject)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                        <x-table.td nowrap>
                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $curriculumSubject->subject->name }}
                            </div>
                        </x-table.td>
                        <x-table.td nowrap>
                            <form
                                method="POST"
                                action="{{ route('dashboard.curriculums.remove-subject', [$curriculum, $curriculumSubject]) }}"
                                class="inline"
                                onsubmit="return confirm('هل أنت متأكد من إزالة هذه المادة من المنهج؟')"
                            >
                                @csrf
                                @method('DELETE')
                                <x-table.action-delete
                                    :permissions="\Perm::CurriculumsManageSubjects"
                                    title="إزالة"
                                    type="submit"
                                />
                            </form>
                        </x-table.td>
                    </tr>
                @endforeach
            </x-table>
        @else
            <div class="text-center py-8">
                <p class="text-sm text-gray-500 dark:text-gray-400">لا توجد مواد دراسية في هذا المنهج</p>
            </div>
        @endif
    </x-ui.card>

    <!-- إضافة مادة جديدة -->
    @if ($canAddSubject && $availableSubjects->count() > 0 && auth()->user()->can(\Perm::CurriculumsManageSubjects))
        <x-ui.card>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                إضافة مادة للمنهج
            </h3>

            <form
                method="POST"
                action="{{ route('dashboard.curriculums.add-subject', $curriculum) }}"
            >
                @csrf

                <div class="flex items-end gap-4">
                    <div class="flex-1">
                        <x-form.select
                            name="subject_id"
                            label="اختر مادة دراسية"
                            :options="$availableSubjects->pluck('name', 'id')->toArray()"
                            selected="{{ old('subject_id') }}"
                            required
                        />
                    </div>
                    <div>
                        <x-ui.button
                            type="submit"
                            variant="primary"
                            :permissions="\Perm::CurriculumsManageSubjects"
                        >
                            <i class="fas fa-plus mr-2"></i>
                            إضافة
                        </x-ui.button>
                    </div>
                </div>
            </form>
        </x-ui.card>
    @endif

</x-layouts.dashboard>
