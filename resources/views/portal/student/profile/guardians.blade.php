<x-layouts.portal pageTitle="أولياء الأمور">
    <x-slot name="sidebar">
        <x-ui.portal-sidebar portalType="student" />
    </x-slot>

    <div class="space-y-6">
        <livewire:common.student-profile.student-guardians
            :student-id="$student->id"
            context="portal"
        />
    </div>
</x-layouts.portal>
