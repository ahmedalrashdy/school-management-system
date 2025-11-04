<?php

namespace Database\Seeders;

use App\Models\Curriculum;
use App\Models\CurriculumSubject;
use App\Models\Section;
use App\Models\Teacher;
use App\Models\TeacherAssignment;
use DB;
use Illuminate\Database\Seeder;

class TeachersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            // 1. إنشاء 50 مدرس
            $teachersCount = 50;
            $existingTeachersCount = Teacher::count();

            if ($existingTeachersCount < $teachersCount) {
                $needed = $teachersCount - $existingTeachersCount;
                $this->command->info("إنشاء {$needed} مدرس...");

                Teacher::factory($needed)->create();
                $this->command->info("تم إنشاء {$needed} مدرس.");
            } else {
                $this->command->info("يوجد بالفعل {$existingTeachersCount} مدرس.");
            }

            // 2. جلب جميع المدرسين
            $teachers = Teacher::all();

            if ($teachers->isEmpty()) {
                $this->command->error('لا توجد مدرسين.');

                return;
            }

            // 3. جلب جميع الشعب الدراسية
            $sections = Section::with(['academicYear', 'grade', 'academicTerm'])->get();

            if ($sections->isEmpty()) {
                $this->command->error('لا توجد شعب دراسية. يرجى تشغيل SectionsAndCurriculumsSeeder أولاً.');

                return;
            }

            $this->command->info("بدء تعيين المدرسين على المناهج الدراسية لـ {$sections->count()} شعبة...");

            $assignmentsCreated = 0;

            // 4. لكل شعبة، تعيين مدرسين للمواد
            foreach ($sections as $section) {
                // جلب المنهج الدراسي الذي يطابق الشعبة
                $curriculum = Curriculum::where('academic_year_id', $section->academic_year_id)
                    ->where('grade_id', $section->grade_id)
                    ->where('academic_term_id', $section->academic_term_id)
                    ->first();

                if (! $curriculum) {
                    $this->command->warn("لا يوجد منهج دراسي للشعبة {$section->id} (الصف: {$section->grade->name}, السنة: {$section->academicYear->name}, الترم: {$section->academicTerm->name})");

                    continue;
                }

                // جلب مواد المنهج الدراسي
                $curriculumSubjects = CurriculumSubject::where('curriculum_id', $curriculum->id)->get();

                if ($curriculumSubjects->isEmpty()) {
                    $this->command->warn("لا توجد مواد في المنهج الدراسي للشعبة {$section->id}");

                    continue;
                }

                // لكل مادة في المنهج
                foreach ($curriculumSubjects as $curriculumSubject) {
                    // التحقق من وجود تعيين مسبق
                    $existingAssignment = TeacherAssignment::where('curriculum_subject_id', $curriculumSubject->id)
                        ->where('section_id', $section->id)
                        ->first();

                    if ($existingAssignment) {
                        continue; // يوجد تعيين مسبق
                    }

                    // البحث عن مدرس متاح (لم يدرس أكثر من 4 مواد في نفس العام الدراسي)
                    $availableTeacher = $this->findAvailableTeacher($teachers, $section->academic_year_id);

                    if (! $availableTeacher) {
                        $this->command->warn("لا يوجد مدرس متاح للشعبة {$section->id} - المادة {$curriculumSubject->id} (جميع المدرسين لديهم 4 مواد في هذا العام)");

                        continue;
                    }

                    // إنشاء التعيين
                    TeacherAssignment::create([
                        'curriculum_subject_id' => $curriculumSubject->id,
                        'teacher_id' => $availableTeacher->id,
                        'section_id' => $section->id,
                    ]);

                    $assignmentsCreated++;
                }

                if ($section->id % 50 == 0) {
                    $this->command->info("تم معالجة {$section->id} شعبة...");
                }
            }

            $this->command->info("تم إنشاء {$assignmentsCreated} تعيين مدرس.");
        });
    }

    /**
     * البحث عن مدرس متاح (لم يدرس أكثر من 4 مواد في نفس العام الدراسي).
     */
    protected function findAvailableTeacher($teachers, int $academicYearId): ?Teacher
    {
        // ترتيب المدرسين عشوائياً لضمان التوزيع العادل
        $shuffledTeachers = $teachers->shuffle();

        foreach ($shuffledTeachers as $teacher) {
            // حساب عدد المواد التي يدرسها المدرس في هذا العام الدراسي
            // استخدام join مباشرة لتحسين الأداء
            $subjectsCount = DB::table('teacher_assignments')
                ->join('curriculum_subject', 'teacher_assignments.curriculum_subject_id', '=', 'curriculum_subject.id')
                ->join('curriculums', 'curriculum_subject.curriculum_id', '=', 'curriculums.id')
                ->where('teacher_assignments.teacher_id', $teacher->id)
                ->where('curriculums.academic_year_id', $academicYearId)
                ->count();

            // إذا كان المدرس يدرس أقل من 4 مواد، يمكن تعيينه
            if ($subjectsCount < 5) {
                return $teacher;
            }
        }

        return null;
    }
}
