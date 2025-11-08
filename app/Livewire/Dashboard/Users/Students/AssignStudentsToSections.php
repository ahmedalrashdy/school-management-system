<?php

namespace App\Livewire\Dashboard\Users\Students;

use App\Models\AcademicYear;
use App\Models\Grade;
use App\Models\Section;
use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class AssignStudentsToSections extends Component
{
    use WithPagination;

    public ?int $academic_year_id = null;

    public ?int $grade_id = null;

    public ?int $academic_term_id = null;

    public array $assignments = [];

    public function mount(?int $academic_year_id = null, ?int $grade_id = null, ?int $academic_term_id = null): void
    {
        $activeYear = school()->activeYear();
        $this->academic_year_id = $academic_year_id ?? $activeYear?->id;
        $this->grade_id = $grade_id;
        $activeTerm = school()->currentAcademicTerm();
        $this->academic_term_id = $academic_term_id ?? $activeTerm?->id;
    }

    public function updatedGradeId(): void
    {
        $this->resetPage();
        $this->assignments = [];
    }

    public function updatedAcademicYearId(): void
    {
        $this->resetPage();
        $this->assignments = [];
    }

    public function updatedAcademicTermId(): void
    {
        $this->resetPage();
        $this->assignments = [];
    }

    public function getAcademicYearsProperty()
    {
        return AcademicYear::whereIn('status', [1, 2])->latest()->get();
    }

    public function getGradesProperty()
    {
        if (! $this->academic_year_id) {
            return collect();
        }

        return Grade::with('stage')->latest()->get();
    }

    public function getSectionsProperty()
    {
        if (! $this->academic_year_id || ! $this->grade_id || ! $this->academic_term_id) {
            return collect();
        }

        return Section::where('academic_year_id', $this->academic_year_id)
            ->where('grade_id', $this->grade_id)
            ->where('academic_term_id', $this->academic_term_id)
            ->withCount('students')
            ->orderBy('name')
            ->get();
    }

    public function getStudentsProperty()
    {
        if (! $this->academic_year_id || ! $this->grade_id || ! $this->academic_term_id) {
            return collect();
        }

        // جلب الطلاب المسجلين في الصف المحدد
        $query = Student::with(['user', 'sections'])
            ->whereHas('enrollments', function ($q) {
                $q->where('academic_year_id', $this->academic_year_id)
                    ->where('grade_id', $this->grade_id);
            });

        // تصفية الطلاب: إما غير مسكنين في شعبة، أو مسكنين ولكن ليس لديهم درجات
        $query->where(function ($q) {
            // الطلاب غير المسكنين في شعبة لهذا الترم
            $q->whereDoesntHave('sections', function ($sectionQuery) {
                $sectionQuery->where('academic_year_id', $this->academic_year_id)
                    ->where('grade_id', $this->grade_id)
                    ->where('academic_term_id', $this->academic_term_id);
            })
                // أو الطلاب المسكنين ولكن ليس لديهم درجات
                ->orWhereHas('sections', function ($sectionQuery) {
                    $sectionQuery->where('academic_year_id', $this->academic_year_id)
                        ->where('grade_id', $this->grade_id)
                        ->where('academic_term_id', $this->academic_term_id);
                })
                ->whereDoesntHave('marks', function ($markQuery) {
                    $markQuery->whereHas('exam', function ($examQuery) {
                        $examQuery->where('academic_year_id', $this->academic_year_id)
                            ->where('academic_term_id', $this->academic_term_id);
                    });
                });
        });

        return $query->orderBy('admission_number')->paginate(20);
    }

    public function save(): void
    {
        if (! $this->academic_year_id || ! $this->grade_id || ! $this->academic_term_id) {
            $this->dispatch('error', message: 'يرجى تحديد السنة الدراسية والصف والفصل الدراسي أولاً.');

            return;
        }

        if (empty($this->assignments)) {
            $this->dispatch('error', message: 'لم يتم تحديد أي تغييرات للحفظ.');

            return;
        }

        // التحقق من صحة البيانات
        $sections = $this->sections->pluck('id')->toArray();
        $validAssignments = [];

        foreach ($this->assignments as $studentId => $sectionId) {
            if ($sectionId === null || $sectionId === '') {
                continue;
            }

            if (! in_array($sectionId, $sections)) {
                continue;
            }

            $validAssignments[$studentId] = $sectionId;
        }

        if (empty($validAssignments)) {
            $this->dispatch('error', message: 'لا توجد تعيينات صحيحة للحفظ.');

            return;
        }

        // حفظ التغييرات
        try {
            DB::transaction(function () use ($validAssignments) {
                foreach ($validAssignments as $studentId => $sectionId) {
                    $student = Student::findOrFail($studentId);

                    // التحقق من وجود درجات
                    if ($student->hasMarksInTerm($this->academic_year_id, $this->academic_term_id)) {
                        continue; // تخطي الطلاب الذين لديهم درجات
                    }

                    // إزالة التوزيعات السابقة للطالب في نفس السياق
                    $student->sections()->wherePivotIn('section_id', function ($query) {
                        $query->select('id')
                            ->from('sections')
                            ->where('academic_year_id', $this->academic_year_id)
                            ->where('grade_id', $this->grade_id)
                            ->where('academic_term_id', $this->academic_term_id);
                    })->detach();

                    // إضافة التوزيع الجديد
                    $student->sections()->syncWithoutDetaching([$sectionId => []]);
                }
            });

            $this->assignments = [];
            $this->dispatch('success', message: 'تم حفظ التوزيع بنجاح.');
            $this->resetPage();
        } catch (\Exception $e) {
            $this->dispatch('error', message: 'حدث خطأ أثناء حفظ التوزيع: '.$e->getMessage());
        }
    }

    public function saveAndGoToNextPage(): void
    {
        $this->save();

        // الانتقال للصفحة التالية إذا كانت موجودة
        $students = $this->students;
        if ($students->hasMorePages()) {
            $this->nextPage();
        }
    }

    public function render()
    {
        $students = collect();
        $sections = collect();

        if ($this->academic_year_id && $this->grade_id && $this->academic_term_id) {
            $students = $this->students;
            $sections = $this->sections;

            // تحميل التوزيعات الحالية للطلاب في الصفحة
            if ($students->count() > 0) {
                $studentIds = $students->pluck('id')->toArray();
                $currentAssignments = DB::table('section_students')
                    ->join('sections', 'section_students.section_id', '=', 'sections.id')
                    ->whereIn('section_students.student_id', $studentIds)
                    ->where('sections.academic_year_id', $this->academic_year_id)
                    ->where('sections.grade_id', $this->grade_id)
                    ->where('sections.academic_term_id', $this->academic_term_id)
                    ->pluck('section_students.section_id', 'section_students.student_id')
                    ->toArray();

                // دمج التوزيعات الحالية مع التغييرات الجديدة
                foreach ($currentAssignments as $studentId => $sectionId) {
                    if (! isset($this->assignments[$studentId])) {
                        $this->assignments[$studentId] = $sectionId;
                    }
                }
            }
        }

        return view('livewire.dashboard.users.students.assign-students-to-sections', [
            'students' => $students,
            'sections' => $sections,
        ]);
    }
}
