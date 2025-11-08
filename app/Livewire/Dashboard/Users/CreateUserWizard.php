<?php

namespace App\Livewire\Dashboard\Users;
use App\Enums\GenderEnum;
use App\Livewire\Dashboard\Users\Steps\UserBasicInfoStep;
use App\Livewire\Dashboard\Users\Steps\UserRolesStep;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class CreateUserWizard extends Component
{
    use AuthorizesRequests;

    public int $currentStep = 1;

    public int $totalSteps = 3;

    public UserBasicInfoStep $userBasicInfo;

    public UserRolesStep $userRoles;

    public function mount(): void
    {
        $this->authorize(\Perm::UsersCreate->value);
    }

    public function nextStep(): void
    {
        if ($this->currentStep < $this->totalSteps) {
            $steps = [
                1 => $this->userBasicInfo,
                2 => $this->userRoles,
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

    public function goToStep(int $step): void
    {
        if ($step >= 1 && $step <= $this->totalSteps && $step <= $this->currentStep) {
            $this->currentStep = $step;
        }
    }

    public function save(): void
    {
        $this->authorize(\Perm::UsersCreate->value);
        $this->userBasicInfo->validate();
        $this->userRoles->validate();

        DB::transaction(function () {
            $user = $this->userBasicInfo->store();
            $roles = Role::whereIn('id', $this->userRoles->selectedRoles)->get();
            $user->assignRole($roles);
        });

        $this->dispatch('show-toast', type: 'success', message: 'تم إنشاء المستخدم بنجاح.');

        $this->redirect(route('dashboard.users.index'), navigate: true);
    }

    #[Computed()]
    public function roles()
    {
        return Role::withCount('permissions')->whereNotIn('name', ['طالب', 'مدرس', 'ولي أمر'])
            ->latest()->get();
    }




    #[Layout('components.layouts.dashboard', ['page-title' => 'إنشاء مستخدم جديد'])]
    public function render()
    {
        return view('livewire.dashboard.users.create-user-wizard', ['genders' => GenderEnum::options()]);
    }
}
