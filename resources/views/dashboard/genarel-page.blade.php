<x-layouts.dashboard page-title="لوحة التحكم">
    <!-- Welcome Section -->
    <div class="mb-8">
        <div
            class="bg-linear-to-l from-primary-500 to-primary-600 dark:from-primary-600 dark:to-primary-700 rounded-xl shadow-lg p-8 text-white">
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                <div class="flex-1">
                    <h1 class="text-3xl font-bold mb-2">
                        مرحباً بك في نظام إدارة المدرسة
                    </h1>
                    <p class="text-primary-100 text-lg">
                        {{ auth()->user()->first_name }} {{ auth()->user()->last_name }}
                    </p>
                    <p class="text-primary-50 mt-2">
                        {{ now()->translatedFormat('l، d F Y') }}
                    </p>
                </div>
                <div class="flex-shrink-0">
                    <div class="w-20 h-20 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
                        <i class="fas fa-school text-4xl text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Information Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <!-- System Overview Card -->
        <x-ui.card
            title="نظرة عامة على النظام"
            icon="fas fa-info-circle"
        >
            <div class="space-y-3">
                <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">
                    نظام إدارة المدرسة المتكامل يوفر منصة موحدة لإدارة جميع العمليات الأكاديمية والإدارية في المدرسة.
                </p>
                <div class="flex items-center gap-2 text-sm text-primary-600 dark:text-primary-400">
                    <i class="fas fa-check-circle"></i>
                    <span>نظام آمن وموثوق</span>
                </div>
            </div>
        </x-ui.card>

        <!-- Features Card -->
        <x-ui.card
            title="المميزات الرئيسية"
            icon="fas fa-star"
        >
            <div class="space-y-2">
                <div class="flex items-start gap-2">
                    <i class="fas fa-check text-success-500 mt-1"></i>
                    <span class="text-sm text-gray-700 dark:text-gray-300">إدارة شاملة للطلاب والمدرسين</span>
                </div>
                <div class="flex items-start gap-2">
                    <i class="fas fa-check text-success-500 mt-1"></i>
                    <span class="text-sm text-gray-700 dark:text-gray-300">تسجيل الحضور والغياب</span>
                </div>
                <div class="flex items-start gap-2">
                    <i class="fas fa-check text-success-500 mt-1"></i>
                    <span class="text-sm text-gray-700 dark:text-gray-300">إدارة الجداول الدراسية</span>
                </div>
                <div class="flex items-start gap-2">
                    <i class="fas fa-check text-success-500 mt-1"></i>
                    <span class="text-sm text-gray-700 dark:text-gray-300">تسجيل الدرجات والنتائج</span>
                </div>
            </div>
        </x-ui.card>

        <!-- Support Card -->
        <x-ui.card
            title="الدعم والمساعدة"
            icon="fas fa-life-ring"
        >
            <div class="space-y-3">
                <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">
                    إذا كنت بحاجة إلى مساعدة أو لديك استفسار، يرجى التواصل مع إدارة النظام.
                </p>
                <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                    <i class="fas fa-envelope"></i>
                    <span>support@school.com</span>
                </div>
            </div>
        </x-ui.card>
    </div>


    <!-- Footer Message -->
    <div class="mt-8 text-center">
        <p class="text-sm text-gray-500 dark:text-gray-400">
            شكراً لاستخدامك نظام إدارة المدرسة المتكامل
        </p>
    </div>
</x-layouts.dashboard>
