<?php

namespace App\Livewire\Dashboard\Examinations\GradingRules;

use App\Models\GradingRule;
use App\Models\Section;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class GradingRulesIndex extends Component
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
    public string $search = '';

    public function mount(): void
    {
        $this->authorize(\Perm::GradingRulesView->value);

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
        $this->search = '';
        $this->resetPage();
    }

    public function getHasActiveFiltersProperty(): bool
    {
        return $this->academic_year_id !== school()->activeYear()?->id
            || $this->academic_term_id !== school()->currentAcademicTerm()?->id
            || $this->grade_id !== null
            || $this->section_id !== null
            || !empty($this->search);
    }

    #[Layout('components.layouts.dashboard', ['page-title' => 'قواعد الاحتساب'])]
    public function render()
    {
        $gradingRules = GradingRule::query()
            ->with([
                'section.grade.stage',
                'curriculumSubject.subject',
            ])
            ->when($this->academic_year_id, function ($q) {
                $q->whereHas('section', fn($sub) => $sub->where('academic_year_id', $this->academic_year_id));
            })
            ->when($this->academic_term_id, function ($q) {
                $q->whereHas('section', fn($sub) => $sub->where('academic_term_id', $this->academic_term_id));
            })
            ->when($this->grade_id, function ($q) {
                $q->whereHas('section', fn($sub) => $sub->where('grade_id', $this->grade_id));
            })
            ->when($this->section_id, fn($q) => $q->where('section_id', $this->section_id))
            ->when($this->search, function ($q) {
                $q->where(function ($subQ) {
                    $subQ->whereHas('section', fn($s) => $s->whereLike('name', "%{$this->search}%"))
                        ->orWhereHas(
                            'curriculumSubject.subject',
                            fn($s) => $s->whereLike('name', "%{$this->search}%")
                        );
                });
            })
            ->latest()
            ->paginate(20);

        return view('livewire.dashboard.examinations.grading-rules.grading-rules-index', [
            'gradingRules' => $gradingRules,
        ]);
    }
}
