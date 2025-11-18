<?php

namespace App\Http\Requests\Dashboard\Examinations\Exams;

use App\Models\AcademicYear;
use App\Models\CurriculumSubject;
use App\Models\Exam;
use Illuminate\Foundation\Http\FormRequest;

class UpdateExamRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $exam = $this->route('exam');

        // لا يمكن تعديل الامتحان إذا كان لديه درجات
        if ($exam instanceof Exam && $exam->hasMarks()) {
            return false;
        }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $exam = $this->route('exam');

        // إذا كان الامتحان لديه درجات، نسمح فقط بتعديل التاريخ
        if ($exam instanceof Exam && $exam->hasMarks()) {
            return [
                'exam_date' => [
                    'required',
                    'date',
                    function ($attribute, $value, $fail) use ($exam) {
                        $academicYear = $exam->academicYear;
                        if (! $academicYear) {
                            return;
                        }

                        $examDate = \Carbon\Carbon::parse($value);
                        if ($examDate->lt($academicYear->start_date) || $examDate->gt($academicYear->end_date)) {
                            $fail('تاريخ الامتحان يجب أن يكون ضمن نطاق تواريخ السنة الدراسية.');
                        }
                    },
                ],
            ];
        }

        // إذا لم يكن لديه درجات، يمكن تعديل جميع الحقول
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

                    $academicYearId = $this->input('academic_year_id');
                    $gradeId = $this->input('grade_id');
                    $academicTermId = $this->integer('academic_term_id');

                    if ($curriculumSubject->curriculum->academic_year_id != $academicYearId
                        || $curriculumSubject->curriculum->grade_id != $gradeId
                        || $curriculumSubject->curriculum->academic_term_id != $academicTermId) {
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
