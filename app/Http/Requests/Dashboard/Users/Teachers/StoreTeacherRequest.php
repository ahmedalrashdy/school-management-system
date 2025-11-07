<?php

namespace App\Http\Requests\Dashboard\Users\Teachers;

use App\Enums\AcademicQualificationEnum;
use App\Enums\GenderEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTeacherRequest extends FormRequest
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
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'gender' => ['required', Rule::enum(GenderEnum::class)],
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('users', 'email')->whereNull('deleted_at'),
            ],
            'phone_number' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('users', 'phone_number')->whereNull('deleted_at'),
            ],
            'address' => ['nullable', 'string', 'max:500'],
            'date_of_birth' => ['required', 'date', 'before:today'],
            'specialization' => ['required', 'string', 'max:255'],
            'qualification' => ['required', Rule::in(array_column(AcademicQualificationEnum::cases(), column_key: 'value'))],
        ];
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
