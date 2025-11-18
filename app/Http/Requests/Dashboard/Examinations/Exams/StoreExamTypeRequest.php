<?php

namespace App\Http\Requests\Dashboard\Examinations\Exams;

use Illuminate\Foundation\Http\FormRequest;

class StoreExamTypeRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255', 'unique:exam_types,name'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
