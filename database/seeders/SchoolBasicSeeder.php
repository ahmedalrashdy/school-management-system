<?php

namespace Database\Seeders;

use App\Enums\AcademicYearStatus;
use App\Models\AcademicTerm;
use App\Models\AcademicYear;
use App\Models\ExamType;
use App\Models\Grade;
use App\Models\Stage;
use App\Models\Subject;
use Carbon\Carbon;
use DB;
use Illuminate\Database\Seeder;

class SchoolBasicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            // 2. المراحل الدراسية (يمنية)
            $stages = [
                ['name' => 'المرحلة الأساسية', 'sort_order' => 1],
                ['name' => 'المرحلة الثانوية', 'sort_order' => 2],
            ];

            foreach ($stages as $stageData) {
                Stage::firstOrCreate(['name' => $stageData['name']], $stageData);
            }

            $basicStage = Stage::where('name', 'المرحلة الأساسية')->first();
            $secondaryStage = Stage::where('name', 'المرحلة الثانوية')->first();

            $this->command->info('Stages created.');

            // 3. الصفوف الدراسية (12 صف)
            $grades = [
                // التعليم الأساسي (1-9)
                ['name' => 'الصف الأول', 'stage_id' => $basicStage->id, 'sort_order' => 1],
                ['name' => 'الصف الثاني', 'stage_id' => $basicStage->id, 'sort_order' => 2],
                ['name' => 'الصف الثالث', 'stage_id' => $basicStage->id, 'sort_order' => 3],
                ['name' => 'الصف الرابع', 'stage_id' => $basicStage->id, 'sort_order' => 4],
                ['name' => 'الصف الخامس', 'stage_id' => $basicStage->id, 'sort_order' => 5],
                ['name' => 'الصف السادس', 'stage_id' => $basicStage->id, 'sort_order' => 6],
                ['name' => 'الصف السابع', 'stage_id' => $basicStage->id, 'sort_order' => 7],
                ['name' => 'الصف الثامن', 'stage_id' => $basicStage->id, 'sort_order' => 8],
                ['name' => 'الصف التاسع', 'stage_id' => $basicStage->id, 'sort_order' => 9],

                // التعليم الثانوي (10-12)
                ['name' => 'الصف الأول الثانوي', 'stage_id' => $secondaryStage->id, 'sort_order' => 10],
                ['name' => 'الصف الثاني الثانوي', 'stage_id' => $secondaryStage->id, 'sort_order' => 11],
                ['name' => 'الصف الثالث الثانوي', 'stage_id' => $secondaryStage->id, 'sort_order' => 12],
            ];

            foreach ($grades as $gradeData) {
                Grade::firstOrCreate(['name' => $gradeData['name']], $gradeData);
            }

            $this->command->info('Grades created.');

            // 4. المواد الدراسية (شاملة للمنهج اليمني)
            $subjects = [
                'القرآن الكريم', 'التربية الإسلامية', 'اللغة العربية',
                'الرياضيات', 'العلوم', 'الاجتماعيات',
                'التربية الوطنية', 'اللغة الإنجليزية',
                'الأحياء', 'الكيمياء', 'الفيزياء',
                'التاريخ', 'الجغرافيا', 'الحاسوب', 'التربية البدنية',
            ];

            foreach ($subjects as $index => $subjectName) {
                Subject::firstOrCreate(['name' => $subjectName], ['sort_order' => $index + 1]);
            }

            $this->command->info('Subjects created.');

            // 5. أنواع الاختبارات
            $examTypes = [
                ['name' => 'اختبار شهري', 'sort_order' => 1],
                ['name' => 'اختبار نهاية الفصل', 'sort_order' => 2],
                ['name' => 'واجبات منزلية', 'sort_order' => 3],
                ['name' => 'مشاركة صفية', 'sort_order' => 4],
                ['name' => 'الحضور والسلوك', 'sort_order' => 5],
            ];

            foreach ($examTypes as $type) {
                ExamType::firstOrCreate(['name' => $type['name']], $type);
            }

            $this->command->info('Exam Types created.');

            // 6. سنة دراسية واحدة نشطة مع ترمين فقط (بدون عشوائية)
            $now = Carbon::now();
            $startYear = $now->month >= 9 ? $now->year : $now->year - 1;
            $startYearDate = Carbon::create($startYear, 9, 1)->startOfDay();
            $endYearDate = Carbon::create($startYear + 1, 6, 30)->endOfDay();

            $yearName = $startYearDate->format('Y').' - '.$endYearDate->format('Y');

            $academicYear = AcademicYear::updateOrCreate([
                'name' => $yearName,
            ], [
                'start_date' => $startYearDate,
                'end_date' => $endYearDate,
                'status' => AcademicYearStatus::Active,
            ]);

            // إنشاء الأترام (الفصول الدراسية) للسنة
            $term1Start = $startYearDate->copy();
            $term1End = $startYearDate->copy()->addMonths(4)->endOfMonth();
            $term2Start = $term1End->copy()->addDay();
            $term2End = $endYearDate->copy();

            AcademicTerm::updateOrCreate([
                'academic_year_id' => $academicYear->id,
                'name' => 'الفصل الدراسي الأول',
            ], [
                'start_date' => $term1Start,
                'end_date' => $term1End,
                'is_active' => $now->between($term1Start, $term1End),
            ]);

            AcademicTerm::updateOrCreate([
                'academic_year_id' => $academicYear->id,
                'name' => 'الفصل الدراسي الثاني',
            ], [
                'start_date' => $term2Start,
                'end_date' => $term2End,
                'is_active' => $now->between($term2Start, $term2End),
            ]);

            $this->command->info('Academic Year and Terms created (1 Active year with 2 terms).');
        });
    }
}
