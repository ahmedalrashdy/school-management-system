<?php

namespace App\Services\Marksheets;

use App\Models\Section;
use Illuminate\Support\Facades\DB;

class SectionAuditService
{
    public function __construct(protected GradingDataRepository $gradingRepo) {}

    public function getSectionAuditData(Section $section): array
    {
        // 1. جلب الهيكل الصحيح (Rules Structure)
        $structure = $this->gradingRepo->getSubjectsWithRules($section);

        // 2. جلب جميع الامتحانات الخام (للتحقق من الشوارد)
        $allRawExams = $this->gradingRepo->getAllExamsInSection($section->id);

        // 3. عدد الطلاب
        $studentsCount = DB::table('section_students')->where('section_id', $section->id)->count();

        // 4. جلب جميع أنواع الامتحانات (للعرض)
        $examTypes = DB::table('exam_types')->pluck('name', 'id')->toArray();

        // 5. تحضير عداد الدرجات لجميع الامتحانات الموجودة
        $marksCounts = $this->gradingRepo->getMarksCounts($allRawExams->pluck('id')->toArray());

        $subjectsAudit = [];
        // تجميع الامتحانات المرتبطة بمواد لتسهيل اكتشاف الامتحانات الشاردة
        $linkedExamIds = [];

        foreach ($structure as $item) {
            $rule = $item['grading_rule'];
            $subjectExamsData = [];

            // إذا وجدت قاعدة، نحلل امتحاناتها
            if ($rule) {
                // تجميع معرفات الامتحانات التابعة لهذه القاعدة
                $ruleExamIds = collect($rule->items)->pluck('exam_id')->toArray();
                if ($rule->final_exam_id) {
                    $ruleExamIds[] = $rule->final_exam_id;
                }

                foreach ($ruleExamIds as $examId) {
                    $linkedExamIds[] = $examId;

                    // البحث عن كائن الامتحان في البيانات
                    $examObj = null;
                    if ($rule->final_exam_id == $examId) {
                        $examObj = $rule->finalExam;
                    } else {
                        $examObj = collect($rule->items)->firstWhere('exam_id', $examId)->exam ?? null;
                    }

                    if ($examObj) {
                        $subjectExamsData[] = $this->analyzeExam($examObj, $marksCounts, $studentsCount, $section->id, $examTypes);
                    }
                }
            }

            // فحص الامتحانات الشاردة (Orphaned) التابعة لنفس المادة لكن غير مربوطة بالقاعدة
            $orphanedExamsData = [];
            $subjectRawExams = $allRawExams->where('curriculum_subject_id', $item['curriculum_subject']->id)
                ->whereNotIn('id', $linkedExamIds); // التي لم تربط بعد

            foreach ($subjectRawExams as $rawExam) {
                // إضافة اسم النوع للعرض
                $rawExam->exam_type_name = $examTypes[$rawExam->exam_type_id] ?? '--';
                $orphanedExamsData[] = [
                    'exam' => $rawExam,
                    'has_marks' => ($marksCounts[$rawExam->id] ?? 0) > 0,
                ];
            }

            $subjectsAudit[] = [
                'subject' => $item['subject'],
                'grading_rule' => $rule,
                'linked_exams' => $subjectExamsData,
                'orphaned_exams' => $orphanedExamsData,
                'status' => $this->determineSubjectStatus($rule, $subjectExamsData, $orphanedExamsData, $studentsCount),
                'students_count' => $studentsCount,
            ];
        }

        return [
            'section' => $section,
            'subjects' => $subjectsAudit,
        ];
    }

    protected function analyzeExam($exam, array $marksCounts, int $studentsCount, int $sectionId, array $examTypes): array
    {
        $count = $marksCounts[$exam->id] ?? 0;
        $isComplete = $studentsCount > 0 && $count >= $studentsCount;

        return [
            'exam' => $exam,
            'marks_count' => $count,
            'students_count' => $studentsCount,
            'completion_percentage' => $studentsCount > 0 ? round(($count / $studentsCount) * 100, 1) : 0,
            'is_complete' => $isComplete,
            'students_without_marks' => ! $isComplete ? $this->gradingRepo->getStudentsMissingMark($sectionId, $exam->id) : [],
        ];
    }

    protected function determineSubjectStatus($rule, $linked, $orphaned, $studentsCount): string
    {
        if (count($orphaned) > 0) {
            return 'chaotic';
        } // يوجد امتحانات شاردة
        if (! $rule && count($linked) === 0) {
            return 'no_rule';
        } // لا توجد قاعدة ولا امتحانات
        if (! $rule && count($linked) > 0) {
            return 'chaotic';
        } // حالة نادرة (امتحانات بدون قاعدة)

        foreach ($linked as $l) {
            if (! $l['is_complete']) {
                return 'incomplete';
            }
        }

        return ($studentsCount > 0 && count($linked) > 0) ? 'complete' : 'no_data';
    }
}
