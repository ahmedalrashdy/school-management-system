<?php

namespace App\Livewire\Common\StudentProfile;

use App\Models\Student;
use Livewire\Attributes\Locked;
use Livewire\Component;

class StudentInfo extends Component
{
    #[Locked()]
    public Student $student;

    public string $context = 'dashboard';

    public function hasSection(): bool
    {
        $activeYear = school()->activeYear();

        return $activeYear && $this->student->sections()->where('academic_year_id', $activeYear->id)->exists();
    }
}
