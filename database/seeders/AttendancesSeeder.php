<?php

namespace Database\Seeders;

use App\Enums\AttendanceModeEnum;
use App\Enums\AttendanceStatusEnum;
use App\Enums\DayOfWeekEnum;
use App\Enums\DayPartEnum;
use App\Enums\SchoolDayType;
use App\Models\AcademicTerm;
use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\AttendanceSheet;
use App\Models\SchoolDay;
use App\Models\Section;
use App\Models\User;
use App\Services\Attendances\AttendanceSheetService;
use Carbon\Carbon;
use DB;
use Illuminate\Database\Seeder;

class AttendancesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            $systemUser = User::first();
            if (!$systemUser) {
                $this->command->error('لا يوجد مستخدمين في النظام. يرجى إنشاء مستخدم أولاً.');

                return;
            }

            // جلب نمط التحضير من إعدادات المدرسة
            $attendanceSheetService = app(AttendanceSheetService::class);
            $attendanceMode = $attendanceSheetService->getAttendanceMode();

            $this->command->info("نمط التحضير: {$attendanceMode->label()}");

            $activeYear = AcademicYear::active()->first();
            $activeTerm = $activeYear
                ? AcademicTerm::where('academic_year_id', $activeYear->id)->where('is_active', true)->first()
                : null;

            if (! $activeYear || ! $activeTerm) {
                $this->command->error('لا توجد سنة نشطة أو ترم نشط. يرجى تشغيل SchoolBasicSeeder أولاً.');

                return;
            }

            // جلب أيام الترم النشط فقط
            $schoolDays = SchoolDay::where('status', SchoolDayType::SchoolDay->value)
                ->where('academic_year_id', $activeYear->id)
                ->where('academic_term_id', $activeTerm->id)
                ->with(['academicYear', 'academicTerm'])
                ->orderBy('date', 'asc')
                ->get();

            if ($schoolDays->isEmpty()) {
                $this->command->error('لا توجد أيام دراسية. يرجى تشغيل SchoolDaysSeeder أولاً.');

                return;
            }

            $this->command->info("بدء تسجيل الحضور لـ {$schoolDays->count()} يوم دراسي...");

            $sheetsCreated = 0;
            $attendancesCreated = 0;
            $daysProcessed = 0;

            foreach ($schoolDays as $schoolDay) {
                // جلب جميع الشعب في نفس السنة والترم
                $sections = Section::where('academic_year_id', $schoolDay->academic_year_id)
                    ->where('academic_term_id', $schoolDay->academic_term_id)
                    ->with([
                        'students',
                        'timetables' => function ($query) {
                            $query->where('is_active', true)->with('slots');
                        }
                    ])
                    ->get();

                if ($sections->isEmpty()) {
                    continue;
                }

                foreach ($sections as $section) {
                    // إنشاء AttendanceSheet حسب النمط
                    $sheets = $this->createAttendanceSheets($section, $schoolDay, $attendanceMode, $attendanceSheetService, $systemUser);

                    foreach ($sheets as $sheet) {
                        // إنشاء Attendance لكل طالب في الشعبة
                        $students = $section->students;

                        foreach ($students as $student) {
                            // التحقق من عدم وجود سجل حضور مسبق
                            $existingAttendance = Attendance::where('attendance_sheet_id', $sheet->id)
                                ->where('student_id', $student->id)
                                ->first();

                            if ($existingAttendance) {
                                continue; // يوجد سجل مسبق
                            }

                            // تحديد حالة الحضور بناءً على الاحتمالات
                            $status = $this->getDeterministicAttendanceStatus($student->id, $schoolDay->id);

                            Attendance::create([
                                'attendance_sheet_id' => $sheet->id,
                                'student_id' => $student->id,
                                'status' => $status,
                                'notes' => $this->getRandomNotes($status),
                                'modified_by' => $systemUser->id,
                            ]);

                            $attendancesCreated++;
                        }

                        $sheetsCreated++;
                    }
                }

                $daysProcessed++;

                if ($daysProcessed % 10 == 0) {
                    $this->command->info("تم معالجة {$daysProcessed} يوم دراسي...");
                }
            }

            $this->command->info("تم إنشاء {$sheetsCreated} سجل تحضير و {$attendancesCreated} سجل حضور.");
            $this->command->info("تم معالجة {$daysProcessed} يوم دراسي.");
        });
    }

    /**
     * إنشاء AttendanceSheet حسب نمط التحضير.
     */
    protected function createAttendanceSheets(
        Section $section,
        SchoolDay $schoolDay,
        AttendanceModeEnum $mode,
        AttendanceSheetService $service,
        User $systemUser
    ): array {
        $sheets = [];

        switch ($mode) {
            case AttendanceModeEnum::PerPeriod:
                $sheets = $this->createPerPeriodSheets($section, $schoolDay, $service, $systemUser);
                break;
            case AttendanceModeEnum::Daily:
                $sheets = $this->createDailySheets($section, $schoolDay, $service, $systemUser);
                break;
            case AttendanceModeEnum::SplitDaily:
                $sheets = $this->createSplitDailySheets($section, $schoolDay, $service, $systemUser);
                break;
        }

        return $sheets;
    }

    /**
     * إنشاء AttendanceSheet لكل حصة (PerPeriod mode).
     */
    protected function createPerPeriodSheets(
        Section $section,
        SchoolDay $schoolDay,
        AttendanceSheetService $service,
        User $systemUser
    ): array {
        $sheets = [];
        $timetable = $section->activeTimetable();

        if (!$timetable) {
            return $sheets;
        }

        // تحديد يوم الأسبوع
        $dayOfWeek = DayOfWeekEnum::fromCarbonDayOfWeek(Carbon::parse($schoolDay->date)->dayOfWeek);

        // جلب الحصص الدراسية لهذا اليوم
        $slots = $timetable->slots()
            ->where('day_of_week', $dayOfWeek->value)
            ->get();

        foreach ($slots as $slot) {
            // إنشاء أو جلب AttendanceSheet مباشرة
            $sheet = AttendanceSheet::firstOrCreate(
                [
                    'school_day_id' => $schoolDay->id,
                    'section_id' => $section->id,
                    'timetable_slot_id' => $slot->id,
                    'day_part' => null,
                ],
                [
                    'taken_by' => $systemUser->id,
                    'updated_by' => $systemUser->id,
                ]
            );
            $sheets[] = $sheet;
        }

        return $sheets;
    }

    /**
     * إنشاء AttendanceSheet مرة واحدة في اليوم (Daily mode).
     */
    protected function createDailySheets(
        Section $section,
        SchoolDay $schoolDay,
        AttendanceSheetService $service,
        User $systemUser
    ): array {
        $sheets = [];

        // إنشاء أو جلب AttendanceSheet مباشرة
        $sheet = AttendanceSheet::firstOrCreate(
            [
                'school_day_id' => $schoolDay->id,
                'section_id' => $section->id,
                'timetable_slot_id' => null,
                'day_part' => DayPartEnum::FULL_DAY->value,
            ],
            [
                'taken_by' => $systemUser->id,
                'updated_by' => $systemUser->id,
            ]
        );
        $sheets[] = $sheet;

        return $sheets;
    }

    /**
     * إنشاء AttendanceSheet مرتين في اليوم (SplitDaily mode).
     */
    protected function createSplitDailySheets(
        Section $section,
        SchoolDay $schoolDay,
        AttendanceSheetService $service,
        User $systemUser
    ): array {
        $sheets = [];

        // إنشاء سجل للفترة الأولى
        $sheet1 = AttendanceSheet::firstOrCreate(
            [
                'school_day_id' => $schoolDay->id,
                'section_id' => $section->id,
                'timetable_slot_id' => null,
                'day_part' => DayPartEnum::PART_ONE_ONLY->value,
            ],
            [
                'taken_by' => $systemUser->id,
                'updated_by' => $systemUser->id,
            ]
        );
        $sheets[] = $sheet1;

        // إنشاء سجل للفترة الثانية
        $sheet2 = AttendanceSheet::firstOrCreate(
            [
                'school_day_id' => $schoolDay->id,
                'section_id' => $section->id,
                'timetable_slot_id' => null,
                'day_part' => DayPartEnum::PART_TWO_ONLY->value,
            ],
            [
                'taken_by' => $systemUser->id,
                'updated_by' => $systemUser->id,
            ]
        );
        $sheets[] = $sheet2;

        return $sheets;
    }

    /**
     * تحديد حالة الحضور بناءً على الاحتمالات.
     */
    protected function getDeterministicAttendanceStatus(int $studentId, int $schoolDayId): AttendanceStatusEnum
    {
        $selector = ($studentId + $schoolDayId) % 20;

        return match (true) {
            $selector < 14 => AttendanceStatusEnum::Present, // 70%
            $selector < 17 => AttendanceStatusEnum::Absent,  // 15%
            $selector < 19 => AttendanceStatusEnum::Excused, // 10%
            default => AttendanceStatusEnum::Late,           // 5%
        };
    }

    /**
     * الحصول على ملاحظات عشوائية حسب حالة الحضور.
     */
    protected function getRandomNotes(AttendanceStatusEnum $status): ?string
    {
        $notes = [
            AttendanceStatusEnum::Present->value => [
                'حضور ممتاز',
            ],
            AttendanceStatusEnum::Absent->value => [
                'غياب بدون عذر',
                'لم يحضر',
            ],
            AttendanceStatusEnum::Excused->value => [
                'إعتذار مسبق',
                'إعتذار ولي الأمر',
            ],
            AttendanceStatusEnum::Late->value => [
                'تأخير 10 دقائق',
                'تأخير 15 دقيقة',
            ],
        ];

        $statusNotes = $notes[$status->value] ?? [null];
        return $statusNotes[0] ?? null;
    }
}
