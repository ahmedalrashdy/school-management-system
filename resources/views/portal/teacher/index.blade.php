<x-layouts.portal pageTitle="لوحة تحكم المدرس">
    <x-slot name="sidebar">
        <x-ui.portal-sidebar portalType="teacher" />
    </x-slot>

    <div class="space-y-6">
        <!-- Welcome Section -->
        <div class="bg-gradient-to-l from-primary-500 to-primary-600 rounded-lg p-6 text-white">
            <h2 class="text-2xl font-bold mb-2">مرحباً، {{ auth()->user()->first_name }}</h2>
            <p class="text-primary-100">هذه هي لوحة التحكم الخاصة بك كمدرس</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <x-ui.stat-card icon="fas fa-door-open" title="عدد الشعب" value="3" color="primary" />
            <x-ui.stat-card icon="fas fa-calendar-check" title="الحصص اليوم" value="5" color="success" />
            <x-ui.stat-card icon="fas fa-clipboard-list" title="المهام المعلقة" value="12" color="warning" />
            <x-ui.stat-card icon="fas fa-user-graduate" title="إجمالي الطلاب" value="85" color="info" />
        </div>

        <!-- Today's Schedule -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    <i class="fas fa-calendar-day text-primary-500 mr-2"></i>
                    جدول الحصص اليوم
                </h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-12 h-12 bg-primary-100 dark:bg-primary-900/30 rounded-lg flex items-center justify-center">
                                <span class="font-bold text-primary-600 dark:text-primary-400">1</span>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">الرياضيات - الصف الخامس أ</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">08:00 - 08:45</p>
                            </div>
                        </div>
                        <span class="text-sm text-gray-500 dark:text-gray-400">قاعة 101</span>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-12 h-12 bg-success-100 dark:bg-success-900/30 rounded-lg flex items-center justify-center">
                                <span class="font-bold text-success-600 dark:text-success-400">2</span>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">الرياضيات - الصف الخامس ب</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">09:00 - 09:45</p>
                            </div>
                        </div>
                        <span class="text-sm text-gray-500 dark:text-gray-400">قاعة 102</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tasks List -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    <i class="fas fa-tasks text-warning-500 mr-2"></i>
                    قائمة المهام
                </h3>
            </div>
            <div class="p-6">
                <div class="space-y-3">
                    <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <input type="checkbox" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                        <div class="flex-1">
                            <p class="font-medium text-gray-900 dark:text-white">تسجيل حضور طلاب الصف الخامس أ</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">الحصة الأولى - اليوم</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <input type="checkbox" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                        <div class="flex-1">
                            <p class="font-medium text-gray-900 dark:text-white">إدخال درجات امتحان الرياضيات</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">قبل نهاية الأسبوع</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.portal>