<?php

namespace App\Livewire\Common\StudentProfile;

use App\Enums\RelationToStudentEnum;
use App\Models\Student;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Component;

class StudentGuardians extends Component
{
    #[Locked()]
    public int $studentId;

    public int $guardianId;

    public int $relationToStudent;

    public string $context = 'dashboard';

    #[Computed]
    public function student()
    {
        return Student::findOrFail($this->studentId);
    }

    public function detachGuardian($guardianId)
    {
        $this->student->guardians()->detach($guardianId);
        $this->dispatch('show-toast', type: 'success', message: 'تمت  فك الإرتباط بنجاح');
    }

    #[Computed()]
    public function guardians()
    {
        return $this->student->guardians()->with('user')->get();
    }

    public function addGuardian()
    {
        $this->validate([
            'relationToStudent' => ['required', Rule::in(array_column(RelationToStudentEnum::cases(), 'value'))],
            'guardianId' => 'required|exists:guardians,id',
        ]);
        $this->student->guardians()->syncWithoutDetaching([
            $this->guardianId => ['relation_to_student' => $this->relationToStudent],
        ]);
        $this->reset(['relationToStudent', 'guardianId']);
        $this->dispatch('close-modal', name: 'attach-guardian-modal');
        $this->dispatch('show-toast', type: 'success', message: 'تم ربط ولي الأمر بالطالب بنجاح');
    }
}
