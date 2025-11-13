<?php

namespace App\View\Components\Ui;

use App\Enums\DayOfWeekEnum;
use App\Models\TimetableSetting;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Timetable extends Component
{
    public $days;

    public $maxPeriodNumber;

    /**
     * Create a new component instance.
     */
    public function __construct(public TimetableSetting $timetableSetting, public $slotsGrouped, public bool $displayTeacherName = true)
    {
        $this->days = $this->orderDays();
        $this->maxPeriodNumber = max(array_map('intval', $this->timetableSetting->periods_per_day));
    }

    public function orderDays(): array
    {
        $setting = $this->timetableSetting;
        $periodsPerDay = $setting->periods_per_day;
        $days = [];

        foreach ($periodsPerDay as $dayKey => $periodNumber) {
            if ($periodNumber == 0) {
                continue;
            }

            $day = DayOfWeekEnum::fromName($dayKey);
            if (! $day) {
                continue;
            }

            $days[] = [
                'day' => $day,
                'period_number' => $periodNumber,
            ];
        }

        usort($days, fn ($a, $b) => $a['day']->value <=> $b['day']->value);

        return $days;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.ui.timetable');
    }
}
