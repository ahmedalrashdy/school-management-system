<?php

namespace App\Http\Requests\Dashboard\Examinations\Exams;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateExamTypeRequest extends FormRequest
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
        $examTypeId = $this->route('exam_type')?->id;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('exam_types', 'name')->ignore($examTypeId),
            ],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
