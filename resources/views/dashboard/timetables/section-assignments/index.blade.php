<x-layouts.dashboard page-title="توزيع الطلاب على الشعب">
    <x-slot name="breadcrumbs">
        <x-ui.breadcrumbs :items="[
        ['label' => 'لوحة التحكم', 'url' => route('dashboard.index'), 'icon' => 'fas fa-home'],
        ['label' => 'توزيع الطلاب', 'icon' => 'fas fa-users-cog'],
    ]" />
    </x-slot>

    <x-ui.main-content-header title="توزيع الطلاب على الشعب" description="توزيع الطلاب على الشعب الدراسية"
        button-text="رجوع" button-link="{{ route('dashboard.index') }}" />

    <livewire:dashboard.users.students.assign-students-to-sections />
</x-layouts.dashboard>