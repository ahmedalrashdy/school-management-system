<x-layouts.dashboard page-title="ملف الطالب">
    <x-slot name="breadcrumbs">
        <x-ui.breadcrumbs :items="[
            ['label' => 'لوحة التحكم', 'url' => route('dashboard.index'), 'icon' => 'fas fa-home'],
            ['label' => 'الطلاب', 'url' => route('dashboard.students.index'), 'icon' => 'fas fa-user-graduate'],
            ['label' => 'ملف الطالب', 'icon' => 'fas fa-eye'],
        ]" />
    </x-slot>

    <div x-data="{ activeTab: 'info' }">
        <x-ui.main-content-header
            title="ملف الطالب"
            description="عرض تفاصيل الطالب: {{ $student->user->full_name }}"
            button-text="رجوع"
            button-link="{{ route('dashboard.students.index') }}"
        />

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
                        @click="activeTab = 'guardians'"
                        :class="activeTab === 'guardians' ? 'border-primary-500 text-primary-600 dark:text-primary-400' :
                            'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition"
                    >
                        <i class="fas fa-users mr-2"></i>
                        أولياء الأمور
                    </button>
                    <button
                        @click="activeTab = 'grades'"
                        :class="activeTab === 'grades' ? 'border-primary-500 text-primary-600 dark:text-primary-400' :
                            'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition"
                    >
                        <i class="fas fa-graduation-cap mr-2"></i>
                        الدرجات
                    </button>
                    <button
                        @click="activeTab = 'attendance'"
                        :class="activeTab === 'attendance' ? 'border-primary-500 text-primary-600 dark:text-primary-400' :
                            'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition"
                    >
                        <i class="fas fa-calendar-check mr-2"></i>
                        الحضور والغياب
                    </button>
                    <button
                        @click="activeTab = 'academic'"
                        :class="activeTab === 'academic' ? 'border-primary-500 text-primary-600 dark:text-primary-400' :
                            'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition"
                    >
                        <i class="fas fa-book mr-2"></i>
                        السجل الأكاديمي
                    </button>
                </nav>
            </div>
        </div>

        <!-- Tab Content: المعلومات الشخصية والأكاديمية -->
        <div
            x-show="activeTab === 'info'"
            x-cloak
        >
            <livewire:common.student-profile.student-info :$student />

        </div>

        <!-- Tab Content: أولياء الأمور -->
        <div
            x-show="activeTab === 'guardians'"
            x-cloak
        >
            <livewire:common.student-profile.student-guardians
                :student-id="$student->id"
                lazy
            />
        </div>

        <!-- Tab Content: الدرجات -->
        <div
            x-show="activeTab === 'grades'"
            x-cloak
        >
            <livewire:common.student-profile.student-grades
                :student-id="$student->id"
                lazy
            />
        </div>

        <!-- Tab Content: الحضور والغياب -->
        <div
            x-show="activeTab === 'attendance'"
            x-cloak
        >
            <livewire:common.student-profile.student-attendances-details
                :student-id="$student->id"
                lazy
            />
        </div>



        <!-- Tab Content: السجل الأكاديمي -->
        <div
            x-show="activeTab === 'academic'"
            x-cloak
        >
            <livewire:common.student-profile.student-academic-record
                :student-id="$student->id"
                lazy
            />
        </div>

    </div>
</x-layouts.dashboard>
