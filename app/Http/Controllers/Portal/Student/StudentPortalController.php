<?php

namespace App\Http\Controllers\Portal\Student;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class StudentPortalController extends Controller
{
    /**
     * Display timetable for authenticated student.
     */
    public function timetable(): View
    {
        $student = $this->getAuthenticatedStudent();
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

        return view('portal.student.timetable', [
            'student' => $student,
            'section' => $section,
            'timetable' => $timetable,
            'slotsGrouped' => $slotsGrouped,
        ]);
    }

    /**
     * Display attendance for authenticated student.
     */
    public function attendance(): View
    {
        $student = $this->getAuthenticatedStudent();

        return view('portal.student.attendance', [
            'student' => $student,
        ]);
    }

    /**
     * Display marks for authenticated student.
     */
    public function marks(): View
    {
        $student = $this->getAuthenticatedStudent();

        return view('portal.student.marks', [
            'student' => $student,
        ]);
    }

    /**
     * Get the authenticated student or abort.
     */
    protected function getAuthenticatedStudent()
    {
        $student = auth()->user()->student;

        if (! $student) {
            abort(404, 'Student profile not found');
        }

        return $student;
    }
}
