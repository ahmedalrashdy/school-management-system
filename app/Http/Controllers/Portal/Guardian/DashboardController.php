<?php

namespace App\Http\Controllers\Portal\Guardian;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the guardian dashboard.
     */
    public function index(): View
    {
        return view('portal.guardian.index');
    }
}
