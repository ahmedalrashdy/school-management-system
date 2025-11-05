<?php

namespace App\Http\Requests\Dashboard\Academics\Grades;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreGradeRequest extends FormRequest
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
            'stage_id' => ['required', 'exists:stages,id'],
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('grades', 'name')->where('stage_id', $this->stage_id),
            ],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
