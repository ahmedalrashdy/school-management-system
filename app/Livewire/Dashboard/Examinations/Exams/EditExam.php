<?php

namespace App\Livewire\Dashboard\Examinations\Exams;

use App\Models\CurriculumSubject;
use App\Models\Exam;
use App\Models\ExamType;
use App\Models\Section;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;

class EditExam extends Component
{
    use AuthorizesRequests;

    public Exam $exam;

    #[Locked]
    public ?int $exam_id = null;

    public ?int $academic_year_id = null;
    public ?int $grade_id = null;
    public ?int $academic_term_id = null;

    public ?int $curriculum_subject_id = null;

    public ?int $exam_type_id = null;
    public ?string $exam_date = null;
    public ?int $max_marks = null;
    public bool $is_final = false;

    // النطاق
    public ?int $section_id = null;

    public function mount(Exam $exam): void
    {
        $this->authorize(\Perm::ExamsUpdate->value);

        $this->exam = $exam->load(['academicYear', 'curriculumSubject.subject', 'section', 'examType']);
        $this->exam_id = $exam->id;

        $this->academic_year_id = $this->exam->academic_year_id;
        $this->grade_id = $this->exam->curriculumSubject->curriculum->grade_id ?? null;
        $this->academic_term_id = $this->exam->academic_term_id;

        $this->curriculum_subject_id = $this->exam->curriculum_subject_id;
        $this->exam_type_id = $this->exam->exam_type_id;
        $this->exam_date = $this->exam->exam_date->format('Y-m-d');
        $this->max_marks = $this->exam->max_marks;
        $this->section_id = $this->exam->section_id;
        $this->is_final = $this->exam->is_final;
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
        if ($this->exam->hasMarks())
            return;

        $this->curriculum_subject_id = null;
        $this->section_id = null;
    }


    public function save()
    {
        $this->authorize(\Perm::ExamsUpdate->value);

        if ($this->exam->hasMarks()) {
            $validated = $this->validate([
                'exam_date' => ['required', 'date'],
            ]);

            $this->exam->update($validated);
        } else {
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
                            $exists = Exam::where('id', '!=', $this->exam_id)
                                ->where('section_id', $this->section_id)
                                ->where('curriculum_subject_id', $this->curriculum_subject_id)
                                ->where('is_final', true)
                                ->exists();
                            if ($exists) {
                                $fail('يوجد اختبار نهائي مسجل بالفعل لهذه المادة والشعبة.');
                            }
                        }
                    }
                ],
            ]);

            $this->exam->update($validated);
        }

        session()->flash('success', 'تم تحديث الامتحان بنجاح.');
        return $this->redirect(route('dashboard.exams.list'), navigate: true);
    }

    #[Layout('components.layouts.dashboard', ['page-title' => 'تعديل امتحان'])]
    public function render()
    {
        return view('livewire.dashboard.examinations.exams.edit-exam');
    }
}
