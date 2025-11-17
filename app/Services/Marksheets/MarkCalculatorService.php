<?php

namespace App\Services\Marksheets;

class MarkCalculatorService
{
    public function __construct(protected LetterGradeService $letterGradeService) {}

    /**
     * حساب درجات مادة واحدة لطالب
     * يعيد هيكل بيانات شامل يستخدم في كشف الطالب وكشف الشعبة
     */
    public function calculateSubjectResult(?object $rule, array $studentMarks): array
    {
        // تهيئة القيم الافتراضية
        $result = [
            'coursework_total' => 0,
            'coursework_max' => 0,
            'coursework_items' => [],
            'final_mark' => null,
            'final_raw_mark' => null,
            'final_max' => 0,
            'final_exam_max' => 0,
            'total' => null,
            'max_total' => 0,
            'percentage' => null,
            'grade' => null,
            'is_passed' => false,
            'passed_mark' => 0,
        ];

        if (! $rule) {
            return $result;
        }

        $result['coursework_max'] = $rule->coursework_max_marks;
        $result['final_max'] = $rule->final_exam_max_marks;
        $result['max_total'] = $rule->total_marks;
        $result['passed_mark'] = $rule->passed_mark;

        // 1. حساب أعمال الفصل
        foreach ($rule->items as $item) {
            $exam = $item->exam;
            $rawMark = $studentMarks[$exam->id] ?? null;
            $weightedScore = 0;

            if ($rawMark !== null && $exam->max_marks > 0) {
                // المعادلة: (خام / عظمى) * (وزن% / 100) * سقف أعمال السنة
                $weightedScore = ($rawMark / $exam->max_marks) * ($item->weight / 100) * $rule->coursework_max_marks;
                $result['coursework_total'] += $weightedScore;
            }

            // تخزين التفاصيل (مهمة لكشف الطالب المفرد)
            $result['coursework_items'][] = [
                'exam_id' => $exam->id,
                'exam_name' => $exam->exam_type_name,
                'exam_date' => $exam->exam_date,
                'raw_mark' => $rawMark !== null ? round($rawMark, 2) : null,
                'max_mark' => $exam->max_marks,
                'weight' => $item->weight,
                'weighted_score' => round($weightedScore, 2),
            ];
        }

        // 2. حساب النهائي مع المعايرة
        if ($rule->final_exam_id && isset($studentMarks[$rule->final_exam_id])) {
            $rawFinal = (float) $studentMarks[$rule->final_exam_id];
            $realFinalExam = $rule->finalExam ?? null;

            $result['final_raw_mark'] = round($rawFinal, 2);
            $result['final_exam_max'] = $realFinalExam->max_marks ?? 0;

            if ($realFinalExam && $realFinalExam->max_marks > 0) {
                // المعادلة: (خام / عظمى الامتحان) * سقف النهائي
                $normalizedFinal = ($rawFinal / $realFinalExam->max_marks) * $rule->final_exam_max_marks;
                $result['final_mark'] = round($normalizedFinal, 2);
            } else {
                $result['final_mark'] = round($rawFinal, 2);
            }
        }

        // 3. المجموع الكلي
        if ($result['final_mark'] !== null) {
            $result['total'] = round($result['coursework_total'] + $result['final_mark'], 2);
        }

        // تقريب مجموع أعمال السنة النهائي للعرض
        $result['coursework_total'] = round($result['coursework_total'], 2);

        // 4. التقدير
        if ($result['total'] !== null && $rule->total_marks > 0) {
            $result['percentage'] = round(($result['total'] / $rule->total_marks) * 100, 2);
            $result['grade'] = $this->letterGradeService->getLetterGrade($result['percentage']);
            $result['is_passed'] = $result['total'] >= $rule->passed_mark;
        }

        return $result;
    }
}
