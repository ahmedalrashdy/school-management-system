<x-layouts.portal pageTitle="لوحة تحكم الطالب">
    <x-slot name="sidebar">
        <x-ui.portal-sidebar portalType="student" />
    </x-slot>

    <div class="space-y-6">
        <!-- Welcome Section -->
        <div class="bg-gradient-to-l from-primary-500 to-primary-600 rounded-lg p-6 text-white">
            <h2 class="text-2xl font-bold mb-2">مرحباً، {{ auth()->user()->first_name }}</h2>
            <p class="text-primary-100">هذه هي لوحة التحكم الخاصة بك كطالب</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <x-ui.stat-card icon="fas fa-book" title="عدد المواد" value="8" color="primary" />
            <x-ui.stat-card icon="fas fa-clipboard-check" title="نسبة الحضور" value="95%" color="success" />
            <x-ui.stat-card icon="fas fa-star" title="المعدل التراكمي" value="85.5" color="info" />
            <x-ui.stat-card icon="fas fa-calendar-alt" title="الحصص اليوم" value="6" color="warning" />
        </div>

        <!-- Upcoming Classes -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    <i class="fas fa-calendar-alt text-primary-500 mr-2"></i>
                    الحصص القادمة
                </h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-12 h-12 bg-primary-100 dark:bg-primary-900/30 rounded-lg flex items-center justify-center">
                                <i class="fas fa-book text-primary-600 dark:text-primary-400"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">الرياضيات</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">الحصة الأولى - 08:00</p>
                            </div>
                        </div>
                        <span class="text-sm text-gray-500 dark:text-gray-400">قاعة 101</span>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-12 h-12 bg-success-100 dark:bg-success-900/30 rounded-lg flex items-center justify-center">
                                <i class="fas fa-flask text-success-600 dark:text-success-400"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">العلوم</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">الحصة الثانية - 09:00</p>
                            </div>
                        </div>
                        <span class="text-sm text-gray-500 dark:text-gray-400">قاعة 205</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Important Notices -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    <i class="fas fa-bell text-warning-500 mr-2"></i>
                    إشعارات مهمة
                </h3>
            </div>
            <div class="p-6">
                <div class="space-y-3">
                    <div
                        class="flex items-start gap-3 p-3 bg-warning-50 dark:bg-warning-900/20 rounded-lg border border-warning-200 dark:border-warning-800">
                        <i class="fas fa-exclamation-triangle text-warning-600 dark:text-warning-400 mt-1"></i>
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white">امتحان نهائي قريب</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">امتحان الرياضيات النهائي يوم الأحد
                                القادم</p>
                        </div>
                    </div>
                    <div
                        class="flex items-start gap-3 p-3 bg-info-50 dark:bg-info-900/20 rounded-lg border border-info-200 dark:border-info-800">
                        <i class="fas fa-info-circle text-info-600 dark:text-info-400 mt-1"></i>
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white">تذكير</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">يرجى تسليم الواجب المنزلي قبل نهاية
                                الأسبوع</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.portal>