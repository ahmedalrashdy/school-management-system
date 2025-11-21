<div>
    <x-slot name="breadcrumbs">
        <x-ui.breadcrumbs :items="[
            ['label' => 'لوحة التحكم', 'url' => route('dashboard.index'), 'icon' => 'fas fa-home'],
            [
                'label' => 'إدارة الجداول الدراسية',
                'url' => route('dashboard.timetables.index'),
                'icon' => 'fas fa-table',
            ],
            ['label' => 'قائمة الجداول', 'url' => route('dashboard.timetables.list'), 'icon' => 'fas fa-list'],
            ['label' => 'بناء الجدول', 'icon' => 'fas fa-calendar-alt'],
        ]" />
    </x-slot>

    <x-ui.main-content-header
        title="بناء الجدول: {{ $this->timetable->name }}"
        description="إضافة وتعديل الحصص الدراسية في الجدول"
        icon="fas fa-calendar-alt"
        button-text="رجوع للقائمة"
        button-link="{{ route('dashboard.timetables.list') }}"
    />

    {{-- Timetable Info --}}
    <x-ui.card class="mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">الشعبة</p>
                <p class="text-lg font-medium text-gray-900 dark:text-white">
                    {{ $this->timetable->section->grade->name }} - شعبة {{ $this->timetable->section->name }}
                </p>
            </div>
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">قالب الإعدادات</p>
                <p class="text-lg font-medium text-gray-900 dark:text-white">
                    {{ $this->timetable->timetableSetting->name }}
                </p>
            </div>
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">عدد الحصص</p>
                <p class="text-lg font-medium text-gray-900 dark:text-white">
                    {{ $this->timetable->slots()->count() }} حصة
                </p>
            </div>
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">الحالة</p>
                @if ($this->timetable->is_active)
                    <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-green-100 text-green-800"
                    >
                        مفعل
                    </span>
                @else
                    <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-gray-100 text-gray-800"
                    >
                        غير مفعل
                    </span>
                @endif
            </div>
        </div>
    </x-ui.card>

    {{-- Visual Timetable Grid --}}
    <x-ui.card>
        <x-ui.timetable
            :slotsGrouped="$this->timetable->getSlotsGrouped()"
            :timetableSetting="$this->timetable->timetableSetting"
        >
            <x-slot:slot>
                @verbatim
                    <div class="flex items-center gap-2">
                        <button
                            type="button"
                            class="md:opacity-0 md:group-hover:opacity-100 transition text-primary-600 hover:text-primary-700 mx-1"
                            title="تعديل"
                            x-on:click="$dispatch('open-modal', {
                            name: 'manage-slot-modal',
                            title: 'تعديل: ' + '{{ $day->label() }}' + ' - الحصة ' + '{{ $periodNumber }}'
                    });
                    $dispatch('edit-slot', {
                        day: {{ $day->value }},
                        period: {{ $periodNumber }},
                        slotId: {{ $period->id }}
                    })"
                        >
                            <i class="fas fa-edit text-xs"></i>
                        </button>
                        <button
                            class="md:opacity-0 md:group-hover:opacity-100 transition text-danger-600 hover:text-danger-700 mx-1"
                            title="حذف"
                            wire:click.stop="deleteSlot({{ $period->id }})"
                            wire:confirm="هل أنت متأكد من حذف هذه الحصة؟"
                        >
                            <i class="fas fa-times text-xs"></i>
                        </button>
                    </div>
                @endverbatim
            </x-slot:slot>
            <x-slot:emptySlot>
                @verbatim
                    <div
                        class="hover:border-primary-400 hover:bg-primary-50 transition cursor-pointer flex flex-col items-center justify-center group h-full w-full py-2"
                        x-on:click="$dispatch('open-modal', {
                            name: 'manage-slot-modal',
                            title: 'إضافة: ' + '{{ $day->label() }}' + ' - الحصة ' + '{{ $periodNumber }}'
                        });
                        $dispatch('edit-slot', {
                            day: {{ $day->value }},
                            period: {{ $periodNumber }}
                        })"
                    >
                        <i class="fas fa-plus text-2xl text-gray-300 group-hover:text-primary-500 transition mb-1"></i>
                        <p class="text-xs text-gray-400 group-hover:text-primary-600">إضافة</p>
                    </div>
                @endverbatim
            </x-slot:emptySlot>
        </x-ui.timetable>
    </x-ui.card>
    <x-ui.modal
        name="manage-slot-modal"
        title="إدارة الحصة"
        maxWidth="2xl"
    >
        <livewire:dashboard.timetables.manage-slot :timetableId="$this->timetableId" />
    </x-ui.modal>
</div>
