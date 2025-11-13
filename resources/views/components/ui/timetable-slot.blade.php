@props(['period', 'start_at','end_at', 'day', 'periodNumber','displayTeacherName'=>true])
<div
    class="relative min-h-20 p-3 rounded-lg bg-primary-50 dark:bg-primary-900/30 border-2 border-primary-200 dark:border-primary-700 hover:shadow-lg transition cursor-pointer group">
    <div class="flex flex-wrap gap-1 items-start justify-between mb-1">
        <p class="text-xs font-semibold text-primary-700 dark:text-primary-300">
            {{ $period->teacherAssignment->curriculumSubject->subject->name }}
        </p>
        {{ $slot }}
    </div>
    @if ($displayTeacherName)
        <p class="text-xs text-gray-600 dark:text-gray-400 mb-1">
            <i class="fas fa-user text-xs ml-1"></i>
            {{strtok(ltrim($period->teacherAssignment->teacher->user->first_name),' ') }}
            {{ $period->teacherAssignment->teacher->user->last_name }}
        </p>
    @endif
    <p class="text-xs text-gray-500 dark:text-gray-500">
        <i class="fas fa-clock text-xs ml-1"></i>
        {{ $start_at }} - {{ $end_at }}
        ({{ $period->duration_minutes }} د)
    </p>
</div>
