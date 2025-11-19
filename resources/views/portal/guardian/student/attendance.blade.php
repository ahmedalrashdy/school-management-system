<x-layouts.portal pageTitle="سجل الحضور والغياب">
    <x-slot name="sidebar">
        <x-ui.portal-sidebar portalType="guardian" />
    </x-slot>

    <livewire:common.student-profile.student-attendances-details :student-id="$student->id" />
</x-layouts.portal>
