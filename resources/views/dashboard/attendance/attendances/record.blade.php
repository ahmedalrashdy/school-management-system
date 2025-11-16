<x-layouts.dashboard>
    <x-slot name="breadcrumbs">
        <x-ui.breadcrumbs :items="[
            ['label' => 'لوحة التحكم', 'url' => route('dashboard.index'), 'icon' => 'fas fa-home'],
            [
                'label' => 'التقويم الدراسي',
                'url' => route('dashboard.academic-calendar.index'),
                'icon' => 'fas fa-calendar-alt',
            ],
            ['label' => 'تحضير الطلاب', 'icon' => 'fas fa-clipboard-check'],
        ]" />
    </x-slot>

    <livewire:dashboard.attendance.attendances.record-attendance
        :section="$section"
        :timetableSlot="$timetableSlot ?? null"
        :schoolDay="$schoolDay ?? null"
        :date="$date ?? null"
        :dayPart="$dayPart ?? null"
    />
</x-layouts.dashboard>
