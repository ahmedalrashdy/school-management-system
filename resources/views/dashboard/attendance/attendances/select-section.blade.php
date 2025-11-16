<x-layouts.dashboard page-title="تحضير اليوم الدراسي">
    <x-slot name="breadcrumbs">
        <x-ui.breadcrumbs :items="[
            ['label' => 'لوحة التحكم', 'url' => route('dashboard.index'), 'icon' => 'fas fa-home'],
            [
                'label' => 'التقويم الدراسي',
                'url' => route('dashboard.academic-calendar.index'),
                'icon' => 'fas fa-calendar-alt',
            ],
            ['label' => 'اختيار الشعبة', 'icon' => 'fas fa-users'],
        ]" />
    </x-slot>

    <x-ui.main-content-header
        title="تحضير اليوم الدراسي"
        :description="$formattedDate"
        icon="fas fa-clipboard-check"
        button-text="رجوع"
        button-link="{{ route('dashboard.academic-calendar.index') }}"
    />

    {{-- Date Info Card --}}
    <x-ui.card class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">التاريخ</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $formattedDate }}</p>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-600 dark:text-gray-400">نمط التحضير</p>
                <p class="text-lg font-semibold text-primary-600 dark:text-primary-400">
                    {{ $attendanceMode->label() }}
                </p>
            </div>
        </div>
    </x-ui.card>

    {{-- Selection Form --}}
    <x-ui.card>
        <div
            x-data="{
                gradeId: null,
                sectionId: null,
                dayPart: null,
                grades: @js(lookup()->getGrades()),
                sections: @js(
    $sections->map(
        fn($s) => [
            'id' => $s->id,
            'name' => $s->name,
            'grade_id' => $s->grade_id,
            'full_name' => $s->grade->name . ' - شعبة ' . $s->name,
        ],
    ),
),
                filteredSections: [],
                attendanceMode: @js($attendanceMode->value),
                isSplitDaily: @js($attendanceMode === \App\Enums\AttendanceModeEnum::SplitDaily),
                dayPartOptions: @js($dayPartOptions),

                init() {
                    this.updateFilteredSections();
                },

                updateFilteredSections() {
                    if (!this.gradeId) {
                        this.filteredSections = [];
                        this.sectionId = null;
                        return;
                    }
                    this.filteredSections = this.sections.filter(s => s.grade_id == this.gradeId);
                    if (this.sectionId && !this.filteredSections.find(s => s.id == this.sectionId)) {
                        this.sectionId = null;
                    }
                },

                canProceed() {
                    if (!this.sectionId) return false;
                    if (this.isSplitDaily && !this.dayPart) return false;
                    return true;
                },

                getAttendanceUrl() {
                    if (!this.canProceed()) return '#';

                    if (this.isSplitDaily) {
                        return `/dashboard/attendances/record/split-daily/${this.sectionId}/{{ $schoolDay->id }}/${this.dayPart}`;
                    } else {
                        return `/dashboard/attendances/record/daily/${this.sectionId}/{{ $schoolDay->id }}`;
                    }
                }
            }"
            x-on:change="updateFilteredSections()"
        >
            <form @submit.prevent="if(canProceed()) window.location.href = getAttendanceUrl()">
                <div class="space-y-6">
                    {{-- Grade Selection --}}
                    <div>
                        <label
                            for="grade_id"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                        >
                            الصف الدراسي
                            <span class="text-red-500">*</span>
                        </label>
                        <select
                            id="grade_id"
                            x-model="gradeId"
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            required
                        >
                            <option value="">اختر الصف الدراسي</option>
                            <template
                                x-for="key in Object.keys(grades)"
                                :key="key"
                            >
                                <option
                                    :value="key"
                                    x-text="grades[key]"
                                ></option>
                            </template>
                        </select>
                    </div>

                    {{-- Section Selection --}}
                    <div>
                        <label
                            for="section_id"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                        >
                            الشعبة الدراسية
                            <span class="text-red-500">*</span>
                        </label>
                        <select
                            id="section_id"
                            x-model="sectionId"
                            :disabled="!gradeId"
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white disabled:bg-gray-100 disabled:cursor-not-allowed dark:disabled:bg-gray-800"
                            required
                        >
                            <option value="">اختر الشعبة الدراسية</option>
                            <template
                                x-for="section in filteredSections"
                                :key="section.id"
                            >
                                <option
                                    :value="section.id"
                                    x-text="section.full_name"
                                ></option>
                            </template>
                        </select>
                        <p
                            x-show="!gradeId"
                            class="mt-1 text-xs text-gray-500 dark:text-gray-400"
                        >
                            <i class="fas fa-info-circle mr-1"></i>
                            يرجى اختيار الصف الدراسي أولاً
                        </p>
                    </div>

                    {{-- Day Part Selection (for SplitDaily mode) --}}
                    <template x-if="isSplitDaily">
                        <div>
                            <label
                                for="dayPart"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                            >
                                الفترة
                                <span class="text-red-500">*</span>
                            </label>
                            <select
                                id="dayPart"
                                x-model="dayPart"
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                required
                            >
                                <option value="">اختر الفترة</option>
                                <template
                                    x-for="(label, value) in dayPartOptions"
                                    :key="value"
                                >
                                    <option
                                        :value="value"
                                        x-text="label"
                                    ></option>
                                </template>
                            </select>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                <i class="fas fa-info-circle mr-1"></i>
                                اختر الفترة: قبل الفسحة أو بعد الفسحة
                            </p>
                        </div>
                    </template>

                    {{-- Action Button --}}
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <x-ui.button
                            as="a"
                            href="{{ route('dashboard.academic-calendar.index') }}"
                            variant="outline"
                        >
                            إلغاء
                        </x-ui.button>
                        <x-ui.button
                            type="submit"
                            variant="primary"
                            x-bind:disabled="!canProceed()"
                        >
                            <i class="fas fa-clipboard-check ml-2"></i>
                            تحضير
                        </x-ui.button>
                    </div>
                </div>
            </form>
        </div>
    </x-ui.card>
</x-layouts.dashboard>
