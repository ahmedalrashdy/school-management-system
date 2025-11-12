<?php

namespace App\Http\Controllers\Dashboard\Timetables;

use App\Enums\DayOfWeekEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Timetables\TimetableSettings\StoreTimetableSettingRequest;
use App\Http\Requests\Dashboard\Timetables\TimetableSettings\UpdateTimetableSettingRequest;
use App\Models\TimetableSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class TimetableSettingController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:' . \Perm::TimetableSettingsManage->value, only: ['index', 'create', 'store', 'edit', 'update', 'destroy']),
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $settings = TimetableSetting::withCount('timetables')
            ->latest()
            ->paginate(20);

        return view('dashboard.timetables.timetable-settings.index', [
            'settings' => $settings,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('dashboard.timetables.timetable-settings.create', [
            'days' => DayOfWeekEnum::formOptions(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTimetableSettingRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // إزالة الأيام التي عدد حصصها 0
        if (isset($data['periods_per_day'])) {
            $data['periods_per_day'] = array_filter(
                $data['periods_per_day'],
                fn($periods) => $periods > 0
            );
        }

        // إذا تم تفعيل القالب، قم بتعطيل جميع القوالب الأخرى
        if (isset($data['is_active']) && $data['is_active']) {
            TimetableSetting::where('is_active', true)->update(['is_active' => false]);
        }

        TimetableSetting::create($data);

        return redirect()
            ->route('dashboard.timetable-settings.index')
            ->with('success', 'تم إنشاء قالب الإعدادات بنجاح.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TimetableSetting $timetableSetting): View
    {
        return view('dashboard.timetables.timetable-settings.edit', [
            'setting' => $timetableSetting,
            'days' => DayOfWeekEnum::formOptions(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTimetableSettingRequest $request, TimetableSetting $timetableSetting): RedirectResponse
    {
        $data = $request->validated();

        // إزالة الأيام التي عدد حصصها 0
        if (isset($data['periods_per_day'])) {
            $data['periods_per_day'] = array_filter(
                $data['periods_per_day'],
                fn($periods) => $periods > 0
            );
        }

        // إذا تم تفعيل القالب، قم بتعطيل جميع القوالب الأخرى
        if (isset($data['is_active']) && $data['is_active'] && !$timetableSetting->is_active) {
            TimetableSetting::where('is_active', true)
                ->where('id', '!=', $timetableSetting->id)
                ->update(['is_active' => false]);
        }

        $timetableSetting->update($data);

        return redirect()
            ->route('dashboard.timetable-settings.index')
            ->with('success', 'تم تحديث قالب الإعدادات بنجاح.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TimetableSetting $timetableSetting): RedirectResponse
    {
        if (!$timetableSetting->canBeDeleted()) {
            $reason = $timetableSetting->getDeletionBlockReason();

            return redirect()
                ->route('dashboard.timetable-settings.index')
                ->with('error', $reason);
        }

        $timetableSetting->delete();

        return redirect()
            ->route('dashboard.timetable-settings.index')
            ->with('success', 'تم حذف قالب الإعدادات بنجاح.');
    }
}
