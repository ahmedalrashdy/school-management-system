<?php

namespace App\Livewire\Common\TeacherProfile;

use App\Models\TeacherAssignment;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Component;

class TeacherAssignments extends Component
{
    #[Locked]
    public int $teacher_id;

    public ?int $academicYearId = null;

    public ?int $academicTermId = null;

    public function mount(): void
    {
        $this->academicYearId = school()->activeYear()?->id;
        $this->academicTermId = school()->currentAcademicTerm()?->id;
    }

    #[Computed]
    public function assignments()
    {
        if (! $this->academicYearId) {
            return collect();
        }
        $query = TeacherAssignment::query()
            ->where('teacher_id', $this->teacher_id)
            ->with([
                'curriculumSubject.subject',
                'section.grade',
                'section.academicTerm',
            ])
            ->whereHas('curriculumSubject.curriculum', function ($q) {
                $q->where('academic_year_id', $this->academicYearId);

                if ($this->academicTermId) {
                    $q->where('academic_term_id', $this->academicTermId);
                }
            });

        return $query->get();
    }
}
