<?php

namespace App\Services;

use App\Enums\AttendanceModeEnum;
use App\Models\AcademicTerm;
use App\Models\AcademicYear;
use App\Models\SchoolSetting;
use App\Services\Attendances\AttendanceSheetService;
use Illuminate\Support\Facades\Cache;

class SchoolContextService
{
    /**
     * Get the active academic year.
     */
    public function activeYear(): ?AcademicYear
    {
        return AcademicYear::active()->first();
    }

    /**
     * Get the upcoming academic year.
     */
    public function upcomingYear(): ?AcademicYear
    {
        return AcademicYear::upcoming()->first();
    }

    public function schoolSetting(string $key, $default = null)
    {
        $settings = SchoolSetting::all()->pluck('value', 'key');

        return $settings->get($key, $default);
    }

    public function getAttendanceMode(): AttendanceModeEnum
    {
        $service = app(AttendanceSheetService::class);

        return $service->getAttendanceMode();
    }

    /**
     * Get the current active academic term.
     * Returns the active term from the active academic year, or null if not found.
     */
    public function currentAcademicTerm(): ?AcademicTerm
    {
        $activeYear = $this->activeYear();

        if (! $activeYear) {
            return null;
        }

        // Get the active term for the active academic year
        return AcademicTerm::where('academic_year_id', $activeYear->id)
            ->where('is_active', true)
            ->first();
    }

    public function getJsonData($key, $default = [])
    {

        $value = $this->schoolSetting($key, null);
        if ($value) {
            // If it's already an array, return it as object
            if (is_array($value)) {
                return (object) $value;
            }

            // If it's a string, decode it
            if (is_string($value)) {
                $decoded = json_decode($value, false);
                // If decoded is array, convert to object
                if (is_array($decoded)) {
                    return (object) $decoded;
                }

                // If decoded is object or null, return as is
                return $decoded;
            }
        }

        return $default;
    }

    public function getArrayData($key, $default = [])
    {

        $value = $this->schoolSetting($key, null);
        if ($value) {
            // If it's already an array, return it as object
            if (is_array($value)) {
                return $value;
            }

            // If it's a string, decode it
            if (is_string($value)) {
                $decoded = json_decode($value, false);

                // If decoded is object or null, return as is
                return $decoded;
            }
        }

        return $default;
    }

    /**
     * Clear the cached academic years and terms.
     */
    public function clearCache(): void
    {
        Cache::forget('school.active_year');
        Cache::forget('school.upcoming_year');
        Cache::forget('school.active_term');
        Cache::forget('school_settings');
    }
}
