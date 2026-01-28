<?php

namespace Database\Seeders;

use App\Models\AcademicTerm;
use App\Services\SchoolDayService;
use DB;
use Illuminate\Database\Seeder;

class SchoolDaysSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            // جلب الأترام الدراسية للسنة النشطة فقط
            $activeYear = \App\Models\AcademicYear::active()->first();
            if (! $activeYear) {
                $this->command->error('لا توجد سنة دراسية نشطة. يرجى تشغيل SchoolBasicSeeder أولاً.');

                return;
            }

            $academicTerms = AcademicTerm::with('academicYear')
                ->where('academic_year_id', $activeYear->id)
                ->orderBy('start_date')
                ->get();

            if ($academicTerms->isEmpty()) {
                $this->command->error('لا توجد أترام دراسية للسنة النشطة. يرجى تشغيل SchoolBasicSeeder أولاً.');

                return;
            }

            $this->command->info("بدء إنشاء ومزامنة الأيام الدراسية لـ {$academicTerms->count()} فصل دراسي...");

            $schoolDayService = app(SchoolDayService::class);
            $termsProcessed = 0;
            $daysCreated = 0;

            foreach ($academicTerms as $term) {
                // حساب عدد الأيام التي سيتم إنشاؤها
                $existingDaysCount = $term->schoolDays()->count();

                // إنشاء الأيام الدراسية للترم (تخطي الترمات غير المرتبطة بسنة صحيحة)
                if (! $term->academicYear) {
                    $this->command->warn("تم تخطي ترم بدون سنة دراسية صالحة (ID: {$term->id})");
                    continue;
                }

                $schoolDayService->generateDaysForTerm($term);

                // حساب عدد الأيام الجديدة
                $newDaysCount = $term->schoolDays()->count() - $existingDaysCount;

                if ($newDaysCount > 0) {
                    $daysCreated += $newDaysCount;
                    $this->command->info("تم إنشاء {$newDaysCount} يوم دراسي للترم: {$term->name} ({$term->academicYear->name})");
                } else {
                    $this->command->info("تم مزامنة الأيام الدراسية للترم: {$term->name} ({$term->academicYear->name}) - جميع الأيام موجودة مسبقاً");
                }

                $termsProcessed++;

                if ($termsProcessed % 10 == 0) {
                    $this->command->info("تم معالجة {$termsProcessed} فصل دراسي...");
                }
            }

            $this->command->info("تم إنشاء ومزامنة الأيام الدراسية لـ {$termsProcessed} فصل دراسي.");
            $this->command->info("إجمالي الأيام الدراسية الجديدة: {$daysCreated} يوم.");
        });
    }
}
