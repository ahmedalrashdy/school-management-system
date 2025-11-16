<?php

namespace App\Livewire\Dashboard\Attendance\Reports;

use App\Jobs\GenerateAttendancePdfJob;
use App\Models\AcademicTerm;
use App\Models\Section;
use Carbon\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Component;

class AttendanceReportsFilter extends Component
{
    public ?int $academicYearId = null;
    public ?int $gradeId = null;
    public ?int $academicTermId = null;
    public ?int $sectionId = null;

    public ?string $startDate = null;
    public ?string $endDate = null;
    public ?string $minDate = null;
    public ?string $maxDate = null;

    public function mount()
    {
        $this->academicYearId = school()->activeYear()?->id;
        $this->academicTermId = school()->currentAcademicTerm()?->id;
        $this->updatedAcademicTermId();
    }

    public function updatedAcademicYearId()
    {
        $this->reset(['academicTermId', 'sectionId', 'startDate', 'endDate', 'minDate', 'maxDate']);
    }

    public function updatedGradeId()
    {
        $this->reset(['sectionId']);
    }

    public function updatedAcademicTermId()
    {
        $this->reset(['sectionId', 'startDate', 'endDate', 'minDate', 'maxDate']);

        if ($this->academicTermId) {
            $term = AcademicTerm::find($this->academicTermId);
            if ($term) {
                // Initialize range with term dates
                $this->minDate = $term->start_date->format('Y-m-d');
                $this->maxDate = $term->end_date->format('Y-m-d');
                $this->startDate = $this->minDate;
                $this->endDate = $this->maxDate;
            }
        }
    }

    public function apply()
    {
        $this->validate([
            'sectionId' => 'required|exists:sections,id',
            'academicTermId' => 'required|exists:academic_terms,id',
            'startDate' => 'nullable|date|after_or_equal:minDate',
            'endDate' => 'nullable|date|before_or_equal:maxDate|after_or_equal:startDate',
        ]);

        $this->dispatch(
            'load-report',
            sectionId: $this->sectionId,
            academicTermId: $this->academicTermId,
            startDate: $this->startDate,
            endDate: $this->endDate,
        );
    }

    public function exportPdf()
    {
        if (!$this->academicTermId || (!$this->sectionId && !$this->gradeId)) {
            $this->dispatch('show-toast', type: 'error', message: 'Missing required parameters');
        }
        GenerateAttendancePdfJob::dispatch(
            auth()->user(),
            $this->sectionId,
            $this->gradeId,
            $this->academicTermId,
            Carbon::parse($this->startDate),
            Carbon::parse($this->endDate)
        );
        $this->dispatch('show-toast', type: 'success', message: 'جاري إعداد التقرير في الخلفية، سيصلك إشعار عند الانتهاء.');
    }

    #[Computed()]
    public function sections()
    {
        if (!$this->academicYearId || !$this->gradeId || !$this->academicTermId) {
            return collect();
        }

        return Section::where('academic_year_id', $this->academicYearId)
            ->where('grade_id', $this->gradeId)
            ->where('academic_term_id', $this->academicTermId)
            ->orderBy('name')
            ->get();
    }

    public function render()
    {
        return view('livewire.dashboard.attendance.reports.attendance-reports-filter');
    }
}
