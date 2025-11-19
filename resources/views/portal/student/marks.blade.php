<x-layouts.portal pageTitle="الدرجات">
    <x-slot name="sidebar">
        <x-ui.portal-sidebar portalType="student" />
    </x-slot>

    <livewire:common.student-profile.student-grades
        :student-id="$student->id"
        context="portal"
    />
</x-layouts.portal>
