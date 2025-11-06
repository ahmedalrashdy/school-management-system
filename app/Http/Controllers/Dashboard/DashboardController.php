<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function index(Request $request): View
    {

        $stats = [
            'total_students' => Student::count(),
            'total_teachers' => Teacher::count(),
            'total_users' => User::where('is_active', true)->count(),
            'active_year' => null,
        ];
        $term = school()->currentAcademicTerm();
        $startOfWeek = today()->startOfWeek(Carbon::SATURDAY);
        $endOfWeek = today()->endOfWeek(Carbon::FRIDAY);

        return view('dashboard.index', [
            'stats' => $stats,
            'lastActivities' => $this->getActivities(),
            'attendanceStatsParms' => [
                'termId' => $term?->id,
                'startDate' => $startOfWeek,
                'endDate' => $endOfWeek,
            ],
        ]);
    }

    public function getActivities()
    {
        $activities = Activity::query()
            ->with(['causer'])
            ->where(function ($q) {
                $q->where('is_batch_root', true)
                    ->orwhereNull('batch_uuid');
            })
            ->latest('created_at')
            ->limit(5)
            ->get();

        $batch_uuids = $activities->whereNotNull('batch_uuid')->pluck('batch_uuid', 'id');
        $batchChildrens = Activity::whereIn('batch_uuid', $batch_uuids->values())
            ->whereNotIn('id', $batch_uuids->keys())->orderBy('created_at')->get()->groupBy('batch_uuid');

        $activities->map(function ($activity) use ($batchChildrens) {
            return $activity->batchChildren = $activity->is_batch_root ? $batchChildrens->get($activity->batch_uuid) : null;
        });

        return $activities;
    }
}
