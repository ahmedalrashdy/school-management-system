<?php

namespace App\Livewire\Dashboard\Examinations\GradingRules;

use App\Models\CurriculumSubject;
use App\Models\Exam;
use App\Models\GradingRule;
use App\Models\GradingRuleItem;
use App\Models\Section;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

class CreateGradingRule extends Component
{
    use AuthorizesRequests;

    public ?int $academic_year_id = null;

    public ?int $grade_id = null;

    public ?int $academic_term_id = null;

    public ?int $section_id = null;

    public ?int $curriculum_subject_id = null;

    // Grading structure
    public int $total_marks = 100;

    public $passed_mark = 50;

    public int $coursework_max_marks = 40;

    public int $final_exam_max_marks = 60;

    public ?int $final_exam_id = null;

    // Coursework items
    public array $courseworkItems = [];

    // Available data
    public array $sections = [];

    public array $subjects = [];

    public array $availableExams = [];

    public array $availableFinalExams = [];

    public function mount(): void
    {
        $this->authorize(\Perm::GradingRulesCreate->value);
        $activeYear = school()->activeYear();
        if ($activeYear) {
            $this->academic_year_id = $activeYear->id;
        }

        $activeTerm = school()->currentAcademicTerm();
        if ($activeTerm) {
            $this->academic_term_id = $activeTerm->id;
        }

        if ($this->academic_year_id && $this->grade_id && $this->academic_term_id) {
            $this->loadSections();
            $this->loadSubjects();
        }
    }

    public function updatedAcademicYearId(): void
    {
        $this->resetFilters();
    }

    public function updatedGradeId(): void
    {
        $this->resetFilters();
    }

    public function updatedAcademicTermId(): void
    {
        $this->resetFilters();
    }

    protected function resetFilters()
    {
        $this->section_id = null;
        $this->curriculum_subject_id = null;
        $this->loadSections();
        $this->loadSubjects();
        $this->resetExams();
    }

    public function updatedSectionId(): void
    {
        $this->loadAvailableExams();
    }

    public function loadSections(): void
    {
        $this->sections = [];

        if (!$this->academic_year_id || !$this->grade_id || !$this->academic_term_id) {
            return;
        }

        $this->sections = Section::where('academic_year_id', $this->academic_year_id)
            ->where('grade_id', $this->grade_id)
            ->where('academic_term_id', $this->academic_term_id)
            ->pluck('name', 'id')
            ->toArray();
    }

    public function loadSubjects(): void
    {
        $this->subjects = [];

        if (!$this->academic_year_id || !$this->grade_id || !$this->academic_term_id) {
            return;
        }

        $this->subjects = CurriculumSubject::query()
            ->whereHas('curriculum', function (Builder $query) {
                $query->where('academic_year_id', $this->academic_year_id)
                    ->where('grade_id', $this->grade_id)
                    ->where('academic_term_id', $this->academic_term_id);
            })
            ->join('subjects', 'curriculum_subject.subject_id', '=', 'subjects.id')
            ->pluck('subjects.name as subject_name', 'curriculum_subject.id')
            ->toArray();

    }

    public function loadAvailableExams(): void
    {
        $this->availableExams = [];
        $this->availableFinalExams = [];

        if (!$this->section_id) {
            return;
        }

        $baseQuery = Exam::where('section_id', $this->section_id)
            ->where('curriculum_subject_id', $this->curriculum_subject_id);
        $exams = $baseQuery->where('is_final', false)->get();
        $this->availableExams = $exams->map(function ($exam) {
            return [
                'id' => $exam->id,
                'text' => $exam->examType->name . ' - ' . ' (' . $exam->max_marks . ' درجة)',
                'max_marks' => $exam->max_marks,
            ];
        })->toArray();

        $this->availableFinalExams = Exam::where('section_id', $this->section_id)
            ->where('curriculum_subject_id', $this->curriculum_subject_id)
            ->where('is_final', true)
            ->get()->map(function ($exam) {
                return [
                    'id' => $exam->id,
                    'text' => $exam->examType->name . ' - ' . ' (' . $exam->max_marks . ' درجة)',
                ];
            })->pluck('text', 'id')->toArray();
    }

    public function resetExams(): void
    {
        $this->availableExams = [];
        $this->availableFinalExams = [];
        $this->courseworkItems = [];
        $this->final_exam_id = null;
    }

    public function addCourseworkItem(): void
    {
        $this->courseworkItems[] = [
            'exam_id' => null,
            'weight' => 0,
        ];
    }

    public function removeCourseworkItem(int $index): void
    {
        unset($this->courseworkItems[$index]);
        $this->courseworkItems = array_values($this->courseworkItems);
    }

    public function validateDistribution(): bool
    {
        return ($this->coursework_max_marks + $this->final_exam_max_marks) == $this->total_marks;
    }

    public function validateCourseworkWeights(): bool
    {
        return collect($this->courseworkItems)->sum('weight') == 100;
    }

    public function save()
    {
        $this->authorize(\Perm::GradingRulesCreate->value);
        $this->validate([
            'section_id' => [
                'required',
                'exists:sections,id',
                function ($attribute, $value, $fail) {
                    $exists = GradingRule::where('section_id', $value)
                        ->where('curriculum_subject_id', $this->curriculum_subject_id)
                        ->exists();
                    if ($exists) {
                        $fail('يوجد بالفعل قاعدة احتساب لهذا المقرر لشعبة في هذا الفصل الدراسي.');
                    }
                }
            ],
            'curriculum_subject_id' => ['required', 'exists:curriculum_subject,id'],
            'total_marks' => ['required', 'integer', 'min:1'],
            'passed_mark' => [
                'required',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) {
                    if ($value > $this->total_marks) {
                        $fail('درجة النجاح يجب أن تكون أقل من أو تساوي الدرجة الكلية.');
                    }
                }
            ],
            'coursework_max_marks' => ['required', 'integer', 'min:0'],
            'final_exam_max_marks' => ['required', 'integer', 'min:0'],
            'final_exam_id' => ['nullable', 'exists:exams,id'],
            'courseworkItems' => ['required', 'array', 'min:1'],
            'courseworkItems.*.exam_id' => ['required', 'exists:exams,id', 'distinct'],
            'courseworkItems.*.weight' => ['required', 'numeric', 'min:0'],
        ]);

        // Validate distribution
        if (!$this->validateDistribution()) {
            $this->addError('distribution', 'مجموع أعمال الفصل والاختبار النهائي يجب أن يساوي الدرجة الكلية.');

            return;
        }

        // Validate coursework weights
        if (!$this->validateCourseworkWeights()) {
            $this->addError('coursework_weights', 'مجموع أوزان أعمال الفصل يجب أن تساوي 100 ');

            return;
        }

        DB::transaction(function () {
            $gradingRule = GradingRule::create([
                'section_id' => $this->section_id,
                'curriculum_subject_id' => $this->curriculum_subject_id,
                'total_marks' => $this->total_marks,
                'passed_mark' => $this->passed_mark,
                'coursework_max_marks' => $this->coursework_max_marks,
                'final_exam_max_marks' => $this->final_exam_max_marks,
                'final_exam_id' => $this->final_exam_id,
                'is_published' => false,
            ]);

            foreach ($this->courseworkItems as $item) {
                GradingRuleItem::create([
                    'grading_rule_id' => $gradingRule->id,
                    'exam_id' => $item['exam_id'],
                    'weight' => $item['weight'],
                ]);
            }
        });

        session()->flash('success', 'تم إنشاء قاعدة الاحتساب بنجاح.');

        return $this->redirect(route('dashboard.grading-rules.index'), navigate: true);
    }

    #[Layout('components.layouts.dashboard', ['page-title' => 'إضافة قاعدة احتساب'])]
    public function render()
    {
        return view('livewire.dashboard.examinations.grading-rules.create-grading-rule');
    }
}
