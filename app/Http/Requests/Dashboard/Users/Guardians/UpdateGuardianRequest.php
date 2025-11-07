<?php

namespace App\Http\Requests\Dashboard\Users\Guardians;

use App\Enums\GenderEnum;
use App\Models\Guardian;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateGuardianRequest extends FormRequest
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
        /** @var Guardian $guardian */
        $guardian = $this->route('guardian');
        $user = $guardian->user;

        $rules = [
            'occupation' => ['nullable', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
        ];
        // إذا كان الحساب لم يُفعّل بعد، يمكن تعديل جميع البيانات
        if ($user->reset_password_required) {
            $rules = array_merge($rules, [
                'first_name' => ['required', 'string', 'max:255'],
                'last_name' => ['required', 'string', 'max:255'],
                'email' => [
                    'nullable',
                    'email',
                    'max:255',
                    Rule::unique('users', 'email')->ignore($user->id),
                ],
                'phone_number' => [
                    'nullable',
                    'string',
                    'max:20',
                    Rule::unique('users', 'phone_number')->ignore($user->id),
                ],
                'gender' => [
                    'required',
                    Rule::enum(GenderEnum::class),
                ],
            ]);
        }

        return $rules;
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        /** @var Guardian $guardian */
        $guardian = $this->route('guardian');
        $user = $guardian->user;

        // التحقق من إدخال إما البريد الإلكتروني أو رقم الهاتف فقط إذا كان الحساب لم يُفعّل
        if ($user->reset_password_required) {
            $validator->after(function ($validator): void {
                if (empty($this->email) && empty($this->phone_number)) {
                    $validator->errors()->add(
                        'email',
                        'يجب إدخال إما البريد الإلكتروني أو رقم الهاتف على الأقل.'
                    );
                    $validator->errors()->add(
                        'phone_number',
                        'يجب إدخال إما البريد الإلكتروني أو رقم الهاتف على الأقل.'
                    );
                }
            });
        }
    }
}
