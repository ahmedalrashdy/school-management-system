<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 overflow-x-auto print:p-0 print:shadow-none">
    {{-- Loading State --}}
    <div
        wire:loading
        wire:target="loadReport"
        class="w-full text-center py-12"
    >
        <div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-primary-500 border-t-transparent">
        </div>
        <p class="mt-2 text-gray-500 dark:text-gray-400">جاري إعداد التقرير...</p>
    </div>

    {{-- Empty State (Initial) --}}
    @if (!$isLoaded && !$sectionName)
        <div
            wire:loading.remove
            wire:target="loadReport"
            class="text-center py-12 text-gray-400"
        >
            <i class="fas fa-file-alt text-4xl mb-3"></i>
            <p>يرجى اختيار معايير التصفية وعرض التقرير</p>
        </div>
    @endif

    {{-- Report Content --}}
    @if ($isLoaded)
        <div
            wire:loading.remove
            wire:target="loadReport"
        >
            {{-- Report Header --}}
            {{-- Report Header --}}
            <div class="mb-6 border-b border-gray-200 dark:border-gray-700 pb-4">
                <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-4">
                    <h2 class="text-xl font-bold text-gray-800 dark:text-white">
                        <i class="fas fa-chart-line text-primary-500 ml-2"></i>
                        تقرير حضور وغياب الطلاب
                    </h2>

                    <div class="flex items-center gap-3">
                        {{-- Date Range --}}
                        <div
                            class="flex items-center gap-2 text-sm bg-gray-100 dark:bg-gray-700 rounded-lg px-3 py-1.5">
                            <span class="text-gray-500 dark:text-gray-400">الفترة:</span>
                            <span
                                class="font-bold text-gray-800 dark:text-gray-200"
                                dir="ltr"
                            >{{ $reportStartDate }}</span>
                            <i class="fas fa-arrow-left text-xs text-gray-400 mx-1"></i>
                            <span
                                class="font-bold text-gray-800 dark:text-gray-200"
                                dir="ltr"
                            >{{ $reportEndDate }}</span>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div
                        class="bg-gray-50 dark:bg-gray-700/50 rounded p-3 text-center sm:text-start border border-gray-100 dark:border-gray-700">
                        <span class="block text-xs text-gray-500 dark:text-gray-400 mb-1">العام الدراسي</span>
                        <span class="font-bold text-gray-800 dark:text-gray-200">{{ $academicYearName }}</span>
                    </div>
                    <div
                        class="bg-gray-50 dark:bg-gray-700/50 rounded p-3 text-center sm:text-start border border-gray-100 dark:border-gray-700">
                        <span class="block text-xs text-gray-500 dark:text-gray-400 mb-1">الصف الدراسي</span>
                        <span class="font-bold text-gray-800 dark:text-gray-200">{{ $gradeName }}</span>
                    </div>
                    <div
                        class="bg-gray-50 dark:bg-gray-700/50 rounded p-3 text-center sm:text-start border border-gray-100 dark:border-gray-700">
                        <span class="block text-xs text-gray-500 dark:text-gray-400 mb-1">الشعبة</span>
                        <span class="font-bold text-gray-800 dark:text-gray-200">{{ $sectionName }}</span>
                    </div>
                </div>
            </div>

            {{-- Table --}}
            @if (count($this->reportData) > 0)
                <table class="min-w-full border-collapse border border-gray-400 text-sm">
                    <thead>
                        <tr class="bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                            <th class="border border-gray-400 px-4 py-3 text-start w-12">#</th>
                            <th class="border border-gray-400 px-4 py-3 text-start">اسم الطالب</th>
                            <th
                                class="border border-gray-400 px-2 py-3 text-center sm:w-24 text-green-700 dark:text-green-400">
                                حضور
                            </th>
                            <th
                                class="border border-gray-400 px-2 py-3 text-center sm:w-24 text-red-700 dark:text-red-400">
                                غياب
                            </th>
                            <th
                                class="border border-gray-400 px-2 py-3 text-center sm:w-24 text-yellow-600 dark:text-yellow-400">
                                تأخر
                            </th>
                            <th
                                class="border border-gray-400 px-2 py-3 text-center sm:w-24 text-blue-600 dark:text-blue-400">
                                عذر
                            </th>
                            <th
                                class="border border-gray-400 px-2 py-3 text-center sm:w-24 bg-yellow-50 dark:bg-yellow-900/20">
                                حضور متأخر
                            </th>
                            <th
                                class="border border-gray-400 px-2 py-3 text-center sm:w-24 bg-red-50 dark:bg-red-900/20">
                                غياب جزئي
                            </th>
                            <th
                                class="border border-gray-400 px-2 py-3 text-center sm:w-24 bg-blue-50 dark:bg-blue-900/20">
                                عذر جزئي
                            </th>
                            <th class="border border-gray-400 px-2 py-3 text-center sm:w-24">
                                نسبة الحضور
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-400">
                        @foreach ($this->reportData as $data)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="border border-gray-400 px-4 py-2 text-center text-gray-500">
                                    {{ $loop->iteration }}
                                </td>
                                <td class="border border-gray-400 px-4 py-2 font-medium text-gray-900 dark:text-white">
                                    {{ $data['student']->user->first_name }} {{ $data['student']->user->last_name }}
                                </td>

                                @php
                                    $stats = $data['stats'];
                                    $present = $stats[\App\Enums\AttendanceStatusEnum::Present->value] ?? 0;
                                    $absent = $stats[\App\Enums\AttendanceStatusEnum::Absent->value] ?? 0;
                                    $late = $stats[\App\Enums\AttendanceStatusEnum::Late->value] ?? 0;
                                    $excused = $stats[\App\Enums\AttendanceStatusEnum::Excused->value] ?? 0;

                                    // Add partials to main stats if they exist in the raw data key
                                    // Assuming Service returns specific keys for partials if we didn't use enum
                                    // For now relying on main Enum keys

                                    $totalPresence = $present + $late; // Late counts as present usually? Or partial?
                                    // Calculating percentage: (Present + Late) / Total Days * 100
                                    $percentage = $daysCount > 0 ? round(($totalPresence / $daysCount) * 100, 1) : 0;
                                @endphp

                                <td
                                    class="border border-gray-400 px-2 py-2 text-center font-bold text-gray-700 dark:text-gray-300">
                                    {{ $present }}
                                </td>
                                <td
                                    class="border border-gray-400 px-2 py-2 text-center font-bold text-gray-700 dark:text-gray-300">
                                    {{ $absent }}
                                </td>
                                <td
                                    class="border border-gray-400 px-2 py-2 text-center font-bold text-gray-700 dark:text-gray-300">
                                    {{ $late }}
                                </td>
                                <td
                                    class="border border-gray-400 px-2 py-2 text-center font-bold text-gray-700 dark:text-gray-300">
                                    {{ $excused }}
                                </td>
                                <td
                                    class="border border-gray-400 px-2 py-2 text-center font-bold bg-yellow-50 dark:bg-yellow-900/20 text-gray-700 dark:text-gray-300">
                                    {{ $stats['present_with_late'] ?? 0 }}
                                </td>
                                <td
                                    class="border border-gray-400 px-2 py-2 text-center font-bold bg-red-50 dark:bg-red-900/20 text-gray-700 dark:text-gray-300">
                                    {{ $stats['partial_absence'] ?? 0 }}
                                </td>
                                <td
                                    class="border border-gray-400 px-2 py-2 text-center font-bold bg-blue-50 dark:bg-blue-900/20 text-gray-700 dark:text-gray-300">
                                    {{ $stats['partial_excused'] ?? 0 }}
                                </td>
                                <td
                                    class="border border-gray-400 px-2 py-2 text-center font-bold {{ $percentage < 75 ? 'text-red-600' : 'text-green-600' }}">
                                    {{ $percentage }}%
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="mt-8 text-xs text-gray-500 text-end">
                    تم استخراج التقرير بتاريخ: {{ now()->translatedFormat('l j F Y - h:i A') }}
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    لا توجد بيانات حضور مسجلة لهذه الفئة
                </div>
            @endif
        </div>
    @endif
</div>
