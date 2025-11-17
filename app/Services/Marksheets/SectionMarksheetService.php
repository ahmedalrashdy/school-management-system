<?php

namespace App\Services\Marksheets;

use App\Models\Section;
use App\Models\Student;

class SectionMarksheetService
{
    public function __construct(
        protected GradingDataRepository $gradingRepo,
        protected MarkCalculatorService $calculator,
        protected LetterGradeService $letterGradeService
    ) {}

    public function getSectionMarksheetData(Section $section): array
    {
        // 1. جلب الطلاب
        $students = $this->getStudents($section);

        // 2. جلب الهيكل الموحد (نفس المستخدم في خدمة الطالب)
        $subjectsStructure = $this->gradingRepo->getSubjectsWithRules($section);

        if ($students->isEmpty() || empty($subjectsStructure)) {
            return ['section' => $section, 'students' => [], 'subjects' => $subjectsStructure];
        }

        // 3. جلب جميع الدرجات دفعة واحدة
        $allMarks = $this->gradingRepo->getMarks($students->pluck('id')->toArray(), $subjectsStructure);

        // 4. معالجة البيانات لكل طالب
        $studentsData = [];
        foreach ($students as $student) {
            $studentMarks = $allMarks[$student->id] ?? [];

            $processedStudent = $this->processStudent($student, $subjectsStructure, $studentMarks);
            $studentsData[] = $processedStudent;
        }

        return [
            'section' => $section,
            'students' => $studentsData,
            'subjects' => $subjectsStructure,
        ];
    }

    protected function processStudent(Student $student, array $subjectsStructure, array $marks): array
    {
        $subjectResults = [];
        $totalMarks = 0;
        $totalMaxMarks = 0;

        foreach ($subjectsStructure as $subjectData) {
            $rule = $subjectData['grading_rule'];

            // استخدام نفس الآلة الحاسبة
            $calc = $this->calculator->calculateSubjectResult($rule, $marks);

            // تشكيل البيانات للعرض المختصر في الجدول العريض
            $subjectResults[] = [
                'subject_id' => $subjectData['subject']->id,
                'subject_name' => $subjectData['subject']->name,
                'coursework' => $calc['coursework_total'],
                'coursework_max' => $calc['coursework_max'],
                'final' => $calc['final_mark'],
                'final_max' => $calc['final_max'],
                'total' => $calc['total'],
                'max_total' => $calc['max_total'],
                'percentage' => $calc['percentage'],
                'grade' => $calc['grade'],
            ];

            if ($calc['total'] !== null) {
                $totalMarks += $calc['total'];
                $totalMaxMarks += $calc['max_total'];
            }
        }

        // حساب المعدل العام للطالب
        $overallPercentage = $totalMaxMarks > 0 ? ($totalMarks / $totalMaxMarks) * 100 : 0;

        return [
            'student' => $student,
            'student_name' => $student->first_name.' '.$student->last_name,
            'admission_number' => $student->admission_number,
            'subjects' => $subjectResults,
            'total_marks' => round($totalMarks, 2),
            'total_max_marks' => $totalMaxMarks,
            'overall_percentage' => round($overallPercentage, 2),
            'overall_grade' => $this->letterGradeService->getLetterGrade($overallPercentage),
        ];
    }

    protected function getStudents(Section $section)
    {
        return Student::query()
            ->select('students.*', 'users.first_name', 'users.last_name')
            ->join('section_students', 'section_students.student_id', '=', 'students.id')
            ->join('users', 'users.id', '=', 'students.user_id')
            ->where('section_students.section_id', $section->id)
            ->orderBy('users.first_name')
            ->orderBy('users.last_name')
            ->get();
    }
}
