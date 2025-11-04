<?php

namespace Database\Seeders;

use App\Enums\AcademicYearStatus;
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
            // جلب البيانات الأساسية
            $academicYears = AcademicYear::orderBy('start_date', 'asc')->get();
            $grades = Grade::orderBy('sort_order', 'asc')->get();

            if ($academicYears->count() < 3) {
                $this->command->error('يجب أن يكون هناك على الأقل 3 سنوات دراسية (1 ماضية + 1 نشطة + 1 قادمة).');
                $this->command->info('يرجى تشغيل SchoolBasicSeeder أولاً.');

                return;
            }

            if ($grades->isEmpty()) {
                $this->command->error('لا توجد صفوف دراسية. يرجى تشغيل SchoolBasicSeeder أولاً.');

                return;
            }

            // عدد الطلاب المراد إنشاؤهم (القيمة الافتراضية: 50)
            // يمكن تغييرها من خلال تعديل القيمة هنا أو تمريرها كمتغير بيئة
            $studentsCount = 100;

            if ($studentsCount <= 0) {
                $studentsCount = 50;
            }

            $this->command->info("بدء إنشاء {$studentsCount} طالب...");

            $studentsCreated = 0;
            $enrollmentsCreated = 0;
            $sectionStudentsCreated = 0;

            // الحصول على السنوات: ماضية، نشطة، قادمة
            $archivedYear = $academicYears->where('status', AcademicYearStatus::Archived)->first();
            $activeYear = $academicYears->where('status', AcademicYearStatus::Active)->first();
            $upcomingYear = $academicYears->where('status', AcademicYearStatus::Upcoming)->first();

            if (!$archivedYear) {
                $this->command->error('لا توجد سنة دراسية مؤرشفة. يرجى التأكد من وجود سنة بصفة Archived.');

                return;
            }

            if (!$activeYear) {
                $this->command->error('لا توجد سنة دراسية نشطة. يرجى التأكد من وجود سنة بصفة Active.');

                return;
            }

            if (!$upcomingYear) {
                $this->command->error('لا توجد سنة دراسية قادمة. يرجى التأكد من وجود سنة بصفة Upcoming.');

                return;
            }

            // إنشاء الطلاب
            for ($i = 0; $i < $studentsCount; $i++) {
                // إنشاء الطالب
                $student = Student::factory()->create();
                $studentsCreated++;

                // تحديد الصفوف التي سيدرس فيها الطالب (3 صفوف متتالية)
                // الصف الأول: في السنة الماضية (Archived)
                // الصف الثاني: في السنة النشطة (Active)
                // الصف الثالث: في السنة القادمة (Upcoming)
                $maxStartingIndex = max(0, $grades->count() - 3);
                $startingGradeIndex = rand(0, $maxStartingIndex);
                $grade1 = $grades->get($startingGradeIndex); // السنة الماضية
                $grade2 = $grades->get($startingGradeIndex + 1); // السنة النشطة
                $grade3 = $grades->get($startingGradeIndex + 2); // السنة القادمة

                // 1. إنشاء enrollment للسنة الماضية (Archived)
                Enrollment::firstOrCreate([
                    'student_id' => $student->id,
                    'academic_year_id' => $archivedYear->id,
                ], [
                    'grade_id' => $grade1->id,
                ]);
                $enrollmentsCreated++;

                // تسجيل الطالب في شعب السنة الماضية لكل ترم
                $archivedTerms = AcademicTerm::where('academic_year_id', $archivedYear->id)->get();
                foreach ($archivedTerms as $academicTerm) {
                    $section = Section::where('academic_year_id', $archivedYear->id)
                        ->where('grade_id', $grade1->id)
                        ->where('academic_term_id', $academicTerm->id)
                        ->first();

                    if ($section) {
                        $student->sections()->syncWithoutDetaching([$section->id]);
                        $sectionStudentsCreated++;
                    }
                }

                // 2. إنشاء enrollment للسنة النشطة (Active)
                Enrollment::firstOrCreate([
                    'student_id' => $student->id,
                    'academic_year_id' => $activeYear->id,
                ], [
                    'grade_id' => $grade2->id,
                ]);
                $enrollmentsCreated++;

                // تسجيل الطالب في شعب السنة النشطة لكل ترم
                $activeTerms = AcademicTerm::where('academic_year_id', $activeYear->id)->get();
                foreach ($activeTerms as $academicTerm) {
                    $section = Section::where('academic_year_id', $activeYear->id)
                        ->where('grade_id', $grade2->id)
                        ->where('academic_term_id', $academicTerm->id)
                        ->first();

                    if ($section) {
                        $student->sections()->syncWithoutDetaching([$section->id]);
                        $sectionStudentsCreated++;
                    }
                }

                // 3. إنشاء enrollment للسنة القادمة (Upcoming)
                Enrollment::firstOrCreate([
                    'student_id' => $student->id,
                    'academic_year_id' => $upcomingYear->id,
                ], [
                    'grade_id' => $grade3->id,
                ]);
                $enrollmentsCreated++;

                // تسجيل الطالب في شعب السنة القادمة لكل ترم
                $upcomingTerms = AcademicTerm::where('academic_year_id', $upcomingYear->id)->get();
                foreach ($upcomingTerms as $academicTerm) {
                    $section = Section::where('academic_year_id', $upcomingYear->id)
                        ->where('grade_id', $grade3->id)
                        ->where('academic_term_id', $academicTerm->id)
                        ->first();

                    if ($section) {
                        $student->sections()->syncWithoutDetaching([$section->id]);
                        $sectionStudentsCreated++;
                    }
                }

                if (($i + 1) % 10 == 0) {
                    $this->command->info('تم إنشاء ' . ($i + 1) . ' طالب...');
                }
            }

            $this->command->info("تم إنشاء {$studentsCreated} طالب.");
            $this->command->info("تم إنشاء {$enrollmentsCreated} تسجيل.");
            $this->command->info("تم تسجيل {$sectionStudentsCreated} طالب في الشعب.");
        });
    }
}
