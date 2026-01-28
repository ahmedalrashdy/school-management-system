<?php

namespace Database\Seeders;

use App\Models\AcademicTerm;
use App\Models\AcademicYear;
use App\Models\Curriculum;
use App\Models\Grade;
use App\Models\Section;
use App\Models\Subject;
use DB;
use Illuminate\Database\Seeder;

class SectionsAndCurriculumsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            // جلب البيانات الأساسية (سنة واحدة نشطة فقط)
            $academicYear = AcademicYear::active()->first();
            $grades = Grade::orderBy('sort_order')->get();
            $subjects = Subject::orderBy('sort_order')->get();

            if (! $academicYear) {
                $this->command->error('لا توجد سنة دراسية نشطة. يرجى تشغيل SchoolBasicSeeder أولاً.');

                return;
            }

            if ($grades->isEmpty()) {
                $this->command->error('لا توجد صفوف دراسية. يرجى تشغيل SchoolBasicSeeder أولاً.');

                return;
            }

            if ($subjects->isEmpty()) {
                $this->command->error('لا توجد مواد دراسية. يرجى تشغيل SchoolBasicSeeder أولاً.');

                return;
            }

            $sectionsCreated = 0;
            $curriculumsCreated = 0;
            $curriculumSubjectsCreated = 0;

            $this->command->info("معالجة السنة الدراسية: {$academicYear->name}");

            // جلب الأترام الدراسية لهذه السنة
            $academicTerms = AcademicTerm::where('academic_year_id', $academicYear->id)
                ->orderBy('start_date')
                ->get();

            if ($academicTerms->isEmpty()) {
                $this->command->warn("لا توجد أترام دراسية للسنة: {$academicYear->name}");

                return;
            }

            // لكل صف دراسي
            foreach ($grades as $grade) {
                // لكل ترم دراسي
                foreach ($academicTerms as $academicTerm) {
                    // 1. إنشاء شعبة واحدة ثابتة لكل صف في كل ترم
                    $section = Section::firstOrCreate([
                        'academic_year_id' => $academicYear->id,
                        'grade_id' => $grade->id,
                        'academic_term_id' => $academicTerm->id,
                        'name' => 'أ',
                    ], [
                        'capacity' => 30,
                    ]);
                    if ($section->wasRecentlyCreated) {
                        $sectionsCreated++;
                    }

                    // 2. إنشاء المنهج الدراسي لكل صف في كل ترم
                    $curriculum = Curriculum::firstOrCreate([
                        'academic_year_id' => $academicYear->id,
                        'grade_id' => $grade->id,
                        'academic_term_id' => $academicTerm->id,
                    ]);

                    if ($curriculum->wasRecentlyCreated) {
                        $curriculumsCreated++;
                    }

                    // 3. إضافة كل المواد للمنهج بشكل ثابت (بدون عشوائية)
                    $subjectIds = $subjects->pluck('id')->all();
                    $beforeCount = $curriculum->subjects()->count();
                    $curriculum->subjects()->sync($subjectIds);
                    $afterCount = $curriculum->subjects()->count();
                    $curriculumSubjectsCreated += max(0, $afterCount - $beforeCount);
                }
            }

            $this->command->info("تم إنشاء {$sectionsCreated} شعبة.");
            $this->command->info("تم إنشاء {$curriculumsCreated} منهج دراسي.");
            $this->command->info("تم إضافة {$curriculumSubjectsCreated} مادة إلى المناهج.");
        });
    }
}
