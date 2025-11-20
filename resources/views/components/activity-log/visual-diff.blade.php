<div class="rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden bg-white dark:bg-gray-800">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">

            {{--
                =========================================
                CASE 1: UPDATE (مقارنة: سابقاً vs حالياً)
                =========================================
            --}}
            @if ($activity->event === 'updated')
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-900/50 text-gray-500 dark:text-gray-400">
                        <th
                            scope="col"
                            class="px-4 py-3 text-right font-medium w-1/4"
                        >الحقل</th>
                        <th
                            scope="col"
                            class="px-4 py-3 text-right font-medium w-1/3 text-red-600/80 dark:text-red-400"
                        >
                            <i class="fas fa-minus-circle mr-1 text-xs"></i> سابقاً
                        </th>
                        <th
                            scope="col"
                            class="px-4 py-3 text-right font-medium w-1/3 text-green-600/80 dark:text-green-400"
                        >
                            <i class="fas fa-plus-circle mr-1 text-xs"></i> حالياً
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                    @forelse($formattedProperties as $row)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors group">
                            <td
                                class="px-4 py-3 font-medium text-gray-700 dark:text-gray-200 whitespace-nowrap bg-gray-50/30 dark:bg-gray-800">
                                {{ $row['label'] }}
                            </td>
                            <td
                                class="px-4 py-3 text-gray-600 dark:text-gray-300 bg-red-50/10 dark:bg-red-900/5 group-hover:bg-red-50/30 dark:group-hover:bg-red-900/10 transition-colors break-words dir-ltr text-right">
                                <div class="opacity-80 line-through decoration-red-400/50 decoration-2">
                                    {!! $row['old'] !!}
                                </div>
                            </td>
                            <td
                                class="px-4 py-3 text-gray-800 dark:text-gray-100 bg-green-50/10 dark:bg-green-900/5 group-hover:bg-green-50/30 dark:group-hover:bg-green-900/10 transition-colors font-medium break-words dir-ltr text-right">
                                {!! $row['new'] !!}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td
                                colspan="3"
                                class="px-4 py-8 text-center text-gray-400 italic"
                            >
                                لا توجد تغييرات جوهرية للعرض.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

                {{--
                =========================================
                CASE 2: OTHERS (Created, Deleted, etc) (قائمة بسيطة)
                =========================================
            --}}
            @else
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-900/50 text-gray-500 dark:text-gray-400">
                        <th
                            scope="col"
                            class="px-4 py-3 text-right font-medium w-1/3"
                        >الحقل</th>
                        <th
                            scope="col"
                            class="px-4 py-3 text-right font-medium w-2/3"
                        >
                            <i class="fas fa-info-circle mr-1 text-xs"></i> القيمة
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                    @forelse($formattedProperties as $row)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors group">
                            <td
                                class="px-4 py-3 font-medium text-gray-700 dark:text-gray-200 whitespace-nowrap bg-gray-50/30 dark:bg-gray-800">
                                {{ $row['label'] }}
                            </td>
                            {{-- تحديد لون الخلفية بناءً على نوع الحدث --}}
                            @php
                                $valueClass = match ($activity->event) {
                                    'deleted',
                                    'detached'
                                        => 'bg-red-50/10 dark:bg-red-900/5 text-red-700 dark:text-red-300',
                                    'created',
                                    'restored',
                                    'attached'
                                        => 'bg-green-50/10 dark:bg-green-900/5 text-gray-700 dark:text-gray-200',
                                    default => 'text-gray-600 dark:text-gray-300',
                                };
                            @endphp

                            <td class="px-4 py-3 {{ $valueClass }} break-words">
                                {!! $row['value'] !!}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td
                                colspan="2"
                                class="px-4 py-8 text-center text-gray-400 italic"
                            >
                                لا توجد بيانات إضافية.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            @endif

        </table>
    </div>
</div>
