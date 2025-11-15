<?php

namespace App\Services;

use App\Models\AcademicYear;
use App\Models\Grade;
use App\Models\Stage;

class LookupService
{
    // we return all fields for   laravel model caching
    // laravel model caching has issue with select stmt

    public function getStages(bool $withAllOption = false)
    {
        $stages = Stage::sorted()->get()->pluck('name', 'id')->toArray();

        // إرجاع النتيجة (سواء كانت جديدة أو مخزنة)
        return $withAllOption
            ? ['' => 'جميع المراحل'] + $stages
            : $stages;
    }

    public function getAcademicYears(bool $withAllOption = false)
    {
        $academicYears = AcademicYear::orderBy('start_date', 'desc')->get()->pluck('name', 'id')->toArray();

        return $withAllOption
            ? ['' => 'جميع السنوات'] + $academicYears
            : $academicYears;
    }

    public function getActiveAndUpcomingYearsOnly(bool $withSelectPlaceholder = false)
    {
        $activeYears = AcademicYear::activeAndUpcomimgOnly()->get()->pluck('name', 'id')->toArray();

        return $withSelectPlaceholder
            ? ['' => 'اختر السنة'] + $activeYears
            : $activeYears;
    }

    public function getGrades(bool $withAllOption = false)
    {
        $grades = Grade::sorted()->get()->pluck('name', 'id')->toArray();

        return $withAllOption
            ? ['' => 'جميع الصفوف'] + $grades
            : $grades;
    }

    // شجر بكل السنوات الدراسية و الاترم الدراسية لكل السنوات  لصفحات الفلتره العامة
    public function yearsTree()
    {
        return AcademicYear::with('academicTerms')
            ->orderByDesc('start_date')
            ->get()
            ->map(fn ($year) => $this->mapYearTree($year));

    }

    // شجر بكل السنوات الدراسية و الاترم الدراسية لكل السنوات  لصفحات الانشاء والتعديل العامة
    public function activeAndUpcomingYearsTree()
    {
        return AcademicYear::activeAndUpcomimgOnly()
            ->with('academicTerms')
            ->orderByDesc('start_date')
            ->get()
            ->map(fn ($year) => $this->mapYearTree($year));
    }

    private function mapYearTree(AcademicYear $year)
    {
        return (object) [
            'id' => $year->id,
            'name' => $year->name,
            'academic_terms' => $year->academicTerms->map(function ($term) {
                return (object) [
                    'id' => $term->id,
                    'academic_year_id' => $term->academic_year_id,
                    'name' => $term->name,
                ];
            }),
        ];
    }
}
