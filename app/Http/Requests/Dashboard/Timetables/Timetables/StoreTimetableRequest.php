<?php

namespace App\Http\Requests\Dashboard\Timetables\Timetables;

use Illuminate\Foundation\Http\FormRequest;

class StoreTimetableRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'section_id' => ['required', 'exists:sections,id'],
            'timetable_setting_id' => ['required', 'exists:timetable_settings,id'],
        ];
    }
}
