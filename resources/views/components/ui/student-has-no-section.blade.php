@props(['student', 'isGuardianDashboard' => false, 'isStudentDashboardLayout' => false])
<x-ui.card>
    <div class="text-center py-12">
        <div
            class="mx-auto w-24 h-24 rounded-full bg-warning-100 dark:bg-warning-900/20 flex items-center justify-center mb-6">
            <i class="fas fa-exclamation-triangle text-4xl text-warning-600 dark:text-warning-400"></i>
        </div>
        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
            لا يوجد شعبة مسجلة
        </h3>
        <p class="text-gray-600 dark:text-gray-400 mb-6 max-w-md mx-auto">
            @if ($isGuardianDashboard)
                الابن {{ $student->user->full_name }} غير مسجل في أي شعبة حالياً.
            @elseif ($isStudentDashboardLayout)
                أنت غير مسجل في أي شعبة حالياً. يرجى التواصل مع الإدارة.
            @else
                الطالب {{ $student->user->full_name }} غير مسجل في أي شعبة حالياً.
                <x-ui.button>تسجيل الطالب</x-ui.button>
            @endif
        </p>
    </div>
</x-ui.card>
