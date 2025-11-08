<?php

namespace App\Livewire\Dashboard\Users\Steps;

use App\Enums\GenderEnum;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Form;

class UserBasicInfoStep extends Form
{
    public string $first_name = '';

    public string $last_name = '';

    public ?int $gender = null;

    public ?string $phone_number = null;

    public ?string $email = null;

    public ?string $address = null;

    public function validStep(): bool
    {
        $this->validate();

        return true;
    }

    protected function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'gender' => ['required', Rule::enum(GenderEnum::class)],
            'phone_number' => [
                'nullable',
                'string',
                'max:255',
                'required_without:email',
                Rule::unique('users', 'phone_number')->whereNull('deleted_at'),
            ],
            'email' => [
                'nullable',
                'email',
                'max:255',
                'required_without:phone_number',
                Rule::unique('users', 'email')->whereNull('deleted_at'),
            ],
            'address' => ['nullable', 'string', 'max:500'],
        ];
    }


    public function store(): User
    {
        $validData = $this->validate();

        return User::create(array_merge($validData, [
            'reset_password_required' => true,
            'is_active' => true,
            'is_admin' => false,
            'password' => Hash::make('default-password'),
        ]));
    }
}
