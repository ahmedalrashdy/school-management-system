<?php

namespace App\Http\Requests\Dashboard\Academics\Stages;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStageRequest extends FormRequest
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
        $stageId = $this->route('stage')?->id;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('stages', 'name')->ignore($stageId),
            ],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
