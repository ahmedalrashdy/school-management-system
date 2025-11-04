<?php

namespace Database\Seeders;

use App\Enums\AttendanceModeEnum;
use App\Enums\AttendanceStatusEnum;
use App\Enums\DayOfWeekEnum;
use App\Enums\DayPartEnum;
use App\Enums\SchoolDayType;
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
            // جلب أول مستخدم (للاستخدام في taken_by و updated_by)
            $systemUser = User::first();
            if (! $systemUser) {
                $this->command->error('لا يوجد مستخدمين في النظام. يرجى إنشاء مستخدم أولاً.');

                return;
            }

            // جلب نمط التحضير من إعدادات المدرسة
            $attendanceSheetService = app(AttendanceSheetService::class);
            $attendanceMode = $attendanceSheetService->getAttendanceMode();

            $this->command->info("نمط التحضير: {$attendanceMode->label()}");

            // جلب جميع الأيام الدراسية (غير العطلة)
            $schoolDays = SchoolDay::where('status', SchoolDayType::SchoolDay->value)
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
                    ->with(['students', 'timetables' => function ($query) {
                        $query->where('is_active', true)->with('slots');
                    }])
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
                            $status = $this->getRandomAttendanceStatus();

                            Attendance::create([
                                'attendance_sheet_id' => $sheet->id,
                                'student_id' => $student->id,
                                'status' => $status,
                                'notes' => $this->getRandomNotes($status),
                                'modified_by' => 1, // افتراضياً المستخدم الأول
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

        if (! $timetable) {
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
    protected function getRandomAttendanceStatus(): AttendanceStatusEnum
    {
        $random = rand(1, 100);

        // الحضور: 70%
        if ($random <= 70) {
            return AttendanceStatusEnum::Present;
        }

        // الغياب: 15%
        if ($random <= 85) {
            return AttendanceStatusEnum::Absent;
        }

        // الإعتذار: 10%
        if ($random <= 95) {
            return AttendanceStatusEnum::Excused;
        }

        // التأخير: 5%
        return AttendanceStatusEnum::Late;
    }

    /**
     * الحصول على ملاحظات عشوائية حسب حالة الحضور.
     */
    protected function getRandomNotes(AttendanceStatusEnum $status): ?string
    {
        $notes = [
            AttendanceStatusEnum::Present->value => [
                null,
                null,
                null,
                'حضور ممتاز',
            ],
            AttendanceStatusEnum::Absent->value => [
                null,
                'غياب بدون عذر',
                'لم يحضر',
            ],
            AttendanceStatusEnum::Excused->value => [
                null,
                'إعتذار مسبق',
                'إعتذار ولي الأمر',
            ],
            AttendanceStatusEnum::Late->value => [
                null,
                'تأخير 10 دقائق',
                'تأخير 15 دقيقة',
            ],
        ];

        $statusNotes = $notes[$status->value] ?? [null];
        $randomNote = $statusNotes[array_rand($statusNotes)];

        return $randomNote;
    }
}
