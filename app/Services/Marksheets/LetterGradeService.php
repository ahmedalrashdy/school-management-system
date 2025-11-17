<?php

namespace App\Services\Marksheets;

class LetterGradeService
{
    /**
     * حساب التقدير اللفظي بناءً على النسبة المئوية
     * نظام ثابت: ممتاز ≥90، جيد جداً ≥80، جيد ≥70، مقبول ≥60، ضعيف <60
     */
    public function getLetterGrade(float $percentage): string
    {
        if ($percentage >= 90) {
            return 'ممتاز';
        }
        if ($percentage >= 80) {
            return 'جيد جداً';
        }
        if ($percentage >= 70) {
            return 'جيد';
        }
        if ($percentage >= 60) {
            return 'مقبول';
        }
        if ($percentage >= 50) {
            return 'ضعيف';
        }

        return 'راسب';
    }

    /**
     * الحصول على لون الـ badge للتقدير
     */
    public function getGradeColor(string $grade): string
    {
        return match ($grade) {
            'ممتاز' => 'success',
            'جيد جداً' => 'info',
            'جيد' => 'primary',
            'مقبول' => 'warning',
            'ضعيف' => 'warning',
            'راسب' => 'danger',
            default => 'gray',
        };
    }
}
