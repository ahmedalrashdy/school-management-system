<?php

namespace App\Http\Controllers\Portal\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the teacher dashboard.
     */
    public function index(): View
    {
        return view('portal.teacher.index');
    }
}
