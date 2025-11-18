<x-layouts.dashboard page-title="تفاصيل قاعدة الاحتساب">

    {{-- مسار التنقل (Breadcrumbs) --}}
    <x-slot name="breadcrumbs">
        <x-ui.breadcrumbs :items="[
            ['label' => 'لوحة التحكم', 'url' => route('dashboard.index'), 'icon' => 'fas fa-home'],
            [
                'label' => 'قواعد الاحتساب',
                'url' => route('dashboard.grading-rules.index'),
                'icon' => 'fas fa-calculator',
            ],
            ['label' => $gradingRule->curriculumSubject->subject->name, 'icon' => 'fas fa-file-invoice'],
        ]" />
    </x-slot>

    {{-- المحتوى الرئيسي --}}
    <div class="space-y-6">

        <!-- 1. ترويسة الصفحة والأزرار -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <div
                        class="p-2 bg-primary-100 text-primary-600 rounded-lg dark:bg-primary-900/30 dark:text-primary-400">
                        <i class="fas fa-book-open"></i>
                    </div>
                    {{ $gradingRule->curriculumSubject->subject->name }}
                </h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1 mr-12">
                    {{ $gradingRule->section->grade->name }} - {{ $gradingRule->section->name }}
                    <span class="mx-2 text-gray-300">•</span>
                    {{ $gradingRule->section->academicYear->name }}
                </p>
            </div>

            <div class="flex items-center gap-3">
                <x-ui.button
                    as="a"
                    href="{{ route('dashboard.grading-rules.index') }}"
                    variant="outline"
                >
                    <i class="fas fa-arrow-right"></i>
                    عودة للقائمة
                </x-ui.button>
                <x-ui.button
                    as="a"
                    href="{{ route('dashboard.grading-rules.edit', $gradingRule) }}"
                    variant="primary"
                    :permissions="\Perm::GradingRulesUpdate"
                >
                    <i class="fas fa-edit"></i>
                    تعديل القاعدة
                </x-ui.button>
            </div>
        </div>

        <!-- 2. بطاقات الإحصائيات العلوية -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- المجموع الكلي -->
            <div
                class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 relative overflow-hidden">
                <div
                    class="absolute top-0 right-0 w-16 h-16 bg-gray-50 dark:bg-gray-700/50 rounded-bl-full -mr-2 -mt-2">
                </div>
                <div class="text-xs text-gray-500 dark:text-gray-400 font-medium uppercase relative z-10">المجموع الكلي
                </div>
                <div class="mt-2 flex items-baseline gap-2 relative z-10">
                    <span
                        class="text-3xl font-bold text-gray-900 dark:text-white">{{ $gradingRule->total_marks }}</span>
                    <span class="text-sm text-gray-500">درجة</span>
                </div>
            </div>

            <!-- درجة النجاح -->
            <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="text-xs text-gray-500 dark:text-gray-400 font-medium uppercase">درجة النجاح</div>
                <div class="mt-2 flex items-baseline gap-2">
                    <span class="text-3xl font-bold text-danger-600">{{ $gradingRule->passed_mark }}</span>
                    <span class="text-sm text-gray-500">درجة</span>
                </div>
            </div>

            <!-- ملخص أعمال الفصل -->
            <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-xl border border-blue-100 dark:border-blue-800">
                <div class="text-xs text-blue-600 dark:text-blue-300 font-medium uppercase">أعمال الفصل</div>
                <div class="mt-2 flex items-baseline gap-2">
                    <span
                        class="text-3xl font-bold text-blue-700 dark:text-blue-400">{{ $gradingRule->coursework_max_marks }}</span>
                    <span class="text-sm text-blue-600/70">درجة</span>
                </div>
            </div>

            <!-- ملخص النهائي -->
            <div
                class="bg-purple-50 dark:bg-purple-900/20 p-4 rounded-xl border border-purple-100 dark:border-purple-800">
                <div class="text-xs text-purple-600 dark:text-purple-300 font-medium uppercase">الاختبار النهائي</div>
                <div class="mt-2 flex items-baseline gap-2">
                    <span
                        class="text-3xl font-bold text-purple-700 dark:text-purple-400">{{ $gradingRule->final_exam_max_marks }}</span>
                    <span class="text-sm text-purple-600/70">درجة</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- 3. المحتوى الرئيسي (الجدول والتفاصيل) -->
            <div class="lg:col-span-2 space-y-6">

                <!-- الشريط البصري للتوزيع -->
                <div
                    class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                        <i class="fas fa-chart-pie text-gray-400"></i>
                        توزيع الدرجات البصري
                    </h3>
                    <div class="h-8 w-full bg-gray-100 dark:bg-gray-700 rounded-lg overflow-hidden flex shadow-inner">
                        <div
                            class="h-full bg-blue-500 flex items-center justify-center text-[11px] text-white font-bold transition-all hover:bg-blue-600 relative group cursor-help"
                            style="width: {{ ($gradingRule->coursework_max_marks / $gradingRule->total_marks) * 100 }}%"
                        >
                            أعمال الفصل ({{ $gradingRule->coursework_max_marks }})
                            <div
                                class="absolute bottom-full mb-2 hidden group-hover:block bg-gray-900 text-white text-xs p-2 rounded whitespace-nowrap">
                                تمثل
                                {{ round(($gradingRule->coursework_max_marks / $gradingRule->total_marks) * 100) }}% من
                                المجموع
                            </div>
                        </div>
                        <div
                            class="h-full bg-purple-500 flex items-center justify-center text-[11px] text-white font-bold transition-all hover:bg-purple-600 relative group cursor-help"
                            style="width: {{ ($gradingRule->final_exam_max_marks / $gradingRule->total_marks) * 100 }}%"
                        >
                            النهائي ({{ $gradingRule->final_exam_max_marks }})
                            <div
                                class="absolute bottom-full mb-2 hidden group-hover:block bg-gray-900 text-white text-xs p-2 rounded whitespace-nowrap">
                                تمثل
                                {{ round(($gradingRule->final_exam_max_marks / $gradingRule->total_marks) * 100) }}% من
                                المجموع
                            </div>
                        </div>
                    </div>
                </div>

                <!-- جدول تفاصيل أعمال الفصل -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div
                        class="p-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50 flex justify-between items-center">
                        <h3 class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <span
                                class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center text-sm"
                            >
                                <i class="fas fa-tasks"></i>
                            </span>
                            تفاصيل أعمال الفصل ({{ $gradingRule->coursework_max_marks }} درجة)
                        </h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-right">
                            <thead
                                class="bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-300 uppercase text-xs"
                            >
                                <tr>
                                    <th class="px-6 py-3 font-medium">البند / الاختبار</th>
                                    <th class="px-6 py-3 font-medium text-center">الوزن النسبي (%)</th>
                                    {{-- العمود المهم --}}
                                    <th
                                        class="px-6 py-3 font-medium text-center bg-blue-50/50 dark:bg-blue-900/10 border-x border-blue-100 dark:border-blue-800/30 text-blue-700 dark:text-blue-300">
                                        يعادل (درجة)
                                    </th>
                                    <th class="px-6 py-3 font-medium text-center">الدرجة العظمى في الورقة</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @foreach ($gradingRule->items as $item)
                                    @php
                                        // معادلة التحويل: (الوزن / 100) * مجموع أعمال الفصل
                                        $calculatedMark = ($item->weight / 100) * $gradingRule->coursework_max_marks;
                                    @endphp
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                        <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                            {{ $item->exam->examType->name }}
                                            @if ($item->exam->title)
                                                <div class="text-xs text-gray-400 font-normal mt-1">
                                                    {{ $item->exam->title }}</div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-200"
                                            >
                                                {{ $item->weight }}%
                                            </span>
                                        </td>

                                        {{-- عرض الدرجة المحسوبة بوضوح --}}
                                        <td
                                            class="px-6 py-4 text-center bg-blue-50/30 dark:bg-blue-900/5 border-x border-blue-50 dark:border-blue-800/20">
                                            <span class="font-bold text-lg text-blue-700 dark:text-blue-400">
                                                {{ floatval($calculatedMark) }}
                                            </span>
                                            <span class="text-[10px] text-blue-400 block -mt-1">من أصل
                                                {{ $gradingRule->coursework_max_marks }}</span>
                                        </td>

                                        <td class="px-6 py-4 text-center text-gray-500">
                                            {{ $item->exam->max_marks }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-50 dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
                                <tr>
                                    <td class="px-6 py-3 font-bold text-gray-900 dark:text-white">الإجمالي</td>
                                    <td class="px-6 py-3 text-center font-bold">100%</td>
                                    <td
                                        class="px-6 py-3 text-center font-bold text-blue-700 dark:text-blue-400 text-lg border-x border-blue-100 dark:border-blue-800/30">
                                        {{ $gradingRule->coursework_max_marks }}
                                    </td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- قسم الاختبار النهائي -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div
                        class="p-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50 flex justify-between items-center">
                        <h3 class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <span
                                class="w-8 h-8 rounded-lg bg-purple-100 text-purple-600 flex items-center justify-center text-sm"
                            >
                                <i class="fas fa-graduation-cap"></i>
                            </span>
                            الاختبار النهائي ({{ $gradingRule->final_exam_max_marks }} درجة)
                        </h3>
                    </div>
                    <div class="p-6">
                        @if ($gradingRule->final_exam_id)
                            <div
                                class="flex items-center justify-between p-4 bg-purple-50 dark:bg-purple-900/10 rounded-lg border border-purple-100 dark:border-purple-800">
                                <div class="flex items-center gap-4">
                                    <div
                                        class="bg-purple-200 dark:bg-purple-800 p-3 rounded-full text-purple-700 dark:text-purple-300">
                                        <i class="fas fa-file-alt text-xl"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-gray-900 dark:text-white">
                                            {{ $gradingRule->finalExam->examType->name }}</h4>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            الدرجة في ورقة الامتحان: <span
                                                class="font-semibold">{{ $gradingRule->finalExam->max_marks }}</span>
                                        </p>
                                    </div>
                                </div>
                                <div class="text-center">
                                    <div class="text-xs text-gray-500 mb-1">المحصلة النهائية</div>
                                    <div class="text-2xl font-bold text-purple-700 dark:text-purple-400">
                                        {{ $gradingRule->final_exam_max_marks }}
                                    </div>
                                </div>
                            </div>
                        @else
                            <div
                                class="text-center py-6 text-gray-500 border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-lg">
                                <i class="fas fa-clock mb-2 text-xl text-gray-400"></i>
                                <p>لم يتم ربط اختبار نهائي بعد.</p>
                                <p class="text-xs mt-1">تم حجز {{ $gradingRule->final_exam_max_marks }} درجة للاختبار
                                    النهائي في النظام.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- 4. القائمة الجانبية (Sidebar) -->
            <div class="space-y-6">

                <!-- بطاقة حالة النشر (Toggle Card) -->
                <!-- بطاقة حالة النشر (Toggle Card) -->
                <div
                    class="bg-white dark:bg-gray-800 p-5 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                    <h3
                        class="text-sm font-semibold text-gray-900 dark:text-white mb-4 border-b pb-2 dark:border-gray-700">
                        <i class="fas fa-shield-alt mr-1"></i>
                        حالة الاعتماد
                    </h3>

                    @php
                        // نجهز الرابط هنا لنستخدمه داخل Alpine
                        $toggleRoute = route('dashboard.grading-rules.toggle-publish', $gradingRule);
                    @endphp

                    @if ($gradingRule->is_published)
                        {{-- حالة: منشور --}}
                        <div class="mb-6 text-center">
                            <div
                                class="inline-flex items-center justify-center w-16 h-16 bg-success-50 text-success-500 rounded-full mb-3 ring-4 ring-success-50/50 dark:bg-success-900/20 dark:text-success-400">
                                <i class="fas fa-check text-3xl"></i>
                            </div>
                            <h4 class="text-success-700 dark:text-success-400 font-bold text-lg">النتائج معتمدة ومنشورة
                            </h4>
                            <p class="text-xs text-gray-500 mt-2 leading-relaxed">
                                الدرجات تظهر حالياً في حسابات الطلاب وأولياء الأمور.
                            </p>
                        </div>

                        @can(\Perm::GradingRulesUpdate->value)
                            <button
                                type="button"
                                x-data
                                @click="$dispatch('open-modal', {
                        name: 'toggle-publish-modal',
                        toggleData: {
                            route: '{{ $toggleRoute }}'
                        }
                    })"
                                class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-white border border-danger-200 text-danger-600 hover:bg-danger-50 hover:border-danger-300 rounded-lg transition text-sm font-medium dark:bg-gray-700 dark:border-gray-600 dark:text-danger-400 dark:hover:bg-gray-600"
                            >
                                <i class="fas fa-eye-slash"></i>
                                إلغاء النشر (إخفاء)
                            </button>
                        @endcan
                    @else
                        {{-- حالة: مسودة --}}
                        <div class="mb-6 text-center">
                            <div
                                class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 text-gray-400 rounded-full mb-3 ring-4 ring-gray-50 dark:bg-gray-700 dark:text-gray-500 dark:ring-gray-800">
                                <i class="fas fa-pencil-alt text-2xl"></i>
                            </div>
                            <h4 class="text-gray-800 dark:text-gray-200 font-bold text-lg">مسودة (غير منشورة)</h4>
                            <p class="text-xs text-gray-500 mt-2 leading-relaxed">
                                الدرجات مخفية حالياً ولا تظهر لأي طالب أو ولي أمر.
                            </p>
                        </div>

                        @can(\Perm::GradingRulesUpdate->value)
                            <button
                                type="button"
                                x-data
                                @click="$dispatch('open-modal', {
                        name: 'toggle-publish-modal',
                        toggleData: {
                            route: '{{ $toggleRoute }}'
                        }
                    })"
                                class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-success-600 hover:bg-success-700 text-white rounded-lg transition text-sm font-medium shadow-md shadow-success-200 dark:shadow-none"
                            >
                                <i class="fas fa-bullhorn"></i>
                                اعتماد ونشر النتائج
                            </button>
                        @endcan
                    @endif

                    <div
                        class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700 flex justify-between text-xs text-gray-400">
                        <span>آخر تحديث:</span>
                        <span>{{ $gradingRule->updated_at->diffForHumans() }}</span>
                    </div>
                </div>

                <!-- بطاقة السياق الأكاديمي -->
                <div
                    class="bg-white dark:bg-gray-800 p-5 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                        <i class="fas fa-info-circle text-gray-400"></i>
                        السياق الأكاديمي
                    </h3>
                    <ul class="space-y-3">
                        <li class="flex justify-between items-center p-2 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                            <span class="text-xs text-gray-500">المرحلة</span>
                            <span
                                class="text-sm font-medium text-gray-900 dark:text-white">{{ $gradingRule->section->grade->stage->name }}</span>
                        </li>
                        <li class="flex justify-between items-center p-2 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                            <span class="text-xs text-gray-500">الصف</span>
                            <span
                                class="text-sm font-medium text-gray-900 dark:text-white">{{ $gradingRule->section->grade->name }}</span>
                        </li>
                        <li class="flex justify-between items-center p-2 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                            <span class="text-xs text-gray-500">الشعبة</span>
                            <span
                                class="text-sm font-medium text-gray-900 dark:text-white">{{ $gradingRule->section->name }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>


    @php
        // تجهيز نصوص وألوان المودال
        if ($gradingRule->is_published) {
            $modalTitle = 'تأكيد إخفاء الدرجات';
            $btnText = 'نعم، قم بالإخفاء';
            $btnVariant = 'danger';
        } else {
            $modalTitle = 'تأكيد نشر النتائج';
            $btnText = 'نعم، نشر الآن';
            $btnVariant = 'success';
        }
    @endphp

    <x-ui.confirm-action
        name="toggle-publish-modal"
        :title="$modalTitle"
        dataKey="toggleData"
        spoofMethod="PATCH"
        :confirmButtonText="$btnText"
        :confirmButtonVariant="$btnVariant"
        :permissions="\Perm::GradingRulesUpdate"
    >
        <div class="space-y-4 text-center sm:text-right">
            @if ($gradingRule->is_published)
                {{-- رسالة التحذير عند الإخفاء --}}
                <div
                    class="bg-red-50 text-red-800 p-4 rounded-lg border border-red-200 dark:bg-red-900/20 dark:text-red-300 dark:border-red-800">
                    <div class="flex items-start gap-3">
                        <i class="fas fa-exclamation-triangle mt-1 text-xl"></i>
                        <div>
                            <h4 class="font-bold text-sm mb-1">تنبيه هام جداً!</h4>
                            <p class="text-sm leading-relaxed">
                                أنت على وشك <strong>إخفاء الدرجات</strong> عن جميع الطلاب وأولياء الأمور.
                                لن يتمكنوا من رؤية نتائجهم في التطبيق أو الموقع حتى تقوم بإعادة النشر مرة أخرى.
                            </p>
                        </div>
                    </div>
                </div>
                <p class="text-gray-600 dark:text-gray-300 text-sm font-medium">
                    هل أنت متأكد تماماً من رغبتك في الاستمرار؟
                </p>
            @else
                {{-- رسالة التحذير عند النشر --}}
                <div
                    class="bg-green-50 text-green-800 p-4 rounded-lg border border-green-200 dark:bg-green-900/20 dark:text-green-300 dark:border-green-800">
                    <div class="flex items-start gap-3">
                        <i class="fas fa-bullhorn mt-1 text-xl"></i>
                        <div>
                            <h4 class="font-bold text-sm mb-1">إتاحة الدرجات للعامة</h4>
                            <p class="text-sm leading-relaxed">
                                سيتمكن جميع طلاب الشعبة وأولياء أمورهم من الاطلاع على درجات <strong>أعمال الفصل
                                    والامتحانات</strong> فوراً.
                            </p>
                        </div>
                    </div>
                </div>
                <p class="text-gray-600 dark:text-gray-300 text-sm">
                    يرجى التأكد من مراجعة الدرجات جيداً قبل النشر. هل تريد الاستمرار؟
                </p>
            @endif
        </div>
    </x-ui.confirm-action>

</x-layouts.dashboard>
