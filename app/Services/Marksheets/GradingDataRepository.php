<?php

namespace App\Services\Marksheets;

use App\Models\Section;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class GradingDataRepository
{
    /**
     * جلب هيكل المواد والقواعد والامتحانات للشعبة
     * هذا الاستعلام يستخدم في كل من كشوف الطلاب وكشوف الشعب
     */
    public function getSubjectsWithRules(Section $section): array
    {
        // 1. استعلام المواد والقواعد الأساسية
        $curriculumSubjectsData = DB::table('curriculum_subject')
            ->select([
                'curriculum_subject.id as curriculum_subject_id',
                'curriculum_subject.curriculum_id',
                'curriculum_subject.subject_id',
                'subjects.name as subject_name',
                'subjects.id as subject_id',
                'grading_rules.id as grading_rule_id',
                'grading_rules.coursework_max_marks',
                'grading_rules.final_exam_max_marks',
                'grading_rules.passed_mark',
                'grading_rules.total_marks',
                'grading_rules.final_exam_id',
                'grading_rules.is_published',
            ])
            ->join('curriculums', 'curriculums.id', '=', 'curriculum_subject.curriculum_id')
            ->join('subjects', 'subjects.id', '=', 'curriculum_subject.subject_id')
            ->leftJoin('grading_rules', function ($join) use ($section) {
                $join->on('grading_rules.curriculum_subject_id', '=', 'curriculum_subject.id')
                    ->where('grading_rules.section_id', '=', $section->id);
            })
            ->where('curriculums.academic_year_id', $section->academic_year_id)
            ->where('curriculums.grade_id', $section->grade_id)
            ->where('curriculums.academic_term_id', $section->academic_term_id)
            ->get();

        if ($curriculumSubjectsData->isEmpty()) {
            return [];
        }

        // 2. تحضير المعرفات لجلب التفاصيل
        $gradingRuleIds = $curriculumSubjectsData->pluck('grading_rule_id')->unique()->filter()->toArray();
        $finalExamIds = $curriculumSubjectsData->pluck('final_exam_id')->unique()->filter()->toArray();

        // 3. جلب بنود القواعد (Coursework Items)
        $ruleItems = collect();
        if (! empty($gradingRuleIds)) {
            $ruleItems = DB::table('grading_rule_items')
                ->select([
                    'grading_rule_items.id',
                    'grading_rule_items.grading_rule_id',
                    'grading_rule_items.exam_id',
                    'grading_rule_items.weight',
                    'exams.max_marks as exam_max_marks',
                    'exams.exam_type_id',
                    'exams.exam_date',
                ])
                ->join('exams', 'exams.id', '=', 'grading_rule_items.exam_id')
                ->whereIn('grading_rule_items.grading_rule_id', $gradingRuleIds)
                ->get()
                ->groupBy('grading_rule_id');
        }

        // 4. جلب الامتحانات النهائية
        $finalExams = collect();
        if (! empty($finalExamIds)) {
            $finalExams = DB::table('exams')
                ->select('id', 'max_marks', 'exam_type_id', 'exam_date')
                ->whereIn('id', $finalExamIds)
                ->get()
                ->keyBy('id');
        }

        // 5. جلب أسماء أنواع الامتحانات (للعرض)
        $allExamTypeIds = $ruleItems->flatten()->pluck('exam_type_id')
            ->merge($finalExams->pluck('exam_type_id'))
            ->unique()->filter()->toArray();

        $examTypesMap = [];
        if (! empty($allExamTypeIds)) {
            $examTypesMap = DB::table('exam_types')->whereIn('id', $allExamTypeIds)->pluck('name', 'id')->toArray();
        }

        // 6. تجميع الهيكل النهائي
        $subjects = [];
        foreach ($curriculumSubjectsData as $data) {
            // تجهيز بنود أعمال الفصل
            $items = $ruleItems->get($data->grading_rule_id, collect())->map(function ($item) use ($examTypesMap) {
                return (object) [
                    'id' => $item->id,
                    'exam_id' => $item->exam_id,
                    'weight' => (float) $item->weight,
                    'exam' => (object) [
                        'id' => $item->exam_id,
                        'max_marks' => (int) $item->exam_max_marks,
                        'exam_type_id' => $item->exam_type_id,
                        'exam_date' => $item->exam_date,
                        'exam_type_name' => $examTypesMap[$item->exam_type_id] ?? 'N/A',
                    ],
                ];
            });

            // تجهيز الامتحان النهائي
            $finalExam = ($data->final_exam_id && isset($finalExams[$data->final_exam_id]))
                ? (object) [
                    'id' => $data->final_exam_id,
                    'max_marks' => (int) $finalExams[$data->final_exam_id]->max_marks,
                    'exam_type_id' => $finalExams[$data->final_exam_id]->exam_type_id,
                    'exam_date' => $finalExams[$data->final_exam_id]->exam_date,
                    'exam_type_name' => $examTypesMap[$finalExams[$data->final_exam_id]->exam_type_id] ?? 'Final',
                ] : null;

            // تجهيز كائن القاعدة
            $gradingRule = $data->grading_rule_id ? (object) [
                'id' => $data->grading_rule_id,
                'coursework_max_marks' => (int) $data->coursework_max_marks,
                'final_exam_max_marks' => (int) $data->final_exam_max_marks,
                'passed_mark' => (float) $data->passed_mark,
                'total_marks' => (int) $data->total_marks,
                'final_exam_id' => $data->final_exam_id,
                'is_published' => (bool) $data->is_published,
                'items' => $items,
                'finalExam' => $finalExam,
            ] : null;

            $subjects[] = [
                'curriculum_subject' => (object) ['id' => $data->curriculum_subject_id],
                'subject' => (object) ['id' => $data->subject_id, 'name' => $data->subject_name],
                'grading_rule' => $gradingRule,
            ];
        }

        return $subjects;
    }

    /**
     * جلب الدرجات لمجموعة طلاب ومجموعة مواد دفعة واحدة
     *
     * @return array [student_id => [exam_id => mark]]
     */
    public function getMarks(array $studentIds, array $subjectsStructure): array
    {
        if (empty($studentIds) || empty($subjectsStructure)) {
            return [];
        }

        // استخراج جميع معرفات الامتحانات (أعمال فصل + نهائي)
        $examIds = [];
        foreach ($subjectsStructure as $subject) {
            $rule = $subject['grading_rule'];
            if (! $rule) {
                continue;
            }

            foreach ($rule->items as $item) {
                $examIds[] = $item->exam_id;
            }
            if ($rule->final_exam_id) {
                $examIds[] = $rule->final_exam_id;
            }
        }
        $examIds = array_unique($examIds);

        if (empty($examIds)) {
            return [];
        }

        $marks = DB::table('marks')
            ->select('student_id', 'exam_id', 'marks_obtained')
            ->whereIn('student_id', $studentIds)
            ->whereIn('exam_id', $examIds)
            ->whereNotNull('marks_obtained')
            ->get();

        // تحويل النتيجة إلى Map للبحث السريع
        $marksMap = [];
        foreach ($marks as $mark) {
            if (! isset($marksMap[$mark->student_id])) {
                $marksMap[$mark->student_id] = [];
            }
            $marksMap[$mark->student_id][$mark->exam_id] = (float) $mark->marks_obtained;
        }

        return $marksMap;
    }

    /**
     * جلب جميع الامتحانات "الخام" الموجودة في الشعبة (سواء كانت مربوطة بقواعد أم لا)
     * يستخدم هذا في خدمة التدقيق (Audit)
     */
    public function getAllExamsInSection(int $sectionId): Collection
    {
        return DB::table('exams')
            ->select(['id', 'curriculum_subject_id', 'exam_type_id', 'exam_date', 'max_marks'])
            ->where('section_id', $sectionId)
            ->get();
    }

    /**
     * جلب عدد الطلاب في الشعب
     *
     * @return array [section_id => count]
     */
    public function getStudentsCounts(array $sectionIds): array
    {
        return DB::table('section_students')
            ->select('section_id', DB::raw('count(*) as count'))
            ->whereIn('section_id', $sectionIds)
            ->groupBy('section_id')
            ->pluck('count', 'section_id')
            ->toArray();
    }

    /**
     * جلب عدد الدرجات المرصودة لمجموعة من الامتحانات
     *
     * @return array [exam_id => count]
     */
    public function getMarksCounts(array $examIds): array
    {
        if (empty($examIds)) {
            return [];
        }

        return DB::table('marks')
            ->select('exam_id', DB::raw('count(*) as count'))
            ->whereIn('exam_id', array_unique($examIds))
            ->whereNotNull('marks_obtained')
            ->groupBy('exam_id')
            ->pluck('count', 'exam_id')
            ->toArray();
    }

    /**
     * جلب الطلاب الذين ليس لديهم درجات في امتحان معين
     */
    public function getStudentsMissingMark(int $sectionId, int $examId): Collection
    {
        return DB::table('section_students')
            ->join('students', 'students.id', '=', 'section_students.student_id')
            ->join('users', 'users.id', '=', 'students.user_id')
            ->where('section_students.section_id', $sectionId)
            ->whereNotExists(function ($query) use ($examId) {
                $query->select(DB::raw(1))
                    ->from('marks')
                    ->whereColumn('marks.student_id', 'section_students.student_id')
                    ->where('marks.exam_id', $examId)
                    ->whereNotNull('marks_obtained');
            })
            ->select('students.id', 'users.first_name', 'users.last_name', 'students.admission_number')
            ->get();
    }

    /**
     * جلب بيانات الطلاب ودرجاتهم لامتحان واحد محدد (لخدمة ExamMarksheet)
     */
    public function getExamStudentsWithMarks(int $examId, int $sectionId, array $filters = []): Collection
    {
        $query = \App\Models\Student::query()
            ->select('students.*')
            ->join('section_students', 'section_students.student_id', 'students.id')
            ->where('section_students.section_id', $sectionId)
            ->join('users', 'users.id', 'students.user_id')
            ->leftJoin('marks', function ($join) use ($examId) {
                $join->on('students.id', '=', 'marks.student_id')
                    ->where('marks.exam_id', '=', $examId);
            })
            ->addSelect(['marks.marks_obtained', 'marks.notes', 'users.first_name', 'users.last_name']);

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('users.first_name', 'like', "%{$search}%")
                    ->orWhere('users.last_name', 'like', "%{$search}%");
            });
        }

        return $query->get();
    }

    /**
     * دالة مخصصة لخدمة Readiness لجلب البيانات الضخمة دفعة واحدة
     * تقوم بتجميع القواعد والبنود لكل الشعب المحددة
     */
    public function getBulkGradingRulesData(array $sectionIds, Collection $curriculumSubjectsMap): array
    {
        // 1. تجميع كل معرفات المواد
        $allSubjectIds = $curriculumSubjectsMap->flatten()->unique()->toArray();
        if (empty($allSubjectIds)) {
            return [[], []];
        }

        // 2. جلب القواعد
        $rules = DB::table('grading_rules')
            ->select('id', 'section_id', 'curriculum_subject_id', 'final_exam_id')
            ->whereIn('section_id', $sectionIds)
            ->whereIn('curriculum_subject_id', $allSubjectIds)
            ->get();

        // 3. جلب البنود
        $ruleIds = $rules->pluck('id')->toArray();
        $items = [];
        if (! empty($ruleIds)) {
            $items = DB::table('grading_rule_items')
                ->select('id', 'grading_rule_id', 'exam_id')
                ->whereIn('grading_rule_id', $ruleIds)
                ->get()
                ->groupBy('grading_rule_id')
                ->toArray();
        }

        // توزيع القواعد حسب الشعبة
        $rulesBySection = [];
        foreach ($sectionIds as $sid) {
            $validSubjects = $curriculumSubjectsMap[$sid] ?? collect();
            $rulesBySection[$sid] = $rules->where('section_id', $sid)
                ->filter(fn ($r) => $validSubjects->contains($r->curriculum_subject_id));
        }

        return [$rulesBySection, $items];
    }
}
