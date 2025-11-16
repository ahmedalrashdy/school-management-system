@props(['selectedDayPart'])

<div class="bg-white dark:bg-gray-800/80 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-2">
    <div class="flex gap-2">
        {{-- Before Break Tab --}}
        <button
            wire:click="switchDayPart({{ \App\Enums\DayPartEnum::PART_ONE_ONLY->value }})"
            class="relative flex-1 flex items-center justify-center gap-3 px-5 py-4 rounded-xl font-semibold transition-all duration-300 overflow-hidden
            {{ $selectedDayPart === \App\Enums\DayPartEnum::PART_ONE_ONLY->value
                ? 'text-white shadow-lg'
                : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/50' }}"
        >
            @if ($selectedDayPart === \App\Enums\DayPartEnum::PART_ONE_ONLY->value)
                <div class="absolute inset-0 bg-gradient-to-l from-amber-500 to-orange-500"></div>
            @endif
            <div class="relative flex items-center gap-3">
                <div
                    class="{{ $selectedDayPart === \App\Enums\DayPartEnum::PART_ONE_ONLY->value ? 'bg-white/20' : 'bg-amber-100 dark:bg-amber-900/30' }} w-10 h-10 rounded-lg flex items-center justify-center">
                    <i
                        class="fas fa-sun {{ $selectedDayPart === \App\Enums\DayPartEnum::PART_ONE_ONLY->value ? 'text-white' : 'text-amber-600 dark:text-amber-400' }}"></i>
                </div>
                <div class="text-right">
                    <span class="block">الفترة الأولى</span>
                    <span
                        class="text-xs {{ $selectedDayPart === \App\Enums\DayPartEnum::PART_ONE_ONLY->value ? 'text-white/70' : 'text-gray-400' }}"
                    >قبل الفسحة</span>
                </div>
            </div>
        </button>

        {{-- Divider --}}
        <div class="w-px bg-gray-200 dark:bg-gray-700 my-2"></div>

        {{-- After Break Tab --}}
        <button
            wire:click="switchDayPart({{ \App\Enums\DayPartEnum::PART_TWO_ONLY->value }})"
            class="relative flex-1 flex items-center justify-center gap-3 px-5 py-4 rounded-xl font-semibold transition-all duration-300 overflow-hidden
            {{ $selectedDayPart === \App\Enums\DayPartEnum::PART_TWO_ONLY->value
                ? 'text-white shadow-lg'
                : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/50' }}"
        >
            @if ($selectedDayPart === \App\Enums\DayPartEnum::PART_TWO_ONLY->value)
                <div class="absolute inset-0 bg-linear-to-l from-indigo-500 to-blue-500"></div>
            @endif
            <div class="relative flex items-center gap-3">
                <div
                    class="{{ $selectedDayPart === \App\Enums\DayPartEnum::PART_TWO_ONLY->value ? 'bg-white/20' : 'bg-indigo-100 dark:bg-indigo-900/30' }} w-10 h-10 rounded-lg flex items-center justify-center">
                    <i
                        class="fas fa-moon {{ $selectedDayPart === \App\Enums\DayPartEnum::PART_TWO_ONLY->value ? 'text-white' : 'text-indigo-600 dark:text-indigo-400' }}"></i>
                </div>
                <div class="text-right">
                    <span class="block">الفترة الثانية</span>
                    <span
                        class="text-xs {{ $selectedDayPart === \App\Enums\DayPartEnum::PART_TWO_ONLY->value ? 'text-white/70' : 'text-gray-400' }}"
                    >بعد الفسحة</span>
                </div>
            </div>
        </button>
    </div>
</div>
