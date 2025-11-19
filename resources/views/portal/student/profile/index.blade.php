<x-layouts.portal pageTitle="الملف الشخصي">
    <x-slot name="sidebar">
        <x-ui.portal-sidebar portalType="student" />
    </x-slot>

    <div class="space-y-6">
        <livewire:common.student-profile.student-info
            :student="$student"
            context="portal"
        />
    </div>
</x-layouts.portal>
