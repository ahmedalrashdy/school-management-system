<?php

namespace App\Livewire\Dashboard\Users\Students\Steps;

use App\Models\Enrollment;
use Livewire\Form;

class StudentEnrollmentStep extends Form
{
    public ?int $academic_year_id = null;

    public ?int $grade_id = null;

    public function validStep(): bool
    {
        $this->validate();

        return true;
    }

    protected function rules()
    {
        return [
            'academic_year_id' => [
                'required',
                'exists:academic_years,id',
            ],
            'grade_id' => [
                'required',
                'exists:grades,id',
            ],
        ];
    }

    public function store(int $studentId)
    {
        $validData = $this->validate();

        return Enrollment::create([
            'student_id' => $studentId,
            ...$validData,
        ]);
    }
}
