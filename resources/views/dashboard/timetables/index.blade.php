<x-layouts.dashboard>
    <x-slot name="breadcrumbs">
        <x-ui.breadcrumbs :items="[
            ['label' => 'لوحة التحكم', 'url' => route('dashboard.index'), 'icon' => 'fas fa-home'],
            ['label' => 'إدارة الجداول الدراسية', 'icon' => 'fas fa-table'],
        ]" />
    </x-slot>

    <x-ui.main-content-header
        title="إدارة الجداول الدراسية"
        icon="fas fa-table"
    />
    {{-- Main Navigation Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Timetables Management Card --}}
        <x-ui.card class="hover:shadow-lg transition-shadow duration-300">
            <div class="text-center p-6">
                <div
                    class="w-20 h-20 mx-auto mb-4 rounded-full bg-info-100 dark:bg-info-900 flex items-center justify-center">
                    <i class="fas fa-table text-4xl text-info-600 dark:info-primary-400"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
                    إدارة الجداول الدراسية
                </h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                    إنشاء وتعديل وعرض الجداول الدراسية للشعب المختلفة بشكل مرئي وتفاعلي
                </p>
                <div class="flex flex-col gap-2">
                    <x-ui.button
                        as="a"
                        href="{{ route('dashboard.timetables.list') }}"
                        variant="info"
                        :permissions="\Perm::TimetablesView"
                    >
                        <i class="fas fa-list"></i>
                        عرض الجداول
                    </x-ui.button>
                    <x-ui.button
                        as="a"
                        href="{{ route('dashboard.timetables.create') }}"
                        variant="outline"
                        :permissions="\Perm::TimetablesCreate"
                    >
                        <i class="fas fa-plus"></i>
                        إنشاء جدول جديد
                    </x-ui.button>
                </div>
            </div>
        </x-ui.card>


        {{-- Teacher Assignments Card --}}
        <x-ui.card class="hover:shadow-lg transition-shadow duration-300">
            <div class="text-center p-6">
                <div
                    class="w-20 h-20 mx-auto mb-4 rounded-full bg-success-100 dark:bg-success-900 flex items-center justify-center">
                    <i class="fas fa-chalkboard-teacher text-4xl text-success-600 dark:text-success-400"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
                    تعيينات المدرسين
                </h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                    إدارة تعيين المدرسين للمواد والشعب الدراسية قبل بناء الجداول
                </p>
                <div class="flex flex-col gap-2">
                    <x-ui.button
                        as="a"
                        href="{{ route('dashboard.teacher-assignments.index') }}"
                        variant="success"
                    >
                        <i class="fas fa-list"></i>
                        عرض التعيينات
                    </x-ui.button>
                </div>
            </div>
        </x-ui.card>
        {{-- Timetable Settings Card --}}
        <x-ui.card class="hover:shadow-lg transition-shadow duration-300">
            <div class="text-center p-6">
                <div
                    class="w-20 h-20 mx-auto mb-4 rounded-full bg-primary-100 dark:bg-primary-900 flex items-center justify-center">
                    <i class="fas fa-cog text-4xl text-primary-600 dark:text-primary-400"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
                    إعدادات قوالب النظام
                </h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                    إنشاء وإدارة قوالب الهياكل الزمنية لليوم الدراسي، تحديد أوقات الحصص والفسح
                </p>
                <div class="flex flex-col gap-2">
                    <x-ui.button
                        as="a"
                        href="{{ route('dashboard.timetable-settings.index') }}"
                        variant="primary"
                        :permissions="\Perm::TimetableSettingsManage"
                    >
                        <i class="fas fa-list"></i>
                        عرض القوالب
                    </x-ui.button>
                    <x-ui.button
                        as="a"
                        href="{{ route('dashboard.timetable-settings.create') }}"
                        variant="outline"
                        :permissions="\Perm::TimetableSettingsManage"
                    >
                        <i class="fas fa-plus"></i>
                        إنشاء قالب جديد
                    </x-ui.button>
                </div>
            </div>
        </x-ui.card>

    </div>
</x-layouts.dashboard>
