@props(['timetableSetting', 'slotsGrouped', 'days', 'maxPeriodNumber', 'displayTeacherName' => true])

<div x-data="{ selectedDay: null }" class="lg:hidden">
    <!-- Day Selector -->
    <div class="mb-4">
        <div class="flex gap-2 overflow-x-auto pb-2 scrollbar-hide">
            @foreach ($days as $dayInfo)
                <button
                    @click="selectedDay = {{ $dayInfo['day']->value }}"
                    :class="selectedDay === {{ $dayInfo['day']->value }} || (selectedDay === null && {{ $loop->first ? 'true' : 'false' }})
                        ? 'bg-primary-500 text-white'
                        : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300'"
                    class="px-4 py-2 rounded-lg font-medium text-sm whitespace-nowrap transition-colors">
                    {{ $dayInfo['day']->label() }}
                </button>
            @endforeach
        </div>
    </div>

    <!-- Timetable Cards for Each Day -->
    <div class="space-y-4">
        @foreach ($days as $dayInfo)
            @php
                $currentTime = \Carbon\Carbon::parse($timetableSetting->first_period_start_time);
                $daySlots = $slotsGrouped[$dayInfo['day']->value] ?? collect();
                $hasPeriodsAfterBreak = $dayInfo['period_number'] > $timetableSetting->periods_before_break;
            @endphp

            <div x-show="selectedDay === {{ $dayInfo['day']->value }} || (selectedDay === null && {{ $loop->first ? 'true' : 'false' }})"
                x-transition
                class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <!-- Day Header -->
                <div class="mb-4 pb-3 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        {{ $dayInfo['day']->label() }}
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ $dayInfo['period_number'] }} حصة
                    </p>
                </div>
                <!-- Periods List -->
                <div class="space-y-3">
                    @for ($periodNum = 1; $periodNum <= $maxPeriodNumber; $periodNum++)
                        @php
                            $period = $daySlots[$periodNum] ?? null;
                            $duration = $period
                                ? $period->duration_minutes
                                : $timetableSetting->default_period_duration_minutes;
                            $start_at = $currentTime->format('H:i');
                            $end_at = $currentTime->copy()->addMinutes($duration)->format('H:i');
                        @endphp

                        @if ($periodNum <= $dayInfo['period_number'])
                            @if ($period)
                                {{-- Filled Slot --}}
                                <x-ui.timetable-slot
                                    :$period
                                    :$start_at
                                    :$end_at
                                    :day="$dayInfo['day']"
                                    :periodNumber="$periodNum"
                                    :$displayTeacherName
                                >
                                    @if (isset($slot))
                                        {!! \Illuminate\Support\Facades\Blade::render((string) $slot, [
                                            'period' => $period,
                                            'start_at' => $start_at,
                                            'end_at' => $end_at,
                                            'day' => $dayInfo['day'],
                                            'periodNumber' => $periodNum,
                                        ]) !!}
                                    @endif
                                </x-ui.timetable-slot>
                            @else
                                {{-- Empty Slot --}}
                                <x-ui.timetable-empty-slot
                                    :$start_at
                                    :$end_at
                                    :day="$dayInfo['day']"
                                    :periodNumber="$periodNum"
                                >
                                    @if (isset($emptySlot))
                                        {!! \Illuminate\Support\Facades\Blade::render((string) $emptySlot, [
                                            'start_at' => $start_at,
                                            'end_at' => $end_at,
                                            'day' => $dayInfo['day'],
                                            'periodNumber' => $periodNum,
                                        ]) !!}
                                    @endif
                                </x-ui.timetable-empty-slot>
                            @endif
                        @endif

                        @php
                            $currentTime->addMinutes($duration);
                        @endphp

                        {{-- Break Period --}}
                        @if ($periodNum == $timetableSetting->periods_before_break && $hasPeriodsAfterBreak)
                            @php
                                $breakStart = $currentTime->format('H:i');
                                $breakEnd = $currentTime->copy()->addMinutes($timetableSetting->break_duration_minutes)->format('H:i');
                                $currentTime->addMinutes($timetableSetting->break_duration_minutes);
                            @endphp
                            <div class="p-4 rounded-lg bg-yellow-50 dark:bg-yellow-900/20 border-2 border-yellow-200 dark:border-yellow-800">
                                <div class="flex items-center justify-center gap-2">
                                    <i class="fas fa-coffee text-yellow-600 dark:text-yellow-400"></i>
                                    <span class="text-sm font-semibold text-yellow-700 dark:text-yellow-400">
                                        فسحة
                                    </span>
                                </div>
                                <p class="text-xs text-yellow-600 dark:text-yellow-500 text-center mt-1">
                                    {{ $breakStart }} - {{ $breakEnd }}
                                </p>
                            </div>
                        @endif
                    @endfor
                </div>
            </div>
        @endforeach
    </div>
</div>

