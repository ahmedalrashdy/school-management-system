<?php

namespace App\Http\Controllers\Dashboard\Timetables;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class SectionAssignmentController extends Controller
{
    /**
     * Display the section assignment page with filters.
     */
    public function index(): View
    {
        return view('dashboard.timetables.section-assignments.index');
    }
}
