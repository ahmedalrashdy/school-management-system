<?php

namespace App\Http\Requests\Dashboard\Settings\SchoolSettings;

use App\Models\SchoolSetting;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSchoolSettingsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $settings = $this->input('settings', []);
        if (! is_array($settings)) {
            return;
        }

        $keysToAdd = [];
        foreach ($settings as $key => $value) {
            if (str_ends_with($key, '_delete')) {
                $originalKey = substr($key, 0, -7);
                if (! array_key_exists($originalKey, $settings)) {
                    $keysToAdd[$originalKey] = null;
                }
            }
        }

        if (! empty($keysToAdd)) {
            $this->merge([
                'settings' => array_merge($settings, $keysToAdd),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $settings = SchoolSetting::all();
        $rules = ['settings' => ['required', 'array']];

        foreach ($settings as $setting) {
            $key = "settings.{$setting->key}";

            switch ($setting->type) {
                case 'file':
                    $deleteKey = "{$key}_delete";
                    // التحقق من وجود طلب الحذف - نستخدم input() للتحقق من وجود الحقل
                    $hasDeleteRequest = $this->input($deleteKey) !== null;

                    // إذا كان هناك طلب حذف، لا نتحقق من الملف
                    // إذا لم يكن هناك طلب حذف، نتحقق من الملف (إذا تم إرساله)
                    $rules[$key] = $hasDeleteRequest
                        ? ['nullable']
                        : ['nullable', 'file', 'mimes:jpg,jpeg,png,gif,svg,webp', 'max:5120'];

                    $rules[$deleteKey] = ['nullable', 'boolean'];
                    break;

                case 'int':
                case 'integer':
                    $rules[$key] = ['nullable', 'integer'];
                    break;

                case 'boolean':
                case 'bool':
                    $rules[$key] = ['nullable', 'boolean'];
                    break;

                case 'json':
                    $rules[$key] = ['nullable', 'json'];
                    break;
                case 'array':
                    $rules[$key] = ['nullable', 'array'];
                    break;

                case 'date':
                    $rules[$key] = ['nullable', 'date'];
                    break;

                case 'string':
                default:
                    $rules[$key] = ['nullable', 'string', 'max:1000'];
                    break;
            }
        }

        return $rules;
    }
}
