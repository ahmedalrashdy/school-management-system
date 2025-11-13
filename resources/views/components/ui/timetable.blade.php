{{-- Desktop Table View --}}
<div class="hidden lg:block overflow-x-auto">
    <table class="w-full border-collapse">
        <thead>
            <tr class="bg-gray-100 dark:bg-gray-700">
                <th
                    class="border border-gray-300 dark:border-gray-600 px-4 py-3 text-right text-sm font-semibold text-gray-700 dark:text-gray-300">
                    اليوم / الحصة
                </th>
                @for ($i = 1; $i <= $maxPeriodNumber; $i++)
                    <th
                        class="border border-gray-300 dark:border-gray-600 px-4 py-3 text-center text-sm font-semibold text-gray-700 dark:text-gray-300">
                        الحصة {{ $i }}
                    </th>
                    @if ($i == $timetableSetting->periods_before_break)
                        <th
                            class="border border-gray-300 dark:border-gray-600 px-4 py-3 text-center text-sm font-semibold text-yellow-700 dark:text-yellow-400 bg-yellow-50 dark:bg-yellow-900/20">
                            <i class="fas fa-coffee ml-1"></i>
                            فسحة
                        </th>
                    @endif
                @endfor
            </tr>
        </thead>
        <tbody>
            @foreach ($days as $dayInfo)
                @php
                    $currentTime = \Carbon\Carbon::parse($timetableSetting->first_period_start_time);
                    $daySlots = $slotsGrouped[$dayInfo['day']->value] ?? collect();
                    $hasPeriodsAfterBreak = $dayInfo['period_number'] > $timetableSetting->periods_before_break;
                @endphp
                <tr>
                    <td
                        class="border border-gray-300 dark:border-gray-600 px-4 py-3 bg-gray-50 dark:bg-gray-800 text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ $dayInfo['day']->label() }}
                    </td>
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
                            <td class="border border-gray-300 dark:border-gray-600 p-2">
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
                            </td>
                        @else
                            <td class="border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-800">
                            </td>
                        @endif

                        @php
                            $currentTime->addMinutes($duration);
                        @endphp

                        {{-- Break Column (after specified periods) --}}
                        @if ($periodNum == $timetableSetting->periods_before_break)
                            @php
                                $breakStart = $currentTime->format('H:i');
                                $breakEnd = $currentTime->copy()->addMinutes($timetableSetting->break_duration_minutes)->format('H:i');
                                $currentTime->addMinutes($timetableSetting->break_duration_minutes);
                            @endphp
                            <td
                                class="border border-gray-300 dark:border-gray-600 px-4 py-3 bg-yellow-50 dark:bg-yellow-900/20 text-center">
                                @if ($hasPeriodsAfterBreak)
                                    <div class="flex flex-col items-center justify-center">
                                        <i class="fas fa-coffee text-yellow-600 dark:text-yellow-400 mb-1"></i>
                                        <p class="text-xs font-medium text-yellow-700 dark:text-yellow-400">
                                            فسحة
                                        </p>
                                        <p class="text-xs text-yellow-600 dark:text-yellow-500 mt-1">
                                            {{ $breakStart }} - {{ $breakEnd }}
                                        </p>
                                    </div>
                                @endif
                            </td>
                        @endif
                    @endfor
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- Mobile Card View --}}
<x-ui.timetable-mobile 
    :timetableSetting="$timetableSetting" 
    :slotsGrouped="$slotsGrouped" 
    :days="$days"
    :maxPeriodNumber="$maxPeriodNumber"
    :displayTeacherName="$displayTeacherName"
>
    @if (isset($slot))
        <x-slot:slot>
            {!! $slot !!}
        </x-slot:slot>
    @endif
    @if (isset($emptySlot))
        <x-slot:emptySlot>
            {!! $emptySlot !!}
        </x-slot:emptySlot>
    @endif
</x-ui.timetable-mobile>
