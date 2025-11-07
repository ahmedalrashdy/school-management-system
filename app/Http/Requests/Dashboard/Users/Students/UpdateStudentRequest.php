<?php

namespace App\Http\Requests\Dashboard\Users\Students;

use App\Enums\GenderEnum;
use App\Models\Student;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStudentRequest extends FormRequest
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
        /** @var Student $student */
        $student = $this->route('student');
        $user = $student->user;

        $rules = [
            // بيانات الطالب (إدارة فقط)
            'date_of_birth' => ['required', 'date', 'before:today'],
            'city' => ['required', 'string', 'max:255'],
            'district' => ['required', 'string', 'max:255'],
        ];

        // البيانات الرسمية (إدارة فقط)
        $rules['first_name'] = ['required', 'string', 'max:255'];
        $rules['last_name'] = ['required', 'string', 'max:255'];
        $rules['gender'] = ['required', Rule::enum(GenderEnum::class)];

        // بيانات الاتصال والعنوان (إدارة يمكن تعديلها دائماً)
        $rules['email'] = [
            'nullable',
            'email',
            'max:255',
            Rule::unique('users', 'email')->ignore($user->id),
        ];
        $rules['phone_number'] = [
            'nullable',
            'string',
            'max:20',
            Rule::unique('users', 'phone_number')->ignore($user->id),
        ];
        $rules['address'] = ['nullable', 'string', 'max:1000'];


        return $rules;
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
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
