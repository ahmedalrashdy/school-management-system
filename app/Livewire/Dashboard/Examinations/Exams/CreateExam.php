<?php

namespace App\Livewire\Dashboard\Examinations\Exams;

use App\Models\CurriculumSubject;
use App\Models\Exam;
use App\Models\ExamType;
use App\Models\Section;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

class CreateExam extends Component
{
    use AuthorizesRequests;

    public ?int $academic_year_id = null;
    public ?int $grade_id = null;
    public ?int $academic_term_id = null;



    // تفاصيل الامتحان
    public ?int $curriculum_subject_id = null;
    public ?int $exam_type_id = null;
    public ?string $exam_date = null;
    public ?int $max_marks = null;
    public bool $is_final = false;
    public ?int $section_id = null;

    public function mount(): void
    {
        $this->authorize(\Perm::ExamsCreate->value);
        $this->academic_year_id = school()->activeYear()?->id;
        $this->academic_term_id = school()->currentAcademicTerm()?->id;
    }


    #[Computed]
    public function curriculumSubjects(): array
    {
        if (!$this->grade_id || !$this->academic_year_id || !$this->academic_term_id) {
            return [];
        }
        return CurriculumSubject::query()
            ->join('curriculums', 'curriculum_subject.curriculum_id', '=', 'curriculums.id')
            ->join('subjects', 'curriculum_subject.subject_id', '=', 'subjects.id')
            ->where('curriculums.academic_year_id', $this->academic_year_id)
            ->where('curriculums.grade_id', $this->grade_id)
            ->where('curriculums.academic_term_id', $this->academic_term_id)
            ->orderBy('subjects.sort_order')
            ->pluck('subjects.name', 'curriculum_subject.id')
            ->toArray();
    }

    #[Computed]
    public function sections(): array
    {
        if (!$this->grade_id || !$this->academic_year_id || !$this->academic_term_id) {
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


    public function updatedAcademicYearId()
    {
        $this->resetDependentFields();
    }

    public function updatedGradeId()
    {
        $this->resetDependentFields();
    }

    public function updatedAcademicTermId()
    {
        $this->resetDependentFields();
    }

    protected function resetDependentFields()
    {
        $this->curriculum_subject_id = null;
        $this->section_id = null;
    }


    public function save()
    {
        $this->authorize(\Perm::ExamsCreate->value);

        $validated = $this->validate([
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'grade_id' => ['required', 'exists:grades,id'],
            'academic_term_id' => ['required', 'exists:academic_terms,id'],

            'curriculum_subject_id' => [
                'required',
                'exists:curriculum_subject,id',
                function ($attribute, $value, $fail) {
                    $exists = CurriculumSubject::query()
                        ->join('curriculums', 'curriculum_subject.curriculum_id', '=', 'curriculums.id')
                        ->where('curriculum_subject.id', $value)
                        ->where('curriculums.academic_year_id', $this->academic_year_id)
                        ->where('curriculums.grade_id', $this->grade_id)
                        ->where('curriculums.academic_term_id', $this->academic_term_id)
                        ->exists();

                    if (!$exists) {
                        $fail('المادة المختارة لا تنتمي للمنهج أو السياق الأكاديمي المحدد.');
                    }
                },
            ],

            'exam_type_id' => ['required', 'exists:exam_types,id'],
            'section_id' => ['required', 'exists:sections,id'],
            'exam_date' => ['required', 'date'],
            'max_marks' => ['required', 'integer', 'min:1'],
            'is_final' => [
                'boolean',
                function ($_, $value, $fail) {
                    if ($value) {
                        $exists = Exam::query()
                            ->where('section_id', $this->section_id)
                            ->where('curriculum_subject_id', $this->curriculum_subject_id)
                            ->where('is_final', true)
                            ->exists();
                        if ($exists) {
                            $fail('يوجد إختبار نهائي مسجل بالفعل لهذه المادة في هذه الشعبة.');
                        }
                    }
                }
            ],
        ]);

        Exam::create($validated);

        session()->flash('success', 'تم إنشاء الامتحان بنجاح.');

        return $this->redirect(route('dashboard.exams.list'), navigate: true);
    }

    #[Layout('components.layouts.dashboard', ['page-title' => 'إضافة امتحان جديد'])]
    public function render()
    {
        return view('livewire.dashboard.examinations.exams.create-exam');
    }
}
