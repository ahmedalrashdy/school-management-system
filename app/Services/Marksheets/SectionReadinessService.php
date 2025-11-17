<?php

namespace App\Services\Marksheets;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SectionReadinessService
{
    public function __construct(protected GradingDataRepository $gradingRepo) {}

    public function getBulkStatistics(Collection $sections): array
    {
        if ($sections->isEmpty()) {
            return [];
        }

        $sectionIds = $sections->pluck('id')->toArray();

        // 1. جلب مواد المنهج (منطق التوزيع حسب الصف الدراسي يبقى هنا لأنه خاص بهذه الخدمة)
        $curriculumSubjectsMap = $this->getBulkCurriculumSubjects($sections);

        // 2. جلب القواعد والبنود من المستودع
        [$gradingRulesMap, $ruleItemsMap] = $this->gradingRepo->getBulkGradingRulesData($sectionIds, collect($curriculumSubjectsMap));

        // 3. جلب أعداد الطلاب
        $studentsCountMap = $this->gradingRepo->getStudentsCounts($sectionIds);

        // 4. جلب أعداد الدرجات (نجمع معرفات الامتحانات أولاً)
        $allExamIds = $this->collectExamIds($gradingRulesMap, $ruleItemsMap);
        $marksCountMap = $this->gradingRepo->getMarksCounts($allExamIds);

        // 5. الحساب النهائي
        $results = [];
        foreach ($sections as $section) {
            $sid = $section->id;
            $curriculumSubjects = $curriculumSubjectsMap[$sid] ?? collect();
            $gradingRules = $gradingRulesMap[$sid] ?? collect();
            $studentsCount = $studentsCountMap[$sid] ?? 0;

            $completedSubjects = $this->calculateCompletion(
                $gradingRules,
                $ruleItemsMap,
                $marksCountMap,
                $studentsCount
            );

            $results[$sid] = [
                'total_subjects' => $curriculumSubjects->count(),
                'subjects_with_rules' => $gradingRules->count(),
                'completed_subjects' => $completedSubjects,
                'is_ready' => $completedSubjects > 0 && $completedSubjects >= $curriculumSubjects->count(),
            ];
        }

        return $results;
    }

    protected function calculateCompletion($gradingRules, $ruleItemsMap, $marksCountMap, int $studentsCount): int
    {
        if ($studentsCount === 0 || $gradingRules->isEmpty()) {
            return 0;
        }

        $completedCount = 0;
        foreach ($gradingRules as $rule) {
            // فحص بنود أعمال الفصل
            $items = $ruleItemsMap[$rule->id] ?? [];
            foreach ($items as $item) {
                if (($marksCountMap[$item->exam_id] ?? 0) < $studentsCount) {
                    continue 2;
                }
            }
            // فحص النهائي
            if ($rule->final_exam_id) {
                if (($marksCountMap[$rule->final_exam_id] ?? 0) < $studentsCount) {
                    continue;
                }
            }
            $completedCount++;
        }

        return $completedCount;
    }

    protected function collectExamIds(array $gradingRulesMap, array $ruleItemsMap): array
    {
        $ids = [];
        foreach ($gradingRulesMap as $rules) {
            foreach ($rules as $rule) {
                if ($rule->final_exam_id) {
                    $ids[] = $rule->final_exam_id;
                }
                $items = $ruleItemsMap[$rule->id] ?? [];
                foreach ($items as $item) {
                    $ids[] = $item->exam_id;
                }
            }
        }

        return array_unique($ids);
    }

    /**
     * منطق خاص بهذه الخدمة لتوزيع المواد حسب الصفوف
     */
    protected function getBulkCurriculumSubjects(Collection $sections): array
    {
        $academicYearId = $sections->first()->academic_year_id;
        $gradeIds = $sections->pluck('grade_id')->unique()->toArray();

        $subjectsByGrade = DB::table('curriculum_subject')
            ->join('curriculums', 'curriculums.id', '=', 'curriculum_subject.curriculum_id')
            ->where('curriculums.academic_year_id', $academicYearId)
            ->where('curriculums.academic_term_id', $sections->first()->academic_term_id)
            ->whereIn('curriculums.grade_id', $gradeIds)
            ->select('curriculums.grade_id', 'curriculum_subject.id')
            ->get()
            ->groupBy('grade_id');

        $results = [];
        foreach ($sections as $section) {
            $results[$section->id] = $subjectsByGrade->get($section->grade_id)?->pluck('id') ?? collect();
        }

        return $results;
    }
}
