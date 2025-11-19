<x-layouts.portal pageTitle="الجدول الدراسي">
    <x-slot name="sidebar">
        <x-ui.portal-sidebar portalType="guardian" />
    </x-slot>

    @if (empty($section))
        <x-ui.student-has-no-section :student="$student" :isGuardianDashboard="true" />
    @elseif (empty($timetable))
        <x-ui.card>
            <div class="text-center py-12">
                <div
                    class="mx-auto w-24 h-24 rounded-full bg-warning-100 dark:bg-warning-900/20 flex items-center justify-center mb-6">
                    <i class="fas fa-calendar-times text-4xl text-warning-600 dark:text-warning-400"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
                    لا يوجد جدول دراسي نشط
                </h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6 max-w-md mx-auto">
                    لا يوجد جدول دراسي نشط للشعبة {{ $section->grade->name }} - شعبة {{ $section->name }}.
                </p>
            </div>
        </x-ui.card>
    @else
        <div class="space-y-6">
            <!-- Header -->
            <div class="bg-linear-to-l from-primary-500 to-primary-600 rounded-lg p-6 text-white">
                <h2 class="text-2xl font-bold mb-2">الجدول الدراسي</h2>
                <p class="text-primary-100">
                    {{ $student->user->full_name }} - {{ $section->grade->name }} - شعبة {{ $section->name }}
                </p>
            </div>
            <!-- Timetable -->
            <x-ui.card>
                <x-ui.timetable :slotsGrouped="$slotsGrouped" :timetableSetting="$timetable->timetableSetting" />
            </x-ui.card>
        </div>
    @endif
</x-layouts.portal>