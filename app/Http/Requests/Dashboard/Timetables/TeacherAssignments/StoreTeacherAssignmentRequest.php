<?php

namespace App\Http\Requests\Dashboard\Timetables\TeacherAssignments;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTeacherAssignmentRequest extends FormRequest
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
            'section_id' => ['required', 'exists:sections,id'],
            'curriculum_subject_id' => ['required', 'exists:curriculum_subject,id'],
            'teacher_id' => [
                'required',
                'exists:teachers,id',
                Rule::unique('teacher_assignments', 'teacher_id')
                    ->where('curriculum_subject_id', $this->curriculum_subject_id)
                    ->where('section_id', $this->section_id),
            ],
        ];
    }
}
