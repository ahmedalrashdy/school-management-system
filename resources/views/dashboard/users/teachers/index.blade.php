<x-layouts.dashboard page-title="المدرسون">
    <x-slot name="breadcrumbs">
        <x-ui.breadcrumbs :items="[
            ['label' => 'لوحة التحكم', 'url' => route('dashboard.index'), 'icon' => 'fas fa-home'],
            ['label' => 'المدرسون', 'icon' => 'fas fa-chalkboard-teacher'],
        ]" />
    </x-slot>

    <x-ui.main-content-header
        title="المدرسون"
        description="إدارة المدرسين في النظام"
        button-text="إضافة مدرس جديد"
        :btnPermissions="\Perm::TeachersCreate"
        button-link="{{ route('dashboard.teachers.create') }}"
    />

    @livewire('dashboard.users.teachers.teachers-index')
</x-layouts.dashboard>
