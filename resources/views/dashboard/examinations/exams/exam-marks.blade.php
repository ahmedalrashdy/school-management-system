<x-layouts.dashboard page-title="كشف درجات الامتحان">

    <x-slot name="breadcrumbs">
        <x-ui.breadcrumbs :items="[
            ['label' => 'الامتحانات', 'url' => route('dashboard.exams.list'), 'icon' => 'fas fa-clipboard-list'],
            ['label' => $exam->curriculumSubject->subject->name, 'icon' => 'fas fa-book'],
            ['label' => 'كشف الدرجات', 'icon' => 'fas fa-poll'],
        ]" />
    </x-slot>

    <div class="space-y-6 pb-12">

        <!-- 1. بطاقة معلومات الامتحان (Header Card - كما هي سابقاً) -->
        <div class="relative overflow-hidden bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            {{-- ... (نفس كود الهيدر السابق) ... --}}
            <div class="relative z-10 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
                 <div class="space-y-3">
                    <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">
                        {{ $exam->curriculumSubject->subject->name }}
                    </h1>
                     <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 flex items-center gap-2">
                         <span>{{ $exam->examType->name }}</span>
                         <span class="text-gray-300 dark:text-gray-600">•</span>
                         <span>{{ $exam->exam_date->format('Y/m/d') }}</span>
                     </p>
                 </div>

                 <div class="flex items-center gap-4">
                    <div class="flex flex-col items-center justify-center min-w-[100px] bg-gray-50 dark:bg-gray-700/50 px-5 py-3 rounded-xl border border-gray-100 dark:border-gray-600/50">
                        <span class="text-[10px] text-gray-400 uppercase tracking-wider font-bold mb-1">الدرجة العظمى</span>
                        <span class="text-3xl font-bold text-gray-900 dark:text-white">{{ $exam->max_marks }}</span>
                    </div>
                 </div>
            </div>
        </div>

        <!-- 2. جدول الدرجات (The Grades Table) -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">

            {{-- Toolbar: Search & Sort --}}
            <div class="p-4 border-b border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800 flex flex-col md:flex-row justify-between items-center gap-4">

                {{-- Search Form --}}
                <form action="{{ url()->current() }}" method="GET" class="w-full gap-2 flex items-center md:w-96 relative group">
                    {{-- الحفاظ على الترتيب الحالي عند البحث --}}
                    @if(request('sort'))
                        <input type="hidden" name="sort" value="{{ request('sort') }}">
                    @endif

                    <input type="text"
                           name="search"
                           value="{{ $filters['search'] }}"
                           placeholder="بحث باسم الطالب..."
                           class="w-full pl-10 pr-4 py-2.5 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl text-sm focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all dark:text-white"
                    >
                    <button
                        class="px-3 py-2 rounded-lg text-xs font-bold border transition-all whitespace-nowrap flex items-center gap-2
                         bg-primary-50 text-primary-700 border-primary-100 dark:bg-primary-900/20 dark:text-primary-300 dark:border-primary-800">
                         بحث <i class="fas fa-search"></i>
                    </button>
                </form>

                {{-- Sort Actions --}}
                <div class="flex items-center gap-2 w-full md:w-auto overflow-x-auto">
                    <span class="text-xs font-bold text-gray-500 whitespace-nowrap hidden md:block">ترتيب حسب:</span>

                    {{--  أبجدي --}}
                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'alpha']) }}"
                       class="px-3 py-2 rounded-lg text-xs font-bold border transition-all whitespace-nowrap flex items-center gap-2
                       {{ ($filters['sort'] == 'alpha' || !$filters['sort'])
                          ? 'bg-primary-50 text-primary-700 border-primary-100 dark:bg-primary-900/20 dark:text-primary-300 dark:border-primary-800'
                          : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:bg-gray-700' }}">
                        <i class="fas fa-sort-alpha-down"></i> الاسم
                    </a>

                    {{-- الأعلى درجة --}}
                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'desc']) }}"
                       class="px-3 py-2 rounded-lg text-xs font-bold border transition-all whitespace-nowrap flex items-center gap-2
                       {{ $filters['sort'] == 'desc'
                          ? 'bg-primary-50 text-primary-700 border-primary-100 dark:bg-primary-900/20 dark:text-primary-300 dark:border-primary-800'
                          : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:bg-gray-700' }}">
                        <i class="fas fa-sort-amount-down"></i> الأعلى درجة
                    </a>

                    {{-- الأقل درجة --}}
                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'asc']) }}"
                       class="px-3 py-2 rounded-lg text-xs font-bold border transition-all whitespace-nowrap flex items-center gap-2
                       {{ $filters['sort'] == 'asc'
                          ? 'bg-primary-50 text-primary-700 border-primary-100 dark:bg-primary-900/20 dark:text-primary-300 dark:border-primary-800'
                          : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:bg-gray-700' }}">
                        <i class="fas fa-sort-amount-up"></i> الأقل درجة
                    </a>
                </div>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-right">
                    <thead class="bg-gray-50 dark:bg-gray-700/30 text-gray-500 dark:text-gray-400 uppercase text-[11px] font-bold tracking-wider">
                        <tr>
                            <th class="px-6 py-4 w-16 text-center">#</th>
                            <th class="px-6 py-4">بيانات الطالب</th>
                            <th class="px-6 py-4 text-center w-32">الحالة</th>

                            {{-- الدرجة في الورقة --}}
                            <th class="px-6 py-4 text-center w-48">
                                الدرجة في الورقة
                                <span class="block text-[9px] text-gray-400 font-normal mt-0.5">من أصل {{ $exam->max_marks }}</span>
                            </th>

                            {{-- العمود المشروط: الدرجة المحتسبة --}}
                            @if($has_rule)
                                <th class="px-6 py-4 text-center w-48 bg-purple-50/40 dark:bg-purple-900/10 text-purple-700 dark:text-purple-300 border-x border-purple-50 dark:border-purple-800/20">
                                    المساهمة في المجموع
                                    <span class="block text-[9px] opacity-70 font-normal mt-0.5">الوزن: {{ $weight_info['weight'] }}%</span>
                                </th>
                            @endif

                            <th class="px-6 py-4 min-w-[200px]">ملاحظات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700/70">
                        @forelse($students as $index => $student)
                            <tr class="group hover:bg-gray-50 dark:hover:bg-gray-700/30 transition duration-150">

                                <td class="px-6 py-4 text-center text-gray-400 dark:text-gray-500 font-mono text-xs">
                                    {{ $index + 1 }}
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-xs font-bold text-gray-500 dark:text-gray-400">
                                            {{ mb_substr($student->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-900 dark:text-white text-sm">
                                                {{ $student->name }}
                                                {{-- Highlight search term if needed --}}
                                            </div>
                                            <div class="text-[11px] text-gray-400 font-mono mt-0.5">{{ $student->admission_number }}</div>
                                        </div>
                                    </div>
                                </td>

                                {{-- الحالة --}}
                                <td class="px-6 py-4 text-center">
                                    @if($student->is_absent)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-bold bg-red-50 text-red-600 dark:bg-red-900/20 dark:text-red-400 border border-red-100 dark:border-red-900/30">
                                            غائب
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-bold bg-green-50 text-green-600 dark:bg-green-900/20 dark:text-green-400 border border-green-100 dark:border-green-900/30">
                                            حاضر
                                        </span>
                                    @endif
                                </td>

                                {{-- الدرجة الخام --}}
                                <td class="px-6 py-4 text-center">
                                    @if($student->is_absent)
                                        <span class="text-gray-300 dark:text-gray-600 font-bold text-xl">-</span>
                                    @else
                                        <div class="flex flex-col items-center justify-center">
                                            <span class="text-xl font-bold text-gray-800 dark:text-white tabular-nums tracking-tight">
                                                {{ $student->raw_mark + 0 }}
                                            </span>

                                            <div class="w-20 h-1.5 bg-gray-100 dark:bg-gray-700 rounded-full mt-2 overflow-hidden">
                                                @php
                                                    $percentage = ($student->raw_mark / $student->exam_max) * 100;
                                                    $colorClass = $percentage >= 50 ? 'bg-primary-500' : 'bg-red-500';
                                                @endphp
                                                <div class="h-full {{ $colorClass }} rounded-full" style="width: {{ $percentage }}%"></div>
                                            </div>
                                        </div>
                                    @endif
                                </td>

                                {{-- الدرجة المحتسبة --}}
                                @if($has_rule)
                                    <td class="px-6 py-4 text-center bg-purple-50/20 dark:bg-purple-900/5 border-x border-purple-50 dark:border-purple-800/10">
                                        @if($student->is_absent)
                                            <span class="text-gray-300 dark:text-gray-600">-</span>
                                        @else
                                            <div class="flex flex-col items-center">
                                                <span class="text-lg font-bold text-purple-700 dark:text-purple-300 tabular-nums">
                                                    {{ round($student->weighted_score, 2) }}
                                                </span>
                                            </div>
                                        @endif
                                    </td>
                                @endif

                                <td class="px-6 py-4">
                                    @if($student->notes)
                                        <div class="text-xs text-gray-600 dark:text-gray-300 truncate max-w-[200px]" title="{{ $student->notes }}">
                                            {{ $student->notes }}
                                        </div>
                                    @else
                                        <span class="text-gray-300 dark:text-gray-600 text-xs italic opacity-50">--</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-16 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-12 h-12 bg-gray-50 dark:bg-gray-700/50 rounded-full flex items-center justify-center mb-3">
                                            <i class="fas fa-search text-gray-300 dark:text-gray-500"></i>
                                        </div>
                                        <p class="text-gray-500 dark:text-gray-400 font-medium text-sm">لا توجد نتائج مطابقة</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.dashboard>
