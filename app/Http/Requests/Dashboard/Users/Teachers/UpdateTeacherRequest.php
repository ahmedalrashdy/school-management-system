<?php

namespace App\Http\Requests\Dashboard\Users\Teachers;

use App\Enums\AcademicQualificationEnum;
use App\Enums\GenderEnum;
use App\Models\Teacher;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTeacherRequest extends FormRequest
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
        /** @var Teacher $teacher */
        $teacher = $this->route('teacher');
        $user = $teacher->user;
        // feilds can be updated if  reset_password_required=false
        $rules = [
            'date_of_birth' => ['required', 'date', 'before:today'],
            'specialization' => ['required', 'string', 'max:255'],
            'qualification' => ['required', Rule::in(array_column(AcademicQualificationEnum::cases(), column_key: 'value'))],
            'is_active' => ['boolean'],
        ];

        if ($user->reset_password_required) {
            // البيانات الرسمية
            $rules['first_name'] = ['required', 'string', 'max:255'];
            $rules['last_name'] = ['required', 'string', 'max:255'];
            $rules['gender'] = ['required', Rule::enum(GenderEnum::class)];

            // بيانات الاتصال والعنوان
            $rules['email'] = [
                'nullable',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id)->whereNull('deleted_at'),
            ];
            $rules['phone_number'] = [
                'nullable',
                'string',
                'max:255',
                Rule::unique('users', 'phone_number')->ignore($user->id)->whereNull('deleted_at'),
            ];
            $rules['address'] = ['nullable', 'string', 'max:500'];
        }

        return $rules;
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            /** @var Teacher $teacher */
            $teacher = $this->route('teacher');
            $user = $teacher->user;

            // إذا كان reset_password_required = true: التحقق من وجود إما الهاتف أو البريد
            if ($user->reset_password_required) {
                $email = $this->input('email');
                $phoneNumber = $this->input('phone_number');
                if (empty($email) && empty($phoneNumber)) {
                    $validator->errors()->add(
                        'email',
                        'يجب إدخال إما البريد الإلكتروني أو رقم الهاتف على الأقل.'
                    );
                    $validator->errors()->add(
                        'phone_number',
                        'يجب إدخال إما البريد الإلكتروني أو رقم الهاتف على الأقل.'
                    );
                }
            }
        });
    }
}
