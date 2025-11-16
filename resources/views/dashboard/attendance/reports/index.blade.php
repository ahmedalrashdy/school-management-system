<x-layouts.dashboard>
    <x-slot name="breadcrumbs">
        <x-ui.breadcrumbs :items="[
            ['label' => 'لوحة التحكم', 'url' => route('dashboard.index'), 'icon' => 'fas fa-home'],
            ['label' => 'تقارير الحضور', 'icon' => 'fas fa-file-alt'],
        ]" />
    </x-slot>

    <x-ui.main-content-header
        title="تقارير الحضور والغياب"
        description="استعرض تقارير تفصيلية عن حضور الطلاب في الشعب الدراسية"
        icon="fas fa-chart-bar"
    />

    {{-- Filter Component --}}
    <livewire:dashboard.attendance.reports.attendance-reports-filter />

    {{-- Reports Table Component --}}
    <livewire:dashboard.attendance.reports.attendance-reports-table />
</x-layouts.dashboard>
