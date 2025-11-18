<x-layouts.dashboard page-title="تفاصيل درجات الطالب">
    <x-slot name="breadcrumbs">
        <x-ui.breadcrumbs :items="[
            ['label' => 'لوحة التحكم', 'url' => route('dashboard.index'), 'icon' => 'fas fa-home'],
            ['label' => 'كشوف الدرجات', 'url' => route('dashboard.marks.index'), 'icon' => 'fas fa-file-alt'],
            ['label' => 'كشف درجات الشعبة', 'url' => route('dashboard.marks.show', $section), 'icon' => 'fas fa-eye'],
            ['label' => 'تفاصيل الطالب', 'icon' => 'fas fa-user'],
        ]" />
    </x-slot>

    <!-- رأس الصفحة -->
    <div class="mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    بطاقة الأداء الأكاديمي
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    تقرير تفصيلي لدرجات ومعايرة الامتحانات
                </p>
            </div>
            <x-ui.button
                as="a"
                href="{{ route('dashboard.marks.show', $section) }}"
                variant="outline"
            >
                <i class="fas fa-arrow-right ml-2"></i> عودة للكشف المجمع
            </x-ui.button>
        </div>
    </div>

    <!-- بطاقة الطالب -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
        <div class="flex items-center gap-4">
            <div class="h-16 w-16 rounded-full bg-primary-100 dark:bg-primary-900 flex items-center justify-center text-primary-600 dark:text-primary-300 text-2xl font-bold">
                {{ substr($student->user->first_name, 0, 1) }}
            </div>
            <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                    {{ $student->user->full_name }}
                </h2>
                <div class="flex flex-wrap gap-4 mt-2 text-sm text-gray-500 dark:text-gray-400">
                    <span class="flex items-center gap-1">
                        <i class="fas fa-id-card text-gray-400"></i>
                        {{ $student->admission_number }}
                    </span>
                    <span class="flex items-center gap-1">
                        <i class="fas fa-layer-group text-gray-400"></i>
                        {{ $section->grade->name }}
                    </span>
                    <span class="flex items-center gap-1">
                        <i class="fas fa-chalkboard text-gray-400"></i>
                        الشعبة ({{ $section->name }})
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- الجدول التفاعلي الرئيسي -->
    <x-student.marks-details :subjects="$subjects" :section="$section" />
</x-layouts.dashboard>
