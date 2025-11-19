<x-layouts.portal pageTitle="الدرجات">
    <x-slot name="sidebar">
        <x-ui.portal-sidebar portalType="guardian" />
    </x-slot>

    <livewire:common.student-profile.student-grades :student-id="$student->id" />
</x-layouts.portal>
