<?php

namespace App\Services\Attendances;

use App\Enums\AttendanceStatusEnum;
use App\Enums\DayPartEnum;
use App\Enums\SchoolDayType;
use App\Models\Attendance;
use App\Models\SchoolDay;
use App\Models\Section;
use App\Models\Student;

class AttendanceSectionService
{
    public function getFullTermAttendance(Student $student, Section $section)
    {

        // 1->fetch all school days of term using academic_term_id
        $schoolDays = SchoolDay::query()
            ->where('academic_year_id', $section->academic_year_id)
            ->where('academic_term_id', $section->academic_term_id)
            ->with([
                'attendanceSheets' => function ($q) use ($section) {
                    $q->where('section_id', $section->id)
                        ->with([
                            'timetableSlot.teacherAssignment.curriculumSubject.subject',
                        ]);
                }
            ])
            ->orderBy('date', 'asc')
            ->get();

        // 2.extract sheetIds
        $allSheetIds = $schoolDays->pluck('attendanceSheets')->flatten()->pluck('id')->toArray();

        $studentAttendances = Attendance::whereIn('attendance_sheet_id', $allSheetIds)
            ->where('student_id', $student->id)
            ->get()
            ->keyBy('attendance_sheet_id');

        // 3.stats
        $structure = [];
        $stats = [
            'total_days' => 0,
            AttendanceStatusEnum::Present->value => 0,
            AttendanceStatusEnum::Absent->value => 0,
            AttendanceStatusEnum::Late->value => 0,
            AttendanceStatusEnum::Excused->value => 0,
            'partial_absence' => 0,
            'present_with_late' => 0,
            'partial_excused' => 0,
        ];

        foreach ($schoolDays as $day) {
            $date = $day->date;
            $monthKey = $date->format('Y-m');
            $monthLabel = $date->translatedFormat('F Y');

            $weekNumber = $date->weekOfMonth;
            $weekKey = "week_{$weekNumber}";

            $dayResult = $this->calculateDailyStatus($day, $studentAttendances);

            // فقط الايام التي تم تسجيل الحضور فيها فعلياً
            if ($dayResult['is_calculated']) {
                $stats['total_days']++;
                $statusCode = $dayResult['final_status_code'];

                if (isset($stats[$statusCode])) {
                    $stats[$statusCode]++;
                }
            }

            // بناء الهيكل الهرمي للواجهة: شهر -> أسبوع -> أيام
            if (!isset($structure[$monthKey])) {
                $structure[$monthKey] = [
                    'label' => $monthLabel,
                    'weeks' => [],
                ];
            }
            if (!isset($structure[$monthKey]['weeks'][$weekKey])) {
                $structure[$monthKey]['weeks'][$weekKey] = [
                    'label' => "الأسبوع {$weekNumber}",
                    'days' => [],
                ];
            }

            $structure[$monthKey]['weeks'][$weekKey]['days'][] = $dayResult;
        }

        return [
            'calendar' => $structure,
            'stats' => $stats,
        ];
    }

    public function getAbsentStudentsList(int $sectionId, ?array $sheetIds = null, ?array $columns = null)
    {
        // جلب الطلاب الغائبين أو غير المسجلين في الشعبة المحددة
        return Student::query()
            ->whereHas(
                'sections',
                fn($q) => $q->where('sections.id', $sectionId)
            )
            ->where(function ($query) use ($sheetIds) {
                $query->whereHas('attendances', function ($q) use ($sheetIds) {
                    $q->whereIn('attendance_sheet_id', $sheetIds)
                        ->where('status', AttendanceStatusEnum::Absent);
                })
                    ->orWhereDoesntHave('attendances', function ($q) use ($sheetIds) {
                        $q->whereIn('attendance_sheet_id', $sheetIds);
                    });
            })
            ->with([
                'user:id,first_name,last_name',
            ])
            ->select($columns ?? ['*'])
            ->get();
    }


    /**
     * Get all students with their attendance status for a specific sheet
     */
    public function getAllStudentsWithAttendance(int $sectionId, ?int $sheetId = null): \Illuminate\Support\Collection
    {
        // Get all students in the section
        $students = \App\Models\Student::query()
            ->whereHas('sections', fn($q) => $q->where('sections.id', $sectionId))
            ->with([
                'user:id,first_name,last_name',
                'attendances' => fn($q) => $q->when($sheetId, fn($query) => $query->where('attendance_sheet_id', $sheetId))
            ])
            ->select(['students.id', 'students.admission_number', 'students.user_id'])
            ->orderBy('students.admission_number')
            ->get();

        return $students->map(function ($student) use ($sheetId) {
            $attendance = $student->attendances->first();

            return [
                'id' => $student->id,
                'admission_number' => $student->admission_number,
                'name' => $student->user->first_name . ' ' . $student->user->last_name,
                'status' => $attendance?->status ?? null,
                'status_label' => $attendance ? $attendance->status->label() : 'غير مسجل',
                'status_color' => $this->getStatusColor($attendance?->status),
                'notes' => $attendance?->notes,
                'recorded_at' => $attendance?->created_at?->format('H:i'),
            ];
        });
    }

    /**
     * Get status color for UI display
     */
    private function getStatusColor(?\App\Enums\AttendanceStatusEnum $status): string
    {
        if (!$status) {
            return 'gray';
        }

        return match ($status) {
            \App\Enums\AttendanceStatusEnum::Present => 'green',
            \App\Enums\AttendanceStatusEnum::Absent => 'red',
            \App\Enums\AttendanceStatusEnum::Late => 'orange',
            \App\Enums\AttendanceStatusEnum::Excused => 'blue',
        };
    }

    private function calculateDailyStatus($day, $studentAttendances)
    {
        $dayData = [
            'date_full' => $day->date->translatedFormat('l j F Y'),
            'date_day' => $day->date->format('d'),
            'day_name' => $day->date->translatedFormat('l'),
            'is_holiday' => $day->status == SchoolDayType::Holiday,
            'is_partial_holiday' => $day->status == SchoolDayType::PartialHoliday,
            'day_part' => $day->day_part,
            'slots' => [],
            'final_status_label' => '',
            'final_status_color' => 'gray', // gray, green, red, orange, yellow, rose
            'final_status_code' => null,
            'is_calculated' => false,
        ];

        // إذا كان عطلة
        if ($dayData['is_holiday']) {
            $dayData['final_status_label'] = $day->status->label();
            $dayData['final_status_color'] = 'blue';

            return $dayData;
        }
        if ($dayData['is_partial_holiday']) {
            $dayData['final_status_label'] = $day->status->label();
            $dayData['final_status_color'] = 'blue';

            return $dayData;
        }

        $dayStatuses = [];

        foreach ($day->attendanceSheets as $sheet) {
            $attRecord = $studentAttendances[$sheet->id] ?? null;
            $status = $attRecord ? $attRecord->status->value : AttendanceStatusEnum::Absent->value; // اذا لم يكن مسجل في سجل التحضير نعتبره غائب

            $dayStatuses[] = $status;

            // بيانات تفصيلية للحصة (للعرض عند الفتح)
            $label = '';
            if ($sheet->timetableSlot) {// timetable slot mode
                $label = 'الحصة ' . $sheet->timetableSlot->period_number;
                $subject = $sheet->timetableSlot->teacherAssignment->curriculumSubject->subject->name;
            } elseif ($sheet->day_part == DayPartEnum::PART_ONE_ONLY || $sheet->day_part == DayPartEnum::PART_TWO_ONLY) {
                $label = $sheet->day_part->label();
                $subject = 'تحضير في الفترة مرة';
            } else {
                $label = $sheet->day_part->label();
                $subject = 'تحضير في اليوم مرة';
            }

            $dayData['slots'][] = [
                'label' => $label,
                'subject' => $subject,
                'status' => $status, // Integer value
                'status_text' => AttendanceStatusEnum::from($status)->label(),
                'notes' => $attRecord?->notes,
            ];
        }

        if (empty($dayStatuses)) {
            $dayData['final_status_label'] = 'غير مرصود';

            return $dayData;
        }

        $counts = array_count_values($dayStatuses);
        $totalSlots = count($dayStatuses); // كم مرة تم التحضير في اليوم

        $presentCount = $counts[AttendanceStatusEnum::Present->value] ?? 0;
        $absentCount = $counts[AttendanceStatusEnum::Absent->value] ?? 0;
        $lateCount = $counts[AttendanceStatusEnum::Late->value] ?? 0;
        $excusedCount = $counts[AttendanceStatusEnum::Excused->value] ?? 0;

        $dayData['is_calculated'] = true;

        // 1.present full day
        if ($presentCount == $totalSlots) {
            $dayData['final_status_code'] = AttendanceStatusEnum::Present->value;
            $dayData['final_status_label'] = 'حضور كامل';
            $dayData['final_status_color'] = 'green';
        }
        // 1.present full day
        elseif ($absentCount == $totalSlots) {
            $dayData['final_status_code'] = AttendanceStatusEnum::Absent->value;
            $dayData['final_status_label'] = 'غياب كامل';
            $dayData['final_status_color'] = 'red';
        } elseif ($excusedCount == $totalSlots) {
            $dayData['final_status_code'] = AttendanceStatusEnum::Excused->value;
            $dayData['final_status_label'] = 'أعتذر عن حضور اليوم';
            $dayData['final_status_color'] = 'yellow';
        } elseif ($lateCount == $totalSlots) {
            $dayData['final_status_code'] = AttendanceStatusEnum::Late->value;
            $dayData['final_status_label'] = 'تأخر في جميع مرات الرصد';
            $dayData['final_status_color'] = 'orange';
        } else {
            // mixed status

            // present with  absent
            if ($absentCount > 0) {
                $dayData['final_status_code'] = 'partial_absence';
                $dayData['final_status_label'] = 'غياب جزئي';
                $dayData['final_status_color'] = 'rose';
            }
            // present with  excused
            elseif ($excusedCount > 0) {
                $dayData['final_status_code'] = 'partial_excused';
                $dayData['final_status_label'] = 'استئذان جزئي';
                $dayData['final_status_color'] = 'amber';
            }
            // present with  late
            elseif ($lateCount > 0) {
                $dayData['final_status_code'] = 'present_with_late';
                $dayData['final_status_label'] = 'حضور وتأخير';
                $dayData['final_status_color'] = 'orange';
            } else {
                $dayData['final_status_code'] = AttendanceStatusEnum::Present->value;
                $dayData['final_status_label'] = 'حضور';
                $dayData['final_status_color'] = 'green';
            }
        }

        return $dayData;
    }
}
