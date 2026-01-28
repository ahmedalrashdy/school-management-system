<?php

namespace Database\Seeders;

use App\Models\CurriculumSubject;
use App\Models\Exam;
use App\Models\ExamType;
use App\Models\GradingRule;
use App\Models\GradingRuleItem;
use App\Models\Section;
use Carbon\Carbon;
use DB;
use Illuminate\Database\Seeder;

class ExamsAndGradingRulesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            // جلب جميع الشعب الدراسية
            $sections = Section::with(['academicYear', 'academicTerm', 'grade'])
                ->get();

            if ($sections->isEmpty()) {
                $this->command->error('لا توجد شعب دراسية. يرجى تشغيل SectionsAndCurriculumsSeeder أولاً.');

                return;
            }

            $this->command->info("بدء إنشاء الامتحانات وقواعد الاحتساب لـ {$sections->count()} شعبة...");

            // جلب أنواع الامتحانات
            $examTypes = ExamType::all();
            if ($examTypes->isEmpty()) {
                $this->command->error('لا توجد أنواع امتحانات. يرجى تشغيل SchoolBasicSeeder أولاً.');

                return;
            }

            $finalExamType = $examTypes->firstWhere('name', 'اختبار نهاية الفصل') ?? $examTypes->last();
            $courseworkExamTypes = $examTypes
                ->whereIn('name', ['اختبار شهري', 'واجبات منزلية', 'مشاركة صفية'])
                ->values();
            if ($courseworkExamTypes->isEmpty()) {
                $courseworkExamTypes = $examTypes->filter(fn ($type) => $type->id !== $finalExamType->id)->values();
            }

            $examsCreated = 0;
            $gradingRulesCreated = 0;
            $sectionsProcessed = 0;

            foreach ($sections as $section) {
                // جلب المواد في المنهج الدراسي للشعبة
                $curriculumSubjects = CurriculumSubject::whereHas('curriculum', function ($query) use ($section) {
                    $query->where('academic_year_id', $section->academic_year_id)
                        ->where('grade_id', $section->grade_id)
                        ->where('academic_term_id', $section->academic_term_id);
                })
                    ->with('subject')
                    ->get();

                if ($curriculumSubjects->isEmpty()) {
                    continue;
                }

                foreach ($curriculumSubjects as $curriculumSubject) {
                    // 1. إنشاء امتحان نهائي
                    $finalExam = $this->createFinalExam($section, $curriculumSubject, $finalExamType);

                    if (! $finalExam) {
                        continue;
                    }

                    $examsCreated++;

                    // 2. إنشاء 2-3 امتحانات أخرى (أعمال الفصل)
                    $courseworkExams = $this->createCourseworkExams($section, $curriculumSubject, $courseworkExamTypes);
                    $examsCreated += count($courseworkExams);

                    // 3. إنشاء قاعدة احتساب
                    $gradingRule = $this->createGradingRule($section, $curriculumSubject, $finalExam, $courseworkExams);
                    if ($gradingRule) {
                        $gradingRulesCreated++;
                    }
                }

                $sectionsProcessed++;

                if ($sectionsProcessed % 50 == 0) {
                    $this->command->info("تم معالجة {$sectionsProcessed} شعبة...");
                }
            }

            $this->command->info("تم إنشاء {$examsCreated} امتحان و {$gradingRulesCreated} قاعدة احتساب.");
            $this->command->info("تم معالجة {$sectionsProcessed} شعبة.");
        });
    }

    /**
     * إنشاء امتحان نهائي.
     */
    protected function createFinalExam(Section $section, CurriculumSubject $curriculumSubject, ExamType $finalExamType): ?Exam
    {
        // التحقق من وجود امتحان نهائي مسبق
        $existingFinal = Exam::where('section_id', $section->id)
            ->where('curriculum_subject_id', $curriculumSubject->id)
            ->where('is_final', true)
            ->first();

        if ($existingFinal) {
            return $existingFinal;
        }

        // الدرجة القصوى للنهائي (ثابتة لثبات البيانات)
        $finalMaxMarks = 30;

        // تحديد تاريخ الامتحان (في نهاية الترم)
        $termEndDate = Carbon::parse($section->academicTerm->end_date);
        $examDate = $termEndDate->copy()->subDays(3); // قبل نهاية الترم بثلاثة أيام

        $exam = Exam::create([
            'academic_year_id' => $section->academic_year_id,
            'academic_term_id' => $section->academic_term_id,
            'exam_type_id' => $finalExamType->id,
            'curriculum_subject_id' => $curriculumSubject->id,
            'section_id' => $section->id,
            'exam_date' => $examDate,
            'max_marks' => $finalMaxMarks,
            'is_final' => true,
        ]);

        return $exam;
    }

    /**
     * إنشاء امتحانات أعمال الفصل.
     */
    protected function createCourseworkExams(Section $section, CurriculumSubject $curriculumSubject, $courseworkExamTypes): array
    {
        $exams = [];

        // عدد الامتحانات ثابت (2)
        $examsCount = min(2, $courseworkExamTypes->count());

        // تاريخ بداية الترم
        $termStartDate = Carbon::parse($section->academicTerm->start_date);
        $termEndDate = Carbon::parse($section->academicTerm->end_date);
        $termDuration = $termStartDate->diffInDays($termEndDate);

        for ($i = 0; $i < $examsCount; $i++) {
            // التحقق من وجود امتحان مسبق
            $examType = $courseworkExamTypes->get($i);
            if (! $examType) {
                break;
            }

            $existingExam = Exam::where('section_id', $section->id)
                ->where('curriculum_subject_id', $curriculumSubject->id)
                ->where('is_final', false)
                ->where('exam_type_id', $examType->id)
                ->first();

            if ($existingExam) {
                $exams[] = $existingExam;

                continue;
            }

            // الدرجة القصوى ثابتة لكل امتحان أعمال فصل
            $maxMarks = 10;

            // توزيع تواريخ الامتحانات على طول الترم
            $progress = ($i + 1) / ($examsCount + 1);
            $examDate = $termStartDate->copy()->addDays((int) ($termDuration * $progress));

            $exam = Exam::create([
                'academic_year_id' => $section->academic_year_id,
                'academic_term_id' => $section->academic_term_id,
                'exam_type_id' => $examType->id,
                'curriculum_subject_id' => $curriculumSubject->id,
                'section_id' => $section->id,
                'exam_date' => $examDate,
                'max_marks' => $maxMarks,
                'is_final' => false,
            ]);

            $exams[] = $exam;
        }

        return $exams;
    }

    /**
     * إنشاء قاعدة احتساب.
     */
    protected function createGradingRule(
        Section $section,
        CurriculumSubject $curriculumSubject,
        Exam $finalExam,
        array $courseworkExams
    ): ?GradingRule {
        // التحقق من وجود قاعدة مسبقة
        $existingRule = GradingRule::where('section_id', $section->id)
            ->where('curriculum_subject_id', $curriculumSubject->id)
            ->first();

        if ($existingRule) {
            return $existingRule;
        }

        // إنشاء قاعدة احتساب
        $gradingRule = GradingRule::create([
            'section_id' => $section->id,
            'curriculum_subject_id' => $curriculumSubject->id,
            'coursework_max_marks' => 20, // أعمال الفصل
            'final_exam_max_marks' => 30, // النهائي
            'total_marks' => 50, // المجموع الكلي
            'passed_mark' => 25, // درجة النجاح (نصف المجموع)
            'final_exam_id' => $finalExam->id,
            'is_published' => false,
        ]);

        // إنشاء GradingRuleItems للامتحانات الأخرى (أوزان ثابتة)
        $count = count($courseworkExams);
        if ($count > 0) {
            $weight = round(100 / $count, 2);
            foreach ($courseworkExams as $exam) {
                GradingRuleItem::create([
                    'grading_rule_id' => $gradingRule->id,
                    'exam_id' => $exam->id,
                    'weight' => $weight,
                ]);
            }
        }

        return $gradingRule;
    }
}
