<?php

namespace App\Livewire\Dashboard\Users\Students;

use App\Models\AcademicTerm;
use App\Models\Section;
use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Component;

class AssignSingleStudentToSection extends Component
{
    public Student $student;

    #[Locked]
    public int $academic_year_id;

    #[Locked]
    public int $grade_id;

    #[Locked]
    public string $grade_name;

    public ?int $academic_term_id = null;
    public ?int $section_id = null;

    public ?string $blockingReason = null;

    public function mount(Student $student): void
    {
        $this->student = $student;
        $this->academic_year_id = school()->activeYear()?->id;
        $enrollment = $this->student->enrollments()
            ->where('academic_year_id', $this->academic_year_id)
            ->with('grade:id,name')
            ->first();

        if (!$enrollment) {
            $this->blockingReason = 'الطالب غير مقيد في أي صف دراسي لهذه السنة.';
            return;
        }

        $this->grade_id = $enrollment->grade_id;
        $this->grade_name = $enrollment->grade->name;

        $this->academic_term_id = school()->currentAcademicTerm()?->id;
        $this->loadCurrentSection();
    }

    public function updatedAcademicTermId(): void
    {
        $this->loadCurrentSection();
        $this->reset('blockingReason');
    }

    public function loadCurrentSection(): void
    {
        if (!$this->academic_term_id) {
            $this->section_id = null;
            return;
        }
        $currentSectionId = DB::table('section_students')
            ->join('sections', 'section_students.section_id', '=', 'sections.id')
            ->where('section_students.student_id', $this->student->id)
            ->where('sections.academic_year_id', $this->academic_year_id)
            ->where('sections.grade_id', $this->grade_id)
            ->where('sections.academic_term_id', $this->academic_term_id)
            ->value('sections.id');

        $this->section_id = $currentSectionId;
    }

    #[Computed]
    public function terms()
    {
        return AcademicTerm::where('academic_year_id', $this->academic_year_id)
            ->pluck('name', 'id');
    }

    #[Computed]
    public function sections()
    {
        if (!$this->academic_term_id)
            return collect();

        return Section::query()
            ->where('academic_year_id', $this->academic_year_id)
            ->where('grade_id', $this->grade_id)
            ->where('academic_term_id', $this->academic_term_id)
            ->orderBy('name')
            ->select('id', 'name', 'capacity')
            ->get();
    }
    public function checkEligibility(): bool
    {
        // 1. التحقق من وجود سنة وصف وترم
        if (!isset($this->grade_id) || !$this->academic_term_id) {
            return false;
        }

        // إذا لم يكن الطالب مسكناً في شعبة بعد، فالعملية مسموحة دائماً
        if (!$this->section_id) {
            return true;
        }

        // ========================================================
        // القيود الصارمة: إذا كان الطالب مسكناً بالفعل، نتحقق من البيانات المرتبطة
        // ========================================================

        // 2. التحقق من وجود درجات (Marks)
        // نفحص جدول الدرجات المرتبط بهذا الطالب في هذا الترم
        $hasMarks = DB::table('marks')
            ->join('exams', 'marks.exam_id', '=', 'exams.id')
            ->where('marks.student_id', $this->student->id)
            ->where('exams.academic_year_id', $this->academic_year_id)
            ->where('exams.academic_term_id', $this->academic_term_id)
            ->exists();

        if ($hasMarks) {
            $this->blockingReason = 'لا يمكن نقل الطالب لوجود درجات مرصودة له في هذا الفصل الدراسي.';
            return false;
        }

        // 3. التحقق من سجلات الحضور (Attendance)
        // نفحص إذا تم تحضير الطالب في أي يوم دراسي تابع لهذا الترم
        $hasAttendance = DB::table('attendances')
            ->join('attendance_sheets', 'attendances.attendance_sheet_id', '=', 'attendance_sheets.id')
            ->join('school_days', 'attendance_sheets.school_day_id', '=', 'school_days.id')
            ->where('attendances.student_id', $this->student->id)
            ->where('school_days.academic_year_id', $this->academic_year_id)
            ->where('school_days.academic_term_id', $this->academic_term_id)
            ->exists();

        if ($hasAttendance) {
            $this->blockingReason = 'لا يمكن نقل الطالب لوجود سجلات حضور في هذا الفصل الدراسي.';
            return false;
        }

        return true;
    }

    public function save(): void
    {
        // التحقق مرة أخرى قبل الحفظ
        if (!$this->checkEligibility()) {
            $this->dispatch('show-toast', type: 'error', message: $this->blockingReason ?? 'عملية غير مسموحة');
            return;
        }

        $this->validate([
            'academic_term_id' => 'required',
            'section_id' => 'required|exists:sections,id',
        ], [
            'section_id.required' => 'يرجى اختيار الشعبة',
        ]);

        // التحقق من أن الشعبة المختارة تابعة لنفس السياق (أمان إضافي)
        $isValidSection = Section::where('id', $this->section_id)
            ->where('academic_year_id', $this->academic_year_id)
            ->where('grade_id', $this->grade_id)
            ->where('academic_term_id', $this->academic_term_id)
            ->exists();

        if (!$isValidSection) {
            $this->dispatch('show-toast', type: 'error', message: 'الشعبة المختارة غير صحيحة لهذا الصف والفصل.');
            return;
        }

        try {
            DB::transaction(function () {
                // 1. حذف التوزيعات القديمة لهذا الطالب في هذا الترم (إن وجدت)
                // نستخدم استعلام مباشر لضمان حذف الارتباط الخاص بهذا الترم فقط
                $currentSectionIds = Section::where('academic_year_id', $this->academic_year_id)
                    ->where('grade_id', $this->grade_id)
                    ->where('academic_term_id', $this->academic_term_id)
                    ->pluck('id');

                if ($currentSectionIds->isNotEmpty()) {
                    $this->student->sections()->detach($currentSectionIds);
                }

                // 2. إضافة التوزيع الجديد
                $this->student->sections()->attach($this->section_id);
            });

            $this->dispatch('close-modal', name: 'assign-student-modal');
            $this->dispatch('show-toast', type: 'success', message: 'تم توزيع الطالب بنجاح.');
            $this->dispatch('student-assigned'); // لتحديث أي قوائم خارجية

        } catch (\Exception $e) {
            report($e);
            $this->dispatch('show-toast', type: 'error', message: 'حدث خطأ أثناء الحفظ.');
        }
    }

    public function render()
    {
        // فحص الصلاحية عند كل عرض للتأكد من حالة الزر
        $canEdit = $this->checkEligibility();

        return view('livewire.dashboard.users.students.assign-single-student-to-section', [
            'canEdit' => $canEdit
        ]);
    }
}
