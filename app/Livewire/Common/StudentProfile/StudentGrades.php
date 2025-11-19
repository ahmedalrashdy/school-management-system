<?php

namespace App\Livewire\Common\StudentProfile;

use App\Models\Section;
use App\Models\Student;
use App\Services\Marksheets\StudentMarksheetService;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Component;

class StudentGrades extends Component
{
    #[Locked]
    public int $studentId;

    public ?int $academicYearId = null;

    public ?int $gradeId = null;

    public ?int $termId = null;

    public array $subjects = [];

    public ?Section $section = null;

    public bool $dataLoaded = false;

    public function mount(int $studentId, StudentMarksheetService $service): void
    {
        $this->studentId = $studentId;

        $latestSection = $this->student->sections()
            ->latest('section_students.created_at')
            ->first();

        if ($latestSection) {
            $this->academicYearId = $latestSection->academic_year_id;
            $this->gradeId = $latestSection->grade_id;
            $this->termId = $latestSection->academic_term_id;
            $this->section = $latestSection;
            $this->fetchMarks($service);
        }
    }

    #[Computed]
    public function student()
    {
        return Student::findOrFail($this->studentId);
    }

    #[Computed]
    public function years()
    {
        return $this->student->academicYears()->distinct()
            ->pluck('academic_years.name', 'academic_years.id')->toArray();
    }

    #[Computed]
    public function grades()
    {
        if (! $this->academicYearId) {
            return [];
        }

        return $this->student->grades()->wherePivot('academic_year_id', $this->academicYearId)
            ->distinct()
            ->pluck('grades.name', 'grades.id')->toArray();
    }

    #[Computed]
    public function terms()
    {
        if (! $this->academicYearId || ! $this->gradeId) {
            return [];
        }

        return $this->student->sections()
            ->where('academic_year_id', $this->academicYearId)
            ->where('grade_id', $this->gradeId)
            ->with('academicTerm')
            ->get()
            ->pluck('academicTerm')
            ->unique('id')
            ->pluck('name', 'id')
            ->toArray();
    }

    public function updatedAcademicYearId()
    {
        $this->resetSelections();
    }

    public function updatedGradeId()
    {
        $this->termId = null;
        $this->resetResults();
    }

    public function updatedTermId()
    {
        if ($this->termId) {
            $this->loadGrades(app(StudentMarksheetService::class));
        } else {
            $this->resetResults();
        }
    }

    protected function resetSelections()
    {
        $this->gradeId = null;
        $this->termId = null;
        $this->resetResults();
    }

    protected function resetResults()
    {
        $this->dataLoaded = false;
        $this->subjects = [];
        $this->section = null;
    }

    public function loadGrades(StudentMarksheetService $service)
    {
        $this->validate([
            'academicYearId' => 'required',
            'gradeId' => 'required',
            'termId' => 'required',
        ]);

        $this->section = $this->student->sections()
            ->with(['grade', 'academicYear', 'academicTerm'])
            ->where('academic_year_id', $this->academicYearId)
            ->where('grade_id', $this->gradeId)
            ->where('academic_term_id', $this->termId)
            ->first();

        $this->fetchMarks($service);
    }

    protected function fetchMarks(StudentMarksheetService $service)
    {
        if ($this->section) {
            $data = $service->getStudentDetailedMarks($this->student, $this->section);
            $this->subjects = $data['subjects'] ?? [];
            $this->dataLoaded = true;
        } else {
            $this->subjects = [];
            $this->dataLoaded = true;
        }
    }

    public function render()
    {
        return view('livewire.common.student-profile.student-grades');
    }
}
