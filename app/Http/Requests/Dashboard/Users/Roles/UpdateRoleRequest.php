<?php

namespace App\Http\Requests\Dashboard\Users\Roles;

use App\Enums\PermissionEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UpdateRoleRequest extends FormRequest
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
        /** @var Role $role */
        $role = $this->route('role');

        $allPermissions = array_column(PermissionEnum::cases(), 'value');

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles', 'name')->ignore($role?->id),
                function ($_, $value, $fail) use ($role) {
                    $spacialRoles = ['مدرس', 'طالب', 'ولي أمر'];
                    if (in_array($value, $spacialRoles) && $role->name != $value) {
                        $fail("name", 'لا يمكن تحديث اسم الدور الى أحد الاسماء (طالب ,ولي أمر , مدرس)');
                    } else if (in_array($role->name, $spacialRoles) && $role->name != $value) {
                        $fail("name", "لا يمكن تعديل  اسم الدور");
                    }
                }
            ],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['required', 'string', Rule::in($allPermissions)],
        ];
    }
}
