<x-layouts.dashboard page-title="قائمة الامتحانات">
    <x-slot name="breadcrumbs">
        <x-ui.breadcrumbs :items="[
            ['label' => 'لوحة التحكم', 'url' => route('dashboard.index'), 'icon' => 'fas fa-home'],
            ['label' => 'إدارة الامتحانات', 'url' => route('dashboard.exams.index'), 'icon' => 'fas fa-clipboard-list'],
            ['label' => 'الامتحانات', 'icon' => 'fas fa-list'],
        ]" />
    </x-slot>

    <x-ui.main-content-header
        title="إدارة الامتحانات"
        description="عرض وإنشاء وتعديل الامتحانات في النظام"
        button-text="إضافة امتحان"
        :btnPermissions="\Perm::ExamsCreate"
        button-link="{{ route('dashboard.exams.create') }}"
    />

    <livewire:dashboard.examinations.exams.exams-index />
</x-layouts.dashboard>
