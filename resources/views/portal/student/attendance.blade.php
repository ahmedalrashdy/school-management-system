<x-layouts.portal pageTitle="سجل الحضور والغياب">
    <x-slot name="sidebar">
        <x-ui.portal-sidebar portalType="student" />
    </x-slot>

    <livewire:common.student-profile.student-attendances-details
        :student-id="$student->id"
        context="portal"
    />
</x-layouts.portal>
