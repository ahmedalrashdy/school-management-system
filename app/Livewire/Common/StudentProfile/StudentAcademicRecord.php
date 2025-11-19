<?php

namespace App\Livewire\Common\StudentProfile;

use App\Models\Enrollment;
use App\Models\Section;
use Livewire\Attributes\Computed;
use Livewire\Component;

class StudentAcademicRecord extends Component
{
    public ?int $studentId;

    #[Computed()]
    public function enrollments()
    {
        return Enrollment::with([
            'grade.stage',
            'academicYear',
        ])
            ->where('student_id', $this->studentId)
            ->get();
    }

    #[Computed()]
    public function sections()
    {
        return Section::query()
            ->join('section_students as ss', 'ss.section_id', 'sections.id')
            ->where('ss.student_id', $this->studentId)
            ->select('sections.*')
            ->with(['academicTerm'])
            ->get();
    }

    public function render()
    {
        return view('livewire.common.student-profile.student-academic-record');
    }
}
