<x-ui.card title="إجراءات سريعة" icon="fas fa-bolt">
    <div class="space-y-2">
        <a href="{{ route('dashboard.students.create') }}"
            class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition group">
            <div
                class="w-8 h-8 rounded-lg bg-primary-100 dark:bg-primary-900 flex items-center justify-center group-hover:bg-primary-200 dark:group-hover:bg-primary-800 transition">
                <i class="fas fa-user-plus text-primary-600 dark:text-primary-400"></i>
            </div>
            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">إضافة طالب جديد</span>
        </a>

        <a href="{{ route('dashboard.exams.create') }}"
            class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition group">
            <div
                class="w-8 h-8 rounded-lg bg-warning-100 dark:bg-warning-900 flex items-center justify-center group-hover:bg-warning-200 dark:group-hover:bg-warning-800 transition">
                <i class="fas fa-file-alt text-warning-600 dark:text-warning-400"></i>
            </div>
            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">إنشاء امتحان</span>
        </a>

        <a href="{{ route('dashboard.teachers.index') }}"
            class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition group">
            <div
                class="w-8 h-8 rounded-lg bg-success-100 dark:bg-success-900 flex items-center justify-center group-hover:bg-success-200 dark:group-hover:bg-success-800 transition">
                <i class="fas fa-chalkboard-teacher text-success-600 dark:text-success-400"></i>
            </div>
            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">إدارة المعلمين</span>
        </a>

        <a href="{{ route('dashboard.attendance-dashboard.index') }}"
            class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition group">
            <div
                class="w-8 h-8 rounded-lg bg-info-100 dark:bg-info-900 flex items-center justify-center group-hover:bg-info-200 dark:group-hover:bg-info-800 transition">
                <i class="fas fa-clipboard-check text-info-600 dark:text-info-400"></i>
            </div>
            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">رصد الحضور</span>
        </a>
    </div>
</x-ui.card>
