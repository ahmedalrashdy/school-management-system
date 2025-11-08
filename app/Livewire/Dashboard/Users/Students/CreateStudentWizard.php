<?php

namespace App\Livewire\Dashboard\Users\Students;

use App\Enums\GenderEnum;
use App\Enums\RelationToStudentEnum;
use App\Livewire\Dashboard\Users\Students\Steps\StudentAcademicInfoStep;
use App\Livewire\Dashboard\Users\Students\Steps\StudentBasicInfoStep;
use App\Livewire\Dashboard\Users\Students\Steps\StudentEnrollmentStep;
use App\Livewire\Dashboard\Users\Students\Steps\StudentGuardiansStep;
use App\Models\Guardian;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Spatie\Activitylog\Facades\LogBatch;

class CreateStudentWizard extends Component
{
    use AuthorizesRequests;

    public int $currentStep = 1;

    public int $totalSteps = 4;

    public array $formState = [];

    public StudentBasicInfoStep $studentBasicInfo;

    public StudentAcademicInfoStep $studentAcademicInfo;

    public StudentGuardiansStep $studentGuardians;

    public StudentEnrollmentStep $studentEnrollment;

    public function nextStep(): void
    {
        if ($this->currentStep < $this->totalSteps) {
            $steps = [
                1 => $this->studentBasicInfo,
                2 => $this->studentAcademicInfo,
                3 => $this->studentGuardians,
            ];
            if ($steps[$this->currentStep]->validStep()) {
                $this->currentStep++;
            }
        }
    }

    public function previousStep(): void
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    public function addGuardian()
    {
        $this->studentGuardians->addGuardian();
    }

    public function removeGuardian(int $index)
    {
        $this->studentGuardians->removeGuardian($index);
    }

    public function goToStep(int $step): void
    {
        if ($step >= 1 && $step <= $this->totalSteps && $step <= $this->currentStep) {
            $this->currentStep = $step;
        }
    }

    public function getRelationsProperty(): array
    {
        return RelationToStudentEnum::options();
    }

    public function updatedStudentGuardians($value, $key)
    {

        $this->studentGuardians->search($value, $key);
    }

    public function save()
    {
        $this->authorize(\Perm::StudentsCreate->value);
        $this->studentEnrollment->validStep();
        DB::transaction(function () {
            LogBatch::withinBatch(function () {
                $user = $this->studentBasicInfo->store();
                $user->assignRole('طالب');
                $student = $this->studentAcademicInfo->store($user->id);
                // 3. معالجة أولياء الأمور
                foreach ($this->studentGuardians->guardians as $guardianData) {
                    $guardianId = null;
                    if (! empty($guardianData['guardian_id'])) {
                        // ولي أمر موجود
                        $guardianId = $guardianData['guardian_id'];
                    } else {
                        // إنشاء ولي أمر جديد
                        $guardianUser = User::create([
                            'first_name' => $guardianData['first_name'],
                            'last_name' => $guardianData['last_name'],
                            'gender' => $guardianData['gender'],
                            'phone_number' => $guardianData['phone_number'] ?? null,
                            'email' => $guardianData['email'] ?? null,
                            'password' => Hash::make('default-password'),
                            'address' => $guardianData['address'] ?? null,
                            'is_active' => true,
                            'is_admin' => false,
                            'reset_password_required' => true,
                        ]);
                        $guardianUser->assignRole('ولي أمر');
                        $guardianModel = Guardian::create([
                            'user_id' => $guardianUser->id,
                            'occupation' => $guardianData['occupation'] ?? null,
                        ]);

                        $guardianId = $guardianModel->id;
                    }
                    // ربط ولي الأمر بالطالب
                    $student->guardians()->attach($guardianId, [
                        'relation_to_student' => $guardianData['relation_to_student'],
                    ]);
                }

                $enrollment = $this->studentEnrollment->store($student->id);
                activity()
                    ->performedOn($student)
                    ->causedBy(auth()->user())
                    ->event('admission')
                    ->log(description: "تم تسجيل الطالب {$user->first_name} {$user->last_name} في {$enrollment->grade->name}");
            });
        });

        $this->dispatch('show-toast', type: 'success', message: 'تم إنشاء الطالب بنجاح.');

        $this->redirect(route('dashboard.students.index'));
    }

    #[Layout('components.layouts.dashboard', ['page-title' => 'إنشاء طالب جديد'])]
    public function render()
    {
        return view('livewire.dashboard.users.students.create-student-wizard', [
            'genders' => GenderEnum::options(),
        ]);
    }
}
