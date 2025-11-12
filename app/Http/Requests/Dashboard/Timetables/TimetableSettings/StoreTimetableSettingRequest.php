<?php

namespace App\Http\Requests\Dashboard\Timetables\TimetableSettings;

use App\Enums\DayOfWeekEnum;
use Illuminate\Foundation\Http\FormRequest;

class StoreTimetableSettingRequest extends FormRequest
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
        $days = array_map(fn ($case) => $case->key(), DayOfWeekEnum::cases());
        $periodsPerDayRules = [];

        foreach ($days as $day) {
            $periodsPerDayRules["periods_per_day.{$day}"] = ['required', 'integer', 'min:0', 'max:12'];
        }

        return array_merge([
            'name' => ['required', 'string', 'max:255', 'unique:timetable_settings,name'],
            'first_period_start_time' => ['required', 'date_format:H:i'],
            'default_period_duration_minutes' => ['required', 'integer', 'min:15', 'max:120'],
            'periods_before_break' => ['required', 'integer', 'min:1', 'max:12'],
            'break_duration_minutes' => ['required', 'integer', 'min:5', 'max:60'],
            'is_active' => ['sometimes', 'boolean'],
            'periods_per_day' => ['required', 'array'],
        ], $periodsPerDayRules);
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        $days = [];
        foreach (DayOfWeekEnum::cases() as $case) {
            $days[$case->key()] = $case->label();
        }
        $messages = [];
        foreach ($days as $dayKey => $dayLabel) {
            $messages["periods_per_day.{$dayKey}.required"] = "عدد الحصص ليوم {$dayLabel} مطلوب.";
            $messages["periods_per_day.{$dayKey}.integer"] = "عدد الحصص ليوم {$dayLabel} يجب أن يكون رقماً صحيحاً.";
            $messages["periods_per_day.{$dayKey}.min"] = "عدد الحصص ليوم {$dayLabel} يجب أن يكون على الأقل 0.";
            $messages["periods_per_day.{$dayKey}.max"] = "عدد الحصص ليوم {$dayLabel} يجب أن يكون على الأكثر 12.";
        }

        return $messages;
    }
}
