@props(['subjects'=>[],'section'])
@php
    
    $letterGradeService = app(\App\Services\Marksheets\LetterGradeService::class);
    // حساب المعدل التراكمي والنسبة المئوية والتقدير
    $totalMarks = 0;
    $totalMaxMarks = 0;
    $overallPercentage = null;
    $overallGrade = null;

    if (!empty($subjects)) {
        foreach ($subjects as $subject) {
            if ($subject['total'] !== null) {
                $totalMarks += $subject['total'];
                $totalMaxMarks += $subject['max_total'];
            }
        }

        if ($totalMaxMarks > 0) {
            $overallPercentage = ($totalMarks / $totalMaxMarks) * 100;
            $overallGrade = $letterGradeService->getLetterGrade($overallPercentage);
        }
    }
@endphp
<div class="space-y-6">
    <!-- Summary Card -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <x-ui.stat-card
            title="المجموع التراكمي"
            icon="fas fa-star"
            :value="$overallPercentage !== null ? number_format($totalMarks, 1) : '-'"
            color="primary"
        />
        <x-ui.stat-card
            title="النسبة المئوية"
            icon="fas fa-percentage"
            :value="$overallPercentage !== null ? number_format($overallPercentage, 1) . '%' : '-'"
            color="info"
        />
        <x-ui.stat-card
            title="التقدير"
            icon="fas fa-trophy"
            :value="$overallGrade ?? '-'"
            color="success"
        />
    </div>
    <!-- Subjects Marks Table -->
    <!-- Subjects Marks Table -->
    <x-ui.card>
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                <i class="fas fa-book text-primary-500 mr-2"></i>
                درجات المواد
            </h3>
        </div>

        <div class="overflow-x-auto ring-1 ring-gray-200 dark:ring-gray-700 rounded-lg">
            <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th
                            class="px-4 py-3 text-right text-sm font-bold text-gray-900 dark:text-white border-l border-gray-200 dark:border-gray-700"
                            scope="col"
                            rowspan="2"
                        >
                            المادة
                        </th>

                        <th
                            class="px-4 py-2 text-center text-sm font-bold text-primary-700 dark:text-primary-400 bg-primary-50 dark:bg-primary-900/20 border-b border-l border-gray-200 dark:border-gray-700"
                            scope="col"
                            colspan="2"
                        >
                            توزيع الدرجات
                        </th>

                        <th
                            class="px-4 py-3 text-center text-sm font-bold text-gray-900 dark:text-white border-l border-gray-200 dark:border-gray-700"
                            scope="col"
                            rowspan="2"
                        >
                            المجموع
                        </th>
                        <th
                            class="px-4 py-3 text-center text-sm font-bold text-gray-900 dark:text-white"
                            scope="col"
                            rowspan="2"
                        >
                            التقدير
                        </th>
                    </tr>
                    <tr>
                        <th
                            class="px-4 py-2 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 border-l border-gray-200 dark:border-gray-700 bg-gray-50/50"
                            scope="col"
                        >
                            أعمال الفصل
                        </th>
                        <th
                            class="px-4 py-2 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 border-l border-gray-200 dark:border-gray-700 bg-gray-50/50"
                            scope="col"
                        >
                            النهائي
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-900">
                    @forelse($subjects as $subject)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                            <td
                                class="px-4 py-3 text-sm font-bold text-gray-900 dark:text-white border-l border-gray-100 dark:border-gray-700">
                                {{ $subject['subject']->name }}
                            </td>

                            <td class="px-2 py-2 text-center border-l border-gray-100 dark:border-gray-700">
                                <div
                                    class="flex flex-col items-center justify-center bg-gray-50 dark:bg-gray-800/50 rounded-md py-1 px-2 border border-gray-100 dark:border-gray-700">
                                    <span
                                        class="text-[10px] text-gray-400 font-medium border-b border-gray-200 dark:border-gray-600 w-full mb-1"
                                    >
                                        من {{ $subject['coursework_max'] }}
                                    </span>
                                    <span class="text-base font-bold text-gray-900 dark:text-white">
                                        {{ $subject['coursework_total'] !== null ? number_format($subject['coursework_total'], 1) : '-' }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-2 py-2 text-center border-l border-gray-100 dark:border-gray-700">
                                <div
                                    class="flex flex-col items-center justify-center bg-gray-50 dark:bg-gray-800/50 rounded-md py-1 px-2 border border-gray-100 dark:border-gray-700">
                                    <span
                                        class="text-[10px] text-gray-400 font-medium border-b border-gray-200 dark:border-gray-600 w-full mb-1"
                                    >
                                        من {{ $subject['final_max'] }}
                                    </span>
                                    <span class="text-base font-bold text-gray-900 dark:text-white">
                                        {{ $subject['final_mark'] !== null ? number_format($subject['final_mark'], 1) : '-' }}
                                    </span>
                                </div>
                            </td>

                            <td class="px-4 py-3 text-center border-l border-gray-100 dark:border-gray-700">
                                @if ($subject['total'] !== null)
                                    <span
                                        class="text-lg font-black text-primary-600 dark:text-primary-400">{{ number_format($subject['total'], 1) }}</span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>

                            <td class="px-4 py-3 text-center">
                                @if ($subject['grade'])
                                    <x-ui.badge :variant="$letterGradeService->getGradeColor($subject['grade'])">
                                        {{ $subject['grade'] }}
                                    </x-ui.badge>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td
                                class="px-4 py-8 text-center text-gray-500 dark:text-gray-400"
                                colspan="5"
                            >
                                لا توجد مواد متاحة
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.card>

    <!-- Detailed Marks (Expandable) -->
    <x-ui.card>
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <span
                    class="p-2 bg-primary-100 dark:bg-primary-900/30 rounded-lg text-primary-600 dark:text-primary-400"
                >
                    <i class="fas fa-chart-pie text-sm"></i>
                </span>
                تفاصيل الدرجات
            </h3>
        </div>

        <div class="space-y-4">
            @forelse($subjects as $subject)
                @php
                    $percentage = $subject['percentage'] ?? 0;
                    $progressColor =
                        $percentage >= 90
                            ? 'bg-green-500'
                            : ($percentage >= 80
                                ? 'bg-blue-500'
                                : ($percentage >= 70
                                    ? 'bg-primary-500'
                                    : ($percentage >= 60
                                        ? 'bg-yellow-500'
                                        : 'bg-red-500')));
                @endphp
                <!-- Subject Item -->
                <div
                    class="group border border-gray-200 dark:border-gray-700 rounded-xl bg-white dark:bg-gray-800 transition-all duration-200 hover:shadow-md hover:border-primary-200 dark:hover:border-primary-800"
                    x-data="{ expanded: false }"
                >

                    <!-- Main Button (Header) -->
                    <button
                        class="w-full px-5 py-4 flex items-center justify-between"
                        @click="expanded = !expanded"
                    >
                        <div class="flex items-center gap-4">
                            <!-- Icon & Name -->
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 rounded-full bg-primary-50 dark:bg-primary-900/20 flex items-center justify-center text-primary-600 dark:text-primary-400 group-hover:bg-primary-100 dark:group-hover:bg-primary-900/40 transition-colors">
                                    <i class="fas fa-book"></i>
                                </div>
                                <div class="text-right">
                                    <h4 class="font-bold text-gray-900 dark:text-white text-base">
                                        {{ $subject['subject']->name }}</h4>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ $section->grade->name }}
                                        - شعبة {{ $section->name }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Right Side: Score & Chevron -->
                        <div class="flex items-center gap-4">
                            <div class="text-left hidden sm:block">
                                @if ($subject['total'] !== null && $subject['max_total'] > 0)
                                    <div class="flex items-center gap-2 mb-1">
                                        <span
                                            class="text-sm font-bold text-gray-900 dark:text-white">{{ number_format($subject['total'], 1) }}</span>
                                        <span class="text-xs text-gray-400">/
                                            {{ $subject['max_total'] }}</span>
                                    </div>
                                    <!-- Mini Progress Bar -->
                                    <div class="w-24 h-1.5 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                                        <div
                                            class="h-full {{ $progressColor }} rounded-full"
                                            style="width: {{ min($percentage, 100) }}%"
                                        ></div>
                                    </div>
                                @else
                                    <span class="text-sm text-gray-400">-</span>
                                @endif
                            </div>

                            <div
                                class="w-8 h-8 rounded-full border border-gray-200 dark:border-gray-700 flex items-center justify-center text-gray-400 transition-transform duration-200 bg-gray-50 dark:bg-gray-800 group-hover:bg-white dark:group-hover:bg-gray-700"
                                :class="{ 'rotate-180 text-primary-500 border-primary-200': expanded }"
                            >
                                <i class="fas fa-chevron-down text-xs"></i>
                            </div>
                        </div>
                    </button>

                    <!-- Expanded Content -->
                    <div
                        class="border-t border-gray-100 dark:border-gray-700/50 bg-gray-50/50 dark:bg-gray-800/50"
                        x-show="expanded"
                        x-collapse
                    >

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-5">

                            <!-- Column 1: Semester Work -->
                            <div
                                class="bg-white dark:bg-gray-700/20 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                                <h5
                                    class="text-sm font-bold text-gray-800 dark:text-gray-200 mb-3 flex items-center gap-2">
                                    <i class="fas fa-briefcase text-primary-500 text-xs"></i>
                                    أعمال الفصل
                                    <span
                                        class="mr-auto text-xs bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300 px-2 py-0.5 rounded-md font-medium"
                                    >
                                        من {{ $subject['coursework_max'] }}
                                    </span>
                                </h5>

                                <div class="space-y-3">
                                    @foreach ($subject['coursework_items'] as $item)
                                        <div
                                            class="flex items-center justify-between text-sm p-2 rounded hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                            <span class="text-gray-600 dark:text-gray-400">
                                                {{ $item['exam_name'] }}
                                                @if ($item['weight'])
                                                    ({{ number_format($item['weight'], 1) }}%)
                                                @endif
                                            </span>
                                            <span
                                                class="font-bold text-gray-900 dark:text-white"
                                                dir="ltr"
                                            >
                                                @if ($item['raw_mark'] !== null)
                                                    {{ number_format($item['raw_mark'], 1) }}
                                                    <span class="text-xs text-gray-400 font-normal">/
                                                        {{ $item['max_mark'] }}</span>
                                                @else
                                                    <span class="text-gray-400">-</span>
                                                @endif
                                            </span>
                                        </div>
                                    @endforeach

                                    @if (!empty($subject['coursework_items']))
                                        <div class="my-2 border-t border-gray-100 dark:border-gray-700 border-dashed">
                                        </div>
                                    @endif

                                    <div
                                        class="flex items-center justify-between text-sm font-bold bg-gray-50 dark:bg-gray-800 p-2 rounded-lg text-primary-700 dark:text-primary-400">
                                        <span>مجموع الأعمال</span>
                                        <span dir="ltr">
                                            {{ $subject['coursework_total'] !== null ? number_format($subject['coursework_total'], 1) : '-' }}
                                            / {{ $subject['coursework_max'] }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Column 2: Final Exam -->
                            <div
                                class="bg-white dark:bg-gray-700/20 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                                <h5
                                    class="text-sm font-bold text-gray-800 dark:text-gray-200 mb-3 flex items-center gap-2">
                                    <i class="fas fa-file-signature text-secondary-500 text-xs"></i>
                                    الامتحان النهائي
                                    <span
                                        class="mr-auto text-xs bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 px-2 py-0.5 rounded-md font-medium"
                                    >
                                        من {{ $subject['final_max'] }}
                                    </span>
                                </h5>

                                <div class="space-y-3">
                                    @if ($subject['final_raw_mark'] !== null)
                                        <div class="flex items-center justify-between text-sm p-2">
                                            <span class="text-gray-600 dark:text-gray-400">درجة ورقة
                                                الإمتحان</span>
                                            <span
                                                class="font-bold text-gray-900 dark:text-white"
                                                dir="ltr"
                                            >
                                                {{ number_format($subject['final_raw_mark'], 1) }}
                                                <span class="text-xs text-gray-400 font-normal">/
                                                    {{ $subject['final_exam_max'] }}</span>
                                            </span>
                                        </div>
                                        <div class="my-2 border-t border-gray-100 dark:border-gray-700 border-dashed">
                                        </div>
                                    @endif

                                    <div
                                        class="flex items-center justify-between text-sm font-bold bg-gray-50 dark:bg-gray-800 p-2 rounded-lg text-secondary-700 dark:text-secondary-400">
                                        <span>الدرجة المحتسبة</span>
                                        <span dir="ltr">
                                            {{ $subject['final_mark'] !== null ? number_format($subject['final_mark'], 1) : '-' }}
                                            / {{ $subject['final_max'] }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>
                <!-- End Subject Item -->
            @empty
                <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                    لا توجد مواد متاحة
                </div>
            @endforelse
        </div>
    </x-ui.card>
</div>
