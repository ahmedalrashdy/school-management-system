<x-layouts.dashboard page-title="إضافة قاعدة احتساب">
    <x-slot name="breadcrumbs">
        <x-ui.breadcrumbs :items="[
        ['label' => 'لوحة التحكم', 'url' => route('dashboard.index'), 'icon' => 'fas fa-home'],
        ['label' => 'سياسات الدرجات', 'url' => route('dashboard.grading-rules.index'), 'icon' => 'fas fa-calculator'],
        ['label' => 'إضافة قاعدة', 'icon' => 'fas fa-plus'],
    ]" />
    </x-slot>

    <x-ui.main-content-header title="إضافة قاعدة احتساب جديدة" />

    @livewire('dashboard.examinations.grading-rules.create-grading-rule')
</x-layouts.dashboard>
