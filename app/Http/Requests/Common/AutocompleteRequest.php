<?php

namespace App\Http\Requests\Common;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AutocompleteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $resources = array_keys(config('autocomplete.resources', []));

        return [
            'resource' => ['required', 'string', Rule::in($resources)],
            'search' => ['nullable', 'string', 'max:255'],
            'cursor' => ['nullable', 'string'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:'.config('autocomplete.max_per_page', 25)],
        ];
    }
}

