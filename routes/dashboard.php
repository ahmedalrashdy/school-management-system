<?php

use App\Http\Controllers\Dashboard\Academics\AcademicTermController;
use App\Http\Controllers\Dashboard\Academics\AcademicYearController;
use App\Http\Controllers\Dashboard\Academics\CurriculumController;
use App\Http\Controllers\Dashboard\Academics\GradeController;
use App\Http\Controllers\Dashboard\Academics\SectionController;
use App\Http\Controllers\Dashboard\Academics\StageController;
use App\Http\Controllers\Dashboard\Academics\SubjectController;
use App\Http\Controllers\Dashboard\ActivityController;
use App\Http\Controllers\Dashboard\Attendance\AttendanceReportsController;
use App\Http\Controllers\Dashboard\Attendance\RecordAttendanceController;
use App\Http\Controllers\Dashboard\Attendance\SelectAttendanceSectionController;
use App\Http\Controllers\Dashboard\Attendance\StudentAttendanceListController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\Examinations\ExamController;
use App\Http\Controllers\Dashboard\Examinations\ExamTypeController;
use App\Http\Controllers\Dashboard\Examinations\GradingRuleController;
use App\Http\Controllers\Dashboard\Examinations\MarksController;
use App\Http\Controllers\Dashboard\Settings\SchoolSettingController;
use App\Http\Controllers\Dashboard\Timetables\SectionAssignmentController;
use App\Http\Controllers\Dashboard\Timetables\TimetableController;
use App\Http\Controllers\Dashboard\Timetables\TimetableSettingController;
use App\Http\Controllers\Dashboard\Users\GuardianController;
use App\Http\Controllers\Dashboard\Users\RoleController;
use App\Http\Controllers\Dashboard\Users\StudentController;
use App\Http\Controllers\Dashboard\Users\TeacherController;
use App\Http\Controllers\Dashboard\Users\UserController;
use App\Livewire\Dashboard\Examinations\Exams\EnterMarks;
use App\Livewire\Dashboard\Timetables\CreateTimetable;
use App\Livewire\Dashboard\Timetables\TeacherAssignments\TeacherAssignmentsIndex;
use App\Livewire\Dashboard\Timetables\TimetableBuilder;
use App\Livewire\Dashboard\Timetables\TimetablesList;
use App\Livewire\Dashboard\Users\CreateUserWizard;
use App\Livewire\Dashboard\Users\Students\CreateStudentWizard;
use App\Livewire\Dashboard\Users\UsersIndex;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('index');

Route::get('/genarel', function () {
    return view('dashboard.genarel-page');
})->name('genarel-page');

// Academic Years Routes
Route::resource('academic-years', AcademicYearController::class);

Route::post('academic-years/{academic_year}/activate', [AcademicYearController::class, 'activate'])
    ->name('academic-years.activate');

// Stages Routes
Route::resource('stages', StageController::class);
// Grades Routes
Route::resource('grades', GradeController::class);
// Academic Terms Routes
Route::resource('academic-terms', AcademicTermController::class);
Route::post('academic-terms/{academic_term}/toggle-active', [AcademicTermController::class, 'toggleActive'])
    ->name('academic-terms.toggle-active');

// Sections Routes
Route::get('sections/{section}/students', [SectionController::class, 'students'])->name('sections.students');
Route::resource('sections', SectionController::class);

// Section Assignments Routes
Route::get('section-assignments', [SectionAssignmentController::class, 'index'])->name('section-assignments.index');

// Subjects Routes
Route::resource('subjects', SubjectController::class);

// Curriculums Routes
Route::resource('curriculums', CurriculumController::class)->except(['edit', 'update']);

Route::post('curriculums/{curriculum}/add-subject', [CurriculumController::class, 'addSubject'])
    ->name('curriculums.add-subject');

Route::delete('curriculums/{curriculum}/remove-subject/{curriculumSubject}', [CurriculumController::class, 'removeSubject'])
    ->name('curriculums.remove-subject');

// Students Routes
Route::get('students/create', CreateStudentWizard::class)->name('students.create')->middleware('can:'.\Perm::StudentsCreate->value);
Route::resource('students', StudentController::class)->only(['index', 'show', 'edit', 'update']);

Route::post('students/{student}/toggle-active', [StudentController::class, 'toggleActive'])->name('students.toggle-active');
Route::post('students/{student}/attach-guardian', [StudentController::class, 'attachGuardian'])->name('students.attach-guardian');
Route::post('students/{student}/detach-guardian', [StudentController::class, 'detachGuardian'])->name('students.detach-guardian');

// Guardians Routes
Route::resource('guardians', GuardianController::class);

Route::post('guardians/{guardian}/attach-student', [GuardianController::class, 'attachStudent'])
    ->name('guardians.attach-student');

Route::post('guardians/{guardian}/detach-student', [GuardianController::class, 'detachStudent'])
    ->name('guardians.detach-student');

Route::post('guardians/{guardian}/toggle-active', [GuardianController::class, 'toggleActive'])
    ->name('guardians.toggle-active');

// Teachers Routes
Route::resource('teachers', TeacherController::class)->except(['destroy']);
Route::post('teachers/{teacher}/toggle-active', [TeacherController::class, 'toggleActive'])->name('teachers.toggle-active');

// Teacher Assignments Routes
Route::get('teacher-assignments', TeacherAssignmentsIndex::class)->name('teacher-assignments.index');

// Exam Types Routes
Route::resource('exam-types', ExamTypeController::class);

// Exams Routes
Route::resource('exams', ExamController::class)->except(['store', 'show']);
Route::get('exams/list', [ExamController::class, 'list'])->name('exams.list');
Route::get('exams/{exam}/enter-marks', EnterMarks::class)->name('exams.enter-marks');

// marks
Route::get('exams/{exam}/marks-sheet', [ExamController::class, 'showMarks'])->name('exams.marks.show');
Route::prefix('marks')->name('marks.')->group(function () {
    Route::get('/', [MarksController::class, 'index'])->name('index');
    Route::get('{section}', [MarksController::class, 'show'])->name('show');
    Route::get('{section}/student/{student}', [MarksController::class, 'studentDetails'])->name('student.details');
    Route::get('{section}/audit', [MarksController::class, 'audit'])->name('audit');
});

// Grading Rules Routes
Route::resource('grading-rules', GradingRuleController::class);
Route::patch('/grading-rules/{gradingRule}/toggle-publish', [GradingRuleController::class, 'togglePublish'])
    ->name('grading-rules.toggle-publish');

// Timetable Settings Routes
Route::resource('timetable-settings', TimetableSettingController::class);

// Timetables Routes
Route::get('timetables', [TimetableController::class, 'index'])->name('timetables.index');
Route::get('timetables/list', TimetablesList::class)->name('timetables.list');
Route::get('timetables/create', CreateTimetable::class)->name('timetables.create');
Route::get('timetables/{timetable}/builder', action: TimetableBuilder::class)->name('timetables.builder');
Route::get('sections/{section}/timetable/{timetable?}', [TimetableController::class, 'show'])->name('sections.timetable');

// Attendance Recording Routes - Per Period Mode
Route::get(
    'attendances/record/per-period/{section}/{timetableSlot}/{date}',
    [RecordAttendanceController::class, 'index']
)->name('attendances.record.per-period');

// Attendance Selection Route (from calendar)
Route::get(
    'attendances/select-section/{date}',
    [SelectAttendanceSectionController::class, 'show']
)->name('attendances.select-section');

// Attendance Recording Routes - Daily Mode
Route::get(
    'attendances/record/daily/{section}/{schoolDay}',
    [RecordAttendanceController::class, 'daily']
)->name('attendances.record.daily');

// Attendance Recording Routes - Split Daily Mode
Route::get(
    'attendances/record/split-daily/{section}/{schoolDay}/{dayPart}',
    [RecordAttendanceController::class, 'splitDaily']
)->name('attendances.record.split-daily');

// Student Attendance List Routes - Per Period Mode
Route::get(
    'attendances/students/per-period/{section}/{timetableSlot}/{date}',
    [StudentAttendanceListController::class, 'perPeriod']
)->name('attendances.students.per-period');

// Student Attendance List Routes - Daily Mode
Route::get(
    'attendances/students/daily/{section}/{schoolDay}',
    [StudentAttendanceListController::class, 'daily']
)->name('attendances.students.daily');

// Student Attendance List Routes - Split Daily Mode
Route::get(
    'attendances/students/split-daily/{section}/{schoolDay}/{dayPart}',
    [StudentAttendanceListController::class, 'splitDaily']
)->name('attendances.students.split-daily');

// Roles Routes
Route::resource('roles', RoleController::class);

// Users Routes
Route::get('users', UsersIndex::class)->name('users.index');
Route::get('users/create', CreateUserWizard::class)->name('users.create');
Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
Route::get('users/{user}/manage-roles', [UserController::class, 'manageRoles'])->name('users.manage-roles');
Route::put('users/{user}/roles', [UserController::class, 'updateRoles'])->name('users.update-roles');

// School Settings Routes
Route::get('school-settings', [SchoolSettingController::class, 'index'])->name('school-settings.index');
Route::put('school-settings', [SchoolSettingController::class, 'update'])->name('school-settings.update');

// Academic Calendar Routes
Route::get('academic-calendar', \App\Livewire\Dashboard\Academics\Calendar\AcademicCalendar::class)->name('academic-calendar.index');

// Attendance Dashboard Route
Route::get('attendance-dashboard', \App\Livewire\Dashboard\Attendance\AttendanceDashboard::class)->name('attendance-dashboard.index');

// Attendance Reports Route
Route::get('attendance-reports', [AttendanceReportsController::class, 'index'])->name('attendance.reports.index');
Route::get('reports/{filename}/download', function () {})
    ->name('reports.download');
Route::get('/activities', [ActivityController::class, 'index'])->name('activities.index');
