<x-layouts.portal pageTitle="الجدول الدراسي">
    <x-slot name="sidebar">
        <x-ui.portal-sidebar portalType="teacher" />
    </x-slot>
    <livewire:common.teacher-profile.teacher-timetable :teacher_id="$teacher->id" />
</x-layouts.portal>
