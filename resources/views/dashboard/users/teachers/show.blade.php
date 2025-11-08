<x-layouts.dashboard page-title="ملف المدرس">
    <x-slot name="breadcrumbs">
        <x-ui.breadcrumbs :items="[
            ['label' => 'لوحة التحكم', 'url' => route('dashboard.index'), 'icon' => 'fas fa-home'],
            ['label' => 'المدرسون', 'url' => route('dashboard.teachers.index'), 'icon' => 'fas fa-chalkboard-teacher'],
            ['label' => 'ملف المدرس', 'icon' => 'fas fa-eye'],
        ]" />
    </x-slot>

    <div x-data="{ activeTab: 'info' }">
        <x-ui.main-content-header
            title="ملف المدرس"
            description="عرض تفاصيل المدرس: {{ $teacher->user->full_name }}"
        >
            <x-slot:actions>
                <x-ui.button
                    as="a"
                    href="{{ route('dashboard.teachers.index') }}"
                    wire:navigate
                >رجوع</x-ui.button>
            </x-slot:actions>
        </x-ui.main-content-header>

        <!-- Tabs -->
        <div class="mb-6">
            <div class="border-b border-gray-200 dark:border-gray-700">
                <nav
                    class="-mb-px flex space-x-8"
                    aria-label="Tabs"
                >
                    <button
                        @click="activeTab = 'info'"
                        :class="activeTab === 'info' ? 'border-primary-500 text-primary-600 dark:text-primary-400' :
                            'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition"
                    >
                        <i class="fas fa-user mr-2"></i>
                        المعلومات الشخصية والأكاديمية
                    </button>
                    <button
                        @click="activeTab = 'assignments'"
                        :class="activeTab === 'assignments' ? 'border-primary-500 text-primary-600 dark:text-primary-400' :
                            'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition"
                    >
                        <i class="fas fa-tasks mr-2"></i>
                        التعيينات الدراسية
                    </button>
                    <button
                        @click="activeTab = 'timetable'"
                        :class="activeTab === 'timetable' ? 'border-primary-500 text-primary-600 dark:text-primary-400' :
                            'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition"
                    >
                        <i class="fas fa-calendar-alt mr-2"></i>
                        الجدول الدراسي
                    </button>
                </nav>
            </div>
        </div>

        <!-- Tab Content: المعلومات الشخصية والأكاديمية -->
        <div
            x-show="activeTab === 'info'"
            x-cloak
        >
            <livewire:common.teacher-profile.teacher-info :$teacher />
        </div>

        <!-- Tab Content: التعيينات الدراسية -->
        <div
            x-show="activeTab === 'assignments'"
            x-cloak
        >
            <livewire:common.teacher-profile.teacher-assignments
                :teacher_id="$teacher->id"
                lazy
            />
        </div>

        <!-- Tab Content: الجدول الدراسي -->
        <div
            x-show="activeTab === 'timetable'"
            x-cloak
        >
            <livewire:common.teacher-profile.teacher-timetable
                :teacher_id="$teacher->id"
                lazy
            />
        </div>
    </div>
</x-layouts.dashboard>
