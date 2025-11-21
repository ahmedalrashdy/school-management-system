<?php

use App\Http\Controllers\Portal\Guardian\DashboardController as GuardianDashboardController;
use App\Http\Controllers\Portal\Guardian\GuardianProfileController;
use App\Http\Controllers\Portal\Guardian\GuardianStudentController;
use App\Http\Controllers\Portal\Guardian\StudentSelectionController as GuardianStudentSelectionController;
use App\Http\Controllers\Portal\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Portal\Student\StudentPortalController;
use App\Http\Controllers\Portal\Student\StudentProfileController;
use App\Http\Controllers\Portal\Teacher\DashboardController as TeacherDashboardController;
use App\Http\Controllers\Portal\Teacher\TeacherPortalController;
use App\Http\Controllers\Portal\Teacher\TeacherProfileController;
use Illuminate\Support\Facades\Route;

// Student Portal Routes (Semantic URLs)
Route::prefix('student')->name('student.')->middleware(['role:طالب'])->group(function () {
    Route::get('/', [StudentDashboardController::class, 'index'])->name('index');
    Route::get('timetable', [StudentPortalController::class, 'timetable'])->name('timetable');
    Route::get('attendance', [StudentPortalController::class, 'attendance'])->name('attendance');
    Route::get('marks', [StudentPortalController::class, 'marks'])->name('marks');

    // Profile Routes
    Route::get('profile', [StudentProfileController::class, 'index'])->name('profile');
    Route::get('profile/edit', [StudentProfileController::class, 'editPersonalInfo'])->name('profile.edit');
    Route::post('profile/edit', [StudentProfileController::class, 'updatePersonalInfo'])->name('profile.update');
    Route::get('profile/guardians', [StudentProfileController::class, 'guardians'])->name('profile.guardians');
});

// Teacher Portal Routes
Route::prefix('teacher')->name('teacher.')->middleware(['role:مدرس'])->group(function () {
    Route::get('/', [TeacherDashboardController::class, 'index'])->name('index');
    Route::get('timetable', [TeacherPortalController::class, 'timetable'])->name('timetable');
    Route::get('attendance', [TeacherPortalController::class, 'attendance'])->name('attendance');
    Route::get('marks', [TeacherPortalController::class, 'marks'])->name('marks');

    // Profile Routes
    Route::get('profile', [TeacherProfileController::class, 'index'])->name('profile.index');
    Route::get('profile/edit', [TeacherProfileController::class, 'edit'])->name('profile.edit');
    Route::post('profile/edit', [TeacherProfileController::class, 'update'])->name('profile.update');
});

// Guardian Portal Routes (Semantic URLs)
Route::prefix('guardian')->name('guardian.')->middleware(['role:ولي أمر'])->group(function () {
    Route::get('/', [GuardianDashboardController::class, 'index'])->name('index');
    Route::get('select-student', [GuardianStudentSelectionController::class, 'index'])->name('select-student');

    // Profile Routes
    Route::get('profile', [GuardianProfileController::class, 'index'])->name('profile.index');
    Route::get('profile/edit', [GuardianProfileController::class, 'edit'])->name('profile.edit');
    Route::post('profile/edit', [GuardianProfileController::class, 'update'])->name('profile.update');

    // Guardian viewing child data (semantic URLs: /guardian/student/{id}/marks)
    Route::prefix('student/{student}')->name('student.')->group(function () {
        Route::get('profile', [GuardianStudentController::class, 'profile'])->name('profile');
        Route::get('timetable', [GuardianStudentController::class, 'timetable'])->name('timetable');
        Route::get('attendance', [GuardianStudentController::class, 'attendance'])->name('attendance');
        Route::get('marks', [GuardianStudentController::class, 'marks'])->name('marks');
    });
});
