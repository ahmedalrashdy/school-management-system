<?php

namespace App\Livewire\Dashboard\Academics\Calendar;

use App\Livewire\Forms\Calendar\UpdateDayStatus;
use App\Models\AcademicTerm;
use App\Models\SchoolDay;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

class AcademicCalendar extends Component
{
    use AuthorizesRequests;

    public $academic_year_id;

    public ?AcademicTerm $term = null;

    public $currentMonth;

    public $termStartMonth;

    public $termEndMonth;

    public $canGoNext = false;

    public $canGoPrev = false;

    public function mount(): void
    {

        // current term and current year is the default
        $this->academic_year_id = school()->activeYear()?->id;
        $this->term = school()->currentAcademicTerm();
        if ($this->term) {
            $this->currentMonth = Carbon::parse($this->term->start_date);
            $this->termStartMonth = Carbon::parse($this->term->start_date);
            $this->termEndMonth = Carbon::parse($this->term->end_date);
        }

    }

    public function nextMonth()
    {
        if (!$this->canGoNext) {
            return;
        }

        $this->currentMonth->startOfMonth()->addMonth();
    }

    public function previousMonth()
    {
        if (!$this->canGoPrev) {
            return;
        }

        $this->currentMonth->startOfMonth()->subMonth();
    }

    private function checkNavigationLimits()
    {
        $this->canGoNext = $this->currentMonth->lessThan($this->termEndMonth);
        $this->canGoPrev = $this->currentMonth->greaterThan($this->termStartMonth);
    }

    #[Computed()]
    public function currentMonthDays()
    {
        if ($this->term == null) {
            return collect();
        }
        $this->checkNavigationLimits();
        $monthStart = $this->currentMonth->copy()->startOfMonth();
        $monthEnd = $this->currentMonth->copy()->endOfMonth();

        $dbDays = SchoolDay::query()
            ->where('academic_year_id', $this->academic_year_id)
            ->where('academic_term_id', $this->term->id)
            ->whereMonth('date', $this->currentMonth->month)
            ->get()
            ->keyBy(fn($item) => $item->date->format('Y-m-d'));
        if ($dbDays->count() == 0) {
            return collect();
        }


        $calendarGrid = [];

        // sat day is first day (day+1)%7
        $dayOfWeek = $monthStart->dayOfWeek;
        $paddingSize = ($dayOfWeek + 1) % 7;

        for ($i = 0; $i < $paddingSize; $i++) {
            $calendarGrid[] = null;
        }

        // 5. بناء الأيام
        $currDate = $monthStart->copy();

        while ($currDate <= $monthEnd) {
            $dateString = $currDate->format('Y-m-d');

            $isInterm = false;
            if ($this->termStartMonth && $this->termEndMonth) {

                $isInterm = $currDate->between($this->termStartMonth, $this->termEndMonth);
            }

            $dayData = [
                'date' => $currDate->copy(),
                'is_in_term' => $isInterm,
                'model' => null,
                'is_today' => $currDate->isToday(),
            ];

            if ($isInterm && $dbDays->has($dateString)) {
                $dayData['model'] = $dbDays[$dateString];
            }

            $calendarGrid[] = (object) $dayData;
            $currDate->addDay();
        }

        return collect($calendarGrid);
    }

    // update day
    public UpdateDayStatus $form;

    public function editDay($date)
    {
        $this->form->setDay($date);

        $formattedDate = \Carbon\Carbon::parse($date)->locale('ar')->translatedFormat('l j F Y');

        $this->dispatch('open-modal', name: 'edit-day-modal', data: [
            'date_formatted' => $formattedDate,
            'status' => $this->form->status,
            'part' => $this->form->part,
            'notes' => $this->form->notes,
        ]);
    }

    public function updateDayStatus()
    {
        $this->form->save();
        $this->dispatch('close-modal', name: 'edit-day-modal');
        $this->dispatch('show-toast', message: 'تم تحديث حالة اليوم بنجاح');
    }

    #[Layout('components.layouts.dashboard', ['page-title' => 'إدارة التقويم الدراسي'])]
    public function render()
    {
        return view('livewire.dashboard.academics.calendar.academic-calendar');
    }
}
