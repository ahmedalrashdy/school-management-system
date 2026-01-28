<?php

namespace App\Http\Requests\Dashboard\Users\Users;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Spatie\Permission\Models\Role;

class ManageUserRolesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'roles' => [
                'required',
                'array',
                'min:1'
            ],
            'roles.*' => ['integer'],
        ];
    }


    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            if ($this->roles) {
                $roles = Role::whereIn('id', $this->roles)->get();
                if ($roles?->count() !== count($this->roles)) {
                    $validator->errors()->add('roles', 'بعض الأدوار المختارة غير موجودة.');
                    return;
                }
            }
        });
    }
}
