<?php

namespace App\Http\Requests\Dashboard\Users\Guardians;

use App\Enums\GenderEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreGuardianRequest extends FormRequest
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
            'email' => [
                'nullable',
                'email',
                'max:255',
                'unique:users,email',
            ],
            'phone_number' => [
                'nullable',
                'string',
                'max:20',
                'unique:users,phone_number',
            ],
            'gender' => [
                'required',
                Rule::enum(GenderEnum::class),
            ],
            'occupation' => ['nullable', 'string', 'max:255'],
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
