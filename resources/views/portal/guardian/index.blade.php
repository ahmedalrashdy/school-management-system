<x-layouts.portal pageTitle="لوحة تحكم ولي الأمر">
    <x-slot name="sidebar">
        <x-ui.portal-sidebar portalType="guardian" />
    </x-slot>

    <div class="space-y-6">
        <!-- Welcome Section -->
        <div class="bg-gradient-to-l from-primary-500 to-primary-600 rounded-lg p-6 text-white">
            <h2 class="text-2xl font-bold mb-2">مرحباً، {{ auth()->user()->first_name }}</h2>
            <p class="text-primary-100">هذه هي لوحة التحكم الخاصة بك كولي أمر</p>
        </div>

        <!-- Children List -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    <i class="fas fa-users text-primary-500 mr-2"></i>
                    الأبناء المسجلين
                </h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-16 h-16 bg-primary-100 dark:bg-primary-900/30 rounded-lg flex items-center justify-center">
                                <i class="fas fa-user-graduate text-primary-600 dark:text-primary-400 text-2xl"></i>
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold text-gray-900 dark:text-white">أحمد محمد</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">الصف الخامس - شعبة أ</p>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">رقم القيد: 12345</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-16 h-16 bg-success-100 dark:bg-success-900/30 rounded-lg flex items-center justify-center">
                                <i class="fas fa-user-graduate text-success-600 dark:text-success-400 text-2xl"></i>
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold text-gray-900 dark:text-white">فاطمة محمد</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">الصف الثالث - شعبة ب</p>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">رقم القيد: 12346</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Academic Performance Summary -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    <i class="fas fa-chart-line text-info-500 mr-2"></i>
                    ملخص الأداء الأكاديمي
                </h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="flex items-center justify-between mb-2">
                            <p class="font-medium text-gray-900 dark:text-white">أحمد محمد - الصف الخامس أ</p>
                            <span class="text-sm font-semibold text-primary-600 dark:text-primary-400">المعدل:
                                85.5</span>
                        </div>
                        <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                            <i class="fas fa-clipboard-check text-success-500"></i>
                            <span>نسبة الحضور: 95%</span>
                        </div>
                    </div>
                    <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="flex items-center justify-between mb-2">
                            <p class="font-medium text-gray-900 dark:text-white">فاطمة محمد - الصف الثالث ب</p>
                            <span class="text-sm font-semibold text-primary-600 dark:text-primary-400">المعدل:
                                92.0</span>
                        </div>
                        <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                            <i class="fas fa-clipboard-check text-success-500"></i>
                            <span>نسبة الحضور: 98%</span>
                        </div>
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
                            <p class="font-medium text-gray-900 dark:text-white">اجتماع أولياء الأمور</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">سيتم عقد اجتماع أولياء الأمور يوم الأحد
                                القادم الساعة 10:00 صباحاً</p>
                        </div>
                    </div>
                    <div
                        class="flex items-start gap-3 p-3 bg-info-50 dark:bg-info-900/20 rounded-lg border border-info-200 dark:border-info-800">
                        <i class="fas fa-info-circle text-info-600 dark:text-info-400 mt-1"></i>
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white">تقرير شهري</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">تم رفع التقرير الشهري لأداء الأبناء،
                                يرجى مراجعته</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.portal>