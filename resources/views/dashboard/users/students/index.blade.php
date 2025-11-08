<x-layouts.dashboard page-title="الطلاب">
    <x-slot name="breadcrumbs">
        <x-ui.breadcrumbs :items="[
            ['label' => 'لوحة التحكم', 'url' => route('dashboard.index'), 'icon' => 'fas fa-home'],
            ['label' => 'الطلاب', 'icon' => 'fas fa-user-graduate'],
        ]" />
    </x-slot>

    <x-ui.main-content-header
        title="الطلاب"
        description="إدارة الطلاب في النظام"
        button-text="إضافة طالب جديد"
        :btnPermissions="\Perm::StudentsCreate"
        button-link="{{ route('dashboard.students.create') }}"
    />

    @livewire('dashboard.users.students.students-index')
</x-layouts.dashboard>
