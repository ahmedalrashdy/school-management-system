<?php

namespace Database\Seeders;

use App\Models\Exam;
use App\Models\Mark;
use App\Models\Section;
use DB;
use Illuminate\Database\Seeder;

class MarksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            // جلب جميع الشعب الدراسية
            $sections = Section::with(['students', 'academicYear', 'academicTerm', 'grade'])
                ->get();

            if ($sections->isEmpty()) {
                $this->command->error('لا توجد شعب دراسية. يرجى تشغيل SectionsAndCurriculumsSeeder أولاً.');

                return;
            }

            $this->command->info("بدء تعبئة الدرجات لـ {$sections->count()} شعبة...");

            $marksCreated = 0;
            $sectionsProcessed = 0;

            foreach ($sections as $section) {
                // جلب الطلاب في الشعبة
                $students = $section->students;

                if ($students->isEmpty()) {
                    continue;
                }

                // جلب جميع الامتحانات للشعبة
                $exams = Exam::where('section_id', $section->id)
                    ->with('examType')
                    ->get();

                if ($exams->isEmpty()) {
                    continue;
                }

                // لكل طالب وكل امتحان
                foreach ($students as $student) {
                    foreach ($exams as $exam) {
                        // التحقق من وجود درجة مسبقة
                        $existingMark = Mark::where('student_id', $student->id)
                            ->where('exam_id', $exam->id)
                            ->first();

                        if ($existingMark) {
                            continue; // يوجد درجة مسبقة
                        }

                        // إنشاء درجة عشوائية
                        $marksObtained = $this->getRandomMark($exam->max_marks);

                        Mark::create([
                            'student_id' => $student->id,
                            'exam_id' => $exam->id,
                            'marks_obtained' => $marksObtained,
                            'notes' => $this->getRandomNotes($marksObtained, $exam->max_marks),
                        ]);

                        $marksCreated++;
                    }
                }

                $sectionsProcessed++;

                if ($sectionsProcessed % 50 == 0) {
                    $this->command->info("تم معالجة {$sectionsProcessed} شعبة...");
                }
            }

            $this->command->info("تم إنشاء {$marksCreated} درجة.");
            $this->command->info("تم معالجة {$sectionsProcessed} شعبة.");
        });
    }

    /**
     * الحصول على درجة عشوائية بناءً على الدرجة القصوى.
     * التوزيع: معظم الطلاب يحصلون على درجات جيدة (60-100% من max_marks)
     */
    protected function getRandomMark(int $maxMarks): float
    {
        $random = rand(1, 100);

        // 70% من الطلاب يحصلون على درجات جيدة (60-100%)
        if ($random <= 70) {
            $percentage = rand(60, 100) / 100;
        }
        // 20% يحصلون على درجات متوسطة (40-60%)
        elseif ($random <= 90) {
            $percentage = rand(40, 60) / 100;
        }
        // 10% يحصلون على درجات ضعيفة (0-40%)
        else {
            $percentage = rand(0, 40) / 100;
        }

        $mark = $maxMarks * $percentage;

        // تقريب إلى رقمين عشريين
        return round($mark, 2);
    }

    /**
     * الحصول على ملاحظات عشوائية حسب الدرجة.
     */
    protected function getRandomNotes(float $marksObtained, int $maxMarks): ?string
    {
        $percentage = ($marksObtained / $maxMarks) * 100;

        $notes = [
            // درجات ممتازة (90-100%)
            [
                'range' => [90, 100],
                'options' => [
                    null,
                    null,
                    'أداء ممتاز',
                    'ممتاز جداً',
                ],
            ],
            // درجات جيدة جداً (80-90%)
            [
                'range' => [80, 90],
                'options' => [
                    null,
                    null,
                    'أداء جيد جداً',
                    'مستوى جيد',
                ],
            ],
            // درجات جيدة (70-80%)
            [
                'range' => [70, 80],
                'options' => [
                    null,
                    null,
                    'أداء جيد',
                ],
            ],
            // درجات مقبولة (60-70%)
            [
                'range' => [60, 70],
                'options' => [
                    null,
                    'يحتاج تحسين',
                ],
            ],
            // درجات ضعيفة (أقل من 60%)
            [
                'range' => [0, 60],
                'options' => [
                    null,
                    'يحتاج متابعة',
                    'يحتاج تحسين',
                ],
            ],
        ];

        foreach ($notes as $noteGroup) {
            if ($percentage >= $noteGroup['range'][0] && $percentage <= $noteGroup['range'][1]) {
                $randomNote = $noteGroup['options'][array_rand($noteGroup['options'])];

                return $randomNote;
            }
        }

        return null;
    }
}
