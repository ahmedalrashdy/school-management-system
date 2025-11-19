<x-layouts.portal pageTitle="اختيار الابن">
    <x-slot name="sidebar">
        <x-ui.portal-sidebar portalType="guardian" />
    </x-slot>

    @php
        // Build routes with placeholder '___STUDENT_ID___' that will be replaced in JS
        $routesMap = [];
        foreach ($availableDestinations as $key => $destination) {
            $routesMap[$key] = route($destination['route'], ['student' => '___STUDENT_ID___']);
        }
    @endphp

    <div
        class="space-y-6"
        x-data="{
            selectedStudent: null,
            selectedDestination: @js($preselectedDestination),
            availableDestinations: @js($availableDestinations),
            routesMap: @js($routesMap),
            get destinationRoute() {
                if (!this.selectedStudent || !this.selectedDestination) return null;
                const baseRoute = this.routesMap[this.selectedDestination];
                if (!baseRoute) return null;
                return baseRoute.replace('___STUDENT_ID___', this.selectedStudent);
            },
            canNavigate() {
                return this.selectedStudent && this.selectedDestination;
            },
            navigate() {
                if (this.canNavigate()) {
                    window.location.href = this.destinationRoute;
                }
            },
            getDestinationIconColor(destination) {
                const colors = {
                    'info': 'bg-info-100 dark:bg-info-900/30 text-info-600 dark:text-info-400',
                    'success': 'bg-success-100 dark:bg-success-900/30 text-success-600 dark:text-success-400',
                    'warning': 'bg-warning-100 dark:bg-warning-900/30 text-warning-600 dark:text-warning-400',
                    'primary': 'bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400',
                    'danger': 'bg-danger-100 dark:bg-danger-900/30 text-danger-600 dark:text-danger-400',
                };
                return colors[destination.iconColor] || colors.primary;
            }
        }"
    >
        <!-- Header -->
        <div class="bg-linear-to-l from-primary-500 to-primary-600 rounded-lg p-6 text-white">
            <h2 class="text-2xl font-bold mb-2">اختيار الابن</h2>
            <p class="text-primary-100">اختر الابن والوجهة المطلوبة</p>
        </div>

        <!-- Students List -->
        @if ($students->count() > 0)
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Students Selection -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        <i class="fas fa-user-graduate text-primary-500 mr-2"></i>
                        اختر الابن
                    </h3>
                    <div class="space-y-3">
                        @foreach ($students as $student)
                            @php
                                $section = $student->currentSection();
                            @endphp
                            <button
                                type="button"
                                @click="selectedStudent = {{ $student->id }}"
                                :class="selectedStudent === {{ $student->id }} ?
                                    'bg-primary-50 dark:bg-primary-900/20 border-primary-500 dark:border-primary-400' :
                                    'bg-gray-50 dark:bg-gray-700 border-gray-200 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-600'"
                                class="w-full p-4 rounded-lg border-2 transition text-right"
                            >
                                <div class="flex items-center gap-4">
                                    <div
                                        class="w-12 h-12 bg-primary-100 dark:bg-primary-900/30 rounded-lg flex items-center justify-center shrink-0">
                                        <i class="fas fa-user-graduate text-primary-600 dark:text-primary-400"></i>
                                    </div>
                                    <div class="flex-1 text-right">
                                        <p class="font-semibold text-gray-900 dark:text-white">
                                            {{ $student->user->full_name }}
                                        </p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                            @if ($section)
                                                {{ $section->grade->name }} - شعبة {{ $section->name }}
                                            @else
                                                غير مسجل في شعبة
                                            @endif
                                        </p>
                                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                                            رقم القيد: {{ $student->admission_number }}
                                        </p>
                                    </div>
                                    <div
                                        x-show="selectedStudent === {{ $student->id }}"
                                        class="shrink-0"
                                    >
                                        <i
                                            class="fas fa-check-circle text-primary-600 dark:text-primary-400 text-xl"></i>
                                    </div>
                                </div>
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- Destination Selection -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        <i class="fas fa-route text-primary-500 mr-2"></i>
                        اختر الوجهة
                    </h3>
                    <div class="space-y-3">
                        <template
                            x-for="(destination, key) in availableDestinations"
                            :key="key"
                        >
                            <button
                                type="button"
                                @click="selectedDestination = key"
                                :class="selectedDestination === key ?
                                    'bg-primary-50 dark:bg-primary-900/20 border-primary-500 dark:border-primary-400' :
                                    'bg-gray-50 dark:bg-gray-700 border-gray-200 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-600'"
                                class="w-full p-4 rounded-lg border-2 transition text-right"
                            >
                                <div class="flex items-center gap-4">
                                    <div
                                        :class="getDestinationIconColor(destination)"
                                        class="w-12 h-12 rounded-lg flex items-center justify-center shrink-0"
                                    >
                                        <i :class="destination.icon + ' text-xl'"></i>
                                    </div>
                                    <div class="flex-1 text-right">
                                        <p
                                            class="font-semibold text-gray-900 dark:text-white"
                                            x-text="destination.label"
                                        >
                                        </p>
                                        <p
                                            class="text-sm text-gray-500 dark:text-gray-400 mt-1"
                                            x-text="destination.description"
                                        ></p>
                                    </div>
                                    <div
                                        x-show="selectedDestination === key"
                                        class="shrink-0"
                                    >
                                        <i
                                            class="fas fa-check-circle text-primary-600 dark:text-primary-400 text-xl"></i>
                                    </div>
                                </div>
                            </button>
                        </template>
                    </div>

                    <!-- Navigate Button -->
                    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <x-ui.button
                            type="button"
                            x-on:click="navigate()"
                            x-bind:disabled="!canNavigate()"
                            variant="primary"
                            class="w-full"
                        >
                            <i class="fas fa-arrow-left"></i>
                            الانتقال
                        </x-ui.button>
                    </div>
                </div>
            </div>
        @else
            <x-ui.card>
                <x-ui.empty-state
                    icon="fas fa-users"
                    title="لا يوجد أبناء مسجلين"
                    description="لا يوجد أبناء مرتبطين بحسابك"
                />
            </x-ui.card>
        @endif
    </div>
</x-layouts.portal>
