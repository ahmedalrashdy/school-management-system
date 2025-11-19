<x-layouts.portal pageTitle="ملف الطالب">
    <x-slot name="sidebar">
        <x-ui.portal-sidebar portalType="guardian" />
    </x-slot>

    <div class="space-y-6">
        <!-- Tabs -->
        <div
            x-data="{ activeTab: 'info' }"
            class="space-y-6"
        >
            <div class="border-b border-gray-200 dark:border-gray-700">
                <nav
                    class="-mb-px flex space-x-8 space-x-reverse"
                    aria-label="Tabs"
                >
                    <button
                        @click="activeTab = 'info'"
                        :class="activeTab === 'info' ? 'border-primary-500 text-primary-600 dark:text-primary-400' :
                            'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors"
                    >
                        <i class="fas fa-info-circle mr-2"></i>
                        المعلومات الأساسية
                    </button>

                    <button
                        @click="activeTab = 'guardians'"
                        :class="activeTab === 'guardians' ? 'border-primary-500 text-primary-600 dark:text-primary-400' :
                            'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors"
                    >
                        <i class="fas fa-users mr-2"></i>
                        أولياء الأمور
                    </button>
                </nav>
            </div>

            <!-- Tab Panels -->
            <div
                x-show="activeTab === 'info'"
                x-transition.opacity
            >
                <livewire:common.student-profile.student-info
                    :student="$student"
                    context="portal"
                />
            </div>

            <div
                x-show="activeTab === 'guardians'"
                x-transition.opacity
                style="display: none;"
            >
                <livewire:common.student-profile.student-guardians
                    :student-id="$student->id"
                    context="portal"
                />
            </div>
        </div>
    </div>
</x-layouts.portal>
