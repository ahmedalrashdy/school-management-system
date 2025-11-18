<x-layouts.dashboard page-title="التدقيق والمتابعة">
    <x-slot name="breadcrumbs">
        <x-ui.breadcrumbs :items="[
            ['label' => 'لوحة التحكم', 'url' => route('dashboard.index'), 'icon' => 'fas fa-home'],
            ['label' => 'كشوف الدرجات', 'url' => route('dashboard.marks.index'), 'icon' => 'fas fa-file-alt'],
            ['label' => 'التدقيق والمتابعة', 'icon' => 'fas fa-search'],
        ]" />
    </x-slot>

    <!-- رأس الصفحة مع الإحصائيات السريعة -->
    <div class="mb-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <span class="text-primary-600">
                        <i class="fas fa-microscope"></i>
                    </span>
                    تقرير التدقيق: {{ $section->name }}
                </h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1 text-sm">
                    تقرير تفصيلي لحالة ربط المواد والدرجات المرصودة للعام الحالي
                </p>
            </div>
            <x-ui.button
                as="a"
                href="{{ route('dashboard.marks.index') }}"
                variant="outline"
            >
                <i class="fas fa-arrow-right ml-2"></i> رجوع
            </x-ui.button>
        </div>

        <!-- بطاقات الملخص -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- عدد الطلاب -->
            <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 flex items-center gap-4">
                <div class="p-3 rounded-full bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-300">
                    <i class="fas fa-users text-xl"></i>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">إجمالي الطلاب</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ \App\Models\SectionStudent::where('section_id', $section->id)->count() }}
                    </p>
                </div>
            </div>

            <!-- المواد المكتملة -->
            @php
                $completeCount = collect($subjects)->where('status', 'complete')->count();
                $totalSubjects = count($subjects);
                $percent = $totalSubjects > 0 ? ($completeCount / $totalSubjects) * 100 : 0;
            @endphp
            <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 flex items-center gap-4">
                <div class="p-3 rounded-full bg-green-50 text-green-600 dark:bg-green-900/30 dark:text-green-300">
                    <i class="fas fa-check-circle text-xl"></i>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">مواد مكتملة الرصد</p>
                    <div class="flex items-baseline gap-1">
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $completeCount }}</p>
                        <span class="text-xs text-gray-400">/ {{ $totalSubjects }}</span>
                    </div>
                </div>
            </div>

            <!-- المواد الناقصة -->
            <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 flex items-center gap-4">
                <div class="p-3 rounded-full bg-warning-50 text-warning-600 dark:bg-warning-900/30 dark:text-warning-300">
                    <i class="fas fa-clock text-xl"></i>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">ناقصة الرصد</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ collect($subjects)->where('status', 'incomplete')->count() }}
                    </p>
                </div>
            </div>

            <!-- مشاكل (فوضوية/بدون قاعدة) -->
            @php
                $issuesCount = collect($subjects)->whereIn('status', ['chaotic', 'no_rule'])->count();
            @endphp
            <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 flex items-center gap-4">
                <div class="p-3 rounded-full {{ $issuesCount > 0 ? 'bg-red-50 text-red-600 dark:bg-red-900/30 dark:text-red-300' : 'bg-gray-50 text-gray-400' }}">
                    <i class="fas fa-exclamation-triangle text-xl"></i>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">تنبيهات حرجة</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $issuesCount }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- قائمة المواد -->
    <div class="space-y-4" x-data="{ openSubject: null }">
        @forelse ($subjects as $index => $subjectAudit)
            @php
                $status = $subjectAudit['status'];
                $statusConfig = [
                    'complete'   => ['label' => 'مكتملة', 'bg' => 'bg-green-50', 'border' => 'border-green-200', 'text' => 'text-green-700', 'icon' => 'fa-check-circle'],
                    'incomplete' => ['label' => 'ناقصة الرصد', 'bg' => 'bg-yellow-50', 'border' => 'border-yellow-200', 'text' => 'text-yellow-700', 'icon' => 'fa-hourglass-half'],
                    'chaotic'    => ['label' => 'فوضوية', 'bg' => 'bg-red-50', 'border' => 'border-red-200', 'text' => 'text-red-700', 'icon' => 'fa-random'],
                    'no_rule'    => ['label' => 'بلا قاعدة', 'bg' => 'bg-orange-50', 'border' => 'border-orange-200', 'text' => 'text-orange-700', 'icon' => 'fa-cog'],
                    'no_data'    => ['label' => 'لا توجد بيانات', 'bg' => 'bg-gray-50', 'border' => 'border-gray-200', 'text' => 'text-gray-600', 'icon' => 'fa-ban'],
                ];
                $config = $statusConfig[$status] ?? $statusConfig['no_data'];
                $subjectId = 'sub-' . $index;
            @endphp

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden transition-all duration-200 hover:shadow-md">
                <!-- شريط العنوان القابل للنقر -->
                <button type="button"
                    @click="openSubject === '{{ $subjectId }}' ? openSubject = null : openSubject = '{{ $subjectId }}'"
                    class="w-full flex items-center justify-between p-4 text-start focus:outline-none focus:bg-gray-50 dark:focus:bg-gray-700/50">
                    
                    <div class="flex items-center gap-4">
                        <!-- أيقونة الحالة -->
                        <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $config['bg'] }} {{ $config['text'] }} dark:bg-opacity-20">
                            <i class="fas {{ $config['icon'] }} text-lg"></i>
                        </div>
                        
                        <div>
                            <h3 class="text-base font-bold text-gray-900 dark:text-white">
                                {{ $subjectAudit['subject']->name }}
                            </h3>
                            <div class="flex items-center gap-3 mt-1 text-xs text-gray-500 dark:text-gray-400">
                                @if($subjectAudit['grading_rule'])
                                    <span class="flex items-center gap-1">
                                        <i class="fas fa-clipboard-list"></i>
                                        قاعدة: {{ $subjectAudit['grading_rule']->total_marks }} درجة
                                    </span>
                                @endif
                                <span class="flex items-center gap-1">
                                    <i class="fas fa-file-alt"></i>
                                    {{ count($subjectAudit['linked_exams']) }} امتحانات
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <span class="px-3 py-1 rounded-full text-xs font-medium {{ $config['bg'] }} {{ $config['text'] }} border {{ $config['border'] }} dark:bg-opacity-10 dark:border-opacity-20">
                            {{ $config['label'] }}
                        </span>
                        <i class="fas fa-chevron-down text-gray-400 transition-transform duration-200"
                           :class="{ 'rotate-180': openSubject === '{{ $subjectId }}' }"></i>
                    </div>
                </button>

                <!-- المحتوى التفصيلي -->
                <div x-show="openSubject === '{{ $subjectId }}'" 
                     x-collapse
                     class="border-t border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50">
                    
                    <div class="p-6 grid grid-cols-1 lg:grid-cols-12 gap-6">
                        
                        <!-- القسم الأيمن: إعدادات القاعدة -->
                        <div class="lg:col-span-4 space-y-4">
                            <h4 class="text-sm font-bold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                                <i class="fas fa-sliders-h text-primary-500"></i>
                                هيكل التقييم
                            </h4>

                            @if ($subjectAudit['grading_rule'])
                                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 shadow-sm">
                                    <div class="space-y-3">
                                        <div class="flex justify-between items-center pb-2 border-b border-gray-100 dark:border-gray-700">
                                            <span class="text-sm text-gray-500">أعمال الفصل</span>
                                            <span class="font-bold text-gray-900 dark:text-white">{{ $subjectAudit['grading_rule']->coursework_max_marks }}</span>
                                        </div>
                                        <div class="flex justify-between items-center pb-2 border-b border-gray-100 dark:border-gray-700">
                                            <span class="text-sm text-gray-500">الامتحان النهائي</span>
                                            <span class="font-bold text-gray-900 dark:text-white">{{ $subjectAudit['grading_rule']->final_exam_max_marks }}</span>
                                        </div>
                                        <div class="flex justify-between items-center pt-1">
                                            <span class="text-sm font-bold text-gray-700 dark:text-gray-300">المجموع الكلي</span>
                                            <span class="font-bold text-primary-600">{{ $subjectAudit['grading_rule']->total_marks }}</span>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-4 pt-3 border-t border-gray-100 dark:border-gray-700">
                                        @if ($subjectAudit['grading_rule']->is_published)
                                            <div class="flex items-center gap-2 text-green-600 text-xs font-medium bg-green-50 p-2 rounded">
                                                <i class="fas fa-lock"></i>
                                                القاعدة معتمدة ومنشورة
                                            </div>
                                        @else
                                            <div class="flex items-center gap-2 text-yellow-600 text-xs font-medium bg-yellow-50 p-2 rounded">
                                                <i class="fas fa-lock-open"></i>
                                                القاعدة مسودة (غير منشورة)
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @else
                                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                                    <div class="flex gap-3">
                                        <i class="fas fa-exclamation-circle text-red-500 mt-1"></i>
                                        <div>
                                            <h5 class="text-sm font-bold text-red-800 dark:text-red-300">مفقودة!</h5>
                                            <p class="text-xs text-red-600 dark:text-red-400 mt-1">
                                                لم يتم تعيين قاعدة احتساب لهذه المادة بعد.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- القسم الأيسر: الامتحانات والشوارد -->
                        <div class="lg:col-span-8 space-y-6">
                            
                            <!-- الامتحانات المرتبطة -->
                            <div>
                                <h4 class="text-sm font-bold text-gray-900 dark:text-gray-100 flex items-center gap-2 mb-3">
                                    <i class="fas fa-file-signature text-primary-500"></i>
                                    الامتحانات المرتبطة
                                </h4>

                                @if (count($subjectAudit['linked_exams']) > 0)
                                    <div class="space-y-3">
                                        @foreach ($subjectAudit['linked_exams'] as $examData)
                                            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4 relative overflow-hidden group">
                                                <!-- الشريط الجانبي الملون -->
                                                <div class="absolute right-0 top-0 bottom-0 w-1 {{ $examData['is_complete'] ? 'bg-green-500' : 'bg-yellow-500' }}"></div>

                                                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                                                    <div class="flex-1">
                                                        <div class="flex items-center gap-2">
                                                            <h5 class="text-sm font-bold text-gray-900 dark:text-white">
                                                                {{ $examData['exam']->exam_type_name }}
                                                            </h5>
                                                            <span class="text-xs bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 px-2 py-0.5 rounded">
                                                                {{ $examData['exam']->max_marks }} درجة
                                                            </span>
                                                        </div>
                                                        <p class="text-xs text-gray-500 mt-1">
                                                            <i class="far fa-calendar-alt mr-1"></i> {{ $examData['exam']->exam_date }}
                                                        </p>
                                                    </div>

                                                    <!-- مؤشر التقدم -->
                                                    <div class="flex-1 min-w-[200px]">
                                                        <div class="flex justify-between text-xs mb-1">
                                                            <span class="text-gray-600 dark:text-gray-400">الرصد: {{ $examData['marks_count'] }} / {{ $examData['students_count'] }}</span>
                                                            <span class="font-bold {{ $examData['is_complete'] ? 'text-green-600' : 'text-yellow-600' }}">
                                                                {{ $examData['completion_percentage'] }}%
                                                            </span>
                                                        </div>
                                                        <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-2">
                                                            <div class="h-2 rounded-full transition-all duration-500 {{ $examData['is_complete'] ? 'bg-green-500' : 'bg-yellow-500' }}"
                                                                 style="width: {{ $examData['completion_percentage'] }}%"></div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- الطلاب الغائبين -->
                                                @if (count($examData['students_without_marks']) > 0)
                                                    <div class="mt-4 pt-3 border-t border-gray-100 dark:border-gray-700" x-data="{ showMissing: false }">
                                                        <button @click="showMissing = !showMissing" class="text-xs text-yellow-600 dark:text-yellow-400 hover:underline flex items-center gap-1">
                                                            <i class="fas fa-exclamation-triangle"></i>
                                                            هناك {{ count($examData['students_without_marks']) }} طالب بلا درجة. اضغط للعرض
                                                        </button>
                                                        <div x-show="showMissing" class="mt-2 flex flex-wrap gap-2 animate-fadeIn">
                                                            @foreach ($examData['students_without_marks'] as $student)
                                                                <span class="inline-flex items-center px-2 py-1 rounded text-xs bg-yellow-50 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300 border border-yellow-100 dark:border-yellow-800">
                                                                    {{ $student['name'] }}
                                                                </span>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-6 bg-gray-50 dark:bg-gray-800 rounded-lg border border-dashed border-gray-300 dark:border-gray-600">
                                        <p class="text-sm text-gray-500">لا توجد امتحانات مرتبطة بالقاعدة</p>
                                    </div>
                                @endif
                            </div>

                            <!-- الامتحانات الشاردة -->
                            @if (count($subjectAudit['orphaned_exams']) > 0)
                                <div class="bg-red-50/50 dark:bg-red-900/10 border border-red-200 dark:border-red-800/50 rounded-lg p-4">
                                    <h4 class="text-sm font-bold text-red-700 dark:text-red-400 flex items-center gap-2 mb-2">
                                        <i class="fas fa-unlink"></i>
                                        امتحانات "شاردة" (غير محتسبة)
                                    </h4>
                                    <p class="text-xs text-red-600 dark:text-red-300 mb-4">
                                        هذه الامتحانات موجودة في النظام لهذه المادة والشعبة، ولكنها غير مدرجة في قاعدة الاحتساب الحالية، ولن تظهر في الشهادة النهائية.
                                    </p>

                                    <div class="overflow-x-auto">
                                        <table class="w-full text-sm text-right">
                                            <thead class="bg-red-100/50 dark:bg-red-900/30 text-red-800 dark:text-red-300">
                                                <tr>
                                                    <th class="px-3 py-2 rounded-r">الامتحان</th>
                                                    <th class="px-3 py-2">التاريخ</th>
                                                    <th class="px-3 py-2">العظمى</th>
                                                    <th class="px-3 py-2 rounded-l">هل رُصدت؟</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-red-100 dark:divide-red-800/30">
                                                @foreach ($subjectAudit['orphaned_exams'] as $orphan)
                                                    <tr class="text-gray-700 dark:text-gray-300">
                                                        <td class="px-3 py-2 font-medium">{{ $orphan['exam']->examType?->name ?? 'غير محدد' }}</td>
                                                        <td class="px-3 py-2">{{ $orphan['exam']->exam_date }}</td>
                                                        <td class="px-3 py-2">{{ $orphan['exam']->max_marks }}</td>
                                                        <td class="px-3 py-2">
                                                            @if ($orphan['has_marks'])
                                                                <span class="text-green-600 text-xs font-bold"><i class="fas fa-check"></i> نعم</span>
                                                            @else
                                                                <span class="text-gray-400 text-xs">لا</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif

                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-12 text-center">
                <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-folder-open text-2xl text-gray-400"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">لا توجد مواد</h3>
                <p class="text-gray-500 dark:text-gray-400 mt-2">
                    لم يتم تعيين منهج دراسي لهذه الشعبة بعد.
                </p>
            </div>
        @endforelse
    </div>
</x-layouts.dashboard>