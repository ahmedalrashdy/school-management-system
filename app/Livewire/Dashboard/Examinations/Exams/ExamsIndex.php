<?php

namespace App\Livewire\Dashboard\Examinations\Exams;

use App\Models\Exam;
use App\Models\ExamType;
use App\Models\Section;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class ExamsIndex extends Component
{
    use AuthorizesRequests, WithPagination;

    #[Url(history: true)]
    public ?int $academic_year_id = null;

    #[Url(history: true)]
    public ?int $academic_term_id = null;

    #[Url(history: true)]
    public ?int $grade_id = null;

    #[Url(history: true)]
    public ?int $section_id = null;

    #[Url(history: true)]
    public ?int $exam_type_id = null;

    #[Url(history: true)]
    public string $search = '';

    public function mount(): void
    {
        $this->authorize(\Perm::ExamsView->value);

        if (request()->query('academic_year_id') === null) {
            $this->academic_year_id = school()->activeYear()?->id;
        }

        if (request()->query('academic_term_id') === null) {
            $this->academic_term_id = school()->currentAcademicTerm()?->id;
        }
    }


    #[Computed]
    public function yearsTree()
    {
        return lookup()->yearsTree();
    }

    #[Computed]
    public function grades(): array
    {
        return lookup()->getGrades();
    }

    #[Computed]
    public function sections(): array
    {
        if (!$this->academic_year_id || !$this->grade_id || !$this->academic_term_id) {
            return [];
        }

        return Section::where('academic_year_id', $this->academic_year_id)
            ->where('grade_id', $this->grade_id)
            ->where('academic_term_id', $this->academic_term_id)
            ->pluck('name', 'id')
            ->toArray();
    }

    #[Computed]
    public function examTypes(): array
    {
        return ExamType::sorted()->pluck('name', 'id')->toArray();
    }


    public function updatedGradeId()
    {
        $this->section_id = null;
        $this->resetPage();
    }

    public function updatedAcademicYearId()
    {
        $this->section_id = null;
        $this->resetPage();
    }

    public function updatedAcademicTermId()
    {
        $this->section_id = null;
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->academic_year_id = school()->activeYear()?->id;
        $this->academic_term_id = school()->currentAcademicTerm()?->id;
        $this->grade_id = null;
        $this->section_id = null;
        $this->exam_type_id = null;
        $this->search = '';
        $this->resetPage();
    }

    public function getHasActiveFiltersProperty(): bool
    {
        return $this->academic_year_id !== school()->activeYear()?->id
            || $this->academic_term_id !== school()->currentAcademicTerm()?->id
            || $this->grade_id !== null
            || $this->section_id !== null
            || $this->exam_type_id !== null
            || !empty($this->search);
    }

    #[Layout('components.layouts.dashboard', ['page-title' => 'الامتحانات'])]
    public function render()
    {
        $exams = Exam::query()
            ->with([
                'academicYear',
                'academicTerm',
                'examType',
                'curriculumSubject.subject',
                'curriculumSubject.curriculum.grade.stage',
                'section',
            ])
            ->withCount('marks as marks_count')
            ->when($this->search, function ($q) {
                $q->whereHas('curriculumSubject.subject', function ($subQ) {
                    $subQ->whereLike('name', '%' . $this->search . '%');
                });
            })
            ->when($this->academic_year_id, fn($q) => $q->where('academic_year_id', $this->academic_year_id))
            ->when($this->academic_term_id, fn($q) => $q->where('academic_term_id', $this->academic_term_id))
            ->when($this->grade_id, function ($q) {
                $q->whereHas('curriculumSubject.curriculum', fn($subQ) => $subQ->where('grade_id', $this->grade_id));
            })
            ->when($this->section_id, fn($q) => $q->where('section_id', $this->section_id))
            ->when($this->exam_type_id, fn($q) => $q->where('exam_type_id', $this->exam_type_id))
            ->latest('exam_date')
            ->paginate(20);

        return view('livewire.dashboard.examinations.exams.exams-index', [
            'exams' => $exams,
        ]);
    }
}
