<?php

namespace App\Http\Requests\Dashboard\Users\Students;

use App\Enums\RelationToStudentEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AttachGuardianRequest extends FormRequest
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
        /** @var \App\Models\Student $student */
        $student = $this->route('student');

        return [
            'guardian_id' => [
                'required',
                'exists:guardians,id',
                Rule::unique('guardian_student', 'guardian_id')
                    ->where('student_id', $student->id),
            ],
            'relation_to_student' => [
                'required',
                Rule::enum(RelationToStudentEnum::class),
            ],
        ];
    }
}
