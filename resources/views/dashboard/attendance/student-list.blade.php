<x-layouts.dashboard>
    <x-slot name="breadcrumbs">
        <x-ui.breadcrumbs :items="[
            ['label' => 'لوحة التحكم', 'url' => route('dashboard.index'), 'icon' => 'fas fa-home'],
            [
                'label' => 'الحضور والغياب',
                'url' => route('dashboard.attendance-dashboard.index'),
                'icon' => 'fas fa-users',
            ],
            ['label' => 'قائمة الطلاب', 'icon' => 'fas fa-list-ul'],
        ]" />
    </x-slot>

    <div class="max-w-7xl mx-auto space-y-6">
        {{-- Header Section --}}
        <div
            class="bg-white dark:bg-gray-800/80 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-6">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                {{-- Info Section --}}
                <div class="flex items-center gap-4">
                    <div class="relative">
                        <div
                            class="w-16 h-16 bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl flex items-center justify-center shadow-lg shadow-primary-500/20">
                            <i class="fas fa-users text-white text-2xl"></i>
                        </div>
                        <div
                            class="absolute -top-1 -right-1 w-5 h-5 bg-green-500 rounded-full flex items-center justify-center shadow-md ring-2 ring-white dark:ring-gray-800">
                            <i class="fas fa-list text-white text-[10px]"></i>
                        </div>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                            قائمة الطلاب - {{ $section->name }}
                        </h1>
                        <div class="flex items-center gap-3 mt-1 text-sm text-gray-500 dark:text-gray-400">
                            <span
                                class="inline-flex items-center gap-1.5 bg-gray-100 dark:bg-gray-700/50 px-2 py-0.5 rounded-md"
                            >
                                <i class="fas fa-calendar-day text-xs"></i>
                                {{ $schoolDay->date->translatedFormat('l j F Y') }}
                            </span>
                            <span
                                class="inline-flex items-center gap-1.5 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 px-2 py-0.5 rounded-md"
                            >
                                <i class="fas fa-cog text-xs"></i>
                                {{ $modeLabel }}
                            </span>
                            @if ($contextLabel)
                                <span
                                    class="inline-flex items-center gap-1.5 bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 px-2 py-0.5 rounded-md"
                                >
                                    <i class="fas fa-info-circle text-xs"></i>
                                    {{ $contextLabel }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Stats Section --}}
                <div class="flex items-center gap-4">
                    <div class="text-right">
                        <div class="text-2xl font-black text-gray-900 dark:text-white">{{ $students->count() }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">إجمالي الطلاب</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Students Table --}}
        <x-ui.card>
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    <i class="fas fa-users text-primary-500 mr-2"></i>
                    قائمة الطلاب
                </h3>
                <span class="text-sm text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-800 px-3 py-1 rounded-full">
                    {{ $students->count() }} طالب
                </span>
            </div>

            @if($students->count() > 0)
                <x-table :headers="[
                    ['label' => 'الطالب', 'icon' => 'fas fa-user'],
                    ['label' => 'رقم القيد', 'icon' => 'fas fa-hashtag'],
                    ['label' => 'الحالة', 'icon' => 'fas fa-info-circle'],
                    ['label' => 'الوقت المسجل', 'icon' => 'fas fa-clock'],
                    ['label' => 'الملاحظات', 'icon' => 'fas fa-sticky-note']
                ]">
                    @foreach($students as $student)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                            {{-- Student Name --}}
                            <x-table.td>
                                <div class="flex items-center gap-3">
                                    <div class="relative shrink-0">
                                        <div class="w-10 h-10 rounded-lg flex items-center justify-center font-bold text-white text-sm
                                            {{ $student['status_color'] === 'green' ? 'bg-emerald-500' :
                                               ($student['status_color'] === 'red' ? 'bg-rose-500' :
                                               ($student['status_color'] === 'orange' ? 'bg-amber-500' :
                                               ($student['status_color'] === 'blue' ? 'bg-blue-500' : 'bg-gray-400'))) }}">
                                            {{ substr($student['name'], 0, 1) }}
                                        </div>
                                        <div class="absolute -bottom-1 -right-1 w-4 h-4 rounded-full flex items-center justify-center text-xs font-bold text-white
                                            {{ $student['status_color'] === 'green' ? 'bg-emerald-600' :
                                               ($student['status_color'] === 'red' ? 'bg-rose-600' :
                                               ($student['status_color'] === 'orange' ? 'bg-amber-600' :
                                               ($student['status_color'] === 'blue' ? 'bg-blue-600' : 'bg-gray-500'))) }}">
                                            @if($student['status'])
                                                <i class="fas fa-check text-[8px]"></i>
                                            @else
                                                <i class="fas fa-minus text-[8px]"></i>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm font-bold text-gray-900 dark:text-white">{{ $student['name'] }}</div>
                                    </div>
                                </div>
                            </x-table.td>

                            {{-- Admission Number --}}
                            <x-table.td>
                                <span class="font-mono text-sm font-semibold text-gray-900 dark:text-white">
                                    {{ $student['admission_number'] }}
                                </span>
                            </x-table.td>

                            {{-- Status --}}
                            <x-table.td>
                                <x-ui.badge
                                    :variant="$student['status_color'] === 'green' ? 'success' :
                                             ($student['status_color'] === 'red' ? 'danger' :
                                             ($student['status_color'] === 'orange' ? 'warning' :
                                             ($student['status_color'] === 'blue' ? 'info' : 'secondary')))"
                                >
                                    @if($student['status_color'] === 'green')
                                        <i class="fas fa-check ml-1"></i>
                                    @elseif($student['status_color'] === 'red')
                                        <i class="fas fa-times ml-1"></i>
                                    @elseif($student['status_color'] === 'orange')
                                        <i class="fas fa-clock ml-1"></i>
                                    @elseif($student['status_color'] === 'blue')
                                        <i class="fas fa-user-check ml-1"></i>
                                    @else
                                        <i class="fas fa-question ml-1"></i>
                                    @endif
                                    {{ $student['status_label'] }}
                                </x-ui.badge>
                            </x-table.td>

                            {{-- Recorded At --}}
                            <x-table.td>
                                @if($student['recorded_at'])
                                    <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                                        <i class="fas fa-clock text-xs"></i>
                                        <span>{{ $student['recorded_at'] }}</span>
                                    </div>
                                @else
                                    <span class="text-gray-400 dark:text-gray-600">—</span>
                                @endif
                            </x-table.td>

                            {{-- Notes --}}
                            <x-table.td>
                                @if($student['notes'])
                                    <div class="max-w-xs truncate text-sm text-gray-600 dark:text-gray-400" title="{{ $student['notes'] }}">
                                        {{ $student['notes'] }}
                                    </div>
                                @else
                                    <span class="text-gray-400 dark:text-gray-600">—</span>
                                @endif
                            </x-table.td>
                        </tr>
                    @endforeach
                </x-table>
            @else
                {{-- Empty State --}}
                <x-ui.empty-state
                    icon="fas fa-users"
                    title="لا يوجد طلاب"
                    description="لا يوجد طلاب مسجلين في هذه الشعبة"
                />
            @endif
        </x-ui.card>
    </div>
</x-layouts.dashboard>
