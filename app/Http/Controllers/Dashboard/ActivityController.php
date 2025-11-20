<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityController extends Controller
{
    public function index(Request $request): View
    {
        $activities = Activity::query()
            ->with(['causer'])
            ->where(function ($q) {
                $q->where('is_batch_root', true)
                    ->orwhereNull('batch_uuid');
            })
            ->when($request->filled('event'), fn($q) => $q->where('event', $request->event))
            ->when($request->filled('log_name'), fn($q) => $q->where('log_name', $request->log_name))
            ->latest('created_at')
            ->paginate();

        $items = $activities->getCollection();
        $batch_uuids = $items->whereNotNull('batch_uuid')->pluck('batch_uuid', 'id');
        $batchChildrens = Activity::whereIn('batch_uuid', $batch_uuids->values())
            ->whereNotIn('id', $batch_uuids->keys())->orderBy('created_at')->get()->groupBy('batch_uuid');

        $activities->map(function ($activity) use ($batchChildrens) {
            return $activity->batchChildren = $activity->is_batch_root ? $batchChildrens->get($activity->batch_uuid) : null;
        });

        return view('dashboard.activities.index', compact('activities'));
    }
}
