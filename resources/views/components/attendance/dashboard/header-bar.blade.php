@props(['todayInfo', 'attendanceMode', 'selectedDayPart'])
@php
    $modeConfig = [
        \App\Enums\AttendanceModeEnum::Daily->value => [
            'label' => 'تحضير يومي',
            'icon' => 'fas fa-calendar-day',
            'gradient' => 'from-emerald-500 to-teal-600',
            'bg' => 'bg-emerald-500/20',
        ],
        \App\Enums\AttendanceModeEnum::SplitDaily->value => [
            'label' => 'تحضير على فترتين',
            'icon' => 'fas fa-clock',
            'gradient' => 'from-blue-500 to-indigo-600',
            'bg' => 'bg-blue-500/20',
        ],
        \App\Enums\AttendanceModeEnum::PerPeriod->value => [
            'label' => 'تحضير لكل حصة',
            'icon' => 'fas fa-table-cells',
            'gradient' => 'from-violet-500 to-purple-600',
            'bg' => 'bg-violet-500/20',
        ],
    ];

    $currentMode = $modeConfig[$attendanceMode->value] ?? $modeConfig[\App\Enums\AttendanceModeEnum::Daily->value];
@endphp

<div class="relative overflow-hidden rounded-2xl shadow-lg">
    {{-- Gradient Background --}}
    <div class="absolute inset-0 bg-gradient-to-l {{ $currentMode['gradient'] }}"></div>

    {{-- Pattern Overlay --}}
    <div class="absolute inset-0 opacity-10">
        <svg
            class="w-full h-full"
            xmlns="http://www.w3.org/2000/svg"
        >
            <defs>
                <pattern
                    id="grid"
                    width="40"
                    height="40"
                    patternUnits="userSpaceOnUse"
                >
                    <path
                        d="M 40 0 L 0 0 0 40"
                        fill="none"
                        stroke="white"
                        stroke-width="1"
                    />
                </pattern>
            </defs>
            <rect
                width="100%"
                height="100%"
                fill="url(#grid)"
            />
        </svg>
    </div>

    {{-- Content --}}
    <div class="relative px-6 py-5">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            {{-- Date Section --}}
            <div class="flex items-center gap-4">
                <div class="relative">
                    <div
                        class="w-16 h-16 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center shadow-lg border border-white/20">
                        <i class="fas fa-calendar-alt text-white text-2xl"></i>
                    </div>
                    {{-- Pulse Animation --}}
                    <div class="absolute -top-1 -right-1 w-4 h-4 bg-green-400 rounded-full animate-pulse"></div>
                </div>
                <div>
                    <p class="text-white/70 text-sm font-medium mb-0.5">التاريخ الحالي</p>
                    <h2 class="text-white text-xl lg:text-2xl font-bold tracking-tight">{{ $todayInfo['date'] }}</h2>
                    @if (!empty($todayInfo['hijri_date']))
                        <p class="text-white/60 text-sm mt-0.5">{{ $todayInfo['hijri_date'] }}</p>
                    @endif
                </div>
            </div>

            {{-- Mode & Status Badges --}}
            <div class="flex flex-wrap items-center gap-3">
                {{-- Mode Badge --}}
                <div
                    class="group bg-white/10 hover:bg-white/20 backdrop-blur-md rounded-xl px-4 py-3 flex items-center gap-3 transition-all duration-300 border border-white/10 hover:border-white/20 cursor-default">
                    <div
                        class="w-11 h-11 {{ $currentMode['bg'] }} rounded-xl flex items-center justify-center transition-transform group-hover:scale-110">
                        <i class="{{ $currentMode['icon'] }} text-white text-lg"></i>
                    </div>
                    <div>
                        <p class="text-white/60 text-xs font-medium">نمط التحضير</p>
                        <p class="text-white font-bold">{{ $currentMode['label'] }}</p>
                    </div>
                </div>
                {{-- Day Status Badge --}}
                @if ($todayInfo['is_school_day'])
                    <div
                        class="bg-emerald-500/20 backdrop-blur-md rounded-xl px-4 py-3 flex items-center gap-2 border border-emerald-400/20">
                        <div class="w-2.5 h-2.5 bg-emerald-400 rounded-full animate-pulse"></div>
                        <span class="text-emerald-100 font-semibold">يوم دراسي</span>
                    </div>
                @else
                    <div
                        class="bg-amber-500/20 backdrop-blur-md rounded-xl px-4 py-3 flex items-center gap-2 border border-amber-400/20">
                        <i class="fas fa-exclamation-triangle text-amber-300"></i>
                        <span class="text-amber-100 font-semibold">ليس يوم دراسي</span>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
