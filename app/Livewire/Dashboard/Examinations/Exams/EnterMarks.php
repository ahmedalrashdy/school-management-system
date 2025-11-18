<?php

namespace App\Livewire\Dashboard\Examinations\Exams;

use App\Models\Exam;
use App\Models\Mark;
use App\Models\Student;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class EnterMarks extends Component
{
    use WithPagination;

    public Exam $exam;

    public array $marks = [];

    public function mount(Exam $exam): void
    {
        $this->exam = $exam->load([
            'academicYear', 'academicTerm', 'examType',
            'curriculumSubject.subject',
            'section',
        ]);

        $this->loadMarksForCurrentPage();
    }

    public function updatedPage()
    {
        $this->marks = [];
        $this->loadMarksForCurrentPage();
    }

    #[Computed]
    public function students()
    {
        return Student::query()
            ->join('section_students', function ($join) {
                $join->on('students.id', 'section_students.student_id')
                    ->where('section_students.section_id', $this->exam->section_id);
            })
            ->join('users', 'students.user_id', 'users.id')
            ->select([
                'students.id as id',
                'users.first_name',
                'users.last_name',
                'students.admission_number',
            ])
            ->orderBy('users.first_name')
            ->orderBy('users.last_name')
            ->paginate(15);
    }

    public function loadMarksForCurrentPage()
    {

        $currentStudents = $this->students;

        $studentIds = $currentStudents->pluck('id')->toArray();

        if (empty($studentIds)) {
            return;
        }

        $existingMarks = Mark::where('exam_id', $this->exam->id)
            ->whereIn('student_id', $studentIds)
            ->get()
            ->keyBy('student_id');

        foreach ($currentStudents as $student) {
            // نملأ المصفوفة فقط إذا لم تكن موجودة (لتفادي مسح ما يكتبه المستخدم في حالات معينة)
            if (! isset($this->marks[$student->id])) {
                $markRecord = $existingMarks->get($student->id);
                $this->marks[$student->id] = [
                    'marks_obtained' => $markRecord?->marks_obtained,
                    'notes' => $markRecord?->notes,
                ];
            }
        }
    }

    public function save()
    {
        $this->validate();

        $upsertData = [];

        foreach ($this->marks as $studentId => $data) {
            if (($data['marks_obtained'] === null || $data['marks_obtained'] === '') && empty($data['notes'])) {
                continue;
            }
            $upsertData[] = [
                'student_id' => $studentId,
                'exam_id' => $this->exam->id,
                'marks_obtained' => ($data['marks_obtained'] === '') ? null : $data['marks_obtained'],
                'notes' => $data['notes'] ?? null,
            ];
        }

        if (! empty($upsertData)) {
            Mark::upsert(
                $upsertData,
                ['student_id', 'exam_id'],
                ['marks_obtained', 'notes']
            );
        }

        $this->dispatch('show-toast', type: 'success', message: 'تم حفظ الدرجات للصفحة الحالية بنجاح.');
    }

    public function saveAndGoToNextPage()
    {
        $this->save();
        $this->nextPage();
    }

    protected function rules()
    {
        return [
            'marks' => ['required', 'array'],
            'marks.*.marks_obtained' => ['nullable', 'numeric', 'min:0', "max:{$this->exam->max_marks}"],
            'marks.*.notes' => 'nullable|string|max:255',
        ];
    }

    #[Layout('components.layouts.dashboard', ['page-title' => 'رصد الدرجات'])]
    public function render()
    {
        return view('livewire.dashboard.examinations.exams.enter-marks', [
            'students' => $this->students,
        ]);
    }
}
