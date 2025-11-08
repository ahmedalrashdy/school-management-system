<?php

namespace App\Livewire\Dashboard\Users\Students\Steps;

use App\Models\Student;
use Illuminate\Validation\Rule;
use Livewire\Form;

class StudentAcademicInfoStep extends Form
{
    public string $admission_number = '';

    public ?string $date_of_birth = null;

    public string $city = '';

    public string $district = '';

    public function validStep(): bool
    {
        $this->validate();

        return true;
    }

    public function store(int $userId)
    {
        $validData = $this->validate();

        return Student::create([
            ...$validData,
            'user_id' => $userId,
        ]);
    }

    protected function rules()
    {
        return [
            'admission_number' => [
                'required',
                'string',
                'max:255',
                Rule::unique('students', 'admission_number')->whereNull('deleted_at'),
            ],
            'date_of_birth' => ['required', 'date', 'before:today'],
            'city' => ['required', 'string', 'max:255'],
            'district' => ['required', 'string', 'max:255'],
        ];
    }
}
