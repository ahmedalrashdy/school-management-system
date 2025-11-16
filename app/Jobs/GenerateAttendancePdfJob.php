<?php

namespace App\Jobs;

use App\Enums\SchoolDayType;
use App\Models\SchoolDay;
use App\Models\Section;
use App\Models\Student;
use App\Models\User;
use App\Notifications\ReportReadyNotification;
use App\Services\Attendances\AttendanceReportService;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use App\Enums\AttendanceStatusEnum;
use Illuminate\Support\Facades\Storage;
use Spatie\LaravelPdf\Facades\Pdf;
class GenerateAttendancePdfJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public User $user,
        public ?int $sectionId,
        public ?int $gradeId,
        public int $academicTermId,
        public ?Carbon $reportStartDate,
        public ?Carbon $reportEndDate,

    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // 1. Fetch Sections to Report On
        $sections = Section::query()
            ->with(['grade', 'academicYear'])
            ->when($this->sectionId, fn($q) => $q->where('id', $this->sectionId))
            ->when($this->gradeId && !$this->sectionId, fn($q) => $q->where('grade_id', $this->gradeId))
            ->orderBy('grade_id')
            ->orderBy('name')
            ->get();
        // Get School Days
        $query = SchoolDay::query()
            ->where('academic_term_id', $this->academicTermId)
            ->whereNotIn('status', [SchoolDayType::Holiday]);

        if ($this->reportStartDate) {
            $query->whereDate('date', '>=', $this->reportStartDate);
        }
        if ($this->reportEndDate) {
            $query->whereDate('date', '<=', $this->reportEndDate);
        }

        $schoolDays = $query->orderBy('date')->get();
        $error = null;
        if ($schoolDays->isEmpty()) {
            $error = 'No school days found';
        }

        // If specific range not provided, use actual data range
        if (!$this->reportStartDate && $error === null) {
            $this->reportStartDate = $schoolDays->first()->date->format('Y-m-d');
        }
        if (!$this->reportEndDate && $error === null) {
            $this->reportEndDate = $schoolDays->last()->date->format('Y-m-d');
        }

        $sectionsData = [];
        $isGradeReport = !$this->sectionId && $this->gradeId;
        $service = app(AttendanceReportService::class);

        foreach ($sections as $section) {

            // Fetch students for this section
            $sectionStudents = DB::table('section_students')
                ->where('section_id', $section->id)
                ->get();

            if ($sectionStudents->isEmpty()) {
                continue;
            }
            // Calculate Stats
            $summary = $service->getStudentsAttendancesStats(
                $schoolDays,
                $sectionStudents,
                chunkFetchingAttendances: false
            );
            $daysCount = $summary['total_days'];

            // Fetch User Details
            $studentsInfo = Student::whereIn('id', $sectionStudents->pluck('student_id'))
                ->with(['user:id,first_name,last_name'])
                ->get()
                ->keyBy('id');

            // Initialize aggregation
            $aggregated = [];
            foreach ($studentsInfo as $id => $student) {
                $aggregated[$id] = [
                    'student' => $student,
                    'stats' => $service->getEmptyStats()
                ];
            }

            // Fill stats
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

            // Flatten
            $flattenedStudents = [];
            foreach ($aggregated as $data) {
                $student = $data['student'];
                $stats = $data['stats'];

                $present = $stats[AttendanceStatusEnum::Present->value] ?? 0;
                $late = $stats[AttendanceStatusEnum::Late->value] ?? 0;
                $totalPresence = $present + $late;
                $percentage = $daysCount > 0
                    ? round(($totalPresence / $daysCount) * 100, 1)
                    : 0;

                $flattenedStudents[] = [
                    'name' => $student->user->first_name . ' ' . $student->user->last_name,
                    'stats' => [
                        'present' => $present,
                        'absent' => $stats[AttendanceStatusEnum::Absent->value] ?? 0,
                        'late' => $late,
                        'excused' => $stats[AttendanceStatusEnum::Excused->value] ?? 0,
                        'present_with_late' => $stats['present_with_late'] ?? 0,
                        'partial_absence' => $stats['partial_absence'] ?? 0,
                        'partial_excused' => $stats['partial_excused'] ?? 0,
                    ],
                    'percentage' => $percentage,
                ];
            }

            // Sort by name
            usort($flattenedStudents, function ($a, $b) {
                return strcmp($a['name'], $b['name']);
            });

            $sectionsData[] = [
                'academic_year' => $section->academicYear->name,
                'grade' => $section->grade->name,
                'name' => $section->name,
                'days_count' => $daysCount,
                'students' => $flattenedStudents,
            ];
        }

        $disk = Storage::disk('local');
        $directory = 'reports/attendance';
        $fileName = 'attendance-report-' . now()->format('Y-m-d') . '.pdf';
        $filePath = $directory . '/' . $fileName;
        if (!$disk->exists($directory)) {
            $disk->makeDirectory($directory);
        }
        $absolutePath = $disk->path($filePath);
        Pdf::view('pdf.attendance-report', [
            'sections' => $sectionsData,
            'reportStartDate' => $this->reportStartDate,
            'reportEndDate' => $this->reportEndDate,
            'schoolName' => school()->schoolSetting('school_name', 'نظام المدرسة'),
            'isGradeReport' => $isGradeReport,
        ])
            ->save($absolutePath);

        $this->user->notify(new ReportReadyNotification(
            $fileName,
            $this->user,
        ));
    }
}
