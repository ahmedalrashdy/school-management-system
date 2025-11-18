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

class EditGradingRule extends Component
{
    use AuthorizesRequests;

    public GradingRule $gradingRule;

    // Context
    public ?int $academic_year_id = null;

    public ?int $grade_id = null;

    public ?int $academic_term_id = null;

    public ?int $section_id = null;

    public ?int $curriculum_subject_id = null;

    // Grading structure
    public int $total_marks;

    public $passed_mark;

    public int $coursework_max_marks;

    public int $final_exam_max_marks;

    public ?int $final_exam_id = null;

    // Coursework items
    public array $courseworkItems = [];

    // Available data lists
    public array $sections = [];

    public array $subjects = [];

    public array $availableExams = [];

    public array $availableFinalExams = [];

    public function mount(GradingRule $gradingRule): void
    {
        $this->authorize(\Perm::GradingRulesUpdate->value);
        $this->gradingRule = $gradingRule;

        // 1. Fill Context
        // نحن بحاجة لجلب بيانات السنة والصف من السكشن المرتبط بالقاعدة
        $section = $gradingRule->section;
        $this->academic_year_id = $section->academic_year_id;
        $this->grade_id = $section->grade_id;
        $this->academic_term_id = $section->academic_term_id;
        $this->section_id = $gradingRule->section_id;
        $this->curriculum_subject_id = $gradingRule->curriculum_subject_id;

        // 2. Fill Structure
        $this->total_marks = $gradingRule->total_marks;
        $this->passed_mark = $gradingRule->passed_mark;
        $this->coursework_max_marks = $gradingRule->coursework_max_marks;
        $this->final_exam_max_marks = $gradingRule->final_exam_max_marks;
        $this->final_exam_id = $gradingRule->final_exam_id;

        // 3. Fill Items
        foreach ($gradingRule->items as $item) {
            $this->courseworkItems[] = [
                'exam_id' => $item->exam_id,
                'weight' => (float) $item->weight, // Casting to float to display nicely
            ];
        }

        // 4. Load Lists based on current data
        $this->loadSections();
        $this->loadSubjects();
        $this->loadAvailableExams();
    }

    // --- Listeners for changes (Same as Create to keep lists dynamic) ---

    public function updatedAcademicYearId()
    {
        $this->resetFilters();
    }

    public function updatedGradeId()
    {
        $this->resetFilters();
    }

    public function updatedAcademicTermId()
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

    public function updatedSectionId()
    {
        // إذا غير المستخدم الشعبة، يجب تصفير الاختبارات المختارة لأنها تابعة للشعبة القديمة
        $this->resetExams();
        $this->loadAvailableExams();
    }

    public function updatedCurriculumSubjectId()
    {
        $this->resetExams();
        $this->loadAvailableExams();
    }

    // --- Loaders ---

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

        if (!$this->section_id || !$this->curriculum_subject_id) {
            return;
        }

        $baseQuery = Exam::where('section_id', $this->section_id)
            ->where('curriculum_subject_id', $this->curriculum_subject_id);

        // Coursework Exams
        $exams = (clone $baseQuery)->where('is_final', false)->get();
        $this->availableExams = $exams->map(function ($exam) {
            return [
                'id' => $exam->id,
                'text' => $exam->examType->name . ' - ' . ' (' . $exam->max_marks . ' درجة)',
                'max_marks' => $exam->max_marks,
            ];
        })->toArray();

        // Final Exams
        $this->availableFinalExams = (clone $baseQuery)->where('is_final', true)
            ->get()->map(function ($exam) {
                return [
                    'id' => $exam->id,
                    'text' => $exam->examType->name . ' - ' . ' (' . $exam->max_marks . ' درجة)',
                ];
            })->pluck('text', 'id')->toArray();
    }

    public function resetExams(): void
    {
        // نحتفظ بالقوائم فارغة، ونفرغ العناصر المختارة
        $this->availableExams = [];
        $this->availableFinalExams = [];
        $this->courseworkItems = [];
        $this->final_exam_id = null;
    }

    // --- Actions ---

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

    // --- Validation logic ---

    public function validateDistribution(): bool
    {
        return ($this->coursework_max_marks + $this->final_exam_max_marks) == $this->total_marks;
    }

    public function validateCourseworkWeights(): bool
    {
        return collect($this->courseworkItems)->sum('weight') == 100;
    }

    public function update()
    {
        $this->authorize(\Perm::GradingRulesUpdate->value);
        $this->validate([
            'section_id' => [
                'required',
                'exists:sections,id',
                function ($attribute, $value, $fail) {
                    // التحقق من التكرار مع استثناء السجل الحالي
                    $exists = GradingRule::where('section_id', $value)
                        ->where('curriculum_subject_id', $this->curriculum_subject_id)
                        ->where('id', '!=', $this->gradingRule->id) // استثناء الحالي
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

        if (!$this->validateDistribution()) {
            $this->addError('distribution', 'مجموع أعمال الفصل والاختبار النهائي يجب أن يساوي الدرجة الكلية.');

            return;
        }

        if (!$this->validateCourseworkWeights()) {
            $this->addError('coursework_weights', 'مجموع أوزان أعمال الفصل يجب أن تساوي 100.');

            return;
        }

        DB::transaction(function () {
            // 1. Update Main Rule
            $this->gradingRule->update([
                'section_id' => $this->section_id,
                'curriculum_subject_id' => $this->curriculum_subject_id,
                'total_marks' => $this->total_marks,
                'passed_mark' => $this->passed_mark,
                'coursework_max_marks' => $this->coursework_max_marks,
                'final_exam_max_marks' => $this->final_exam_max_marks,
                'final_exam_id' => $this->final_exam_id,
                // keep is_published as is or logic to reset it if needed
            ]);

            // 2. Sync Items (Delete all old, create new) - الأبسط والأضمن لتجنب تعقيدات التحديث
            $this->gradingRule->items()->delete();

            foreach ($this->courseworkItems as $item) {
                GradingRuleItem::create([
                    'grading_rule_id' => $this->gradingRule->id,
                    'exam_id' => $item['exam_id'],
                    'weight' => $item['weight'],
                ]);
            }
        });

        session()->flash('success', 'تم تحديث قاعدة الاحتساب بنجاح.');

        return $this->redirect(route('dashboard.grading-rules.index'), navigate: true);
    }

    #[Layout('components.layouts.dashboard', ['page-title' => 'تعديل قاعدة احتساب'])]
    public function render()
    {
        return view('livewire.dashboard.examinations.grading-rules.edit-grading-rule');
    }
}
