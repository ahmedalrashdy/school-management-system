<?php

namespace App\Livewire\Dashboard\Attendance;

use App\Models\Section;
use App\Services\Attendances\AttendanceReportService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Locked;
use Livewire\Component;

#[Lazy()]
class Stats extends Component
{
    #[Locked()]
    public int $academicTermId;

    #[Locked()]
    public Carbon $startDate;

    #[Locked()]
    public Carbon $endDate;

    #[Computed()]
    public function chartData()
    {
        return Cache::remember("attendances", 300, function () {
            $summaryDetails = app(AttendanceReportService::class)->getAttendancesStats(
                $this->academicTermId,
                $this->startDate,
                $this->endDate,
                chunkFetchingAttendances: false// حجم البيانات مقبول
            );
            $sectionsIds = $summaryDetails['details']->pluck('section_id');
            $sectionsMetadata = Section::whereIn('id', $sectionsIds)
                ->with(['grade.stage'])
                ->get()
                ->keyBy('id')
                ->map(function ($section) {
                    return [
                        'section_name' => $section->name,
                        'grade_id' => $section->grade_id,
                        'grade_name' => $section->grade->name,
                        'stage_id' => $section->grade->stage_id,
                        'stage_name' => $section->grade->stage->name,
                    ];
                });
            $summary = $summaryDetails['details']->map(function ($row) use ($sectionsMetadata) {
                $row['meta'] = $sectionsMetadata[$row['section_id']] ?? [];
                return $row;
            });
            return $summary;
        });
    }
    public function render()
    {
        return view('livewire.dashboard.attendance.stats');
    }
}
