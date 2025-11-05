<?php

namespace App\Http\Requests\Dashboard\Academics\Sections;

use App\Models\Section;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UpdateSectionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $section = $this->route('section');

        // منع تعديل الشعبة التابعة لسنة مؤرشفة
        if ($section instanceof Section && $section->belongsToArchivedYear()) {
            throw new HttpResponseException(
                redirect()->back()->with('error', 'عذراً، لا يمكن تعديل بيانات هذه الشعبة لأن السنة الدراسية التابعة لها مؤرشفة.')
            );
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
        $section = $this->route('section');

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('sections', 'name')
                    ->where('academic_year_id', $section->academic_year_id)
                    ->where('grade_id', $section->grade_id)
                    ->where('academic_term_id', $section->academic_term_id)
                    ->ignore($section->id),
            ],
            'capacity' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
