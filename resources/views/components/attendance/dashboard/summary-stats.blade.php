@props(['stats'])

@php

    $capacity = $stats['total_capacity'] > 1 ? $stats['total_capacity'] : 0;

    $presentPercentage = $capacity > 0 ? round(($stats['present'] / $capacity) * 100) : 0;
    $absentPercentage = $capacity > 0 ? round(($stats['absent'] / $capacity) * 100) : 0;
    $latePercentage = $capacity > 0 ? round(($stats['late'] / $capacity) * 100) : 0;
    $excusedPercentage = $capacity > 0 ? round(($stats['excused'] / $capacity) * 100) : 0;

    $isPerPeriod = $stats['mode'] === \App\Enums\AttendanceModeEnum::PerPeriod->value;
@endphp

<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">

    {{-- 1. Total Students Card (Unique Headcount) --}}
    <div
        class="group relative bg-white dark:bg-gray-800/80 rounded-2xl shadow-sm hover:shadow-lg border border-gray-100 dark:border-gray-700/50 p-5 transition-all duration-300 hover:-translate-y-1">
        <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-gray-500/10 to-transparent rounded-2xl">
        </div>
        <div class="relative flex items-start justify-between">
            <div>
                <p class="text-gray-500 dark:text-gray-400 text-xs font-bold uppercase tracking-wider mb-2">الطلاب</p>
                <p class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">
                    {{ number_format($stats['total_students']) }}
                </p>
                <p class="text-[10px] text-gray-400 dark:text-gray-500 mt-1">عدد الطلاب الكلي</p>
            </div>
            <div
                class="w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                <i class="fas fa-users text-gray-600 dark:text-gray-300 text-lg"></i>
            </div>
        </div>
    </div>

    {{-- 2. Present Card --}}
    <div
        class="group relative bg-white dark:bg-gray-800/80 rounded-2xl shadow-sm hover:shadow-lg border border-gray-100 dark:border-gray-700/50 p-5 transition-all duration-300 hover:-translate-y-1">
        <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-emerald-500/10 to-transparent rounded-2xl">
        </div>
        <div class="relative flex items-start justify-between">
            <div>
                <p class="text-emerald-600/70 dark:text-emerald-400/70 text-xs font-bold uppercase tracking-wider mb-2">
                    حضور</p>
                <p class="text-3xl font-black text-emerald-600 dark:text-emerald-400 tracking-tight">
                    {{ number_format($stats['present']) }}
                </p>
                <div class="flex items-center gap-2 mt-2">
                    <span
                        class="text-xs font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/20 px-1.5 py-0.5 rounded"
                    >
                        {{ $presentPercentage }}%
                    </span>
                    @if ($isPerPeriod)
                        <span class="text-[9px] text-gray-400">تراكمي</span>
                    @endif
                </div>
            </div>
            <div
                class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-500/20 group-hover:scale-110 transition-transform">
                <i class="fas fa-check text-white text-lg"></i>
            </div>
        </div>
    </div>

    {{-- 3. Absent Card --}}
    <div
        class="group relative bg-white dark:bg-gray-800/80 rounded-2xl shadow-sm hover:shadow-lg border border-gray-100 dark:border-gray-700/50 p-5 transition-all duration-300 hover:-translate-y-1">
        <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-rose-500/10 to-transparent rounded-2xl">
        </div>
        <div class="relative flex items-start justify-between">
            <div>
                <p class="text-rose-600/70 dark:text-rose-400/70 text-xs font-bold uppercase tracking-wider mb-2">غياب
                </p>
                <p class="text-3xl font-black text-rose-600 dark:text-rose-400 tracking-tight">
                    {{ number_format($stats['absent']) }}
                </p>
                <div class="flex items-center gap-2 mt-2">
                    <span
                        class="text-xs font-bold text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-900/20 px-1.5 py-0.5 rounded"
                    >
                        {{ $absentPercentage }}%
                    </span>
                    @if ($isPerPeriod)
                        <span class="text-[9px] text-gray-400">حصة</span>
                    @endif
                </div>
            </div>
            <div
                class="w-12 h-12 bg-gradient-to-br from-rose-500 to-rose-600 rounded-xl flex items-center justify-center shadow-lg shadow-rose-500/20 group-hover:scale-110 transition-transform">
                <i class="fas fa-times text-white text-lg"></i>
            </div>
        </div>
    </div>

    {{-- 4. Late Card --}}
    <div
        class="group relative bg-white dark:bg-gray-800/80 rounded-2xl shadow-sm hover:shadow-lg border border-gray-100 dark:border-gray-700/50 p-5 transition-all duration-300 hover:-translate-y-1">
        <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-amber-500/10 to-transparent rounded-2xl">
        </div>
        <div class="relative flex items-start justify-between">
            <div>
                <p class="text-amber-600/70 dark:text-amber-400/70 text-xs font-bold uppercase tracking-wider mb-2">تأخر
                </p>
                <p class="text-3xl font-black text-amber-600 dark:text-amber-400 tracking-tight">
                    {{ number_format($stats['late']) }}
                </p>
                <div class="flex items-center gap-2 mt-2">
                    <span
                        class="text-xs font-bold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/20 px-1.5 py-0.5 rounded"
                    >
                        {{ $latePercentage }}%
                    </span>
                </div>
            </div>
            <div
                class="w-12 h-12 bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl flex items-center justify-center shadow-lg shadow-amber-500/20 group-hover:scale-110 transition-transform">
                <i class="fas fa-clock text-white text-lg"></i>
            </div>
        </div>
    </div>

    {{-- 5. Excused Card (New) --}}
    <div
        class="group relative bg-white dark:bg-gray-800/80 rounded-2xl shadow-sm hover:shadow-lg border border-gray-100 dark:border-gray-700/50 p-5 transition-all duration-300 hover:-translate-y-1">
        <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-sky-500/10 to-transparent rounded-2xl">
        </div>
        <div class="relative flex items-start justify-between">
            <div>
                <p class="text-sky-600/70 dark:text-sky-400/70 text-xs font-bold uppercase tracking-wider mb-2">بعذر</p>
                <p class="text-3xl font-black text-sky-600 dark:text-sky-400 tracking-tight">
                    {{ number_format($stats['excused']) }}
                </p>
                <div class="flex items-center gap-2 mt-2">
                    <span
                        class="text-xs font-bold text-sky-600 dark:text-sky-400 bg-sky-50 dark:bg-sky-900/20 px-1.5 py-0.5 rounded"
                    >
                        {{ $excusedPercentage }}%
                    </span>
                </div>
            </div>
            <div
                class="w-12 h-12 bg-gradient-to-br from-sky-500 to-sky-600 rounded-xl flex items-center justify-center shadow-lg shadow-sky-500/20 group-hover:scale-110 transition-transform">
                <i class="fas fa-file-medical text-white text-lg"></i>
            </div>
        </div>
    </div>

    {{-- 6. Recording Progress Card --}}
    <div
        class="group relative bg-white dark:bg-gray-800/80 rounded-2xl shadow-sm hover:shadow-lg border border-gray-100 dark:border-gray-700/50 p-5 transition-all duration-300 hover:-translate-y-1">
        <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-violet-500/10 to-transparent rounded-2xl">
        </div>
        <div class="relative h-full flex flex-col justify-between">
            <div class="flex items-start justify-between mb-1">
                <div>
                    <p
                        class="text-violet-600/70 dark:text-violet-400/70 text-xs font-bold uppercase tracking-wider mb-1">
                        نسبة الرصد</p>
                    <p class="text-3xl font-black text-violet-600 dark:text-violet-400 tracking-tight">
                        {{ $stats['recording_percentage'] }}%
                    </p>
                </div>
                <div class="w-10 h-10 bg-violet-100 dark:bg-violet-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-chart-pie text-violet-600 dark:text-violet-400"></i>
                </div>
            </div>

            {{-- Progress Bar --}}
            <div class="w-full">
                <div class="relative h-2 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden mb-1">
                    <div
                        class="absolute inset-0 bg-gradient-to-r from-violet-400 via-purple-500 to-violet-600 rounded-full transition-all duration-700 ease-out"
                        style="width: {{ $stats['recording_percentage'] }}%"
                    >
                    </div>
                </div>
                <p class="text-[10px] text-gray-400 text-right">
                    {{ $stats['recorded_sections'] }} / {{ $stats['total_sections'] }} شعبة
                </p>
            </div>
        </div>
    </div>
</div>
