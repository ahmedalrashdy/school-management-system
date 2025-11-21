<div>
    <x-slot name="breadcrumbs">
        <x-ui.breadcrumbs :items="[
            ['label' => 'لوحة التحكم', 'url' => route('dashboard.index'), 'icon' => 'fas fa-home'],
            [
                'label' => 'إدارة الجداول الدراسية',
                'url' => route('dashboard.timetables.index'),
                'icon' => 'fas fa-table',
            ],
            ['label' => 'إنشاء جدول جديد', 'icon' => 'fas fa-plus-circle'],
        ]" />
    </x-slot>

    <x-ui.main-content-header
        title="إنشاء جدول دراسي جديد"
        description="إنشاء جدول دراسي لشعبة معينة"
        icon="fas fa-plus-circle"
        button-text="رجوع"
        button-link="{{ route('dashboard.timetables.list') }}"
    />

    <x-ui.card>
        <form wire:submit="save">
            {{-- Section Selection with Cascade Filters --}}
            <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">
                    <i class="fas fa-filter ml-2"></i>
                    اختيار الشعبة <span class="text-red-500">*</span>
                </h3>
                @php
                    $grades = lookup()->getGrades();
                @endphp
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                    {{-- Grade --}}
                    <x-form.select
                        name="gradeId"
                        label="الصف الدراسي"
                        :options="$grades"
                        wire:model.live="gradeId"
                    />
                    {{-- Section --}}
                    <x-form.select
                        name="sectionId"
                        label="الشعبة"
                        :options="$this->sections->pluck('name', 'id')->toArray()"
                        wire:model.live="sectionId"
                        placeholder="اختر الشعبة"
                        required
                    />
                </div>

                @error('sectionId')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Timetable Name --}}
            <x-form.input
                wire:model="name"
                name="name"
                label="اسم الجدول"
                placeholder="مثال: جدول الصف الخامس - شعبة أ - 2024-2025"
                required
            />

            {{-- Timetable Setting --}}
            <x-form.select
                name="timetableSettingId"
                label="قالب الإعدادات"
                :options="$this->timetableSettings"
                wire:model="timetableSettingId"
                placeholder="اختر قالب الإعدادات"
                required
            />

            @if (empty($this->timetableSettings))
                <x-ui.alert
                    type="warning"
                    class="mt-2"
                >
                    لا يوجد قوالب إعدادات. يجب إنشاء قالب أولاً.
                    <a
                        href="{{ route('dashboard.timetable-settings.create') }}"
                        class="underline font-medium"
                    >
                        إنشاء قالب جديد
                    </a>
                </x-ui.alert>
            @endif

            {{-- Is Active --}}
            <x-form.checkbox
                wire:model="isActive"
                name="isActive"
                label="تفعيل هذا الجدول (سيتم تعطيل أي جدول آخر نشط لنفس الشعبة)"
            />

            {{-- Action Buttons --}}
            <div class="mt-6 flex items-center gap-4">
                <x-ui.button
                    type="submit"
                    variant="primary"
                >
                    <i class="fas fa-save ml-2"></i>
                    حفظ الجدول
                </x-ui.button>
                <a
                    href="{{ route('dashboard.timetables.list') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition"
                    wire:navigate
                >
                    إلغاء
                </a>
            </div>
        </form>
    </x-ui.card>
</div>
