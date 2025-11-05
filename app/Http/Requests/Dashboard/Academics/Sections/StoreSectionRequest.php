<?php

namespace App\Http\Requests\Dashboard\Academics\Sections;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSectionRequest extends FormRequest
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
            'grade_id' => ['required', 'exists:grades,id'],
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('sections', 'name')
                    ->where('academic_year_id', school()->activeYear()?->id)
                    ->where('grade_id', $this->grade_id)
                    ->where('academic_term_id', school()->currentAcademicTerm()?->id),
            ],
            'capacity' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
