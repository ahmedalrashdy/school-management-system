<?php

namespace App\Livewire\Dashboard\Attendance\Attendances;

use App\Enums\AttendanceModeEnum;
use App\Enums\AttendanceStatusEnum;
use App\Enums\DayPartEnum;
use App\Models\Attendance;
use App\Models\AttendanceSheet;
use App\Models\SchoolDay;
use App\Models\Section;
use App\Models\TimetableSlot;
use App\Services\Attendances\AttendanceSheetService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class RecordAttendance extends Component
{
    use WithPagination;

    public Section $section;

    public ?TimetableSlot $timetableSlot = null;

    public ?SchoolDay $schoolDay = null;

    public ?DayPartEnum $dayPart = null;

    public AttendanceSheet $attendanceSheet;

    public array $attendances = [];

    public array $attendanceNotes = [];

    private AttendanceSheetService $attendanceSheetService;

    /**
     * Mount method that handles all three modes based on route parameters.
     */
    public function mount(
        AttendanceSheetService $attendanceSheetService,
    ) {
        $this->attendanceSheetService = $attendanceSheetService;

        // Determine mode based on provided parameters
        if ($this->timetableSlot) {
            // PerPeriod mode
            $this->timetableSlot->load([
                'timetable',
                'teacherAssignment.curriculumSubject.subject',
                'teacherAssignment.teacher.user',
            ]);

            $this->dayPart = null;
        } elseif ($this->schoolDay && $this->dayPart === null) {
            // Daily mode
            $this->timetableSlot = null;
            $this->dayPart = DayPartEnum::FULL_DAY;
        } elseif ($this->schoolDay && $this->dayPart !== null) {
            // SplitDaily mode
            $this->timetableSlot = null;
        } else {
            abort(404, 'Invalid attendance recording parameters');
        }

        $this->loadAttendanceSheet();
    }

    /**
     * Load or create attendance sheet based on current mode.
     */
    private function loadAttendanceSheet(): void
    {
        // Validate that we can record attendance
        if (
            !$this->attendanceSheetService->canRecordAttendance(
                $this->section,
                $this->schoolDay,
                $this->timetableSlot,
                $this->dayPart
            )
        ) {
            session()->flash('error', 'لا يمكن تسجيل الحضور لهذا اليوم.');

            return;
        }

        $this->attendanceSheet = $this->attendanceSheetService->getOrCreateSheet(
            $this->section,
            $this->schoolDay,
            $this->timetableSlot,
            $this->dayPart
        );

        $this->attendanceSheet->load(['takenBy', 'updatedBy']);
    }

    /**
     * Get display information based on mode.
     */
    public function getDisplayInfo(): array
    {
        $mode = school()->getAttendanceMode();

        return match ($mode) {
            AttendanceModeEnum::PerPeriod => [
                'title' => 'تسجيل الحضور - ' . $this->timetableSlot->teacherAssignment->curriculumSubject->subject->name,
                'subtitle' => 'الحصة ' . $this->timetableSlot->period_number,
                'info' => [
                    'المادة' => $this->timetableSlot->teacherAssignment->curriculumSubject->subject->name,
                    'المدرس' => $this->timetableSlot->teacherAssignment->teacher->user->first_name . ' ' .
                        $this->timetableSlot->teacherAssignment->teacher->user->last_name,
                    'رقم الحصة' => $this->timetableSlot->period_number,
                    'التاريخ' => $this->schoolDay->date->format('Y-m-d'),
                ],
            ],
            AttendanceModeEnum::Daily => [
                'title' => 'تسجيل الحضور اليومي',
                'subtitle' => $this->section->grade->name . ' - شعبة ' . $this->section->name,
                'info' => [
                    'الشعبة' => $this->section->grade->name . ' - شعبة ' . $this->section->name,
                    'التاريخ' => $this->schoolDay->date->format('Y-m-d'),
                ],
            ],
            AttendanceModeEnum::SplitDaily => [
                'title' => 'تسجيل الحضور - ' . $this->dayPart->label(),
                'subtitle' => $this->section->grade->name . ' - شعبة ' . $this->section->name,
                'info' => [
                    'الشعبة' => $this->section->grade->name . ' - شعبة ' . $this->section->name,
                    'الفترة' => $this->dayPart->label(),
                    'التاريخ' => $this->schoolDay->date->format('Y-m-d'),
                ],
            ],
        };
    }

    /**
     * Get the back route based on mode.
     */
    public function getBackRoute(): string
    {
        $mode = school()->getAttendanceMode();

        return match ($mode) {
            AttendanceModeEnum::PerPeriod => route('dashboard.sections.timetable', $this->section),
            AttendanceModeEnum::Daily, AttendanceModeEnum::SplitDaily => route('dashboard.academic-calendar.index'),
        };
    }

    private function loadAttendancesForStudents(array $studentIds): void
    {
        // جلب بيانات الحضور المحفوظة للطلاب المحددين فقط
        $existingAttendances = Attendance::where('attendance_sheet_id', $this->attendanceSheet->id)
            ->whereIn('student_id', $studentIds)
            ->get()
            ->keyBy('student_id');

        foreach ($studentIds as $studentId) {
            // تحميل البيانات فقط إذا لم تكن محملة مسبقاً
            if (!isset($this->attendances[$studentId])) {
                $attendance = $existingAttendances->get($studentId);

                $this->attendances[$studentId] = $attendance
                    ? $attendance->status->value
                    : AttendanceStatusEnum::Present->value; // الحالة الافتراضية: حاضر

                $this->attendanceNotes[$studentId] = $attendance?->notes ?? '';
            }
        }
    }

    public function save(): void
    {
        $this->saveAttendancesForCurrentPage();

        session()->flash('success', 'تم حفظ بيانات الحضور بنجاح.');
    }

    public function saveAndGoToNextPage(): void
    {
        $this->saveAttendancesForCurrentPage();

        $students = $this->getStudentsQuery()->paginate(15);
        $currentPage = $this->getPage();

        if ($currentPage < $students->lastPage()) {
            $this->nextPage();
            session()->flash('success', 'تم حفظ البيانات والانتقال للصفحة التالية.');
        } else {
            session()->flash('success', 'تم حفظ جميع البيانات.');
        }
    }

    public function saveAndExit(): void
    {
        $this->saveAttendancesForCurrentPage();

        session()->flash('success', 'تم حفظ بيانات الحضور بنجاح.');

        $this->redirect($this->getBackRoute());
    }

    private function saveAttendancesForCurrentPage(): void
    {
        $students = $this->getStudentsQuery()
            ->paginate(15, ['*'], 'page', $this->getPage());

        foreach ($students as $student) {
            if (!isset($this->attendances[$student->id])) {
                // إذا لم تكن هناك بيانات، استخدم القيمة الافتراضية
                $this->attendances[$student->id] = AttendanceStatusEnum::Present->value;
            }

            Attendance::updateOrCreate(
                [
                    'attendance_sheet_id' => $this->attendanceSheet->id,
                    'student_id' => $student->id,
                ],
                [
                    'status' => AttendanceStatusEnum::from($this->attendances[$student->id]),
                    'notes' => $this->attendanceNotes[$student->id] ?? null,
                    'modified_by' => Auth::id(),
                ]
            );
        }

        // Update the attendance sheet's updated_by
        $this->attendanceSheet->update(['updated_by' => Auth::id()]);

        $this->dispatch('show-toast', type: 'success', message: 'تم تحضير طلاب الصفحة الحالية');
    }

    private function getStudentsQuery()
    {
        return $this->section->students()
            ->with('user')
            ->orderBy('admission_number');
    }

    public function render()
    {
        $students = $this->getStudentsQuery()->paginate(15);

        // تحميل بيانات الحضور للطلاب في الصفحة الحالية فقط
        if ($students->count() > 0) {
            $studentIds = $students->pluck('id')->toArray();
            $this->loadAttendancesForStudents($studentIds);
        }

        $displayInfo = $this->getDisplayInfo();

        return view('livewire.dashboard.attendance.attendances.record-attendance', [
            'students' => $students,
            'displayInfo' => $displayInfo,
        ]);
    }
}
