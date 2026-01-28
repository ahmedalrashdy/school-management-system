<?php

namespace Database\Seeders;

use App\Enums\RelationToStudentEnum;
use App\Models\Guardian;
use App\Models\Student;
use DB;
use Illuminate\Database\Seeder;

class GuardiansSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            // جلب جميع الطلاب
            $students = Student::all();

            if ($students->isEmpty()) {
                $this->command->error('لا توجد طلاب. يرجى تشغيل StudentsSeeder أولاً.');

                return;
            }

            $this->command->info("بدء إنشاء أولياء الأمور لـ {$students->count()} طالب...");

            $guardiansCreated = 0;
            $relationshipsCreated = 0;

            foreach ($students as $student) {
                // ولي أمر واحد ثابت لكل طالب لتقليل العشوائية
                $guardian = Guardian::factory()->create();
                $guardiansCreated++;

                $student->guardians()->attach($guardian->id, [
                    'relation_to_student' => RelationToStudentEnum::Father->value,
                ]);
                $relationshipsCreated++;
            }

            $this->command->info("تم إنشاء {$guardiansCreated} ولي أمر.");
            $this->command->info("تم إنشاء {$relationshipsCreated} علاقة بين أولياء الأمور والطلاب.");
        });
    }
}
