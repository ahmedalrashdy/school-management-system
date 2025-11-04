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
            // جلب البيانات الأساسية
            $academicYears = AcademicYear::all();
            $grades = Grade::all();
            $subjects = Subject::all();

            if ($academicYears->isEmpty()) {
                $this->command->error('لا توجد سنوات دراسية. يرجى تشغيل SchoolBasicSeeder أولاً.');

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

            // أسماء الشعب المحتملة
            $sectionNames = ['أ', 'ب', 'ج'];

            // لكل سنة دراسية
            foreach ($academicYears as $academicYear) {
                $this->command->info("معالجة السنة الدراسية: {$academicYear->name}");

                // جلب الأترام الدراسية لهذه السنة
                $academicTerms = AcademicTerm::where('academic_year_id', $academicYear->id)->get();

                if ($academicTerms->isEmpty()) {
                    $this->command->warn("لا توجد أترام دراسية للسنة: {$academicYear->name}");

                    continue;
                }

                // لكل صف دراسي
                foreach ($grades as $grade) {
                    // لكل ترم دراسي
                    foreach ($academicTerms as $academicTerm) {
                        // 1. إنشاء الشعب (عدد عشوائي بين 1-3)
                        $sectionsCount = rand(1, 3);

                        for ($i = 0; $i < $sectionsCount; $i++) {
                            $sectionName = $sectionNames[$i] ?? chr(65 + $i); // أ، ب، ج أو A, B, C

                            Section::firstOrCreate([
                                'academic_year_id' => $academicYear->id,
                                'grade_id' => $grade->id,
                                'academic_term_id' => $academicTerm->id,
                                'name' => $sectionName,
                            ], [
                                'capacity' => null, // يمكن تحديد السعة لاحقاً
                            ]);

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

                        // 3. إضافة مواد عشوائية للمنهج (4-6 مواد)
                        // التحقق من المواد الموجودة بالفعل في المنهج
                        $existingSubjectIds = $curriculum->subjects->pluck('id')->toArray();

                        // إذا كان المنهج جديداً أو لا يحتوي على مواد، أضف مواد جديدة
                        if (empty($existingSubjectIds)) {
                            $subjectsCount = rand(4, 6);
                            $availableSubjects = $subjects->whereNotIn('id', $existingSubjectIds);
                            $selectedSubjects = $availableSubjects->random(min($subjectsCount, $availableSubjects->count()));

                            foreach ($selectedSubjects as $subject) {
                                $curriculum->subjects()->attach($subject->id);
                                $curriculumSubjectsCreated++;
                            }
                        }
                    }
                }
            }

            $this->command->info("تم إنشاء {$sectionsCreated} شعبة.");
            $this->command->info("تم إنشاء {$curriculumsCreated} منهج دراسي.");
            $this->command->info("تم إضافة {$curriculumSubjectsCreated} مادة إلى المناهج.");
        });
    }
}
