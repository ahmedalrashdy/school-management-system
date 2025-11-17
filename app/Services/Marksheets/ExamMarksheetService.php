<?php

namespace App\Services\Marksheets;

use App\Models\Exam;
use App\Models\GradingRuleItem;

class ExamMarksheetService
{
    public function __construct(protected GradingDataRepository $gradingRepo) {}

    public function getExamMarksSheetData(Exam $exam, array $filters = []): array
    {
        // 1. التحقق من وجود قاعدة واحتساب الوزن
        $ruleItem = GradingRuleItem::where('exam_id', $exam->id)->first();
        $hasRule = $ruleItem !== null;
        $weight = $hasRule ? (float) $ruleItem->weight : 0;

        // 2. جلب الطلاب مع درجاتهم من المستودع
        $students = $this->gradingRepo->getExamStudentsWithMarks($exam->id, $exam->section_id, $filters);

        // 3. تنسيق البيانات وحساب الوزن
        $rows = $students->map(function ($student) use ($exam, $hasRule, $weight) {
            $rawMark = $student->marks_obtained !== null ? (float) $student->marks_obtained : null;
            $isAbsent = $rawMark === null;

            $weightedScore = 0;
            // معادلة الوزن البسيطة: (خام / عظمى) * الوزن
            if (! $isAbsent && $exam->max_marks > 0 && $hasRule) {
                $weightedScore = ($rawMark / $exam->max_marks) * $weight;
            }

            return (object) [
                'id' => $student->id,
                'name' => $student->first_name.' '.$student->last_name,
                'admission_number' => $student->admission_number,
                'is_absent' => $isAbsent,
                'raw_mark' => $rawMark,
                'exam_max' => $exam->max_marks,
                'notes' => $student->notes,
                'weighted_score' => round($weightedScore, 2),
                'sort_value' => $isAbsent ? -1 : $rawMark, // للفرز
            ];
        });

        // 4. الفرز (Locale Sort)
        $sort = $filters['sort'] ?? 'alpha';
        $rows = match ($sort) {
            'desc' => $rows->sortByDesc('sort_value'),
            'asc' => $rows->sortBy(fn ($r) => $r->is_absent ? 999999 : $r->raw_mark),
            default => $rows->sortBy('name'),
        };

        return [
            'exam' => $exam,
            'students' => $rows->values(),
            'has_rule' => $hasRule,
            'weight_info' => $hasRule ? ['weight' => $weight] : null,
        ];
    }
}
