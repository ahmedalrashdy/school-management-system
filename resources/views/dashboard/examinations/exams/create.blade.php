<x-layouts.dashboard page-title="إضافة امتحان">
    <x-slot name="breadcrumbs">
        <x-ui.breadcrumbs :items="[
        ['label' => 'لوحة التحكم', 'url' => route('dashboard.index'), 'icon' => 'fas fa-home'],
        ['label' => 'إدارة الامتحانات', 'url' => route('dashboard.exams.index'), 'icon' => 'fas fa-clipboard-list'],
        ['label' => 'الامتحانات', 'url' => route('dashboard.exams.list'), 'icon' => 'fas fa-list'],
        ['label' => 'إضافة امتحان جديد', 'icon' => 'fas fa-plus'],
    ]" />
    </x-slot>

    <livewire:dashboard.examinations.exams.create-exam />
</x-layouts.dashboard>