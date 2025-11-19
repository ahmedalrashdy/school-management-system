<?php

namespace App\Http\Controllers\Portal\Guardian;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\View\View;

class GuardianStudentController extends Controller
{
    /**
     * Display timetable for a specific student (guardian view).
     */
    public function timetable(Student $student): View
    {

        $this->authorizeGuardianAccess($student);

        $section = $student->currentSection();
        $timetable = $section?->activeTimetable();

        $timetable?->load([
            'timetableSetting',
            'slots' => function ($query) {
                $query->with([
                    'teacherAssignment.curriculumSubject.subject',
                    'teacherAssignment.teacher.user',
                ]);
            },
        ]);

        $slotsGrouped = $timetable?->getSlotsGrouped() ?? collect();

        return view('portal.guardian.student.timetable', [
            'student' => $student,
            'section' => $section,
            'timetable' => $timetable,
            'slotsGrouped' => $slotsGrouped,
        ]);
    }

    /**
     * Display attendance for a specific student (guardian view).
     */
    public function attendance(Student $student): View
    {
        $this->authorizeGuardianAccess($student);

        return view('portal.guardian.student.attendance', [
            'student' => $student,
        ]);
    }

    /**
     * Display marks for a specific student (guardian view).
     */
    public function marks(Student $student): View
    {
        $this->authorizeGuardianAccess($student);

        return view('portal.guardian.student.marks', [
            'student' => $student,
        ]);
    }

    /**
     * Display profile for a specific student (guardian view).
     */
    public function profile(Student $student): View
    {
        $this->authorizeGuardianAccess($student);

        return view('portal.guardian.student.profile', compact('student'));
    }

    /**
     * Authorize that the guardian has access to this student.
     */
    protected function authorizeGuardianAccess(Student $student): void
    {
        $guardian = auth()->user()->guardian;

        if (! $guardian) {
            abort(404, 'Guardian profile not found');
        }

        if (! $guardian->students()->where('students.id', $student->id)->exists()) {
            abort(403, 'You do not have access to this student');
        }
    }
}
