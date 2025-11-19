<?php

namespace App\Http\Controllers\Portal\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class TeacherPortalController extends Controller
{
    /**
     * Display attendance dashboard for authenticated teacher.
     */
    public function attendance(): View
    {
        $user = auth()->user();
        $teacher = $user->teacher;

        if (! $teacher) {
            abort(404, 'Teacher profile not found');
        }

        return view('portal.teacher.attendance.index', [
            'teacher' => $teacher,
        ]);
    }

    /**
     * Display timetable for authenticated teacher.
     */
    public function timetable(): View
    {
        $user = auth()->user();
        $teacher = $user->teacher;

        if (! $teacher) {
            abort(404, 'Teacher profile not found');
        }

        return view('portal.teacher.timetable.index', [
            'teacher' => $teacher,
        ]);
    }

    /**
     * Display marks dashboard for authenticated teacher.
     */
    public function marks(): View
    {
        $user = auth()->user();
        $teacher = $user->teacher;

        if (! $teacher) {
            abort(404, 'Teacher profile not found');
        }

        return view('portal.teacher.marks.index', [
            'teacher' => $teacher,
        ]);
    }
}
