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

            // أنواع صلة القرابة الشائعة (الأب والأم أولاً)
            $commonRelations = [
                RelationToStudentEnum::Father,
                RelationToStudentEnum::Mother,
            ];

            // أنواع صلة القرابة الإضافية
            $additionalRelations = [
                RelationToStudentEnum::Brother,
                RelationToStudentEnum::Sister,
                RelationToStudentEnum::Grandfather,
                RelationToStudentEnum::Grandmother,
                RelationToStudentEnum::Uncle,
                RelationToStudentEnum::Aunt,
                RelationToStudentEnum::MaternalUncle,
                RelationToStudentEnum::MaternalAunt,
                RelationToStudentEnum::Guardian,
            ];

            foreach ($students as $student) {
                // تحديد عدد أولياء الأمور لهذا الطالب (1-3)
                $guardiansCount = rand(1, 3);

                // نبدأ بالأب والأم إذا كان العدد 2 أو أكثر
                $relationsToUse = [];
                if ($guardiansCount >= 2) {
                    // إضافة الأب والأم
                    $relationsToUse[] = RelationToStudentEnum::Father;
                    $relationsToUse[] = RelationToStudentEnum::Mother;

                    // إذا كان العدد 3، نضيف ولي أمر إضافي
                    if ($guardiansCount === 3) {
                        $relationsToUse[] = fake()->randomElement($additionalRelations);
                    }
                } else {
                    // إذا كان العدد 1 فقط، نختار عشوائياً بين الأب والأم
                    $relationsToUse[] = fake()->randomElement($commonRelations);
                }

                // إنشاء أولياء الأمور وربطهم بالطالب
                foreach ($relationsToUse as $relation) {
                    // إنشاء ولي الأمر
                    $guardian = Guardian::factory()->create();
                    $guardiansCreated++;

                    // ربط ولي الأمر بالطالب
                    $student->guardians()->attach($guardian->id, [
                        'relation_to_student' => $relation->value,
                    ]);
                    $relationshipsCreated++;
                }

                if ($student->id % 100 == 0) {
                    $this->command->info("تم معالجة {$student->id} طالب...");
                }
            }

            $this->command->info("تم إنشاء {$guardiansCreated} ولي أمر.");
            $this->command->info("تم إنشاء {$relationshipsCreated} علاقة بين أولياء الأمور والطلاب.");
        });
    }
}
