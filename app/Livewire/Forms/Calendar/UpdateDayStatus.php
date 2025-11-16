<?php

namespace App\Livewire\Forms\Calendar;

use App\Enums\DayPartEnum;
use App\Enums\SchoolDayType;
use App\Models\SchoolDay;
use Livewire\Attributes\Validate;
use Livewire\Form;

class UpdateDayStatus extends Form
{
    public ?SchoolDay $dayModel = null;

    public bool $canChangeStatus = true;

    #[Validate('required|date')]
    public $date;

    #[Validate('required')]
    public $status;

    #[Validate('required')]
    public $part;

    #[Validate('nullable|string|max:255')]
    public $notes = '';

    public function setDay($dateString)
    {
        $this->date = $dateString;

        $activeYear = school()->activeYear();
        $activeTerm = school()->currentAcademicTerm();

        $this->dayModel = SchoolDay::where('date', $dateString)
            ->where('academic_year_id', $activeYear?->id)
            ->when($activeTerm, function ($query) use ($activeTerm) {
                $query->where('academic_term_id', $activeTerm->id);
            })
            ->first();

        if ($this->dayModel) {
            // جلب القيمة من العمود status
            // نفترض أن الموديل يقوم بـ Cast للعمود status إلى SchoolDayType Enum
            $this->status = $this->dayModel->status->value ?? $this->dayModel->status;
            $this->part = $this->dayModel->day_part->value ?? $this->dayModel->day_part;
            $this->notes = $this->dayModel->notes;
            $this->canChangeStatus = $this->dayModel->hasAttendanceRecords() == false;
        } else {
            // قيم افتراضية
            $this->reset(['status', 'part', 'notes', 'dayModel']);
            $this->status = SchoolDayType::SchoolDay->value;
            $this->part = DayPartEnum::FULL_DAY->value;
        }
    }

    public function save()
    {
        $this->validate();

        if ($this->status != SchoolDayType::PartialHoliday->value) {
            $this->part = DayPartEnum::FULL_DAY->value;
        } elseif ($this->status == SchoolDayType::PartialHoliday->value && $this->part == DayPartEnum::FULL_DAY->value) {
            $this->part = DayPartEnum::PART_ONE_ONLY;
        }

        $activeYear = school()->activeYear();
        $activeTerm = school()->currentAcademicTerm();

        $data = [
            'academic_year_id' => $activeYear?->id,
            'academic_term_id' => $activeTerm?->id,
            'date' => $this->date,
            'status' => $this->status,
            'day_part' => $this->part,
            'notes' => $this->notes,
        ];

        if ($this->dayModel) {
            $this->dayModel->update($data);
        } else {
            SchoolDay::create($data);
        }

        $this->reset();
    }
}
