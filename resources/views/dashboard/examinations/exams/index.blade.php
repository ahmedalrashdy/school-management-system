<x-layouts.dashboard page-title="الامتحانات">
    <x-slot name="breadcrumbs">
        <x-ui.breadcrumbs :items="[
        ['label' => 'لوحة التحكم', 'url' => route('dashboard.index'), 'icon' => 'fas fa-home'],
        ['label' => 'إدارة الامتحانات', 'icon' => 'fas fa-clipboard-list'],
    ]" />
    </x-slot>

    <x-ui.main-content-header title="الامتحانات" description="إدارة الامتحانات وأنواعها في النظام" />

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Card: إدارة الامتحانات -->
        <a href="{{ route('dashboard.exams.list') }}" class="group">
            <x-ui.card class="h-full hover:shadow-lg transition-shadow duration-300 cursor-pointer">
                <div class="flex items-start gap-4">
                    <div
                        class="flex-shrink-0 w-16 h-16 rounded-xl bg-primary-100 dark:bg-primary-900 flex items-center justify-center group-hover:bg-primary-200 dark:group-hover:bg-primary-800 transition-colors">
                        <i class="fas fa-file-alt text-3xl text-primary-600 dark:text-primary-400"></i>
                    </div>
                    <div class="flex-1">
                        <h3
                            class="text-xl font-bold text-gray-900 dark:text-white mb-2 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
                            إدارة الامتحانات
                        </h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                            عرض وإنشاء وتعديل الامتحانات مع إمكانية الفلترة الذكية حسب السنة الدراسية، الفصل، الصف،
                            الشعبة، ونوع الامتحان.
                        </p>
                        <div class="flex items-center gap-2 text-primary-600 dark:text-primary-400 font-medium text-sm">
                            <span>الانتقال للإدارة</span>
                            <i class="fas fa-arrow-left group-hover:translate-x-[-4px] transition-transform"></i>
                        </div>
                    </div>
                </div>
            </x-ui.card>
        </a>

        <!-- Card: إدارة أنواع الامتحانات -->
        <a href="{{ route('dashboard.exam-types.index') }}" class="group">
            <x-ui.card class="h-full hover:shadow-lg transition-shadow duration-300 cursor-pointer">
                <div class="flex items-start gap-4">
                    <div
                        class="flex-shrink-0 w-16 h-16 rounded-xl bg-success-100 dark:bg-success-900 flex items-center justify-center group-hover:bg-success-200 dark:group-hover:bg-success-800 transition-colors">
                        <i class="fas fa-cog text-3xl text-success-600 dark:text-success-400"></i>
                    </div>
                    <div class="flex-1">
                        <h3
                            class="text-xl font-bold text-gray-900 dark:text-white mb-2 group-hover:text-success-600 dark:group-hover:text-success-400 transition-colors">
                            إدارة أنواع الامتحانات
                        </h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                            إدارة أنواع التقييمات المستخدمة في المدرسة مثل الامتحانات الشهرية، النهائية، الاختبارات
                            القصيرة، والمشاريع العملية.
                        </p>
                        <div class="flex items-center gap-2 text-success-600 dark:text-success-400 font-medium text-sm">
                            <span>الانتقال للإدارة</span>
                            <i class="fas fa-arrow-left group-hover:translate-x-[-4px] transition-transform"></i>
                        </div>
                    </div>
                </div>
            </x-ui.card>
        </a>

        <!-- Card: إدارة سياسات الدرجات -->
        <a href="{{ route('dashboard.grading-rules.index') }}" class="group">
            <x-ui.card class="h-full hover:shadow-lg transition-shadow duration-300 cursor-pointer">
                <div class="flex items-start gap-4">
                    <div
                        class="flex-shrink-0 w-16 h-16 rounded-xl bg-warning-100 dark:bg-warning-900 flex items-center justify-center group-hover:bg-warning-200 dark:group-hover:bg-warning-800 transition-colors">
                        <i class="fas fa-calculator text-3xl text-warning-600 dark:text-warning-400"></i>
                    </div>
                    <div class="flex-1">
                        <h3
                            class="text-xl font-bold text-gray-900 dark:text-white mb-2 group-hover:text-warning-600 dark:group-hover:text-warning-400 transition-colors">
                            إدارة سياسات الدرجات
                        </h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                            تحديد قواعد احتساب الدرجات، توزيع الأوزان بين أعمال الفصل والاختبار النهائي، والمعايرة التلقائية.
                        </p>
                        <div class="flex items-center gap-2 text-warning-600 dark:text-warning-400 font-medium text-sm">
                            <span>الانتقال للإدارة</span>
                            <i class="fas fa-arrow-left group-hover:translate-x-[-4px] transition-transform"></i>
                        </div>
                    </div>
                </div>
            </x-ui.card>
        </a>
    </div>
</x-layouts.dashboard>
