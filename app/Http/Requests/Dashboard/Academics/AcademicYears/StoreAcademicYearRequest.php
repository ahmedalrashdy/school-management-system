<?php

namespace App\Http\Requests\Dashboard\Academics\AcademicYears;

use App\Enums\AcademicYearStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAcademicYearRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255', 'unique:academic_years,name'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'status' => [
                'required',
                Rule::in([AcademicYearStatus::Active->value, AcademicYearStatus::Upcoming->value]),
            ],
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $hasActiveYear = school()->activeYear() != null;
            if ($hasActiveYear && $this->status == AcademicYearStatus::Active->value) {
                $validator->errors()->add(
                    'status',
                    'لا يمكن إنشاء سنة دراسية بحالة "نشطة" لأن هناك سنة نشطة بالفعل. يجب أن تكون الحالة "قادمة".'
                );
            } elseif (school()->upcomingYear() != null && $this->status == AcademicYearStatus::Upcoming->value) {
                $validator->errors()->add(
                    'status',
                    'لا يمكن إنشاء اكثر من سنة بحالة قادمة'
                );
            }
        });
    }
}
