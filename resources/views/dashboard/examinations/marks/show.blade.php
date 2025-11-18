<x-layouts.dashboard page-title="كشف درجات الشعبة">
    <x-slot name="breadcrumbs">
        <x-ui.breadcrumbs :items="[
            ['label' => 'لوحة التحكم', 'url' => route('dashboard.index'), 'icon' => 'fas fa-home'],
            ['label' => 'كشوف الدرجات', 'url' => route('dashboard.marks.index'), 'icon' => 'fas fa-file-alt'],
            ['label' => 'كشف درجات الشعبة', 'icon' => 'fas fa-table'],
        ]" />
    </x-slot>

    <!-- رأس الصفحة مع أزرار الإجراءات -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <i class="fas fa-layer-group text-primary-600 dark:text-primary-400"></i>
                كشف الدرجات الشامل: <span class="text-primary-600 dark:text-primary-400">{{ $section->name }}</span>
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                المصفوفة النهائية لدرجات {{ count($students) }} طالب في {{ count($subjects) }} مواد دراسية
            </p>
        </div>
        <div class="flex gap-2">
            <x-ui.button
                type="button"
                onclick="window.print()"
                variant="outline"
                class="hidden md:inline-flex"
            >
                <i class="fas fa-print ml-2"></i> طباعة
            </x-ui.button>
            <x-ui.button
                as="a"
                href="{{ route('dashboard.marks.index') }}"
                variant="primary"
            >
                <i class="fas fa-arrow-right ml-2"></i> رجوع
            </x-ui.button>
        </div>
    </div>

    <!-- إحصائيات سريعة للشعبة -->
    @if (count($students) > 0)
        @php
            $totalStudents = count($students);
            $passedStudents = collect($students)->filter(fn($s) => $s['overall_grade'] !== 'راسب')->count();
            $passRate = $totalStudents > 0 ? ($passedStudents / $totalStudents) * 100 : 0;
            $avgPercentage = collect($students)->avg('overall_percentage');
        @endphp
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm flex items-center gap-4">
                <div class="p-3 rounded-full bg-blue-50 text-blue-600 dark:bg-blue-900/20 dark:text-blue-400">
                    <i class="fas fa-users text-lg"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">عدد الطلاب</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $totalStudents }}</p>
                </div>
            </div>
            
            <div class="bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm flex items-center gap-4">
                <div class="p-3 rounded-full bg-green-50 text-green-600 dark:bg-green-900/20 dark:text-green-400">
                    <i class="fas fa-chart-pie text-lg"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">نسبة النجاح</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">{{ number_format($passRate, 1) }}%</p>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm flex items-center gap-4">
                <div class="p-3 rounded-full bg-purple-50 text-purple-600 dark:bg-purple-900/20 dark:text-purple-400">
                    <i class="fas fa-chart-line text-lg"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">متوسط التحصيل</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">{{ number_format($avgPercentage, 1) }}%</p>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm flex items-center gap-4">
                <div class="p-3 rounded-full bg-orange-50 text-orange-600 dark:bg-orange-900/20 dark:text-orange-400">
                    <i class="fas fa-book-open text-lg"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">المواد الدراسية</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">{{ count($subjects) }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- الجدول الرئيسي -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden relative">
        @if (count($students) > 0 && count($subjects) > 0)
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-right border-collapse">
                    <thead class="bg-gray-50 dark:bg-gray-900/50 text-gray-500 dark:text-gray-400 text-xs uppercase font-semibold">
                        <tr>
                            <!-- العمود المثبت: اسم الطالب -->
                            <th scope="col" class="sticky right-0 z-20 bg-gray-50 dark:bg-gray-900 px-4 py-4 min-w-[200px] border-b border-gray-200 dark:border-gray-700 shadow-[4px_0_10px_-5px_rgba(0,0,0,0.1)]">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-user-graduate"></i>
                                    بيانات الطالب
                                </div>
                            </th>

                            <!-- أعمدة المواد -->
                            @foreach ($subjects as $subjectData)
                                <th scope="col" class="px-4 py-4 min-w-[140px] border-b border-gray-200 dark:border-gray-700 text-center border-l border-dashed border-gray-200 dark:border-gray-800">
                                    <div class="flex flex-col items-center gap-1.5">
                                        <span class="text-sm font-bold text-gray-800 dark:text-gray-200">{{ $subjectData['subject']->name }}</span>
                                        <div class="flex items-center gap-1 text-[10px] text-gray-400 dark:text-gray-500 bg-gray-100 dark:bg-gray-800 px-2 py-0.5 rounded-full">
                                            <span>م: {{ $subjectData['grading_rule']->total_marks }}</span>
                                            <span class="w-px h-2 bg-gray-300 dark:bg-gray-600"></span>
                                            <span>ن: {{ $subjectData['grading_rule']->passed_mark }}</span>
                                        </div>
                                    </div>
                                </th>
                            @endforeach

                            <!-- العمود المثبت: المجموع -->
                            <th scope="col" class=" bg-primary-50 dark:bg-primary-900/20 px-4 py-4 min-w-[120px] border-b border-gray-200 dark:border-gray-700 text-center shadow-[-4px_0_10px_-5px_rgba(0,0,0,0.1)]">
                                النتيجة النهائية
                            </th>
                        </tr>
                    </thead>
                    
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                        @foreach ($students as $studentData)
                            <tr class="group hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors duration-150">
                                
                                <!-- بيانات الطالب (مثبت) -->
                                <td class="sticky right-0 z-10 bg-white dark:bg-gray-800 group-hover:bg-gray-50 dark:group-hover:bg-gray-750 px-4 py-3 border-l border-gray-100 dark:border-gray-700 shadow-[4px_0_10px_-5px_rgba(0,0,0,0.05)]">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <div class="font-bold text-gray-900 dark:text-white text-sm mb-0.5">
                                                {{ $studentData['student_name'] }}
                                            </div>
                                            <div class="text-xs font-mono text-gray-400 dark:text-gray-500">
                                                #{{ $studentData['admission_number'] }}
                                            </div>
                                        </div>
                                        <a href="{{ route('dashboard.marks.student.details', ['section' => $section, 'student' => $studentData['student']]) }}" 
                                           class="text-gray-300 hover:text-primary-500 dark:text-gray-600 dark:hover:text-primary-400 transition-colors"
                                           title="التفاصيل">
                                            <i class="fas fa-external-link-alt text-xs"></i>
                                        </a>
                                    </div>
                                </td>

                                <!-- درجات المواد -->
                                @foreach ($studentData['subjects'] as $subjectMark)
                                    @php
                                        // تحديد لون الدرجة
                                        $scoreColor = 'text-gray-700 dark:text-gray-300';
                                        if ($subjectMark['total'] === null) $scoreColor = 'text-gray-300 dark:text-gray-600';
                                        elseif ($subjectMark['percentage'] < 50) $scoreColor = 'text-red-600 dark:text-red-400 font-bold';
                                        elseif ($subjectMark['percentage'] >= 90) $scoreColor = 'text-green-600 dark:text-green-400 font-bold';
                                    @endphp
                                    <td class="px-2 py-3 text-center border-l border-dashed border-gray-100 dark:border-gray-800">
                                        <div class="flex flex-col items-center justify-center h-full">
                                            <!-- الدرجة الكلية -->
                                            <span class="text-base font-mono {{ $scoreColor }}">
                                                {{ $subjectMark['total'] !== null ? number_format($subjectMark['total'], 1) : '-' }}
                                            </span>
                                            
                                            <!-- تفاصيل صغيرة (أعمال / نهائي) -->
                                            <div class="flex gap-2 mt-1 text-[10px] text-gray-400 dark:text-gray-500 font-mono opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                                <span title="أعمال الفصل">{{ $subjectMark['coursework'] > 0 ? number_format($subjectMark['coursework'], 0) : '-' }}</span>
                                                <span class="text-gray-300 dark:text-gray-700">|</span>
                                                <span title="النهائي">{{ $subjectMark['final'] !== null ? number_format($subjectMark['final'], 0) : '-' }}</span>
                                            </div>
                                        </div>
                                    </td>
                                @endforeach

                                <!-- النتيجة النهائية (مثبت) -->
                                <td class=" bg-primary-50/30 dark:bg-gray-800 group-hover:bg-primary-50/50 dark:group-hover:bg-gray-750 px-4 py-3 text-center border-r border-gray-100 dark:border-gray-700 shadow-[-4px_0_10px_-5px_rgba(0,0,0,0.05)]">
                                    <div class="flex flex-col items-center">
                                        <span class="font-bold font-mono text-sm {{ $studentData['overall_percentage'] >= 50 ? 'text-gray-900 dark:text-white' : 'text-red-600 dark:text-red-400' }}">
                                            {{ number_format($studentData['overall_percentage'], 1) }}%
                                        </span>
                                        <x-ui.badge 
                                            :variant="app(\App\Services\Marksheets\LetterGradeService::class)->getGradeColor($studentData['overall_grade'])" 
                                            size="sm"
                                            class="mt-1">
                                            {{ $studentData['overall_grade'] }}
                                        </x-ui.badge>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <!-- حالة فارغة -->
            <div class="flex flex-col items-center justify-center py-16 px-4 text-center">
                <div class="bg-gray-50 dark:bg-gray-700/50 p-6 rounded-full mb-4">
                    <i class="fas fa-clipboard-list text-4xl text-gray-300 dark:text-gray-500"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">لا توجد بيانات للعرض</h3>
                <p class="text-gray-500 dark:text-gray-400 max-w-sm mx-auto">
                    @if (count($students) === 0)
                        لم يتم تسجيل أي طلاب في هذه الشعبة بعد.
                    @else
                        يجب إكمال رصد الدرجات للمواد واحتسابها لتظهر في هذا الكشف الشامل.
                    @endif
                </p>
                <div class="mt-6">
                    <x-ui.button
                        as="a"
                        href="{{ route('dashboard.marks.index') }}"
                        variant="outline"
                    >
                        العودة للرصد
                    </x-ui.button>
                </div>
            </div>
        @endif
    </div>

    <!-- تذييل ومفتاح الألوان -->
    <div class="mt-6 flex flex-wrap gap-4 text-xs text-gray-500 dark:text-gray-400 justify-center">
        <div class="flex items-center gap-2">
            <span class="w-3 h-3 rounded-full bg-green-500"></span>
            <span>ممتاز (≥90%)</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="w-3 h-3 rounded-full bg-gray-700 dark:bg-gray-300"></span>
            <span>ناجح (≥50%)</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="w-3 h-3 rounded-full bg-red-500"></span>
            <span>راسب (&lt;50%)</span>
        </div>
        <div class="flex items-center gap-2 ml-4">
            <i class="fas fa-info-circle"></i>
            <span>حرك المؤشر فوق الدرجة لعرض التفصيل (أعمال | نهائي)</span>
        </div>
    </div>
</x-layouts.dashboard>

<style>
    /* تحسين شكل شريط التمرير */
    .custom-scrollbar::-webkit-scrollbar {
        height: 8px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f5f9;
    }
    .dark .custom-scrollbar::-webkit-scrollbar-track {
        background: #1f2937;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }
    .dark .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #4b5563;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
</style>