<?php

namespace App\Http\Requests\Dashboard\Users\Students;

use App\Enums\RelationToStudentEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AttachStudentRequest extends FormRequest
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
        /** @var \App\Models\Guardian $guardian */
        $guardian = $this->route('guardian');

        return [
            'student_id' => [
                'required',
                'exists:students,id',
                Rule::unique('guardian_student', 'student_id')
                    ->where('guardian_id', $guardian->id),
            ],
            'relation_to_student' => [
                'required',
                'integer',
                Rule::enum(RelationToStudentEnum::class),
            ],
        ];
    }
}
