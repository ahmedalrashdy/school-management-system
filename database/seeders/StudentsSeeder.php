<?php

namespace Database\Seeders;

use App\Models\AcademicTerm;
use App\Models\AcademicYear;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\Section;
use App\Models\Student;
use DB;
use Illuminate\Database\Seeder;

class StudentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            // جلب البيانات الأساسية (سنة واحدة نشطة فقط)
            $academicYear = AcademicYear::active()->first();
            $grades = Grade::orderBy('sort_order', 'asc')->get();

            if (! $academicYear) {
                $this->command->error('لا توجد سنة دراسية نشطة. يرجى تشغيل SchoolBasicSeeder أولاً.');

                return;
            }

            if ($grades->isEmpty()) {
                $this->command->error('لا توجد صفوف دراسية. يرجى تشغيل SchoolBasicSeeder أولاً.');

                return;
            }

            $terms = AcademicTerm::where('academic_year_id', $academicYear->id)
                ->orderBy('start_date')
                ->get();
            if ($terms->isEmpty()) {
                $this->command->error('لا توجد أترام دراسية للسنة النشطة. يرجى تشغيل SchoolBasicSeeder أولاً.');

                return;
            }

            // عدد ثابت لكل صف لتجنب العشوائية
            $studentsPerGrade = 4;
            $studentsCount = $grades->count() * $studentsPerGrade;

            $this->command->info("بدء إنشاء {$studentsCount} طالب لسنة دراسية واحدة...");

            $studentsCreated = 0;
            $enrollmentsCreated = 0;
            $sectionStudentsCreated = 0;

            // إنشاء الطلاب وتوزيعهم على الصفوف بشكل ثابت
            foreach ($grades as $grade) {
                for ($i = 0; $i < $studentsPerGrade; $i++) {
                    $student = Student::factory()->create();
                    $studentsCreated++;

                    // Enrollment للسنة النشطة فقط
                    Enrollment::firstOrCreate([
                        'student_id' => $student->id,
                        'academic_year_id' => $academicYear->id,
                    ], [
                        'grade_id' => $grade->id,
                    ]);
                    $enrollmentsCreated++;

                    // ربط الطالب بالشعبة لكل ترم
                    foreach ($terms as $term) {
                        $section = Section::where('academic_year_id', $academicYear->id)
                            ->where('grade_id', $grade->id)
                            ->where('academic_term_id', $term->id)
                            ->first();

                        if ($section) {
                            $student->sections()->syncWithoutDetaching([$section->id]);
                            $sectionStudentsCreated++;
                        }
                    }
                }
            }

            $this->command->info("تم إنشاء {$studentsCreated} طالب.");
            $this->command->info("تم إنشاء {$enrollmentsCreated} تسجيل.");
            $this->command->info("تم تسجيل {$sectionStudentsCreated} طالب في الشعب.");
        });
    }
}
