<?php

namespace App\Livewire\Dashboard\Attendance\Reports;
use App\Enums\SchoolDayType;
use App\Jobs\GenerateAttendancePdfJob;
use App\Models\SchoolDay;
use App\Models\Section;
use App\Services\Attendances\AttendanceReportService;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;

class AttendanceReportsTable extends Component
{
    private $_reportData = [];
    public $daysCount = 0;
    public $isLoaded = false;
    public $sectionName = '';
    public $gradeName = '';
    public $academicYearName = '';
    public $reportStartDate = '';
    public $reportEndDate = '';
    public $sectionId;
    public $academicTermId;

    protected $listeners = ['load-report' => 'loadReport'];

    #[Computed()]
    public function reportData()
    {
        return $this->_reportData;
    }
    public function loadReport($sectionId, $academicTermId, $startDate = null, $endDate = null)
    {
        $this->reset(['daysCount', 'isLoaded', 'sectionName', 'gradeName', 'academicYearName']);

        // Prevent empty calls
        if (!$sectionId || !$academicTermId) {
            return;
        }

        $this->sectionId = $sectionId;
        $this->academicTermId = $academicTermId;

        // Get Section Info for Header
        $section = Section::with(['grade', 'academicYear'])->find($sectionId);
        if ($section) {
            $this->sectionName = $section->name;
            $this->gradeName = $section->grade->name;
            $this->academicYearName = $section->academicYear->name;
        }

        // 1. Fetch School Days
        $query = SchoolDay::query()
            ->where('academic_term_id', $academicTermId)
            ->whereNotIn('status', [SchoolDayType::Holiday]);

        if ($startDate) {
            $query->whereDate('date', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('date', '<=', $endDate);
        }

        $schoolDays = $query->orderBy('date')->get();

        if ($schoolDays->isEmpty()) {
            $this->isLoaded = true;
            return;
        }

        $this->reportStartDate = $schoolDays->first()->date->format('Y-m-d');
        $this->reportEndDate = $schoolDays->last()->date->format('Y-m-d');

        // 2. Fetch Section Students
        // We use DB table and wrap in Eloquent Collection because the Service type-hints EloquentCollection
        // and SectionStudent model does not exist in the project.
        $sectionStudents = DB::table('section_students')
            ->where('section_id', $sectionId)
            ->get();

        // 3. Call Service
        $service = app(AttendanceReportService::class);
        $summary = $service->getStudentsAttendancesStats(
            $schoolDays,
            $sectionStudents,
            chunkFetchingAttendances: false
        );


        $this->daysCount = $summary['total_days'];

        // 4. Aggregate Data
        $aggregated = [];

        // Pre-fill with all students to ensure everyone is listed even if 0 attendance
        $studentsInfo = \App\Models\Student::whereIn('id', $sectionStudents->pluck('student_id'))
            ->with(['user:id,first_name,last_name'])
            ->get()
            ->keyBy('id');

        foreach ($studentsInfo as $studentId => $student) {
            $aggregated[$studentId] = [
                'student' => $student,
                'stats' => app(AttendanceReportService::class)->getEmptyStats(),
            ];
        }

        foreach ($summary['details'] as $dayData) {
            if (isset($dayData['students'])) {
                foreach ($dayData['students'] as $studentStat) {
                    $sid = $studentStat['student_id'];
                    $status = $studentStat['status'];

                    if (isset($aggregated[$sid])) {
                        if (!isset($aggregated[$sid]['stats'][$status])) {
                            $aggregated[$sid]['stats'][$status] = 0;
                        }
                        $aggregated[$sid]['stats'][$status] += 1;
                    }
                }
            }
        }
        $this->_reportData = $aggregated;
        $this->isLoaded = true;

    }

    public function render()
    {
        return view('livewire.dashboard.attendance.reports.attendance-reports-table');
    }
}
