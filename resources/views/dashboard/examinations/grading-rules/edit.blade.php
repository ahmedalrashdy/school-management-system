<x-layouts.dashboard page-title="تعديل قاعدة احتساب">
    <x-slot name="breadcrumbs">
        <x-ui.breadcrumbs :items="[
        ['label' => 'لوحة التحكم', 'url' => route('dashboard.index'), 'icon' => 'fas fa-home'],
        ['label' => 'سياسات الدرجات', 'url' => route('dashboard.grading-rules.index'), 'icon' => 'fas fa-calculator'],
        ['label' => 'تعديل قاعدة', 'icon' => 'fas fa-edit'],
    ]" />
    </x-slot>

    <x-ui.main-content-header title="تعديل قاعدة الاحتساب" />

    @livewire('dashboard.examinations.grading-rules.edit-grading-rule', ['gradingRule' => $gradingRule])
</x-layouts.dashboard>
