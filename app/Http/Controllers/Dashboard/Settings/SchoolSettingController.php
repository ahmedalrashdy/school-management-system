<?php

namespace App\Http\Controllers\Dashboard\Settings;

use App\Enums\AttendanceModeEnum;
use App\Enums\DayOfWeekEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Settings\SchoolSettings\UpdateSchoolSettingsRequest;
use App\Models\SchoolSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SchoolSettingController extends Controller
{
    /**
     * Display the school settings page.
     */
    public function index(): View
    {
        $settings = SchoolSetting::orderBy('group')
            ->orderBy('label')
            ->get()
            ->groupBy('group');
        $enumSettings = [
            'attendence_mode' => AttendanceModeEnum::options(),
            'weekend_days' => DayOfWeekEnum::options(),
        ];

        return view('dashboard.settings.school-settings.index', [
            'settings' => $settings,
            'enumSettings' => $enumSettings,
        ]);
    }

    /**
     * Update school settings.
     */
    public function update(UpdateSchoolSettingsRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $inputSettings = $validated['settings'] ?? [];

        // 1. Fetch all settings to minimize DB queries
        $existingSettings = SchoolSetting::all()->keyBy('key');

        // 2. Prepare data for bulk update
        $upsertData = [];

        // Handle array settings that might be missing from input
        $arraySettings = $existingSettings->where('type', 'array');
        foreach ($arraySettings as $key => $setting) {
            if (!isset($inputSettings[$key])) {
                $inputSettings[$key] = [];
            }
        }

        foreach ($inputSettings as $key => $value) {
            if (!$existingSettings->has($key)) {
                continue;
            }

            $setting = $existingSettings->get($key);

            // Handle File Uploads
            if ($setting->type === 'file') {
                $value = $this->handleFileUpload($request, $setting);
            }

            // Process Value
            $processedValue = $this->processValue($value, $setting->type);

            // Prepare row for upsert
            // Note: We must json_encode ALL values (including strings) because the target column is JSON type.
            // MySQL expects a valid JSON string (e.g. "text" not text) for JSON columns.
            $dbValue = $processedValue === null
                ? null
                : json_encode($processedValue, JSON_UNESCAPED_UNICODE);

            $upsertData[] = [
                'key' => $key,
                'value' => $dbValue,
                // We must provide other required columns to satisfy the INSERT requirement of upsert,
                'type' => $setting->type,
                'group' => $setting->group,
                'label' => $setting->label,
                'updated_at' => now(),
            ];
        }

        // 3. Perform Bulk Update
        if (!empty($upsertData)) {
            SchoolSetting::upsert(
                $upsertData,
                ['key'], // Unique column(s) to match
                ['value', 'updated_at'] // Columns to update if match found
            );


        }
        new SchoolSetting()->flushCache();
        return redirect()
            ->route('dashboard.school-settings.index')
            ->with('success', 'تم تحديث الإعدادات بنجاح.');
    }

    /**
     * Handle file upload logic.
     */
    private function handleFileUpload($request, $setting): ?string
    {
        $key = $setting->key;
        $oldValue = $setting->value;

        // Check for delete request
        if ($request->has("settings.{$key}_delete")) {
            if ($oldValue && Storage::disk('public')->exists($oldValue)) {
                Storage::disk('public')->delete($oldValue);
            }

            return null;
        }

        // Check for new file upload
        if ($request->hasFile("settings.{$key}")) {
            // Delete old file
            if ($oldValue && Storage::disk('public')->exists($oldValue)) {
                Storage::disk('public')->delete($oldValue);
            }

            // Upload new file
            return $request->file("settings.{$key}")->store('school-settings', 'public');
        }

        // Return old value if no change
        return $oldValue;
    }

    /**
     * Process value based on setting type.
     */
    private function processValue(mixed $value, string $type): mixed
    {
        return match ($type) {
            'int', 'integer' => $value !== null ? (int) $value : null,
            'boolean', 'bool' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'json' => $this->processJsonValue($value),
            'array' => is_array($value) ? $value : (is_null($value) ? [] : [$value]),
            'date' => $value,
            default => $value,
        };
    }

    private function processJsonValue(mixed $value): ?array
    {
        if (is_array($value)) {
            return $value;
        }

        if (is_string($value) && $value !== '') {
            return json_decode($value, true) ?? null;
        }

        return null;
    }
}
