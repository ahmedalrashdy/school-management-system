<?php

namespace App\Livewire\Dashboard\Users\Steps;

use Livewire\Form;
use Spatie\Permission\Models\Role;

class UserRolesStep extends Form
{
    public array $selectedRoles = [];

    public function validStep(): bool
    {

        $this->validate();

        return true;
    }

    protected function rules(): array
    {
        return [
            'selectedRoles' => [
                'required',
                'array',
                'min:1',
                function ($_, $val, $fail) {
                    if (Role::whereIn('id', $this->selectedRoles)->whereIn('name', ['مدرس', 'طالب', 'ولي أمر'])->exists()) {
                        $fail("selectedRoles", " لا يمكن إضافة دور (طالب او مدرس او ولي أمر  ) للمستخدم من هنا ");
                    }
                }
            ],

        ];
    }

    public function messages(): array
    {
        return [
            'selectedRoles.required' => 'يجب تحديد دور واحد على الأقل.',
            'selectedRoles.array' => 'الأدوار يجب أن تكون مصفوفة.',
            'selectedRoles.min' => 'يجب تحديد دور واحد على الأقل.',
            'selectedRoles.*.exists' => 'الدور المحدد غير موجود.',
        ];
    }

    public function validationAttributes(): array
    {
        return [
            'selectedRoles' => 'الأدوار',
            'selectedRoles.*' => 'الدور',
        ];
    }
}
