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
                    $finalExam = $this->createFinalExam($section, $curriculumSubject, $examTypes);

                    if (! $finalExam) {
                        continue;
                    }

                    $examsCreated++;

                    // 2. إنشاء 2-3 امتحانات أخرى (أعمال الفصل)
                    $courseworkExams = $this->createCourseworkExams($section, $curriculumSubject, $examTypes);
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
    protected function createFinalExam(Section $section, CurriculumSubject $curriculumSubject, $examTypes): ?Exam
    {
        // التحقق من وجود امتحان نهائي مسبق
        $existingFinal = Exam::where('section_id', $section->id)
            ->where('curriculum_subject_id', $curriculumSubject->id)
            ->where('is_final', true)
            ->first();

        if ($existingFinal) {
            return $existingFinal;
        }

        // اختيار نوع امتحان نهائي (آخر نوع في القائمة عادة)
        $finalExamType = $examTypes->last() ?? $examTypes->first();

        // اختيار الدرجة القصوى للنهائي (50 أو 70 أو 60)
        $finalMaxMarks = [50, 70, 60][rand(0, 2)];

        // تحديد تاريخ الامتحان (في نهاية الترم)
        $termEndDate = Carbon::parse($section->academicTerm->end_date);
        $examDate = $termEndDate->copy()->subDays(rand(1, 7)); // قبل نهاية الترم بـ 1-7 أيام

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
    protected function createCourseworkExams(Section $section, CurriculumSubject $curriculumSubject, $examTypes): array
    {
        $exams = [];

        // عدد الامتحانات (2-3)
        $examsCount = rand(2, 3);

        // اختيار أنواع امتحانات عشوائية (غير النهائي)
        $courseworkExamTypes = $examTypes->where('id', '!=', $examTypes->last()?->id)->shuffle();

        // توزيع الأوزان (يجب أن يكون مجموعها 100%)
        $weights = $this->distributeWeights($examsCount);

        // تاريخ بداية الترم
        $termStartDate = Carbon::parse($section->academicTerm->start_date);
        $termEndDate = Carbon::parse($section->academicTerm->end_date);
        $termDuration = $termStartDate->diffInDays($termEndDate);

        for ($i = 0; $i < $examsCount; $i++) {
            // التحقق من وجود امتحان مسبق
            $existingExam = Exam::where('section_id', $section->id)
                ->where('curriculum_subject_id', $curriculumSubject->id)
                ->where('is_final', false)
                ->where('exam_type_id', $courseworkExamTypes->get($i % $courseworkExamTypes->count())?->id)
                ->first();

            if ($existingExam) {
                $exams[] = $existingExam;

                continue;
            }

            // اختيار نوع امتحان
            $examType = $courseworkExamTypes->get($i % $courseworkExamTypes->count()) ?? $courseworkExamTypes->first();

            // الدرجة القصوى (من مضاعفات 5)
            $maxMarks = $this->getRandomMultipleOfFive(10, 50); // من 10 إلى 50

            // توزيع تواريخ الامتحانات على طول الترم
            $progress = ($i + 1) / ($examsCount + 1); // توزيع متساوي
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
     * توزيع الأوزان على الامتحانات (مجموعها 100%).
     */
    protected function distributeWeights(int $count): array
    {
        $weights = [];

        if ($count == 2) {
            // توزيع متساوي أو 60/40
            $w1 = rand(40, 60);
            $w2 = 100 - $w1;
            $weights = [$w1, $w2];
        } elseif ($count == 3) {
            // توزيع متساوي تقريباً
            $w1 = rand(25, 40);
            $w2 = rand(25, 40);
            $w3 = 100 - $w1 - $w2;
            $weights = [$w1, $w2, $w3];
        }

        // التأكد من أن المجموع = 100
        $total = array_sum($weights);
        if ($total != 100) {
            $diff = 100 - $total;
            $weights[0] += $diff;
        }

        return $weights;
    }

    /**
     * الحصول على قيمة عشوائية من مضاعفات 5.
     */
    protected function getRandomMultipleOfFive(int $min, int $max): int
    {
        $min = (int) ceil($min / 5) * 5;
        $max = (int) floor($max / 5) * 5;

        $random = rand($min / 5, $max / 5) * 5;

        return $random;
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

        // إنشاء GradingRuleItems للامتحانات الأخرى
        $weights = $this->distributeWeights(count($courseworkExams));

        foreach ($courseworkExams as $index => $exam) {
            GradingRuleItem::create([
                'grading_rule_id' => $gradingRule->id,
                'exam_id' => $exam->id,
                'weight' => $weights[$index] ?? (100 / count($courseworkExams)),
            ]);
        }

        return $gradingRule;
    }
}
