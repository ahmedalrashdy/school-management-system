<?php

use App\Enums\AcademicYearStatus;
use App\Models\AcademicYear;
use App\Services\SchoolContextService;
use Illuminate\Support\Facades\Cache;

test('can get active academic year', function () {
    $activeYear = AcademicYear::create([
        'name' => '2024-2025',
        'start_date' => '2024-09-01',
        'end_date' => '2025-06-30',
        'status' => AcademicYearStatus::Active->value,
    ]);

    $service = new SchoolContextService();

    expect($service->activeYear())
        ->not->toBeNull()
        ->and($service->activeYear()->id)->toBe($activeYear->id);
});

test('can get upcoming academic year', function () {
    $upcomingYear = AcademicYear::create([
        'name' => '2025-2026',
        'start_date' => '2025-09-01',
        'end_date' => '2026-06-30',
        'status' => AcademicYearStatus::Upcoming->value,
    ]);

    $service = new SchoolContextService();

    expect($service->upcomingYear())
        ->not->toBeNull()
        ->and($service->upcomingYear()->id)->toBe($upcomingYear->id);
});

test('returns null when no active year exists', function () {
    $service = new SchoolContextService();

    expect($service->activeYear())->toBeNull();
});

test('returns null when no upcoming year exists', function () {
    $service = new SchoolContextService();

    expect($service->upcomingYear())->toBeNull();
});

test('caches academic years', function () {
    $activeYear = AcademicYear::create([
        'name' => '2024-2025',
        'start_date' => '2024-09-01',
        'end_date' => '2025-06-30',
        'status' => AcademicYearStatus::Active->value,
    ]);

    $service = new SchoolContextService();

    // Clear cache first
    Cache::flush();

    // First call should query database
    $firstCall = $service->activeYear();

    // Delete the year from database
    $activeYear->delete();

    // Second call should return cached result
    $secondCall = $service->activeYear();

    expect($firstCall)->not->toBeNull()
        ->and($secondCall)->not->toBeNull()
        ->and($secondCall->id)->toBe($firstCall->id);
});

test('can clear cache', function () {
    $activeYear = AcademicYear::create([
        'name' => '2024-2025',
        'start_date' => '2024-09-01',
        'end_date' => '2025-06-30',
        'status' => AcademicYearStatus::Active->value,
    ]);

    $service = new SchoolContextService();

    // Populate cache
    $service->activeYear();

    // Clear cache
    $service->clearCache();

    // Delete the year
    $activeYear->delete();

    // Should return null after cache is cleared
    expect($service->activeYear())->toBeNull();
});

test('school helper function returns SchoolContextService instance', function () {
    expect(school())->toBeInstanceOf(SchoolContextService::class);
});
