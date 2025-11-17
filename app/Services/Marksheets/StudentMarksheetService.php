<?php

namespace App\Services\Marksheets;

use App\Models\Section;
use App\Models\Student;

class StudentMarksheetService
{
    public function __construct(
        protected GradingDataRepository $gradingRepo,
        protected MarkCalculatorService $calculator
    ) {}

    public function getStudentDetailedMarks(Student $student, Section $section): array
    {
        // 1. جلب الهيكل الموحد
        $subjectsStructure = $this->gradingRepo->getSubjectsWithRules($section);

        if (empty($subjectsStructure)) {
            return ['student' => $student, 'section' => $section, 'subjects' => []];
        }

        // 2. جلب درجات الطالب
        $marksData = $this->gradingRepo->getMarks([$student->id], $subjectsStructure);
        $studentMarks = $marksData[$student->id] ?? [];

        // 3. الحساب باستخدام الخدمة الموحدة
        $detailedSubjects = [];
        foreach ($subjectsStructure as $subjectData) {
            $gradingRule = $subjectData['grading_rule'];

            // حساب النتائج
            $calculatedResult = $this->calculator->calculateSubjectResult($gradingRule, $studentMarks);

            // دمج معلومات المادة مع النتائج
            $detailedSubjects[] = array_merge([
                'subject' => $subjectData['subject'],
                'grading_rule' => $gradingRule,
            ], $calculatedResult);
        }

        return [
            'student' => $student,
            'section' => $section,
            'subjects' => $detailedSubjects,
        ];
    }
}
