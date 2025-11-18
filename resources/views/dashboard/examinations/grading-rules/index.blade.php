<x-layouts.dashboard page-title="قائمة قواعد الاحتساب">
    <x-slot name="breadcrumbs">
        <x-ui.breadcrumbs :items="[
            ['label' => 'لوحة التحكم', 'url' => route('dashboard.index'), 'icon' => 'fas fa-home'],
            [
                'label' => 'سياسات الدرجات',
                'url' => route('dashboard.grading-rules.index'),
                'icon' => 'fas fa-calculator',
            ],
            ['label' => 'قائمة القواعد', 'icon' => 'fas fa-list'],
        ]" />
    </x-slot>

    <x-ui.main-content-header title="قواعد الاحتساب">
        <x-slot name="actions">
            <x-ui.button
                as="a"
                href="{{ route('dashboard.grading-rules.create') }}"
                variant="primary"
                :permissions="\Perm::GradingRulesCreate"
            >
                <i class="fas fa-plus"></i>
                إضافة قاعدة جديدة
            </x-ui.button>
        </x-slot>
    </x-ui.main-content-header>

    @livewire('dashboard.examinations.grading-rules.grading-rules-index')
</x-layouts.dashboard>
