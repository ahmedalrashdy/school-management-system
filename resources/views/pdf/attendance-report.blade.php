<!DOCTYPE html>
<html dir="rtl">

<head>
    <meta
        http-equiv="Content-Type"
        content="text/html; charset=utf-8"
    />
    <link
        rel="preconnect"
        href="https://fonts.bunny.net"
    >
    <link
        href="https://fonts.bunny.net/css?family=tajawal:400,500,700&display=swap"
        rel="stylesheet"
    />

    <!-- Font Awesome -->
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
    />

    <!-- استخدم Vite إذا كنت في بيئة تطوير، أو رابط CSS المترجم في الإنتاج -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        @font-face {
            font-family: 'Tajawal';
            src: url('https://fonts.bunny.net/tajawal/files/tajawal-latin-400-normal.woff2') format('woff2');
            font-weight: 400;
            font-style: normal;
        }

        @page {
            size: A4;
            margin: 1.0cm 1.0cm 1.0cm 1.0cm;
            /* تقليل الهامش قليلاً للاستفادة من المساحة */
        }

        body {
            font-family: 'Tajawal', sans-serif;
            background-color: #fff;
            margin: 0;
            padding: 0;
        }

        /* هذا هو التغيير الجوهري */
        .new-page-start {
            page-break-before: always;
            /* للمتصفحات القديمة */
            break-before: page;
            /* للمتصفحات الحديثة */
            display: block;
        }

        .no-break-inside {
            break-inside: avoid;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        @media print {
            thead {
                display: table-header-group;
            }

            tfoot {
                display: table-footer-group;
            }

            tr {
                break-inside: avoid;
            }
        }
    </style>
</head>

<body>

    {{--
      Header Global
      ملاحظة: بما أننا نستخدم Spatie PDF، هذا الهيدر سيظهر مرة واحدة في بداية المستند.
      إذا أردت تكراره، Spatie توفر خيار ->headerView()، لكن سنبقيه هنا للصفحة الأولى كما طلبت.
    --}}
    <header class="mb-8 border-b-2 border-gray-800 pb-4 no-break-inside">
        <div class="flex justify-between items-start w-full">
            {{-- اليمين --}}
            <div class="text-center w-1/3 pt-2">
                <p class="font-bold text-base leading-relaxed">الجمهورية اليمنية</p>
                <p class="font-bold text-base leading-relaxed">وزارة التربية والتعليم</p>
                <p class="font-bold text-base leading-relaxed mt-1 text-gray-700">
                    {{ $schoolName ?? 'مدرسة المستقبل الحديثة' }}</p>
            </div>

            {{-- الوسط: الشعار --}}
            <div class="w-1/3 text-center">
                <div
                    class="w-24 h-24 mx-auto border-4 border-double border-gray-800 rounded-full flex items-center justify-center shadow-sm">
                    <i class="fa-solid fa-graduation-cap text-4xl text-gray-700"></i>
                </div>
            </div>

            {{-- اليسار: المعلومات --}}
            <div class="text-right w-1/3 text-sm pt-4 pr-8">
                <div class="flex flex-col gap-1">
                    <p><span class="font-bold ml-1">التاريخ:</span> {{ now()->translatedFormat('d F Y') }}</p>
                    <p><span class="font-bold ml-1">المستخدم:</span> {{ auth()->user()->name ?? 'N/A' }}</p>
                    @if (!empty($sections) && isset($sections[0]['academic_year']))
                        <p><span class="font-bold ml-1">العام الدراسي:</span> <span
                                class="bg-gray-100 px-2 rounded">{{ $sections[0]['academic_year'] }}</span></p>
                    @endif
                </div>
            </div>
        </div>

        <div class="text-center mt-6">
            <h1 class="text-2xl font-extrabold text-gray-900 inline-block border-b-4 border-gray-800 pb-1">
                تقرير إحصائيات الحضور والغياب
            </h1>

            @if (isset($reportStartDate) && isset($reportEndDate))
                <div
                    class="mt-3 inline-flex items-center bg-gray-100 rounded-lg px-3 py-1 text-sm border border-gray-200">
                    <span class="font-bold text-gray-600 ml-2">الفترة:</span>
                    <span dir="ltr">{{ $reportStartDate }}</span>
                    <span class="mx-2 text-gray-400">-></span>
                    <span dir="ltr">{{ $reportEndDate }}</span>
                </div>
            @endif
        </div>
    </header>

    {{-- المحتوى --}}
    @foreach ($sections as $index => $section)
        {{-- وعاء القسم --}}
        <div class="{{ !$loop->first ? 'new-page-start' : '' }} w-full">
            {{-- 2. تصميم بطاقة الصف والشعبة المحسن --}}
            <div class="no-break-inside mb-4 bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                <div class="flex items-stretch">
                    {{-- شريط جانبي ملون --}}
                    <div class="w-2 bg-gray-800"></div>

                    {{-- المحتوى --}}
                    <div class="flex-1 p-3 flex justify-between items-center bg-gray-50">
                        <div class="flex items-center gap-4">
                            <div class="flex flex-col">
                                <span class="text-xs text-gray-500 font-bold mb-1">الصف الدراسي</span>
                                <span class="text-lg font-bold text-gray-800">
                                    <i class="fa-solid fa-layer-group ml-2 text-gray-400 text-sm"></i>
                                    {{ $section['grade'] }}
                                </span>
                            </div>

                            @if (isset($section['name']))
                                <div class="h-8 w-px bg-gray-300 mx-2"></div> {{-- فاصل --}}

                                <div class="flex flex-col">
                                    <span class="text-xs text-gray-500 font-bold mb-1">الشعبة</span>
                                    <span class="text-lg font-bold text-gray-800">
                                        <i class="fa-solid fa-users-rectangle ml-2 text-gray-400 text-sm"></i>
                                        {{ $section['name'] }}
                                    </span>
                                </div>
                            @endif
                        </div>

                        {{-- إحصائية سريعة (اختياري) --}}
                        <div class="text-center bg-white px-3 py-1 rounded border border-gray-200">
                            <span class="block text-xs text-gray-400">عدد الطلاب</span>
                            <span class="font-bold text-gray-800">{{ count($section['students']) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- الجدول --}}
            <table class="w-full text-xs text-center border border-gray-300 mb-6">
                <thead>
                    <tr class="bg-gray-800 text-white">
                        <th class="p-2 w-8 border-l border-gray-600">#</th>
                        <th class="p-2 text-right border-l border-gray-600">اسم الطالب</th>
                        <th class="p-2 w-10 border-l border-gray-600">حضور</th>
                        <th class="p-2 w-10 border-l border-gray-600 bg-red-900/30">غياب</th>
                        <th class="p-2 w-10 border-l border-gray-600">تأخر</th>
                        <th class="p-2 w-10 border-l border-gray-600">عذر</th>
                        <th class="p-2 w-14 bg-yellow-600/20 border-l border-gray-600">حضور.ج</th>
                        <th class="p-2 w-14 bg-red-600/20 border-l border-gray-600">غياب.ج</th>
                        <th class="p-2 w-14 bg-blue-600/20 border-l border-gray-600">عذر.ج</th>
                        <th class="p-2 w-14 font-bold">النسبة</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($section['students'] as $student)
                        <tr class="border-b border-gray-200 hover:bg-gray-50 {{ $loop->even ? 'bg-gray-50/50' : '' }}">
                            <td class="p-2 border-l border-gray-200 font-bold text-gray-500">{{ $loop->iteration }}
                            </td>
                            <td class="p-2 text-right border-l border-gray-200 font-bold text-gray-700">
                                {{ $student['name'] }}</td>

                            {{-- الأرقام --}}
                            <td class="p-1 border-l border-gray-200">{{ $student['stats']['present'] }}</td>
                            <td class="p-1 border-l border-gray-200 font-bold text-red-600 bg-red-50">
                                {{ $student['stats']['absent'] }}</td>
                            <td class="p-1 border-l border-gray-200">{{ $student['stats']['late'] }}</td>
                            <td class="p-1 border-l border-gray-200">{{ $student['stats']['excused'] }}</td>

                            {{-- الجزئي --}}
                            <td class="p-1 border-l border-gray-200 bg-yellow-50 text-yellow-700">
                                {{ $student['stats']['present_with_late'] }}</td>
                            <td class="p-1 border-l border-gray-200 bg-red-50 text-red-700">
                                {{ $student['stats']['partial_absence'] }}</td>
                            <td class="p-1 border-l border-gray-200 bg-blue-50 text-blue-700">
                                {{ $student['stats']['partial_excused'] }}</td>

                            {{-- النسبة --}}
                            <td
                                class="p-1 font-bold {{ $student['percentage'] < 75 ? 'text-red-600' : 'text-green-700' }}">
                                {{ $student['percentage'] }}%
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div
                class="no-break-inside mt-4 pt-4 border-t border-gray-200 flex justify-between text-xs font-bold text-gray-500 px-8">
                <div class="text-center">
                    <p class="mb-8">مدير المدرسة</p>
                    <p class="text-gray-300">..................</p>
                </div>
                <div class="text-center">
                    <p class="mb-8">وكيل شؤون الطلاب</p>
                    <p class="text-gray-300">..................</p>
                </div>
                <div class="text-center">
                    <p class="mb-8">المشرفة الإدارية</p>
                    <p class="text-gray-300">..................</p>
                </div>
            </div>

        </div>
    @endforeach
</body>

</html>
