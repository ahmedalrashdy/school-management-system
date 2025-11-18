<x-layouts.dashboard page-title="تعديل الامتحان">
    <x-slot name="breadcrumbs">
        <x-ui.breadcrumbs :items="[
        ['label' => 'لوحة التحكم', 'url' => route('dashboard.index'), 'icon' => 'fas fa-home'],
        ['label' => 'إدارة الامتحانات', 'url' => route('dashboard.exams.index'), 'icon' => 'fas fa-clipboard-list'],
        ['label' => 'الامتحانات', 'url' => route('dashboard.exams.list'), 'icon' => 'fas fa-list'],
        ['label' => 'تعديل امتحان', 'icon' => 'fas fa-edit'],
    ]" />
    </x-slot>

    <livewire:dashboard.examinations.exams.edit-exam :exam="$exam" />
</x-layouts.dashboard>