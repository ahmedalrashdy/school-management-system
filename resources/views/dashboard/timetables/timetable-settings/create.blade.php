<x-layouts.dashboard page-title="إضافة إعداد جدول">
    <x-slot name="breadcrumbs">
        <x-ui.breadcrumbs :items="[
            ['label' => 'لوحة التحكم', 'url' => route('dashboard.index'), 'icon' => 'fas fa-home'],
            [
                'label' => 'إدارة الجداول الدراسية',
                'url' => route('dashboard.timetables.index'),
                'icon' => 'fas fa-table',
            ],
            [
                'label' => 'إعدادات الجدول الدراسي',
                'url' => route('dashboard.timetable-settings.index'),
                'icon' => 'fas fa-cog',
            ],
            ['label' => 'إضافة إعداد جديد', 'icon' => 'fas fa-plus'],
        ]" />
    </x-slot>

    <x-ui.main-content-header
        title="إضافة قالب إعدادات جديد"
        description="إنشاء قالب جديد لإعدادات الوقت"
        button-text="رجوع"
        button-link="{{ route('dashboard.timetable-settings.index') }}"
    />

    @php
        $periodsPerDay = [];
        foreach ($days as $day) {
            $periodsPerDay[$day['key']] = old("periods_per_day.{$day['key']}", 7);
        }
    @endphp
    <x-ui.card>
        <form
            method="POST"
            action="{{ route('dashboard.timetable-settings.store') }}"
            x-data="{
                days: @js($days),
                periodsPerDay: @js($periodsPerDay)
            }"
        >
            @csrf

            <x-form.input
                name="name"
                label="اسم القالب"
                placeholder="مثال:إعدادات جداول المرحلة الثانوية 2024-2025"
                required
            />

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <x-form.input
                    name="first_period_start_time"
                    label="وقت بدء اليوم"
                    type="time"
                    value="07:00"
                    required
                />

                <x-form.input
                    name="default_period_duration_minutes"
                    label="مدة الحصة الافتراضية (بالدقائق)"
                    type="number"
                    min="15"
                    max="120"
                    value="45"
                    required
                />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <x-form.input
                    name="periods_before_break"
                    label="عدد الحصص قبل الفسحة"
                    type="number"
                    min="1"
                    max="12"
                    value="3"
                    required
                />

                <x-form.input
                    name="break_duration_minutes"
                    label="مدة الفسحة (بالدقائق)"
                    type="number"
                    min="5"
                    max="60"
                    value="30"
                    required
                />
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    عدد الحصص لكل يوم <span class="text-red-500">*</span>
                </label>
                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <template
                            x-for="day in days"
                            :key="day.key"
                        >
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                                    x-text="day.label + ' *'"
                                ></label>
                                <x-form.input
                                    name="periods_per_day"
                                    type="number"
                                    min="0"
                                    max="12"
                                    required
                                    x-bind:name="'periods_per_day[' + day.key + ']'"
                                    x-model="periodsPerDay[day.key]"
                                />
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <x-form.checkbox
                name="is_active"
                label="تفعيل هذا القالب (سيتم تعطيل أي قالب آخر نشط تلقائياً)"
            />

            <div class="mt-6 flex items-center gap-4">
                <x-ui.button
                    type="submit"
                    variant="primary"
                >
                    <i class="fas fa-save mr-2"></i>
                    حفظ
                </x-ui.button>
                <x-ui.button
                    as="a"
                    href="{{ route('dashboard.timetable-settings.index') }}"
                    variant="outline"
                >
                    إلغاء
                </x-ui.button>
            </div>
        </form>
    </x-ui.card>
</x-layouts.dashboard>
