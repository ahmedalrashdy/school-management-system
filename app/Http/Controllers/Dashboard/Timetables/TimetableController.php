<?php

namespace App\Http\Controllers\Dashboard\Timetables;

use App\Http\Controllers\Controller;
use App\Models\Section;
use App\Models\Timetable;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class TimetableController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:'.\Perm::TimetablesView->value, only: ['index', 'show']),
            new Middleware('can:'.\Perm::TimetablesActivate->value, only: ['toggle']),
        ];
    }

    /**
     * Display the timetable home page.
     */
    public function index(): View
    {
        return view('dashboard.timetables.index');
    }

    /**
     * Display the timetable for a section.
     */
    public function show(Section $section, ?Timetable $timetable): View
    {
        $timetable = $timetable->exists ? $timetable : $section->activeTimetable();

        if ($timetable) {
            $timetable->load([
                'timetableSetting',
                'section.grade.stage',
                'section.academicYear',
                'slots' => function ($query) {
                    $query->with([
                        'teacherAssignment.curriculumSubject.subject',
                        'teacherAssignment.teacher.user',
                    ]);
                },
            ]);

            $slotsGrouped = $timetable->getSlotsGrouped();
        } else {
            $slotsGrouped = null;
        }

        return view('dashboard.timetables.show', [
            'section' => $section->load(['grade.stage', 'academicYear']),
            'timetable' => $timetable,
            'slotsGrouped' => $slotsGrouped,
        ]);
    }
}
