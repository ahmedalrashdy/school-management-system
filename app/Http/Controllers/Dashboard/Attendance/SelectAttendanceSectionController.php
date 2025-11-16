<?php

namespace App\Http\Controllers\Dashboard\Attendance;

use App\Enums\AttendanceModeEnum;
use App\Enums\DayPartEnum;
use App\Http\Controllers\Controller;
use App\Models\SchoolDay;
use App\Models\Section;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SelectAttendanceSectionController extends Controller
{
    /**
     * Show the page to select section for attendance recording.
     */
    public function show(Request $request, string $date): View
    {
        $activeYear = school()->activeYear();
        if (! $activeYear) {
            abort(404, 'لا توجد سنة دراسية نشطة');
        }

        // التحقق من نمط التحضير
        $attendanceMode = school()->getAttendanceMode();

        // لا يمكن فتح هذه الصفحة إلا إذا كان النمط Daily أو SplitDaily
        if ($attendanceMode === AttendanceModeEnum::PerPeriod) {
            abort(403, 'لا يمكن استخدام هذه الصفحة مع نمط التحضير لكل حصة');
        }

        // التحقق من وجود اليوم الدراسي
        $schoolDay = SchoolDay::where('academic_year_id', $activeYear->id)
            ->whereDate('date', $date)
            ->first();

        if (! $schoolDay) {
            abort(404, 'اليوم المحدد غير موجود في التقويم الدراسي');
        }

        // التحقق من أن اليوم ليس عطلة
        if ($schoolDay->isHoliday || $schoolDay->isPartialHoliday) {
            abort(403, 'لا يمكن تسجيل الحضور في يوم عطلة');
        }

        // جلب الشعب - استخدام academic_term_id من schoolDay أو currentAcademicTerm
        $academicTermId = $schoolDay->academic_term_id ?? school()->currentAcademicTerm()?->id;
        $sections = Section::where('academic_year_id', $activeYear->id)
            ->when($academicTermId, function ($query) use ($academicTermId) {
                $query->where('academic_term_id', $academicTermId);
            })
            ->with(['grade.stage', 'academicTerm'])
            ->orderBy('name')
            ->get();

        // تنسيق التاريخ
        $formattedDate = Carbon::parse($date)->locale('ar')->translatedFormat('l j F Y');

        return view('dashboard.attendance.attendances.select-section', [
            'date' => $date,
            'formattedDate' => $formattedDate,
            'schoolDay' => $schoolDay,
            'sections' => $sections,
            'attendanceMode' => $attendanceMode,
            'dayPartOptions' => [
                DayPartEnum::PART_ONE_ONLY->value => DayPartEnum::PART_ONE_ONLY->label(),
                DayPartEnum::PART_TWO_ONLY->value => DayPartEnum::PART_TWO_ONLY->label(),
            ],
        ]);
    }
}
