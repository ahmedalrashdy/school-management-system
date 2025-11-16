@props([
    'present' => 0,
    'absent' => 0,
    'late' => 0,
    'excused' => 0,
    'size' => 'xs', // 'xs' or 'sm'
    'variant' => 'default', // 'default' or 'compact'
    'showDarkMode' => true,
    'excusedColor' => 'blue', // 'blue' or 'sky'
    'containerClass' => 'flex items-center gap-2 overflow-x-auto no-scrollbar py-1',
])

@php
    $textSize = $size === 'sm' ? 'text-sm' : 'text-xs';
    $iconSize = $size === 'sm' ? 'text-xs' : 'text-[10px]';
    $padding = 'px-2.5 py-1'; // Same padding for both sizes
    $fontWeight = $variant === 'compact' ? 'font-semibold' : 'font-bold';

    // Excused color classes
    $excusedColorClass =
        $excusedColor === 'sky'
            ? [
                'bg' => 'bg-sky-50',
                'text' => 'text-sky-700',
                'border' => 'border-sky-100',
                'dark' => $showDarkMode ? 'dark:bg-sky-900/20 dark:text-sky-400 dark:border-sky-900/30' : '',
            ]
            : [
                'bg' => 'bg-blue-50',
                'text' => 'text-blue-700',
                'border' => 'border-blue-100',
                'dark' => $showDarkMode ? 'dark:bg-blue-900/20 dark:text-blue-400 dark:border-blue-900/30' : '',
            ];

    // Dark mode classes
    $darkModeClasses = $showDarkMode
        ? [
            'present' => 'dark:bg-emerald-900/20 dark:text-emerald-400 dark:border-emerald-900/30',
            'absent' => 'dark:bg-rose-900/20 dark:text-rose-400 dark:border-rose-900/30',
            'late' => 'dark:bg-amber-900/20 dark:text-amber-400 dark:border-amber-900/30',
        ]
        : [
            'present' => '',
            'absent' => '',
            'late' => '',
        ];

    // Icon classes based on variant
    $presentIcon = $variant === 'compact' ? 'fa-check' : 'fa-check-circle';
    $absentIcon = $variant === 'compact' ? 'fa-times' : 'fa-times-circle';
@endphp

<div class="{{ $containerClass }}">
    {{-- Present Badge --}}
    <span
        title="حضور"
        class="shrink-0 inline-flex items-center gap-1.5 {{ $padding }} bg-emerald-50 text-emerald-700 rounded-lg {{ $textSize }} {{ $fontWeight }} border border-emerald-100 {{ $darkModeClasses['present'] }}"
    >
        <i class="fas {{ $presentIcon }} {{ $iconSize }}"></i> {{ $present }}
    </span>

    {{-- Absent Badge --}}
    <span
        title="غياب"
        class="shrink-0 inline-flex items-center gap-1.5 {{ $padding }} bg-rose-50 text-rose-700 rounded-lg {{ $textSize }} {{ $fontWeight }} border border-rose-100 {{ $darkModeClasses['absent'] }}"
    >
        <i class="fas {{ $absentIcon }} {{ $iconSize }}"></i> {{ $absent }}
    </span>

    {{-- Late Badge --}}
    <span
        title="تأخير"
        class="shrink-0 inline-flex items-center gap-1.5 {{ $padding }} bg-amber-50 text-amber-700 rounded-lg {{ $textSize }} {{ $fontWeight }} border border-amber-100 {{ $darkModeClasses['late'] }}"
    >
        <i class="fas fa-clock {{ $iconSize }}"></i> {{ $late }}
    </span>

    {{-- Excused Badge --}}
    <span
        title="معذور"
        class="shrink-0 inline-flex items-center gap-1.5 {{ $padding }} {{ $excusedColorClass['bg'] }} {{ $excusedColorClass['text'] }} rounded-lg {{ $textSize }} {{ $fontWeight }} border {{ $excusedColorClass['border'] }} {{ $excusedColorClass['dark'] }}"
    >
        <i class="fas fa-user-check {{ $iconSize }}"></i> {{ $excused }}
    </span>
</div>
