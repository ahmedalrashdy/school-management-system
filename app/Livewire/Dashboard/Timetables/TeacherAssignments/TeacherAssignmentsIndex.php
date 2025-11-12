<?php

namespace App\Livewire\Dashboard\Timetables\TeacherAssignments;

use App\Models\Curriculum;
use App\Models\Grade;
use App\Models\Section;
use App\Models\TeacherAssignment;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

class TeacherAssignmentsIndex extends Component
{
    use AuthorizesRequests;
    #[Url(history: true)]
    public ?int $academic_year_id = null;
    #[Url(history: true)]
    public ?int $grade_id = null;
    #[Url(history: true)]
    public ?int $academic_term_id = null;

    // --- Form Data (Create) ---
    public ?int $newTeacherId = null;

    public function mount(): void
    {
        $this->authorize(\Perm::TeacherAssignmentsView);
        $this->academic_year_id = $this->academic_year_id ?: school()->activeYear()?->id;
        $this->academic_term_id = $this->academic_term_id ?: school()->currentAcademicTerm()?->id;
        $this->grade_id = $this->grade_id ?: Grade::sorted()->first()?->id;
    }

    #[Computed()]
    public function curriculum()
    {
        if (!$this->academic_year_id || !$this->grade_id)
            return null;

        return Curriculum::where('academic_year_id', $this->academic_year_id)
            ->where('grade_id', $this->grade_id)
            ->where('academic_term_id', $this->academic_term_id)
            ->with(['curriculumSubjects.subject'])
            ->first();
    }

    #[Computed()]
    public function sections()
    {
        if (!$this->academic_year_id || !$this->grade_id)
            return collect();

        return Section::where('academic_year_id', $this->academic_year_id)
            ->where('grade_id', $this->grade_id)
            ->where('academic_term_id', $this->academic_term_id)
            ->withCount('students')
            ->orderBy('name')
            ->get();
    }

    #[Computed()]
    public function assignments()
    {
        if (!$this->academic_year_id || !$this->grade_id)
            return collect();

        $sections = $this->sections;
        $curriculum = $this->curriculum;

        if ($sections->isEmpty() || !$curriculum)
            return collect();

        return TeacherAssignment::with(['teacher.user', 'section', 'curriculumSubject.subject'])
            ->whereIn('section_id', $sections->pluck('id'))
            ->whereIn('curriculum_subject_id', $curriculum->curriculumSubjects->pluck('id'))
            ->get()
            ->keyBy(fn($item) => "{$item->section_id}_{$item->curriculum_subject_id}");
    }

    // --- Actions ---

    public function updatedAcademicYearId(): void
    {
        $this->academic_term_id = null;
    }

    public function store(int $sectionId, int $curriculumSubjectId): void
    {

        $this->authorize(\Perm::TeacherAssignmentsCreate);
        $section = Section::findOrFail($sectionId);
        if ($section->academic_year_id !== school()->activeYear()?->id) {
            $this->dispatch('show-toast', type: 'error', message: "لا يمكن تعيين المدرسين خارج العام الحالي");
        }
        $this->validate([
            'newTeacherId' => [
                'required',
                'exists:teachers,id',
                Rule::unique('teacher_assignments', 'teacher_id')
                    ->where('curriculum_subject_id', $curriculumSubjectId)
                    ->where('section_id', $sectionId),
            ],
        ], messages: [
            'newTeacherId.required' => 'يرجى اختيار المدرس.',
            'newTeacherId.unique' => 'هذا المدرس معين بالفعل لهذه المادة في هذه الشعبة.',
        ]);

        TeacherAssignment::create([
            'section_id' => $sectionId,
            'curriculum_subject_id' => $curriculumSubjectId,
            'teacher_id' => $this->newTeacherId,
        ]);

        $this->reset('newTeacherId');
        $this->dispatch('close-modal', name: 'create-assignment-modal');
        $this->dispatch('show-toast', type: 'success', message: 'تم تعيين المدرس بنجاح.');
    }


    public function destroy(int $assignmentId): void
    {
        $this->authorize(\Perm::TeacherAssignmentsDelete);
        $assignment = TeacherAssignment::findOrFail($assignmentId);

        if ($assignment->timetableSlots()->exists()) {
            $this->dispatch('show-toast', type: 'error', message: 'لا يمكن حذف تعيين المدرس لان هناك  حصص دراسية مرتبطة');
        } else {
            $assignment->delete();
            $this->dispatch('show-toast', type: 'success', message: 'تم حذف التعيين بنجاح.');
        }

        $this->dispatch('close-modal', name: 'delete-assignment-modal');
    }

    #[Layout('components.layouts.dashboard', ['page-title' => 'تعيينات المدرسين'])]
    public function render()
    {
        return view('livewire.dashboard.timetables.teacher-assignments.teacher-assignments-index');
    }
}
