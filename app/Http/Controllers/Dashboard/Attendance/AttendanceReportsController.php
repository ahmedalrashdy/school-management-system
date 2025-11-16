<?php

namespace App\Http\Controllers\Dashboard\Attendance;

use App\Http\Controllers\Controller;

class AttendanceReportsController extends Controller
{
    /**
     * Display the attendance reports page
     */
    public function index()
    {
        return view('dashboard.attendance.reports.index');
    }
}
