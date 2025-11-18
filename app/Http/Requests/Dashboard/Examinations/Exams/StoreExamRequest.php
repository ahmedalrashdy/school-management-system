<?php

namespace App\Http\Requests\Dashboard\Examinations\Exams;

use App\Models\AcademicYear;
use App\Models\CurriculumSubject;
use Illuminate\Foundation\Http\FormRequest;

class StoreExamRequest extends FormRequest
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
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'grade_id' => ['required', 'exists:grades,id'],
            'academic_term_id' => ['required', 'exists:academic_terms,id'],
            'curriculum_subject_id' => [
                'required',
                'exists:curriculum_subject,id',
                function ($attribute, $value, $fail) {
                    $curriculumSubject = CurriculumSubject::with('curriculum')->find($value);
                    if (! $curriculumSubject) {
                        return;
                    }

                    $academicYearId = $this->integer('academic_year_id');
                    $gradeId = $this->integer('grade_id');
                    $academicTermId = $this->integer('academic_term_id');

                    if (
                        $curriculumSubject->curriculum->academic_year_id != $academicYearId
                        || $curriculumSubject->curriculum->grade_id != $gradeId
                        || $curriculumSubject->curriculum->academic_term_id != $academicTermId
                    ) {
                        $fail('المادة المختارة لا تنتمي للمنهج المحدد.');
                    }
                },
            ],
            'exam_type_id' => ['required', 'exists:exam_types,id'],
            'exam_date' => [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    $academicYearId = $this->input('academic_year_id');
                    if (! $academicYearId) {
                        return;
                    }

                    $academicYear = AcademicYear::find($academicYearId);
                    if (! $academicYear) {
                        return;
                    }

                    $examDate = \Carbon\Carbon::parse($value);
                    if ($examDate->lt($academicYear->start_date) || $examDate->gt($academicYear->end_date)) {
                        $fail('تاريخ الامتحان يجب أن يكون ضمن نطاق تواريخ السنة الدراسية المختارة.');
                    }
                },
            ],
            'max_marks' => ['required', 'integer', 'min:1'],
            'section_id' => ['nullable', 'exists:sections,id'],
        ];
    }
}
