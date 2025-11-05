<?php

namespace App\Http\Requests\Dashboard\Academics\Grades;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateGradeRequest extends FormRequest
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
        $grade = $this->route('grade');
        $stageId = $this->input('stage_id', $grade?->stage_id);

        return [
            'stage_id' => ['required', 'exists:stages,id'],
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('grades', 'name')
                    ->where('stage_id', $stageId)
                    ->ignore($grade?->id),
            ],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
