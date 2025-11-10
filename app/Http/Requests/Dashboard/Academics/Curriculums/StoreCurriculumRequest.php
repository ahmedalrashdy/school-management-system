<?php

namespace App\Http\Requests\Dashboard\Academics\Curriculums;

use App\Enums\AcademicYearStatus;
use App\Models\AcademicYear;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCurriculumRequest extends FormRequest
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
            'academic_year_id' => [
                'required',
                'exists:academic_years,id',
                function ($attribute, $value, $fail) {
                    $academicYear = AcademicYear::find($value);
                    if ($academicYear && $academicYear->status === AcademicYearStatus::Archived) {
                        $fail('لا يمكن إنشاء منهج لسنة دراسية مؤرشفة.');
                    }
                },
            ],
            'grade_id' => ['required', 'exists:grades,id'],
            'academic_term_id' => [
                'required',
                'exists:academic_terms,id',
                Rule::unique('curriculums', 'academic_term_id')
                    ->where('academic_year_id', $this->academic_year_id)
                    ->where('grade_id', $this->grade_id),
            ],
            'subject_ids' => ['required', 'array', 'min:1'],
            'subject_ids.*' => ['exists:subjects,id'],
        ];
    }
}
